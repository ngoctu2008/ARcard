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

// Headers
$headers = [
    'STT',
    'Họ và tên',
    'Ngày sinh (dd/mm/yyyy)',
    'Số hiệu văn bằng',
    'Số vào sổ',
    'Ngày cấp (dd/mm/yyyy)',
    'Xếp loại (VN)',
    'Xếp loại (EN)'
];

// Custom fields
$fields_q = $db->query("SELECT title FROM " . NV_PREFIXLANG . "_" . $module_data . "_fields WHERE status=1 ORDER BY weight ASC");
while ($field = $fields_q->fetch()) {
    $headers[] = $field['title'];
}

$data = [
    $headers,
    ['1', 'Nguyen Van A', '01/01/2000', 'B12345', 'S001', '01/06/2022', 'Gioi', 'Excellent'] // Example row
];

$xlsx = Shuchkin\SimpleXLSXGen::fromArray($data);
$xlsx->downloadAs('Mau_Nhap_Lieu_Van_Bang.xlsx');
exit();
