<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Phạm Ngọc Tú <ngoctu.dnkd@gmail.com>
 * @Copyright (C) 2024 Phạm Ngọc Tú. All rights reserved
 * @License: Not free source, more information contact ngoctu.dnkd@gmail.com
 * @Createdate Thu, 01 Jan 2024 00:00:00 GMT
 */

if (!defined('NV_IS_MODADMIN')) {
    die('Stop!!!');
}

$page_title = $lang_module['cat_manage'];
$table_cat = NV_PREFIXLANG . "_" . $module_data . "_cat";

// AJAX: Get Alias
if ($nv_Request->isset_request('get_alias_title', 'post')) {
    $title = $nv_Request->get_title('get_alias_title', 'post', '');
    $alias = change_alias($title);
    die($alias);
}

// AJAX: Change Status
if ($nv_Request->isset_request('change_status', 'post')) {
    $catid = $nv_Request->get_int('catid', 'post', 0);
    if ($catid > 0) {
        $sql = "SELECT status FROM " . $table_cat . " WHERE catid=" . $catid;
        $status = $db->query($sql)->fetchColumn();
        $new_status = ($status == 1) ? 0 : 1;
        $db->query("UPDATE " . $table_cat . " SET status=" . $new_status . " WHERE catid=" . $catid);
        $nv_Cache->delMod($module_name);
        die('OK_' . $new_status);
    }
    die('NO');
}

// AJAX: Change Weight
if ($nv_Request->isset_request('ajax_action', 'post')) {
    $catid = $nv_Request->get_int('catid', 'post', 0);
    $new_vid = $nv_Request->get_int('new_vid', 'post', 0);
    if ($catid > 0 && $new_vid > 0) {
        $sql = "SELECT weight FROM " . $table_cat . " WHERE catid=" . $catid;
        $weight = $db->query($sql)->fetchColumn();

        $sql = "SELECT catid, weight FROM " . $table_cat . " WHERE catid!=" . $catid . " ORDER BY weight ASC";
        $result = $db->query($sql);
        $weight_list = [];
        while ($row = $result->fetch()) {
            $weight_list[$row['catid']] = $row['weight'];
        }

        if ($new_vid > $weight) { // Move down
            foreach ($weight_list as $id => $w) {
                if ($w > $weight && $w <= $new_vid) {
                    $db->query("UPDATE " . $table_cat . " SET weight=" . ($w - 1) . " WHERE catid=" . $id);
                }
            }
        } elseif ($new_vid < $weight) { // Move up
            foreach ($weight_list as $id => $w) {
                if ($w >= $new_vid && $w < $weight) {
                    $db->query("UPDATE " . $table_cat . " SET weight=" . ($w + 1) . " WHERE catid=" . $id);
                }
            }
        }

        $db->query("UPDATE " . $table_cat . " SET weight=" . $new_vid . " WHERE catid=" . $catid);

        // Normalize
        $sql = "SELECT catid FROM " . $table_cat . " ORDER BY weight ASC";
        $result = $db->query($sql);
        $w = 1;
        while ($row = $result->fetch()) {
            $db->query("UPDATE " . $table_cat . " SET weight=" . $w . " WHERE catid=" . $row['catid']);
            $w++;
        }

        $nv_Cache->delMod($module_name);
        die('OK');
    }
    die('NO');
}

// Delete
if ($nv_Request->isset_request('delete_id', 'get')) {
    $catid = $nv_Request->get_int('delete_id', 'get', 0);
    if ($catid > 0) {
        $check = $db->query("SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE catid=" . $catid)->fetchColumn();
        if ($check > 0) {
            $error = 'Error: Category is not empty';
        } else {
            $db->query("DELETE FROM " . $table_cat . " WHERE catid=" . $catid);
            $nv_Cache->delMod($module_name);
            Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat');
            die();
        }
    }
}

// Save
$error = '';
$catid = $nv_Request->get_int('catid', 'post,get', 0);
if ($nv_Request->isset_request('submit', 'post')) {
    $title = $nv_Request->get_title('title', 'post', '');
    $alias = $nv_Request->get_title('alias', 'post', '');
    $description = $nv_Request->get_title('description', 'post', '');
    $image = $nv_Request->get_string('image', 'post', '');
    // Status in form? Template doesn't show status in form, only list. Assuming Active(1) or keeping existing.
    // Actually, user sample form doesn't have status input. Defaults to 1 for new.

    if (empty($alias)) {
        $alias = change_alias($title);
    } else {
        $alias = change_alias($alias);
    }

    if (empty($title)) {
        $error = $lang_module['error_title'];
    } else {
        if ($catid > 0) {
            $sql = "UPDATE " . $table_cat . " SET title=:title, alias=:alias, description=:description, image=:image WHERE catid=:catid";
            $data_insert = [
                ':title' => $title,
                ':alias' => $alias,
                ':description' => $description,
                ':image' => $image,
                ':catid' => $catid
            ];
            $sth = $db->prepare($sql);
            $sth->execute($data_insert);
        } else {
            $sql = "INSERT INTO " . $table_cat . " (title, alias, description, image, status) VALUES (:title, :alias, :description, :image, 1)";
            $data_insert = [
                ':title' => $title,
                ':alias' => $alias,
                ':description' => $description,
                ':image' => $image
            ];
            $catid = $db->insert_id($sql, 'catid', $data_insert);
            if ($catid > 0) {
                // Set weight to Max + 1 or Count
                $count = $db->query("SELECT COUNT(*) FROM " . $table_cat)->fetchColumn();
                $db->query("UPDATE " . $table_cat . " SET weight=" . $count . " WHERE catid=" . $catid);
            }
        }
        $nv_Cache->delMod($module_name);
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat');
        die();
    }
}

// Fetch List
$q = $nv_Request->get_title('q', 'get', '');
$where = '';
if (!empty($q)) {
    $where = " WHERE title LIKE '%" . $q . "%'";
}

$sql = "SELECT * FROM " . $table_cat . $where . " ORDER BY weight ASC";
$result = $db->query($sql);
$array_cat = [];
while ($row = $result->fetch()) {
    $array_cat[$row['catid']] = $row;
}

$xtpl = new XTemplate('cat.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', 'cat');
$xtpl->assign('Q', $q);

if (!empty($error)) {
    $xtpl->assign('ERROR', $error);
    $xtpl->parse('main.error');
}

// View List
$num_cats = count($array_cat);
foreach ($array_cat as $cat) {
    $cat['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat&catid=' . $cat['catid'];
    $cat['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat&delete_id=' . $cat['catid'];
    $cat['status_check'] = ($cat['status'] == 1) ? 'checked' : '';

    // Weight Loop
    for ($i = 1; $i <= $num_cats; $i++) {
        $xtpl->assign('WEIGHT', [
            'key' => $i,
            'title' => $i,
            'selected' => ($i == $cat['weight']) ? 'selected="selected"' : ''
        ]);
        $xtpl->parse('main.view.loop.weight_loop');
    }

    $xtpl->assign('CHECK', $cat['status_check']);
    $xtpl->assign('VIEW', $cat);
    $xtpl->parse('main.view.loop');
}
$xtpl->parse('main.view');

// Form
$row = [];
if ($catid > 0 and isset($array_cat[$catid])) {
    $row = $array_cat[$catid];
    $caption = $lang_module['edit_cat'];
} else {
    $row = ['catid' => 0, 'title' => '', 'alias' => '', 'description' => '', 'image' => ''];
    $caption = $lang_module['add_cat'];
}
$xtpl->assign('ROW', $row);
$xtpl->assign('CAPTION', $caption);

$xtpl->parse('main.auto_get_alias');
$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
