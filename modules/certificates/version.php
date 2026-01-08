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

$module_version = [
    'name' => 'Certificates',
    'modfuncs' => 'main,search',
    'change_alias' => 'main,search',
    'submenu' => 'main,search',
    'is_sysmod' => 0,
    'virtual' => 1,
    'version' => '4.5.07',
    'date' => 'Thu, 01 Jan 2024 00:00:00 GMT',
    'author' => 'Phạm Ngọc Tú',
    'uploads_dir' => [$module_name],
    'note' => 'Module tra cứu văn bằng chứng chỉ'
];
