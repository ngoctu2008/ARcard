<?php

/**
 * @version 4.x
 * @author Phạm Ngọc Tú <ngoctu.dnkd@gmail.com>
 * @copyright (C) 2009-2021 Phạm Ngọc Tú. All rights reserved
 * @license GNU/GPL version 2 or any later version
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    exit('Stop!!!');
}

$page_title = $lang_module['automation'];

// Fetch News-type modules
$sql = "SELECT title, custom_title, module_file FROM " . NV_MODULES_TABLE . " WHERE module_file='news' OR module_file IN (SELECT module_file FROM " . NV_MODULES_TABLE . " WHERE module_data='news')";
// A more robust way to find "news-like" modules is checking `module_data` if available or file structure.
// In NukeViet, "virtual modules" have `module_data` pointing to the real module (e.g., 'news').
// `NV_MODULES_TABLE` usually has: title, module_file, module_data.
// If it is a virtual module of news, `module_data` = 'news'.
// If it is the original 'news' module, `module_file`='news' and `module_data`='news'.

$sql = "SELECT title, custom_title, module_file FROM " . NV_MODULES_TABLE . " WHERE module_data='news' AND act=1";
$result = $db->query($sql);
$news_modules = [];
while ($row = $result->fetch()) {
    $news_modules[] = $row;
}

// Load current automation config
$sql = "SELECT config_name, config_value FROM " . NV_CONFIG_GLOBALTABLE . " WHERE lang='" . NV_LANG_DATA . "' AND module='" . $module_name . "'";
$result = $db->query($sql);
$module_config = [];
while ($row = $result->fetch()) {
    $module_config[$row['config_name']] = $row['config_value'];
}

if ($nv_Request->isset_request('save', 'post')) {
    $auto_active = $nv_Request->isset_request('auto_active', 'post') ? 1 : 0;
    $auto_modules = $nv_Request->get_array('auto_modules', 'post', []);

    $db->query("REPLACE INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . NV_LANG_DATA . "', '" . $module_name . "', 'auto_active', " . $auto_active . ")");
    $db->query("REPLACE INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . NV_LANG_DATA . "', '" . $module_name . "', 'auto_modules', " . $db->quote(implode(',', $auto_modules)) . ")");

    $nv_Cache->delMod('settings');
    Header("Location: " . NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op);
    die();
}

$xtpl = new XTemplate('automation.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);

$saved_modules = isset($module_config['auto_modules']) ? explode(',', $module_config['auto_modules']) : [];
$auto_active = isset($module_config['auto_active']) ? $module_config['auto_active'] : 0;

$xtpl->assign('DATA', [
    'auto_active_checked' => $auto_active ? 'checked="checked"' : ''
]);

foreach ($news_modules as $mod) {
    $mod['checked'] = in_array($mod['module_file'], $saved_modules) ? 'checked="checked"' : '';
    $xtpl->assign('MODULE', $mod);
    $xtpl->parse('main.module');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
