<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Phạm Ngọc Tú <ngoctu.dnkd@gmail.com>
 * @Copyright (C) 2024. All rights reserved
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    die('Stop!!!');
}

$page_title = $lang_module['template_manage'];

// AJAX Action for Weight
if ($nv_Request->isset_request('ajax_action', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $new_vid = $nv_Request->get_int('new_vid', 'post', 0);
    if ($id > 0) {
        $sql = "UPDATE `" . NV_PREFIXLANG . "_" . $module_data . "_templates` SET weight=" . $new_vid . " WHERE id=" . $id;
        $db->query($sql);
        die('OK');
    }
    die('NO');
}

// AJAX Action for Status
if ($nv_Request->isset_request('change_status', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $new_status = $nv_Request->get_int('new_status', 'post', 0);
    if ($id > 0) {
        $sql = "UPDATE `" . NV_PREFIXLANG . "_" . $module_data . "_templates` SET status=" . $new_status . " WHERE id=" . $id;
        $db->query($sql);
        die('OK');
    }
    die('NO');
}

// Delete
if ($nv_Request->isset_request('delete', 'post')) {
    $id_del = $nv_Request->get_int('delete', 'post', 0);
    if ($id_del > 0) {
        $db->query("DELETE FROM `" . NV_PREFIXLANG . "_" . $module_data . "_templates` WHERE id=" . $id_del);
        die('OK');
    }
    die('NO');
}

$q = $nv_Request->get_title('q', 'get', '');
$catid = $nv_Request->get_int('catid', 'get', 0);
$per_page = 20;
$page = $nv_Request->get_int('page', 'get', 1);

$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('Q', $q);

// Fetch Categories for Filter
$sql_cat = "SELECT catid, title FROM `" . NV_PREFIXLANG . "_" . $module_data . "_cat` ORDER BY weight ASC";
$result_cat = $db->query($sql_cat);
while ($row_cat = $result_cat->fetch()) {
    $row_cat['selected'] = ($row_cat['catid'] == $catid) ? 'selected="selected"' : '';
    $xtpl->assign('CAT', $row_cat);
    $xtpl->parse('main.cat');
}

// Build Search Query
$where = [];
$params = [];

if (!empty($q)) {
    $where[] = "t.title LIKE :q";
    $params[':q'] = '%' . $q . '%';
}

if ($catid > 0) {
    $where[] = "t.catid = :catid";
    $params[':catid'] = $catid;
}

$sql_where = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Count Total
$sql_count = "SELECT COUNT(*) FROM `" . NV_PREFIXLANG . "_" . $module_data . "_templates` t " . $sql_where;
$stmt_count = $db->prepare($sql_count);
foreach ($params as $key => $val) {
    $stmt_count->bindValue($key, $val);
}
$stmt_count->execute();
$num_items = $stmt_count->fetchColumn();

// Pagination
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&catid=' . $catid . '&q=' . $q;
$generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);

// Fetch Data
$sql = "SELECT t.*, c.title as cat_title FROM `" . NV_PREFIXLANG . "_" . $module_data . "_templates` t LEFT JOIN `" . NV_PREFIXLANG . "_" . $module_data . "_cat` c ON t.catid = c.catid " . $sql_where . " ORDER BY t.weight ASC LIMIT " . ($page - 1) * $per_page . "," . $per_page;
$stmt = $db->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();

$num = $num_items; // For visual weight (total items) - Though weight logic usually needs total count of ALL items to render full dropdown, keeping simplistic here or use $num_items if we want dropdown 1..Total
// Actually weight dropdown typically 1..Total. Let's use $num_items for the loop if filtering isn't active, but usually weight management is best done without filter.
// If filtering, weight changing might be confusing. But let's proceed.

while ($item = $stmt->fetch()) {
    $item['add_time'] = nv_date('H:i d/m/y', $item['add_time']);
    $item['edit_time'] = nv_date('H:i d/m/y', $item['edit_time']);

    // Status Select
    $status_active = ($item['status'] == 1) ? 'selected="selected"' : '';
    $status_inactive = ($item['status'] == 0) ? 'selected="selected"' : '';

    $xtpl->assign('STATUS', array('key' => 1, 'title' => $lang_module['active'], 'selected' => $status_active));
    $xtpl->parse('main.list.status');
    $xtpl->assign('STATUS', array('key' => 0, 'title' => $lang_module['inactive'], 'selected' => $status_inactive));
    $xtpl->parse('main.list.status');

    // Weight Select (Limit to 50 or total items to avoid massive dropdowns)
    // Only show weight dropdown if no filter or small list? Or just 1..NumItems
    // If filtered, weight changing is tricky. Let's keep it but based on total count.
    $weight_limit = $num_items;
    for ($i = 1; $i <= $weight_limit; $i++) {
        $weight_selected = ($i == $item['weight']) ? 'selected="selected"' : '';
        $xtpl->assign('WEIGHT', array('key' => $i, 'title' => $i, 'selected' => $weight_selected));
        $xtpl->parse('main.list.weight');
    }

    $xtpl->assign('ITEM', $item);
    $xtpl->parse('main.list');
}

if (!empty($generate_page)) {
    $xtpl->assign('GENERATE_PAGE', $generate_page);
    $xtpl->parse('main.generate_page');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
