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

$id = $nv_Request->get_int('id', 'post', 0);
if ($id > 0) {
    $db->query("DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE id=" . $id);
    nv_del_moduleCache($module_name);
    die('OK');
}
die('ERR');
