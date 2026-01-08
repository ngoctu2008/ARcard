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
$error = '';
$catid = $nv_Request->get_int('catid', 'get,post', 0);

if ($nv_Request->isset_request('save', 'post')) {
    $title = $nv_Request->get_title('title', 'post', '');
    $alias = $nv_Request->get_title('alias', 'post', '');
    $status = $nv_Request->get_int('status', 'post', 1);

    if (empty($alias)) {
        $alias = change_alias($title);
    } else {
        $alias = change_alias($alias);
    }

    if (empty($title)) {
        $error = $lang_module['error_title'];
    } else {
        if ($catid > 0) {
            $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_cat SET title=:title, alias=:alias, status=:status WHERE catid=:catid";
            $data_insert = [
                ':title' => $title,
                ':alias' => $alias,
                ':status' => $status,
                ':catid' => $catid
            ];
            $db->query_check($sql, $data_insert);
        } else {
            $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_cat (title, alias, status) VALUES (:title, :alias, :status)";
            $data_insert = [
                ':title' => $title,
                ':alias' => $alias,
                ':status' => $status
            ];
            $catid = $db->insert_id($sql, 'catid', $data_insert);
        }
        $db->get_pdo()->exec("UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_cat SET weight=" . $catid . " WHERE catid=" . $catid);
        nv_del_moduleCache($module_name);
        Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat');
        die();
    }
}

if ($nv_Request->isset_request('delete', 'post')) {
    $catid = $nv_Request->get_int('catid', 'post', 0);
    if ($catid > 0) {
        $check = $db->query("SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE catid=" . $catid)->fetchColumn();
        if ($check > 0) {
            die('Error: Category is not empty');
        }
        $db->query("DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_cat WHERE catid=" . $catid);
        nv_del_moduleCache($module_name);
        die('OK');
    }
}

$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_cat ORDER BY weight ASC";
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

if (!empty($error)) {
    $xtpl->assign('ERROR', $error);
    $xtpl->parse('main.error');
}

// Form
$row = [];
if ($catid > 0 and isset($array_cat[$catid])) {
    $row = $array_cat[$catid];
    $caption = $lang_module['edit_cat'];
} else {
    $row = ['catid' => 0, 'title' => '', 'alias' => '', 'status' => 1];
    $caption = $lang_module['add_cat'];
}

$xtpl->assign('CAPTION', $caption);
$xtpl->assign('ROW', $row);

foreach ($array_cat as $cat) {
    $cat['status_str'] = ($cat['status'] == 1) ? $lang_module['active'] : $lang_module['inactive'];
    $cat['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat&catid=' . $cat['catid'];
    $xtpl->assign('CAT', $cat);
    $xtpl->parse('main.loop');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
