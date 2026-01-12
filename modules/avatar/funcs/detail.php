<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Phạm Ngọc Tú <ngoctu.dnkd@gmail.com>
 * @Copyright (C) 2024 Phạm Ngọc Tú. All rights reserved
 *
 * @Createdate Dec 19, 2025
 */

if (!defined('NV_IS_MOD_AVATAR')) {
    die('Stop!!!');
}

global $id, $catid;

if ($id == 0) {
    nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content']);
}

// Fetch Row
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE id=" . $id . " AND status=1";
$row = $db->query($sql)->fetch();

if (empty($row)) {
    nv_info_die($lang_global['error_404_title'], $lang_global['error_404_title'], $lang_global['error_404_content']);
}

// Cat Info
if ($row['catid'] > 0) {
    $cat_info = nv_avatar_get_cat($row['catid']);
} else {
    $cat_info = array('alias' => '');
}

// Check permissions
if (!defined('NV_IS_ADMIN') && !empty($cat_info)) {
    if (!empty($cat_info['groups_view'])) {
        if (!nv_user_in_groups($cat_info['groups_view'])) {
             nv_info_die($lang_global['error_403_title'], $lang_global['error_403_title'], $lang_global['error_403_content']);
        }
    }
}

// Check Use Permissions
$allow_use = true;
if (!defined('NV_IS_ADMIN') && !empty($cat_info)) {
    if (!empty($cat_info['groups_use'])) {
        if (!nv_user_in_groups($cat_info['groups_use'])) {
             $allow_use = false;
        }
    }
}

$page_title = $row['title'];
$key_words = $row['title'];

// Open Graph Meta Tags
$og_image = (preg_match('/^(http|https):\/\//', $row['image'])) ? $row['image'] : NV_MY_DOMAIN . NV_BASE_SITEURL . $row['image'];
$og_url = NV_MY_DOMAIN . nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&id=' . $row['id'], true);
$og_description = !empty($row['description']) ? $row['description'] : $row['title'];

$my_head .= '<meta property="og:title" content="' . $page_title . '" />' . PHP_EOL;
$my_head .= '<meta property="og:type" content="website" />' . PHP_EOL;
$my_head .= '<meta property="og:image" content="' . $og_image . '" />' . PHP_EOL;
$my_head .= '<meta property="og:url" content="' . $og_url . '" />' . PHP_EOL;
$my_head .= '<meta property="og:description" content="' . $og_description . '" />' . PHP_EOL;

// Update View
$sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_rows SET views=views+1 WHERE id=" . $id;
$db->query($sql);

$contents = nv_theme_avatar_detail($row, $cat_info, $allow_use);

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
