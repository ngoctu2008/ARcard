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

if ($nv_Request->isset_request('save', 'post')) {
    $array_config = [];
    $array_config['per_page'] = $nv_Request->get_int('per_page', 'post', 20);
    $array_config['who_view'] = $nv_Request->get_array('who_view', 'post', []);
    $array_config['who_view'] = implode(',', $array_config['who_view']);

    $sth = $db->prepare("SELECT config_name FROM " . NV_CONFIG_GLOBALTABLE . " WHERE lang = '" . NV_LANG_DATA . "' AND module = :module_name");
    $sth->bindValue(':module_name', $module_name, PDO::PARAM_STR);
    $sth->execute();
    $existing_configs = $sth->fetchAll(PDO::FETCH_COLUMN);

    foreach ($array_config as $config_name => $config_value) {
        if (in_array($config_name, $existing_configs)) {
            $sth = $db->prepare("UPDATE " . NV_CONFIG_GLOBALTABLE . " SET config_value = :config_value WHERE lang = '" . NV_LANG_DATA . "' AND module = :module_name AND config_name = :config_name");
        } else {
            $sth = $db->prepare("INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . NV_LANG_DATA . "', :module_name, :config_name, :config_value)");
        }
        $sth->bindValue(':module_name', $module_name, PDO::PARAM_STR);
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
    'per_page' => isset($module_config[$module_name]['per_page']) ? $module_config[$module_name]['per_page'] : 20
]);

// Groups
$groups_list = nv_groups_list();
$who_view = isset($module_config[$module_name]['who_view']) ? explode(',', $module_config[$module_name]['who_view']) : ['0']; // Default all visitors (0)

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
