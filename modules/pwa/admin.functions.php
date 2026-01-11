<?php

/**
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) {
    exit('Stop!!!');
}

$allow_func = array('main', 'config', 'notification');

$submenu['main'] = $lang_module['main']; // Define main to prevent warnings
$submenu['config'] = $lang_module['config'];
if (isset($lang_module['send_notification'])) {
    $submenu['notification'] = $lang_module['send_notification'];
}

define('NV_IS_FILE_ADMIN', true);
