<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Phạm Ngọc Tú <ngoctu.dnkd@gmail.com>
 * @Copyright (C) 2024. All rights reserved
 * @Createdate Sat, 25 May 2024 10:00:00 GMT
 */

if (!defined('NV_IS_MOD_AI_PROMPTS')) {
    die('Stop!!!');
}

// URL format: catid-alias
$catid = 0;
$alias = '';

// If routing set catid in array_op or standard var
if (isset($array_op[0])) {
    $parts = explode('-', $array_op[0], 2); // Split ID and Alias
    if (count($parts) >= 2) {
        $catid = intval($parts[0]);
    }
}

if ($catid == 0) {
    Header('Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
    die();
}

$sql_cat = "SELECT * FROM `" . NV_PREFIXLANG . "_" . $module_data . "_cat` WHERE catid=" . $catid . " AND status=1";
$result_cat = $db->query($sql_cat);
$cat_info = $result_cat->fetch();

if (empty($cat_info)) {
    Header('Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
    die();
}

$page_title = $cat_info['title'];
$key_words = $cat_info['title'];
$description = $cat_info['description'];

$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);

$cat_info['link'] = nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '/' . $cat_info['catid'] . '-' . $cat_info['alias'] . '.html', true);
$xtpl->assign('CAT', $cat_info);

// Get templates for this cat
$sql_t = "SELECT id, title, alias, icon, description FROM `" . NV_PREFIXLANG . "_" . $module_data . "_templates` WHERE catid=" . $catid . " AND status=1 ORDER BY weight ASC";
$result_t = $db->query($sql_t);

while ($item = $result_t->fetch()) {
    // Shortened Link
    $item['link'] = nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '/' . $item['alias'] . '-' . $item['id'] . '.html', true);
    $xtpl->assign('ITEM', $item);
    $xtpl->parse('main.cat.item');
}

$xtpl->parse('main.cat');
$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
