<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Tên Của Bạn <email@domain.com>
 * @Copyright (C) 2024. All rights reserved
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    die('Stop!!!');
}

$page_title = $lang_module['template_manage'];

// AJAX Action for Weight
if ($nv_Request->isset_request('ajax_action', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $new_vid = $nv_Request->get_int('new_vid', 'post', 0);
    if ($id > 0) {
        $sql = "UPDATE `" . NV_PREFIXLANG . "_" . $module_data . "_templates` SET weight=" . $new_vid . " WHERE id=" . $id;
        $db->query($sql);
        die('OK');
    }
    die('NO');
}

// AJAX Action for Status
if ($nv_Request->isset_request('change_status', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $new_status = $nv_Request->get_int('new_status', 'post', 0);
    if ($id > 0) {
        $sql = "UPDATE `" . NV_PREFIXLANG . "_" . $module_data . "_templates` SET status=" . $new_status . " WHERE id=" . $id;
        $db->query($sql);
        die('OK');
    }
    die('NO');
}

// Delete
if ($nv_Request->isset_request('delete', 'post')) {
    $id_del = $nv_Request->get_int('delete', 'post', 0);
    if ($id_del > 0) {
        $db->query("DELETE FROM `" . NV_PREFIXLANG . "_" . $module_data . "_templates` WHERE id=" . $id_del);
        die('OK');
    }
    die('NO');
}

$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);

// List of existing templates
$sql = "SELECT t.*, c.title as cat_title FROM `" . NV_PREFIXLANG . "_" . $module_data . "_templates` t LEFT JOIN `" . NV_PREFIXLANG . "_" . $module_data . "_cat` c ON t.catid = c.catid ORDER BY t.weight ASC";
$result = $db->query($sql);
$num = $result->rowCount(); // Get total rows for weight
$all_items = array();
while ($item = $result->fetch()) {
    $all_items[] = $item;
}

foreach ($all_items as $item) {
    $item['add_time'] = nv_date('H:i d/m/y', $item['add_time']);
    $item['edit_time'] = nv_date('H:i d/m/y', $item['edit_time']);

    // Status Select
    $status_active = ($item['status'] == 1) ? 'selected="selected"' : '';
    $status_inactive = ($item['status'] == 0) ? 'selected="selected"' : '';

    $xtpl->assign('STATUS', array('key' => 1, 'title' => $lang_module['active'], 'selected' => $status_active));
    $xtpl->parse('main.list.status');
    $xtpl->assign('STATUS', array('key' => 0, 'title' => $lang_module['inactive'], 'selected' => $status_inactive));
    $xtpl->parse('main.list.status');

    // Weight Select
    for ($i = 1; $i <= $num; $i++) {
        $weight_selected = ($i == $item['weight']) ? 'selected="selected"' : '';
        $xtpl->assign('WEIGHT', array('key' => $i, 'title' => $i, 'selected' => $weight_selected));
        $xtpl->parse('main.list.weight');
    }

    $xtpl->assign('ITEM', $item);
    $xtpl->parse('main.list');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
