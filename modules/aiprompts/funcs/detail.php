<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Tên Của Bạn <email@domain.com>
 * @Copyright (C) 2024. All rights reserved
 * @Createdate Sat, 25 May 2024 10:00:00 GMT
 */

if (!defined('NV_IS_MOD_AIPROMPTS')) {
    die('Stop!!!');
}

// URL format: detail/alias-id
$alias = isset($array_op[1]) ? $array_op[1] : '';
$array_page = explode('-', $alias);
$id = intval(end($array_page));

if ($id > 0) {
    $sql = "SELECT * FROM " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_templates WHERE id=" . $id . " AND status=1";
    $result = $db->query($sql);
    $row = $result->fetch();
}

if (empty($row)) {
    Header('Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . $lang . '&' . NV_NAME_VARIABLE . '=' . $module_name);
    die();
}

$page_title = $row['title'];
$key_words = $row['title'];

$xtpl = new XTemplate('detail.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('ROW', $row);

// Parse JSON config
$input_config = json_decode($row['input_config'], true);

if (!empty($input_config)) {
    foreach ($input_config as $conf) {
        $conf['required_star'] = ($conf['required']) ? ' <span class="text-danger">(*)</span>' : '';
        $conf['required_attr'] = ($conf['required']) ? 'required="required"' : '';

        $xtpl->assign('CONF', $conf);

        if ($conf['type'] == 'text') {
            $xtpl->parse('main.input_text');
        } elseif ($conf['type'] == 'number') {
             $xtpl->parse('main.input_number');
        } elseif ($conf['type'] == 'textarea') {
             $xtpl->parse('main.input_textarea');
        } elseif ($conf['type'] == 'select') {
             foreach ($conf['options'] as $opt) {
                 $xtpl->assign('OPTION', array('value' => trim($opt), 'title' => trim($opt)));
                 $xtpl->parse('main.input_select.option');
             }
             $xtpl->parse('main.input_select');
        }
    }
}

// Pass Prompt Body to JS safely
// We can store it in a hidden div or assign to a JS variable
// JS variable is cleaner but XTemplate makes hidden div easier
$xtpl->assign('PROMPT_BODY', htmlspecialchars($row['prompt_body']));

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
