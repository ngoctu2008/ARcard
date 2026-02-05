<?php

/**
 * @version 4.x
 * @author Phạm Ngọc Tú <ngoctu.dnkd@gmail.com>
 * @copyright (C) 2009-2021 Phạm Ngọc Tú. All rights reserved
 * @license GNU/GPL version 2 or any later version
 */

if (!defined('NV_MAINFILE')) {
    exit('Stop!!!');
}

header('Content-Type: application/json; charset=utf-8');

// Load latest push payload from config
$sql = "SELECT config_value FROM " . NV_CONFIG_GLOBALTABLE . " WHERE lang='" . $lang . "' AND module='" . $module_name . "' AND config_name='last_push_payload'";
$result = $db->query($sql);
$row = $result->fetch();

if ($row) {
    // Ensure unicode characters are not escaped if the stored JSON was escaped
    // But usually DB stores it correctly. We just echo it.
    // However, if the client JS needs JSON, and DB content is JSON string, echoing it is correct.
    echo $row['config_value'];
} else {
    echo json_encode(['title' => 'Thông báo mới', 'body' => 'Có thông tin cập nhật mới.'], JSON_UNESCAPED_UNICODE);
}
die();
