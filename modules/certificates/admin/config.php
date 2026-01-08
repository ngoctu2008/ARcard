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

$page_title = $lang_module['config'];
$table_config = NV_PREFIXLANG . "_" . $module_data . "_config";

// Load Local Config
$sql = "SELECT config_name, config_value FROM " . $table_config;
$result = $db->query($sql);
$local_config = [];
while ($row = $result->fetch()) {
    $local_config[$row['config_name']] = $row['config_value'];
}

if ($nv_Request->isset_request('save', 'post')) {
    $array_config = [];
    $array_config['per_page'] = $nv_Request->get_int('per_page', 'post', 20);
    $array_config['per_page_cat'] = $nv_Request->get_int('per_page_cat', 'post', 20);
    $array_config['active_captcha'] = $nv_Request->get_int('active_captcha', 'post', 1);
    $array_config['who_view'] = $nv_Request->get_array('who_view', 'post', []);
    $array_config['who_view'] = implode(',', $array_config['who_view']);

    foreach ($array_config as $config_name => $config_value) {
        $sth = $db->prepare("REPLACE INTO " . $table_config . " (config_name, config_value) VALUES (:config_name, :config_value)");
        $sth->bindValue(':config_name', $config_name, PDO::PARAM_STR);
        $sth->bindValue(':config_value', $config_value, PDO::PARAM_STR);
        $sth->execute();
    }

    $nv_Cache->delMod($module_name);
    Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=config');
    die();
}

$xtpl = new XTemplate('config.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', 'config');

$xtpl->assign('DATA', [
    'per_page' => isset($local_config['per_page']) ? $local_config['per_page'] : 20,
    'per_page_cat' => isset($local_config['per_page_cat']) ? $local_config['per_page_cat'] : 20,
    'active_captcha' => isset($local_config['active_captcha']) ? $local_config['active_captcha'] : 1
]);

$xtpl->assign('CAPTCHA_0', ($xtpl->vars['DATA']['active_captcha'] == 0) ? 'selected="selected"' : '');
$xtpl->assign('CAPTCHA_1', ($xtpl->vars['DATA']['active_captcha'] == 1) ? 'selected="selected"' : '');

// Groups
$groups_list = nv_groups_list();
$who_view = isset($local_config['who_view']) ? explode(',', $local_config['who_view']) : ['0']; // Default all visitors (0)

foreach ($groups_list as $group_id => $group_title) {
    $xtpl->assign('GROUP', [
        'id' => $group_id,
        'title' => $group_title,
        'checked' => in_array($group_id, $who_view) ? 'checked' : ''
    ]);
    $xtpl->parse('main.group');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
