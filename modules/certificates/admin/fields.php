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

$fid = $nv_Request->get_int('fid', 'get,post', 0);
$error = '';

if ($nv_Request->isset_request('save', 'post')) {
    $row = [];
    $row['field'] = $nv_Request->get_title('field', 'post', '');
    $row['title'] = $nv_Request->get_title('title', 'post', '');
    $row['description'] = $nv_Request->get_title('description', 'post', '');
    $row['required'] = $nv_Request->get_int('required', 'post', 0);
    $row['field_type'] = $nv_Request->get_title('field_type', 'post', 'textbox');
    $row['field_choices'] = $nv_Request->get_string('field_choices', 'post', ''); // Choices: key|label

    // Basic validation
    if (empty($row['field']) || empty($row['title'])) {
        $error = $lang_module['error_required'];
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $row['field'])) {
        $error = "Field name must be alphanumeric";
    } else {
        // Check duplicate field name
        $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_fields WHERE field=:field";
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
                $old_row = $db->query("SELECT field FROM " . NV_PREFIXLANG . "_" . $module_data . "_fields WHERE fid=" . $fid)->fetch();

                $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_fields SET
                    field=:field, title=:title, description=:description, required=:required,
                    field_type=:field_type, field_choices=:field_choices
                    WHERE fid=" . $fid;
                $data = [
                    ':field' => $row['field'],
                    ':title' => $row['title'],
                    ':description' => $row['description'],
                    ':required' => $row['required'],
                    ':field_type' => $row['field_type'],
                    ':field_choices' => $row['field_choices']
                ];
                $db->query_check($sql, $data);

                // Alter table if field name changed
                if ($old_row['field'] != $row['field']) {
                    $db->query("ALTER TABLE " . NV_PREFIXLANG . "_" . $module_data . "_rows CHANGE `" . $old_row['field'] . "` `" . $row['field'] . "` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL");
                }
             } else {
                 $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_fields
                    (field, title, description, required, field_type, field_choices, status) VALUES
                    (:field, :title, :description, :required, :field_type, :field_choices, 1)";
                 $data = [
                    ':field' => $row['field'],
                    ':title' => $row['title'],
                    ':description' => $row['description'],
                    ':required' => $row['required'],
                    ':field_type' => $row['field_type'],
                    ':field_choices' => $row['field_choices']
                ];
                $fid = $db->insert_id($sql, 'fid', $data);

                // Alter table to add column
                // Assuming TEXT for simplicity for now. In real Users module, it varies.
                $db->query("ALTER TABLE " . NV_PREFIXLANG . "_" . $module_data . "_rows ADD `" . $row['field'] . "` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL");
             }
             nv_del_moduleCache($module_name);
             Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=fields');
             die();
        }
    }
}

if ($nv_Request->isset_request('delete', 'post')) {
    $fid = $nv_Request->get_int('fid', 'post', 0);
    if ($fid > 0) {
        $row = $db->query("SELECT field FROM " . NV_PREFIXLANG . "_" . $module_data . "_fields WHERE fid=" . $fid)->fetch();
        if ($row) {
            $db->query("DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_fields WHERE fid=" . $fid);
            $db->query("ALTER TABLE " . NV_PREFIXLANG . "_" . $module_data . "_rows DROP COLUMN `" . $row['field'] . "`");
            nv_del_moduleCache($module_name);
            die('OK');
        }
    }
    die('ERR');
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
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_fields ORDER BY weight ASC";
$result = $db->query($sql);
$array_fields = [];
while ($r = $result->fetch()) {
    $array_fields[] = $r;
}
$num_fields = count($array_fields);

foreach ($array_fields as $r) {
    $r['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=fields&fid=' . $r['fid'];
    $r['status_checked'] = ($r['status'] == 1) ? 'checked' : '';

    // Weight Loop
    for ($i = 1; $i <= $num_fields; $i++) {
        $xtpl->assign('WEIGHT', [
            'key' => $i,
            'title' => $i,
            'selected' => ($i == $r['weight']) ? 'selected' : ''
        ]);
        $xtpl->parse('main.loop.weight_loop');
    }

    $xtpl->assign('ROW', $r);
    $xtpl->parse('main.loop');
}

// Edit Form Data
if ($fid > 0) {
    $row = $db->query("SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_fields WHERE fid=" . $fid)->fetch();
    $caption = $lang_module['edit_field'];
} else {
    $row = ['fid' => 0, 'field' => '', 'title' => '', 'description' => '', 'required' => 0, 'field_type' => 'textbox', 'field_choices' => ''];
    $caption = $lang_module['add_field'];
}

$xtpl->assign('CAPTION', $caption);
$xtpl->assign('FORM', $row);

// Type select
$types = ['textbox' => 'Textbox', 'number' => 'Number', 'textarea' => 'Textarea', 'editor' => 'Editor', 'select' => 'Select Box', 'date' => 'Date'];
foreach ($types as $key => $val) {
    $xtpl->assign('TYPE', ['key' => $key, 'title' => $val, 'selected' => ($key == $row['field_type'] ? 'selected' : '')]);
    $xtpl->parse('main.field_type');
}

$xtpl->assign('REQUIRED_CHECKED', $row['required'] ? 'checked' : '');

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
