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

 = ->get_int('id', 'post', 0);
 = ->get_string('mod', 'post', '');
 = ->get_int('new_weight', 'post', 0);

if ( > 0 && !empty()) {
    if ( == 'cat') {
         = NV_PREFIXLANG . "_" .  . "_cat";
         = "catid";
    } elseif ( == 'fields') {
         = NV_PREFIXLANG . "_" .  . "_fields";
         = "fid";
    } else {
        die('NO');
    }

     = "UPDATE " .  . " SET weight=" .  . " WHERE " .  . "=" . ;
    ->query();
    nv_del_moduleCache();
    die('OK');
}
die('NO');
