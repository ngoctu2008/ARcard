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

$page_title = $lang_module['main_manage'];
$table_rows = NV_PREFIXLANG . "_" . $module_data . "_rows";

// AJAX: Change Status
if ($nv_Request->isset_request('change_status', 'post')) {
    $id = $nv_Request->get_int('catid', 'post', 0); // JS sends catid
    if ($id > 0) {
        $sql = "SELECT status FROM " . $table_rows . " WHERE id=" . $id;
        $status = $db->query($sql)->fetchColumn();
        $new_status = ($status == 1) ? 0 : 1;
        $db->query("UPDATE " . $table_rows . " SET status=" . $new_status . " WHERE id=" . $id);
        $nv_Cache->delMod($module_name);
        die('OK_' . $new_status);
    }
    die('NO');
}

// Fetch categories for filter
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_cat ORDER BY weight ASC";
$result = $db->query($sql);
$array_cat = [];
while ($row = $result->fetch()) {
    $array_cat[$row['catid']] = $row;
}

// Pagination and Filter
$page = $nv_Request->get_int('page', 'get', 1);
$per_page = isset($module_config[$module_name]['per_page']) ? $module_config[$module_name]['per_page'] : 20;
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name;

$catid = $nv_Request->get_int('catid', 'get', 0);
$q = $nv_Request->get_string('q', 'get', '');
$status = $nv_Request->get_int('status', 'get', -1);
$order_by = $nv_Request->get_string('order_by', 'get', 'id');
$order_dir = $nv_Request->get_string('order_dir', 'get', 'DESC');

$where = [];
$params = [];

if ($catid > 0) {
    $where[] = 'catid=' . $catid;
    $base_url .= '&catid=' . $catid;
}
if ($status >= 0) {
    $where[] = 'status=' . $status;
    $base_url .= '&status=' . $status;
}
if (!empty($q)) {
    $where[] = '(fullname LIKE :q1 OR cert_number LIKE :q2 OR reg_number LIKE :q3)';
    $params[':q1'] = '%' . $q . '%';
    $params[':q2'] = '%' . $q . '%';
    $params[':q3'] = '%' . $q . '%';
    $base_url .= '&q=' . urlencode($q);
}

// Validate Sort
$allowed_orders = ['id', 'fullname', 'birthdate', 'cert_number', 'reg_number', 'issue_date', 'status'];
if (!in_array($order_by, $allowed_orders)) $order_by = 'id';
if ($order_dir != 'ASC' && $order_dir != 'DESC') $order_dir = 'DESC';

$base_url .= '&order_by=' . $order_by . '&order_dir=' . $order_dir;

$where_sql = '';
if (!empty($where)) {
    $where_sql = ' WHERE ' . implode(' AND ', $where);
}

// Count total
$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows" . $where_sql;
$sth = $db->prepare($sql);
foreach ($params as $key => $val) {
    $sth->bindValue($key, $val);
}
$sth->execute();
$num_items = $sth->fetchColumn();

// Get data
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows" . $where_sql . " ORDER BY " . $order_by . " " . $order_dir . " LIMIT " . ($page - 1) * $per_page . "," . $per_page;
$sth = $db->prepare($sql);
foreach ($params as $key => $val) {
    $sth->bindValue($key, $val);
}
$sth->execute();

$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', 'main');
$xtpl->assign('Q', $q);

$xtpl->assign('STATUS_' . $status, 'selected="selected"');

// Sort Links
$cols = ['id', 'fullname', 'birthdate', 'cert_number', 'reg_number', 'issue_date', 'status'];
foreach ($cols as $col) {
    $new_dir = ($order_by == $col && $order_dir == 'ASC') ? 'DESC' : 'ASC';
    $icon = ($order_by == $col) ? ($order_dir == 'ASC' ? 'fa-sort-asc' : 'fa-sort-desc') : 'fa-sort';
    $xtpl->assign('SORT_' . strtoupper($col), NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&order_by=' . $col . '&order_dir=' . $new_dir . '&catid=' . $catid . '&status=' . $status . '&q=' . $q);
    $xtpl->assign('ICON_' . strtoupper($col), $icon);
}

// Categories select
foreach ($array_cat as $cat) {
    $cat['selected'] = ($cat['catid'] == $catid) ? 'selected="selected"' : '';
    $xtpl->assign('CAT', $cat);
    $xtpl->parse('main.cat');
}

// Rows
while ($row = $sth->fetch()) {
    $row['cat_title'] = isset($array_cat[$row['catid']]) ? $array_cat[$row['catid']]['title'] : '';
    $row['issue_date_str'] = ($row['issue_date'] > 0) ? date('d/m/Y', $row['issue_date']) : '';
    $row['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=content&id=' . $row['id'];
    $row['status_check'] = ($row['status'] == 1) ? 'checked' : '';
    $xtpl->assign('ROW', $row);
    $xtpl->assign('CHECK', $row['status_check']); // Map to CHECK for consistency with sample
    $xtpl->parse('main.loop');
}

$generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
if (!empty($generate_page)) {
    $xtpl->assign('GENERATE_PAGE', $generate_page);
    $xtpl->parse('main.page'); // Note: Sample used 'generate_page' block and NV_GENERATE_PAGE var
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
