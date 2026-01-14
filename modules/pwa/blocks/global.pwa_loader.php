<?php

/**
 * @version 4.x
 * @author Phạm Ngọc Tú <ngoctu.dnkd@gmail.com>
 * @copyright (C) 2009-2021 Phạm Ngọc Tú. All rights reserved
 * @license GNU/GPL version 2 or any later version
 */

if (!defined('NV_MAINFILE')) {
    exit('Stop!!!');
}

function nv_global_pwa_loader($block_config)
{
    global $nv_Request, $db, $global_config, $module_info;

    // Determine the actual PWA module name (it might be installed with a different name)
    // We look for a module that uses the 'pwa' file/folder.
    $sql = "SELECT title FROM " . NV_MODULES_TABLE . " WHERE module_file = 'pwa' AND act = 1";
    $result = $db->query($sql);
    $pwa_module_name = $result->fetchColumn();

    if (empty($pwa_module_name)) {
        return '';
    }

    // PWA Manifest
    $manifestUrl = NV_BASE_SITEURL . 'index.php?nv=' . $pwa_module_name . '&op=manifest';
    $swUrl = NV_BASE_SITEURL . 'index.php?nv=' . $pwa_module_name . '&op=sw';

    // Load module config (VAPID Key and Subscribe Note)
    $sql = "SELECT config_name, config_value FROM " . NV_CONFIG_GLOBALTABLE . " WHERE lang='" . NV_LANG_DATA . "' AND module='" . $pwa_module_name . "'";
    $result = $db->query($sql);
    $module_config = [];
    while ($row = $result->fetch()) {
        $module_config[$row['config_name']] = $row['config_value'];
    }

    $vapidPublicKey = isset($module_config['vapid_public_key']) ? $module_config['vapid_public_key'] : '';
    $subscribeNote = isset($module_config['subscribe_note']) ? $module_config['subscribe_note'] : '';

    // Load PWA module language
    if (file_exists(NV_ROOTDIR . '/modules/' . $pwa_module_name . '/language/' . NV_LANG_DATA . '.php')) {
        require NV_ROOTDIR . '/modules/' . $pwa_module_name . '/language/' . NV_LANG_DATA . '.php';
    } elseif (file_exists(NV_ROOTDIR . '/modules/' . $pwa_module_name . '/language/en.php')) {
        require NV_ROOTDIR . '/modules/' . $pwa_module_name . '/language/en.php';
    } else {
        $lang_module = [];
    }

    // Determine template path
    $block_theme = $global_config['module_theme'];
    if (!file_exists(NV_ROOTDIR . '/themes/' . $block_theme . '/modules/pwa/global.pwa_loader.tpl')) {
        $block_theme = 'default';
    }

    // Check if default theme file exists, if not, use the module's template
    if (!file_exists(NV_ROOTDIR . '/themes/' . $block_theme . '/modules/pwa/global.pwa_loader.tpl')) {
        if (file_exists(NV_ROOTDIR . '/modules/pwa/template/block/global.pwa_loader.tpl')) {
             $xtpl = new XTemplate('global.pwa_loader.tpl', NV_ROOTDIR . '/modules/pwa/template/block');
        } else {
             return ''; // No template found
        }
    } else {
        $xtpl = new XTemplate('global.pwa_loader.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/pwa');
    }

    $xtpl->assign('MANIFEST_URL', $manifestUrl);
    $xtpl->assign('SW_URL', $swUrl);
    $xtpl->assign('VAPID_PUBLIC_KEY', $vapidPublicKey);
    $xtpl->assign('SUBSCRIBE_NOTE', $subscribeNote);
    $xtpl->assign('MODULE_NAME', $pwa_module_name);
    $xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);
    $xtpl->assign('LANG', $lang_module);

    $xtpl->parse('main');
    return $xtpl->text('main');
}

if (defined('NV_SYSTEM')) {
    $content = nv_global_pwa_loader($block_config);
}
