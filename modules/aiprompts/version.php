<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Tên Của Bạn <email@domain.com>
 * @Copyright (C) 2024. All rights reserved
 * @Createdate Sat, 25 May 2024 10:00:00 GMT
 */

if (!defined('NV_MAINFILE')) {
    die('Stop!!!');
}

$module_version = array(
    'name' => 'AIPrompts',
    'modfuncs' => 'main,detail',
    'change_alias' => 'main,detail',
    'submenu' => 'main,detail',
    'is_sysmod' => 0,
    'virtual' => 1,
    'version' => '1.0.01',
    'date' => 'Sun, 26 May 2024 10:00:00 GMT',
    'author' => 'AIPrompts Team',
    'uploads_dir' => array($module_name),
    'note' => 'Module tạo câu lệnh AI Prompt'
);
