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

if (isset($lang_module['send_notification'])) {
    $page_title = $lang_module['send_notification'];
}

// Load config for VAPID keys
$sql = "SELECT config_name, config_value FROM " . NV_CONFIG_GLOBALTABLE . " WHERE lang='" . NV_LANG_DATA . "' AND module='" . $module_name . "'";
$result = $db->query($sql);
$module_config = [];
while ($row = $result->fetch()) {
    $module_config[$row['config_name']] = $row['config_value'];
}

if (empty($module_config['vapid_public_key']) || empty($module_config['vapid_private_key'])) {
    $xtpl = new XTemplate('error.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
    $xtpl->assign('ERROR', $lang_module['error_vapid']);
    $xtpl->parse('main');
    $contents = $xtpl->text('main');
} else {
    $xtpl = new XTemplate('notification.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
    $xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
    $xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
    $xtpl->assign('MODULE_NAME', $module_name);
    $xtpl->assign('OP', $op);

    // Check if form submitted
    if ($nv_Request->isset_request('send', 'post')) {
        $title = $nv_Request->get_title('title', 'post', '');
        $message = $nv_Request->get_string('message', 'post', '');
        $url = $nv_Request->get_string('url', 'post', '');

        require_once NV_ROOTDIR . '/modules/' . $module_file . '/library/PushHelper.php';
        $pushHelper = new \NukeViet\Module\Pwa\Library\PushHelper($db, $module_config);

        $result = $pushHelper->sendPush($title, $message, $url);

        if ($result['status'] == 'success') {
             $xtpl->assign('SUCCESS_MSG', sprintf($lang_module['send_success'], $result['count']));
             $xtpl->parse('main.success_msg');
        } else {
             $xtpl->assign('ERROR', $result['message']);
             $xtpl->parse('main.error_msg');
        }
    }

    $xtpl->parse('main');
    $contents = $xtpl->text('main');
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
