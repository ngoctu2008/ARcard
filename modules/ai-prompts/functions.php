<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Phạm Ngọc Tú <ngoctu.dnkd@gmail.com>
 * @Copyright (C) 2024. All rights reserved
 * @Createdate Sat, 25 May 2024 10:00:00 GMT
 */

if (!defined('NV_SYSTEM')) {
    die('Stop!!!');
}

define('NV_IS_MOD_AI_PROMPTS', true);

$allow_func = array('main', 'detail');

// Shorten URL Support: Handle .../alias-id.html
// This logic runs early. Standard NV behavior: if standard func not found, it might 404 or check main.
// We intercept here.
if ($op == 'main' || !file_exists(NV_ROOTDIR . '/modules/' . $module_file . '/funcs/' . $op . '.php')) {
    // Check pattern: alias-id
    if (preg_match('/^([a-z0-9\-]+)\-([0-9]+)$/i', $op, $m)) {
        // It matches alias-id structure
        $op = 'detail';
        // We need to inject into $array_op so detail.php can parse it similarly
        // Or better, set global variables detail.php can use, or modify $array_op.
        // detail.php uses: $alias = isset($array_op[1]) ... $id = intval(end($array_page));
        // If URL is /ai-prompts/alias-id.html, $op = 'alias-id', $array_op = ['alias-id']
        // detail.php expects 'detail/alias-id'.
        // So we fake it.
        $array_op = array('detail', $m[0]);
    }
}
