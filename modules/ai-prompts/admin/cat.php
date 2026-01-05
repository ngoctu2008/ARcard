<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Tên Của Bạn <email@domain.com>
 * @Copyright (C) 2024. All rights reserved
 * @Createdate Sat, 25 May 2024 10:00:00 GMT
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    die('Stop!!!');
}

$page_title = $lang_module['cat_manage'];

$sql = "SELECT * FROM `" . NV_PREFIXLANG . "_" . $module_data . "_cat` ORDER BY weight ASC";
$result = $db->query($sql);

$array_cat = array();
while ($row = $result->fetch()) {
    $array_cat[$row['catid']] = $row;
}

$error = '';
$catid = $nv_Request->get_int('catid', 'get,post', 0);

if ($nv_Request->isset_request('save', 'post')) {
    $row = array();
    $row['title'] = $nv_Request->get_title('title', 'post', '');
    $row['alias'] = $nv_Request->get_title('alias', 'post', '');
    $row['description'] = $nv_Request->get_string('description', 'post', '');
    $row['weight'] = $nv_Request->get_int('weight', 'post', 0);
    $row['status'] = $nv_Request->isset_request('status', 'post') ? 1 : 0;

    if (empty($row['alias'])) {
        $row['alias'] = change_alias($row['title']);
    }

    if (empty($row['title'])) {
        $error = $lang_module['error_title'];
    } else {
        // Check alias unique
        $sql = "SELECT catid FROM `" . NV_PREFIXLANG . "_" . $module_data . "_cat` WHERE alias= :alias AND catid != :catid";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':alias', $row['alias'], PDO::PARAM_STR);
        $stmt->bindParam(':catid', $catid, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->fetch()) {
             $error = $lang_module['error_alias'];
        } else {
            if ($catid > 0) {
                $sql = "UPDATE `" . NV_PREFIXLANG . "_" . $module_data . "_cat` SET title=:title, alias=:alias, description=:description, weight=:weight, status=:status WHERE catid=:catid";
                $data_insert = array(
                    ':title' => $row['title'],
                    ':alias' => $row['alias'],
                    ':description' => $row['description'],
                    ':weight' => $row['weight'],
                    ':status' => $row['status'],
                    ':catid' => $catid
                );
                $db->prepare($sql)->execute($data_insert);
                $nv_Cache->delMod($module_name);
                Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . $lang . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat');
                die();
            } else {
                $sql = "INSERT INTO `" . NV_PREFIXLANG . "_" . $module_data . "_cat` (title, alias, description, weight, status) VALUES (:title, :alias, :description, :weight, :status)";
                $data_insert = array(
                    ':title' => $row['title'],
                    ':alias' => $row['alias'],
                    ':description' => $row['description'],
                    ':weight' => $row['weight'],
                    ':status' => $row['status']
                );

                 if ($db->prepare($sql)->execute($data_insert)) {
                    $nv_Cache->delMod($module_name);
                    Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . $lang . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat');
                    die();
                 }
            }
        }
    }
} elseif ($catid > 0) {
    $row = $array_cat[$catid];
} else {
    $row = array(
        'catid' => 0,
        'title' => '',
        'alias' => '',
        'description' => '',
        'weight' => 0,
        'status' => 1
    );
}

// Delete
if ($nv_Request->isset_request('delete', 'post')) {
    $catid_del = $nv_Request->get_int('delete', 'post', 0);
    if ($catid_del > 0) {
        $db->query("DELETE FROM `" . NV_PREFIXLANG . "_" . $module_data . "_cat` WHERE catid=" . $catid_del);
        $db->query("DELETE FROM `" . NV_PREFIXLANG . "_" . $module_data . "_templates` WHERE catid=" . $catid_del);
        $nv_Cache->delMod($module_name);
        die('OK');
    }
    die('NO');
}

$xtpl = new XTemplate('cat.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);
$xtpl->assign('ERROR', $error);

foreach ($array_cat as $cat) {
    $xtpl->assign('CAT', $cat);
    $xtpl->parse('main.list');
}

if (!empty($error)) {
    $xtpl->parse('main.error');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
