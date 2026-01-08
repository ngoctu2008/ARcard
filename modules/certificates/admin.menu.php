<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Phạm Ngọc Tú <ngoctu.dnkd@gmail.com>
 * @Copyright (C) 2024 Phạm Ngọc Tú. All rights reserved
 * @License: Not free source, more information contact ngoctu.dnkd@gmail.com
 * @Createdate Thu, 01 Jan 2024 00:00:00 GMT
 */

if (!defined('NV_ADMIN')) {
    die('Stop!!!');
}

$menu_top = [
    'cat' => $lang_module['cat_manage'],
    'main' => $lang_module['main_manage'],
    'import' => $lang_module['import_excel'],
    'config' => $lang_module['config']
];

$allow_func = ['main', 'cat', 'import', 'content', 'config', 'del_cat', 'change_status', 'del_content'];
