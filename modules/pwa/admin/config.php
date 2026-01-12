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

$page_title = $lang_module['config'];

// Call helper to generate VAPID keys if needed
// We will do this via a simple helper function or class inclusion
// For now, let's include the WebPush library if it exists, or define the generation logic here for simplicity

$error = '';

// Define config structure
if ($nv_Request->isset_request('save', 'post')) {
    $cfg = [
        'manifest_name' => $nv_Request->get_title('manifest_name', 'post', ''),
        'manifest_short_name' => $nv_Request->get_title('manifest_short_name', 'post', ''),
        'theme_color' => $nv_Request->get_title('theme_color', 'post', '#000000'),
        'background_color' => $nv_Request->get_title('background_color', 'post', '#ffffff'),
        'icon_path' => $nv_Request->get_string('icon_path', 'post', ''),
        'vapid_public_key' => $nv_Request->get_string('vapid_public_key', 'post', ''),
        'vapid_private_key' => $nv_Request->get_string('vapid_private_key', 'post', ''),
    ];

    foreach ($cfg as $cf_name => $cf_value) {
        $db->query("REPLACE INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . NV_LANG_DATA . "', '" . $module_name . "', '" . $cf_name . "', " . $db->quote($cf_value) . ")");
    }

    $nv_Cache->delMod('settings');
    Header("Location: " . NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op);
    die();
}

// Generate Keys Action
if ($nv_Request->isset_request('generate_keys', 'post')) {
    // Check if openssl is available
    if (!function_exists('openssl_pkey_new')) {
        $error = "OpenSSL PHP extension is required to generate keys.";
    } else {
        require_once NV_ROOTDIR . '/modules/' . $module_file . '/library/Vapid.php';
        $keys = \NukeViet\Module\Pwa\Library\Vapid::createVapidKeys();

        if (isset($keys['error'])) {
             $error = $keys['error'];
        } elseif ($keys) {
            $db->query("REPLACE INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . NV_LANG_DATA . "', '" . $module_name . "', 'vapid_public_key', " . $db->quote($keys['publicKey']) . ")");
            $db->query("REPLACE INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . NV_LANG_DATA . "', '" . $module_name . "', 'vapid_private_key', " . $db->quote($keys['privateKey']) . ")");
            $nv_Cache->delMod('settings');
            // Redirect on success
            Header("Location: " . NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op);
            die();
        }
    }
    // If we are here, there was an error, so we fall through to display the page with $error set.
}

// Load config
$sql = "SELECT config_name, config_value FROM " . NV_CONFIG_GLOBALTABLE . " WHERE lang='" . NV_LANG_DATA . "' AND module='" . $module_name . "'";
$result = $db->query($sql);
$module_config = [];
while ($row = $result->fetch()) {
    $module_config[$row['config_name']] = $row['config_value'];
}

$xtpl = new XTemplate('config.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);

if (!empty($error)) {
    $xtpl->assign('ERROR', $error);
    $xtpl->parse('main.error_msg');
}

$xtpl->assign('DATA', [
    'manifest_name' => isset($module_config['manifest_name']) ? $module_config['manifest_name'] : $global_config['site_name'],
    'manifest_short_name' => isset($module_config['manifest_short_name']) ? $module_config['manifest_short_name'] : $global_config['site_name'],
    'theme_color' => isset($module_config['theme_color']) ? $module_config['theme_color'] : '#3b82f6',
    'background_color' => isset($module_config['background_color']) ? $module_config['background_color'] : '#ffffff',
    'icon_path' => isset($module_config['icon_path']) ? $module_config['icon_path'] : '',
    'vapid_public_key' => isset($module_config['vapid_public_key']) ? $module_config['vapid_public_key'] : '',
    'vapid_private_key' => isset($module_config['vapid_private_key']) ? $module_config['vapid_private_key'] : ''
]);

if(empty($module_config['vapid_public_key'])) {
    $xtpl->parse('main.generate_keys');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
