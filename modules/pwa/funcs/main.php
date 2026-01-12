<?php

/**
 * @version 4.x
 * @author Phạm Ngọc Tú <ngoctu.dnkd@gmail.com>
 * @copyright (C) 2009-2021 Phạm Ngọc Tú. All rights reserved
 * @license GNU/GPL version 2 or any later version
 */

if (!defined('NV_MAINFILE')) {
    exit('Stop!!!');
}

// Simple main page to prevent 404 if module is accessed
$page_title = $module_info['custom_title'];

$contents = '<div class="alert alert-info" style="text-align:center;">';
$contents .= '<h3>' . $module_info['custom_title'] . '</h3>';
$contents .= '<p>' . $lang_module['subscribe_success'] . '</p>'; // Using a generic string
$contents .= '</div>';

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
