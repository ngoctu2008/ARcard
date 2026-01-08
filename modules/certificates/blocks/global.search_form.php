<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Phạm Ngọc Tú <ngoctu.dnkd@gmail.com>
 * @Copyright (C) 2024 Phạm Ngọc Tú. All rights reserved
 * @License: Not free source, more information contact ngoctu.dnkd@gmail.com
 * @Createdate Thu, 01 Jan 2024 00:00:00 GMT
 */

if (!defined('NV_MAINFILE')) {
    die('Stop!!!');
}

if (!nv_function_exists('nv_block_config_certificates_search')) {
    function nv_block_config_certificates_search($module, $data_block, $lang_block, $file_tpl, $array_cat, $func_name)
    {
        return '';
    }
}

if (!nv_function_exists('nv_block_certificates_search')) {
    function nv_block_certificates_search($block_config)
    {
        global $site_mods, $module_info, $module_name, $module_file, $global_config, $db, $db_config;

        $module = $block_config['module'];

        // Find module link
        $mod_link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . $global_config['site_lang'] . '&' . NV_NAME_VARIABLE . '=' . $module;

        $xtpl = new XTemplate('block_search_form.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
        $xtpl->assign('ACTION', $mod_link);
        $xtpl->assign('LANG', \NV_LANG_DATA); // This might not pass the specific module lang.
        // We should load module language if needed, but blocks run in global context.
        // Hardcoding simplified labels or loading language.

        // Load language for block if needed, but typically blocks use global or pass vars.
        // Let's assume we use Vietnamese directly or try to load.
        $lang_module = [];
        if (file_exists(NV_ROOTDIR . '/modules/' . $module . '/language/' . \NV_LANG_DATA . '.php')) {
            include NV_ROOTDIR . '/modules/' . $module . '/language/' . \NV_LANG_DATA . '.php';
        }

        $xtpl->assign('LANG', $lang_module);

        $xtpl->parse('main');
        return $xtpl->text('main');
    }
}

if (defined('NV_SYSTEM')) {
    $content = nv_block_certificates_search($block_config);
}
