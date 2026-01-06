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

$allow_func = array('main', 'detail', 'cat');

// Shorten URL Support
// Intercept patterns when standard func file doesn't exist or $op is ambiguous
if ($op == 'main' || !file_exists(NV_ROOTDIR . '/modules/' . $module_file . '/funcs/' . $op . '.php')) {

    // Pattern 1: ID-Alias (Category) -> e.g., 1-giao-duc
    if (preg_match('/^([0-9]+)\-([a-z0-9\-]+)$/i', $op, $m)) {
        $op = 'cat';
        $array_op = array($m[0]); // Pass '1-giao-duc' to cat.php
    }
    // Pattern 2: Alias-ID (Detail Template) -> e.g., tro-ly-soan-bai-5
    elseif (preg_match('/^([a-z0-9\-]+)\-([0-9]+)$/i', $op, $m)) {
        $op = 'detail';
        // detail.php logic might need adjustment if it expects strict structure,
        // but passing the full string lets it parse ID from end.
        $array_op = array('detail', $m[0]);
    }
}
