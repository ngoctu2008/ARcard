<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Phạm Ngọc Tú <ngoctu.dnkd@gmail.com>
 * @Copyright (C) 2024 Phạm Ngọc Tú. All rights reserved
 * @License: Not free source, more information contact ngoctu.dnkd@gmail.com
 * @Createdate Thu, 01 Jan 2024 00:00:00 GMT
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) {
    die('Stop!!!');
}

$page_title = $lang_module['fields_manage'];
$table_fields = NV_PREFIXLANG . "_" . $module_data . "_fields";

// AJAX: Get Alias (Field Name)
if ($nv_Request->isset_request('get_alias_title', 'post')) {
    $title = $nv_Request->get_title('get_alias_title', 'post', '');
    $alias = change_alias($title);
    $alias = str_replace('-', '_', $alias); // Fields usually use underscores
    die($alias);
}

// AJAX: Change Status
if ($nv_Request->isset_request('change_status', 'post')) {
    $fid = $nv_Request->get_int('fid', 'post', 0); // User sample uses catid, here fid maps to id param in JS if we adjust
    // JS sends 'catid='+id. I should check param name in JS.
    // Sample JS: 'change_status=1&catid='+id.
    // So I must look for 'catid' even if it is a field ID, OR update JS.
    // To strictly follow sample code, the JS sends `catid`.
    // I will use `catid` in PHP request to match the JS, but logically it is FID.
    $id = $nv_Request->get_int('catid', 'post', 0);

    if ($id > 0) {
        $sql = "SELECT status FROM " . $table_fields . " WHERE fid=" . $id;
        $status = $db->query($sql)->fetchColumn();
        $new_status = ($status == 1) ? 0 : 1;
        $db->query("UPDATE " . $table_fields . " SET status=" . $new_status . " WHERE fid=" . $id);
        $nv_Cache->delMod($module_name);
        die('OK_' . $new_status);
    }
    die('NO');
}

// AJAX: Change Weight
if ($nv_Request->isset_request('ajax_action', 'post')) {
    $id = $nv_Request->get_int('catid', 'post', 0);
    $new_vid = $nv_Request->get_int('new_vid', 'post', 0);
    $content = 'NO_' . $id;
    if ($new_vid > 0) {
        $sql = 'SELECT fid FROM ' . NV_PREFIXLANG . '_' . $module_data . '_fields WHERE fid!=' . $id . ' ORDER BY weight ASC';
        $result = $db->query($sql);
        $weight = 0;
        while ($row = $result->fetch()) {
            ++$weight;
            if ($weight == $new_vid) ++$weight;
            $sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_fields SET weight=' . $weight . ' WHERE fid=' . $row['fid'];
            $db->query($sql);
        }
        $sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_fields SET weight=' . $new_vid . ' WHERE fid=' . $id;
        $db->query($sql);
        $content = 'OK_' . $id;
    }
    $nv_Cache->delMod($module_name);
    include NV_ROOTDIR . '/includes/header.php';
    echo $content;
    include NV_ROOTDIR . '/includes/footer.php';
    die();
}

$fid = $nv_Request->get_int('fid', 'get,post', 0);
$error = '';

if ($nv_Request->isset_request('save', 'post')) {
    $row = [];
    $row['field'] = $nv_Request->get_title('alias', 'post', ''); // Input name is alias in template
    if (empty($row['field'])) {
        $row['field'] = $nv_Request->get_title('field', 'post', ''); // Fallback
    }
    $row['title'] = $nv_Request->get_title('title', 'post', '');
    $row['description'] = $nv_Request->get_title('description', 'post', '');
    $row['required'] = $nv_Request->get_int('required', 'post', 0); // Checkbox handling? Not in sample template but logic needed.
    $row['field_type'] = $nv_Request->get_title('field_type', 'post', 'textbox');
    $row['field_choices'] = $nv_Request->get_string('field_choices', 'post', '');
    $row['catids'] = $nv_Request->get_array('catids', 'post', []);
    $row['catids'] = !empty($row['catids']) ? implode(',', $row['catids']) : '0';

    // Basic validation
    if (empty($row['field']) || empty($row['title'])) {
        $error = $lang_module['error_required'];
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $row['field'])) {
        $error = "Field name must be alphanumeric";
    } else {
        // Check duplicate field name
        $sql = "SELECT COUNT(*) FROM " . $table_fields . " WHERE field=:field";
        if ($fid > 0) {
            $sql .= " AND fid!=" . $fid;
        }
        $sth = $db->prepare($sql);
        $sth->bindParam(':field', $row['field'], PDO::PARAM_STR);
        $sth->execute();
        if ($sth->fetchColumn() > 0) {
            $error = "Field name exists!";
        } else {
            // Save logic
             if ($fid > 0) {
                 // Get old field name to rename column if needed
                $old_row = $db->query("SELECT field FROM " . $table_fields . " WHERE fid=" . $fid)->fetch();

                $sql = "UPDATE " . $table_fields . " SET
                    field=:field, title=:title, description=:description, required=:required,
                    field_type=:field_type, field_choices=:field_choices, catids=:catids
                    WHERE fid=" . $fid;
                $data = [
                    ':field' => $row['field'],
                    ':title' => $row['title'],
                    ':description' => $row['description'],
                    ':required' => $row['required'],
                    ':field_type' => $row['field_type'],
                    ':field_choices' => $row['field_choices'],
                    ':catids' => $row['catids']
                ];
                $sth = $db->prepare($sql);
                $sth->execute($data);

                // Alter table if field name changed
                if ($old_row['field'] != $row['field']) {
                    // Check if target column exists
                    $col_exists = $db->query("SHOW COLUMNS FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows LIKE '" . $row['field'] . "'")->fetchColumn();
                    if (!$col_exists) {
                        $db->query("ALTER TABLE " . NV_PREFIXLANG . "_" . $module_data . "_rows CHANGE `" . $old_row['field'] . "` `" . $row['field'] . "` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL");
                    }
                }
             } else {
                 $sql = "INSERT INTO " . $table_fields . "
                    (field, title, description, required, field_type, field_choices, catids, status) VALUES
                    (:field, :title, :description, :required, :field_type, :field_choices, :catids, 1)";
                 $data = [
                    ':field' => $row['field'],
                    ':title' => $row['title'],
                    ':description' => $row['description'],
                    ':required' => $row['required'],
                    ':field_type' => $row['field_type'],
                    ':field_choices' => $row['field_choices'],
                    ':catids' => $row['catids']
                ];
                $sth = $db->prepare($sql);
                $sth->execute($data);
                $fid = $db->lastInsertId();

                if ($fid > 0) {
                    $count = $db->query("SELECT COUNT(*) FROM " . $table_fields)->fetchColumn();
                    $db->query("UPDATE " . $table_fields . " SET weight=" . $count . " WHERE fid=" . $fid);
                }

                // Alter table to add column
                $col_exists = $db->query("SHOW COLUMNS FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows LIKE '" . $row['field'] . "'")->fetchColumn();
                if (!$col_exists) {
                    $db->query("ALTER TABLE " . NV_PREFIXLANG . "_" . $module_data . "_rows ADD `" . $row['field'] . "` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL");
                }
             }
             $nv_Cache->delMod($module_name);
             Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=fields');
             die();
        }
    }
}

if ($nv_Request->isset_request('delete_id', 'get')) {
    $fid = $nv_Request->get_int('delete_id', 'get', 0);
    if ($fid > 0) {
        $row = $db->query("SELECT field FROM " . $table_fields . " WHERE fid=" . $fid)->fetch();
        if ($row) {
            $db->query("DELETE FROM " . $table_fields . " WHERE fid=" . $fid);
            $db->query("ALTER TABLE " . NV_PREFIXLANG . "_" . $module_data . "_rows DROP COLUMN `" . $row['field'] . "`");
            $nv_Cache->delMod($module_name);
            Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=fields');
            die();
        }
    }
}

// Prepare View
$xtpl = new XTemplate('fields.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', 'fields');

if (!empty($error)) {
    $xtpl->assign('ERROR', $error);
    $xtpl->parse('main.error');
}

// List fields
$sql = "SELECT * FROM " . $table_fields . " ORDER BY weight ASC";
$result = $db->query($sql);
$array_fields = [];
while ($r = $result->fetch()) {
    $array_fields[] = $r;
}
$num_fields = count($array_fields);

foreach ($array_fields as $r) {
    $r['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=fields&fid=' . $r['fid'];
    $r['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=fields&delete_id=' . $r['fid'];
    $r['status_check'] = ($r['status'] == 1) ? 'checked' : '';

    // Weight Loop
    for ($i = 1; $i <= $num_fields; $i++) {
        $xtpl->assign('WEIGHT', [
            'key' => $i,
            'title' => $i,
            'selected' => ($i == $r['weight']) ? 'selected="selected"' : ''
        ]);
        $xtpl->parse('main.view.loop.weight_loop');
    }

    $xtpl->assign('CHECK', $r['status_check']);
    $xtpl->assign('VIEW', $r);
    $xtpl->parse('main.view.loop');
}
$xtpl->parse('main.view');

// Edit Form Data
if ($fid > 0) {
    $row = $db->query("SELECT * FROM " . $table_fields . " WHERE fid=" . $fid)->fetch();
    $caption = $lang_module['edit_field'];
    $row['alias'] = $row['field']; // Map field to alias
} else {
    $row = ['fid' => 0, 'field' => '', 'alias' => '', 'title' => '', 'description' => '', 'required' => 0, 'field_type' => 'textbox', 'field_choices' => '', 'catids' => '0'];
    $caption = $lang_module['add_field'];
}

// Categories list for assignment
$sql = "SELECT catid, title FROM " . NV_PREFIXLANG . "_" . $module_data . "_cat ORDER BY weight ASC";
$result = $db->query($sql);
$catids_arr = explode(',', $row['catids']);

while ($cat = $result->fetch()) {
    $cat['checked'] = (in_array($cat['catid'], $catids_arr) || $row['catids'] == '0') ? 'checked' : '';
    // Note: If '0', implies ALL. But UI usually allows checking specific ones.
    // If saving '0', maybe we need a "Check All" or "All Categories" option.
    // Let's implement: "All" checkbox (value 0) + list of cats.
    // For now, let's just list cats. If none checked, maybe default to all or none?
    // Let's assume user must check at least one or we have a specific "Ap dung tat ca" checkbox.
    $xtpl->assign('CAT', $cat);
    $xtpl->parse('main.cat_list');
}

// Check "All" logic? If $row['catids'] == '0', check all boxes or check a special box?
// Let's rely on standard checkboxes. If user checks all, it saves '1,2,3'.
// If user wants '0' (all future cats too), we need a specific input.
// Added a specific checkbox for 'All'
$is_all = ($row['catids'] == '0');
$xtpl->assign('ALL_CHECKED', $is_all ? 'checked' : '');

$xtpl->assign('CAPTION', $caption);
$xtpl->assign('ROW', $row); // Use ROW to match sample

// Type select
$types = ['textbox' => 'Textbox', 'number' => 'Number', 'textarea' => 'Textarea', 'editor' => 'Editor', 'select' => 'Select Box', 'date' => 'Date'];
foreach ($types as $key => $val) {
    $xtpl->assign('TYPE', ['key' => $key, 'title' => $val, 'selected' => ($key == $row['field_type']) ? 'selected' : '']);
    $xtpl->parse('main.field_type');
}

$xtpl->assign('REQUIRED_CHECKED', $row['required'] ? 'checked' : '');

$xtpl->parse('main.auto_get_alias');
$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
