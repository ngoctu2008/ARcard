<?php

/**
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    exit('Stop!!!');
}

$module_version = [
    'name' => 'PWA',
    'modfuncs' => 'main,manifest,sw,subscribe,latest_push,offline',
    'change_alias' => 'main',
    'submenu' => 'main',
    'is_sysmod' => 0,
    'virtual' => 1,
    'version' => '4.5.07',
    'date' => 'Fri, 25 Oct 2024 16:00:00 GMT',
    'author' => 'Jules (AI)',
    'note' => 'Progressive Web App Module for NukeViet',
    'uploads_dir' => [
        $module_upload
    ],
    'files_dir' => [
        $module_upload
    ]
];
