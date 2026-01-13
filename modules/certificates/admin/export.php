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

require_once NV_ROOTDIR . '/modules/' . $module_file . '/lib/SimpleXLSXGen.php';
use Shuchkin\SimpleXLSXGen;

$page_title = $lang_module['export_excel'];

if ($nv_Request->isset_request('export', 'post')) {
    $catid = $nv_Request->get_int('catid', 'post', 0);

    // Fetch data
    $where = [];
    $params = [];
    if ($catid > 0) {
        $where[] = "catid=" . $catid;
    }

    $sql_where = "";
    if (!empty($where)) {
        $sql_where = " WHERE " . implode(" AND ", $where);
    }

    // Get custom fields for headers
    $custom_fields = [];
    $sql_fields = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_fields WHERE status=1 ORDER BY weight ASC";
    $result_fields = $db->query($sql_fields);
    while ($field = $result_fields->fetch()) {
        // Filter if field is specific to this category
        $show = false;
        if ($field['catids'] == '0' || empty($field['catids'])) {
            $show = true;
        } else {
            $arr = explode(',', $field['catids']);
            if (in_array($catid, $arr)) {
                $show = true;
            }
        }

        if ($show) {
            $custom_fields[] = $field;
        }
    }

    // Headers
    $headers = ['STT', $lang_module['fullname'], $lang_module['birthdate'], $lang_module['cert_number'], $lang_module['reg_number'], $lang_module['issue_date'], $lang_module['classification'], 'Classification (EN)'];
    foreach ($custom_fields as $f) {
        $headers[] = $f['title'];
    }

    $data = [$headers];

    // Fetch Rows
    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows" . $sql_where . " ORDER BY id DESC";
    $result = $db->query($sql);

    $i = 1;
    while ($row = $result->fetch()) {
        $r = [
            $i++,
            $row['fullname'],
            $row['birthdate'],
            $row['cert_number'],
            $row['reg_number'],
            ($row['issue_date'] > 0) ? date('d/m/Y', $row['issue_date']) : '',
            $row['classification'],
            $row['classification_en']
        ];

        foreach ($custom_fields as $f) {
            $r[] = isset($row[$f['field']]) ? $row[$f['field']] : '';
        }

        $data[] = $r;
    }

    $cat_alias = 'certificates';
    if ($catid > 0) {
        $cat_alias = $db->query("SELECT alias FROM " . NV_PREFIXLANG . "_" . $module_data . "_cat WHERE catid=" . $catid)->fetchColumn();
    }
    $filename = $cat_alias . '_' . date('Ymd_Hi') . '.xlsx';

    SimpleXLSXGen::fromArray($data)->downloadAs($filename);
    die();
}

$xtpl = new XTemplate('export.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', 'export');

// Categories
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_cat ORDER BY weight ASC";
$result = $db->query($sql);
while ($cat = $result->fetch()) {
    $xtpl->assign('CAT', $cat);
    $xtpl->parse('main.cat');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
