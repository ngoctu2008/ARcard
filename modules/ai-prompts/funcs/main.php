<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Tên Của Bạn <email@domain.com>
 * @Copyright (C) 2024. All rights reserved
 * @Createdate Sat, 25 May 2024 10:00:00 GMT
 */

if (!defined('NV_IS_MOD_AI_PROMPTS')) {
    die('Stop!!!');
}

$page_title = $module_info['custom_title'];
$key_words = $module_info['keywords'];

$sql = "SELECT catid, title FROM `" . NV_PREFIXLANG . "_" . $module_data . "_cat` WHERE status=1 ORDER BY weight ASC";
$result = $db->query($sql);

$array_cat = array();
while ($row = $result->fetch()) {
    $array_cat[$row['catid']] = $row;
}

$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);

foreach ($array_cat as $cat) {
    $xtpl->assign('CAT', $cat);

    // Get templates for this cat
    $sql_t = "SELECT id, title, alias, description FROM `" . NV_PREFIXLANG . "_" . $module_data . "_templates` WHERE catid=" . $cat['catid'] . " AND status=1 ORDER BY weight ASC";
    $result_t = $db->query($sql_t);

    while ($item = $result_t->fetch()) {
        $item['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=detail/' . $item['alias'] . '-' . $item['id'];
        $xtpl->assign('ITEM', $item);
        $xtpl->parse('main.cat.item');
    }

    $xtpl->parse('main.cat');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
