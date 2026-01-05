<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Tên Của Bạn <email@domain.com>
 * @Copyright (C) 2024. All rights reserved
 * @Createdate Sat, 25 May 2024 10:00:00 GMT
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    die('Stop!!!');
}

$page_title = $lang_module['main'];

$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
