<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Phạm Ngọc Tú <ngoctu.dnkd@gmail.com>
 * @Copyright (C) 2024 Phạm Ngọc Tú. All rights reserved
 * @License: Not free source, more information contact ngoctu.dnkd@gmail.com
 * @Createdate Thu, 01 Jan 2024 00:00:00 GMT
 */

if (!defined('NV_ADMIN')) {
    die('Stop!!!');
}

// Ensure the function exists before declaring it to avoid redeclaration errors
if (!function_exists('certificates_insert_sample_data')) {
    function certificates_insert_sample_data($module_data, $lang) {
        global $db, $db_config;

        $table_cat = $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_cat";
        $table_rows = $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_rows";

        // Sample Categories
        $sql_cat = "INSERT INTO " . $table_cat . " (catid, title, alias, description, image, status, weight) VALUES
        (1, 'Bằng Tốt nghiệp THPT', 'bang-tot-nghiep-thpt', 'Tra cứu bằng tốt nghiệp Trung học phổ thông', '', 1, 1),
        (2, 'Chứng chỉ Tin học', 'chung-chi-tin-hoc', 'Chứng chỉ ứng dụng CNTT cơ bản và nâng cao', '', 1, 2),
        (3, 'Chứng chỉ Ngoại ngữ', 'chung-chi-ngoai-ngu', 'Chứng chỉ Tiếng Anh A1, A2, B1...', '', 1, 3),
        (4, 'Bằng Lái xe', 'bang-lai-xe', 'Giấy phép lái xe các hạng A1, B2, C', '', 1, 4);";

        // Sample Rows (Certificates)
        // timestamp 1577836800 = 01/01/2020
        // timestamp 1609459200 = 01/01/2021
        // timestamp 1640995200 = 01/01/2022

        $sql_rows = "INSERT INTO " . $table_rows . " (id, catid, fullname, birthdate, cert_number, reg_number, issue_date, classification, classification_en, status) VALUES
        (NULL, 1, 'Nguyễn Văn Nam', '01/01/2000', 'B12345', 'S001', 1590969600, 'Giỏi', 'Good', 1),
        (NULL, 1, 'Trần Thị Hương', '15/05/2000', 'B12346', 'S002', 1590969600, 'Khá', 'Fair', 1),
        (NULL, 2, 'Lê Văn Cường', '20/10/1995', 'IT001', 'CNTT01', 1609459200, 'Xuất sắc', 'Excellent', 1),
        (NULL, 2, 'Phạm Minh Tú', '10/12/1998', 'IT002', 'CNTT02', 1612137600, 'Khá', 'Good', 1),
        (NULL, 3, 'Hoàng Thị Lan', '05/03/2001', 'ENG001', 'NN001', 1640995200, 'B1', 'B1', 1),
        (NULL, 4, 'Đỗ Văn Hùng', '12/07/1990', 'GPLX001', 'LX001', 1577836800, '', '', 1);";

        // Execute Inserts
        // Note: Using try/catch or simple execution. Since this is data file, we usually just run query.
        // However, duplicate entry might error out if re-run.
        // We use INSERT IGNORE logic or simply allow it to fail silently if keys exist.
        // But for standard NukeViet data files, usually we assume clean install or check manually.
        // Let's just execute standard queries.

        // We split by row to handle potential errors individually or just bulk insert.
        // But NukeViet usually expects raw SQL commands or logic.
        // Since the prompt asked for "data_vi.php", this file is usually included during install or can be run manually.
        // The user provided a snippet: $db->query("INSERT...");
        // So I will execute them.

        try {
            $db->query($sql_cat);
        } catch(Exception $e) {
            // Ignore if exists
        }

        try {
            $db->query($sql_rows);
        } catch(Exception $e) {
            // Ignore if exists
        }
    }

    // Call the function immediately if this file is included in a context where $module_data is set
    // Usually data_vi.php is included by the installer.
    if (isset($module_data) && isset($lang)) {
        certificates_insert_sample_data($module_data, $lang);
    }
}
