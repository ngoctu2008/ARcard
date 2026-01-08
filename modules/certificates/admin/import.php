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

require_once NV_ROOTDIR . '/modules/' . $module_file . '/lib/SimpleXLSX.php';

$page_title = $lang_module['import_excel'];
$error = '';
$info = '';

if ($nv_Request->isset_request('import', 'post')) {
    $rows = $nv_Request->get_array('rows', 'post', []);
    $catid = $nv_Request->get_int('catid', 'post', 0);

    if (empty($rows)) {
        $error = "No data to import";
    } else {
        $count = 0;
        foreach ($rows as $row) {
            // Check if cert_number exists
            $check = $db->query("SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE cert_number=" . $db->quote($row['cert_number']))->fetchColumn();

            // Format issue date
            $issue_date = 0;
            if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $row['issue_date'], $m)) {
                $issue_date = mktime(0, 0, 0, $m[2], $m[1], $m[3]);
            } elseif (preg_match('/^([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})$/', $row['issue_date'], $m)) {
                $issue_date = mktime(0, 0, 0, $m[2], $m[3], $m[1]);
            }

            if ($check == 0) {
                $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_rows
                (catid, fullname, birthdate, cert_number, reg_number, issue_date, classification) VALUES
                (:catid, :fullname, :birthdate, :cert_number, :reg_number, :issue_date, :classification)";

                $data = [
                    ':catid' => $catid,
                    ':fullname' => $row['fullname'],
                    ':birthdate' => $row['birthdate'],
                    ':cert_number' => $row['cert_number'],
                    ':reg_number' => $row['reg_number'],
                    ':issue_date' => $issue_date,
                    ':classification' => $row['classification']
                ];

                if($db->insert_id($sql, 'id', $data)) {
                    $count++;
                }
            } else {
                 // Option: Update if exists. For now, we skip or update?
                 // Requirement: "Báo lỗi hoặc Cập nhật (tùy chọn)". I will choose Update.
                 $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_rows SET
                    catid=:catid, fullname=:fullname, birthdate=:birthdate, reg_number=:reg_number, issue_date=:issue_date, classification=:classification
                    WHERE cert_number=:cert_number";
                 $data = [
                    ':catid' => $catid,
                    ':fullname' => $row['fullname'],
                    ':birthdate' => $row['birthdate'],
                    ':cert_number' => $row['cert_number'],
                    ':reg_number' => $row['reg_number'],
                    ':issue_date' => $issue_date,
                    ':classification' => $row['classification']
                ];
                $db->query_check($sql, $data);
                $count++;
            }
        }
        $info = sprintf($lang_module['import_success'], $count);
        nv_del_moduleCache($module_name);
    }
}

$xtpl = new XTemplate('import.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', 'import');

if (!empty($error)) {
    $xtpl->assign('ERROR', $error);
    $xtpl->parse('main.error');
}
if (!empty($info)) {
    $xtpl->assign('INFO', $info);
    $xtpl->parse('main.info');
}

// Check uploaded file
if (isset($_FILES['import_file']) && is_uploaded_file($_FILES['import_file']['tmp_name'])) {
    if ($xlsx = SimpleXLSX::parse($_FILES['import_file']['tmp_name'])) {
        $rows = $xlsx->rows();

        // Remove header row if needed (assuming 1st row is header)
        if (!empty($rows)) {
            unset($rows[0]);
        }

        $catid = $nv_Request->get_int('catid', 'post', 0);
        $xtpl->assign('CATID', $catid);

        $i = 0;
        foreach ($rows as $r) {
            // Structure: STT (0), Fullname (1), Birthdate (2), CertNum (3), RegNum (4), IssueDate (5), Class (6)
            // Adjust indices based on user requirement: "STT, Họ tên, Ngày sinh, Số hiệu, Số vào sổ, Ngày cấp, Xếp loại"
            if (empty($r[1]) || empty($r[3])) continue; // Skip if no name or cert number

            $item = [
                'index' => $i,
                'fullname' => $r[1],
                'birthdate' => isset($r[2]) ? $r[2] : '', // String
                'cert_number' => isset($r[3]) ? $r[3] : '',
                'reg_number' => isset($r[4]) ? $r[4] : '',
                'issue_date' => isset($r[5]) ? $r[5] : '', // Expecting dd/mm/yyyy
                'classification' => isset($r[6]) ? $r[6] : ''
            ];

            $xtpl->assign('ITEM', $item);
            $xtpl->parse('main.preview.loop');
            $i++;
        }
        $xtpl->parse('main.preview');
    } else {
        $xtpl->assign('ERROR', SimpleXLSX::parseError());
        $xtpl->parse('main.error');
    }
}

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
