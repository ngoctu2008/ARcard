<?php

/**
 * @version 4.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
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
    echo $row['config_value'];
} else {
    echo json_encode(['title' => 'New Notification', 'body' => 'You have a new update.']);
}
die();
