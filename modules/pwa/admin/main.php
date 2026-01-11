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

Header("Location: " . NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . $lang . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=config");
die();
