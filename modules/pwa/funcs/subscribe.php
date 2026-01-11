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

$endpoint = $nv_Request->get_string('endpoint', 'post', '');
$keys = $nv_Request->get_typed_array('keys', 'post', 'string', []);
$userId = defined('NV_IS_USER') ? $user_info['userid'] : 0;

if (empty($endpoint) || empty($keys['p256dh']) || empty($keys['auth'])) {
    $contents = ['status' => 'error', 'message' => 'Invalid data'];
    header('Content-Type: application/json');
    echo json_encode($contents);
    die();
}

$jsonKeys = json_encode($keys);

// Check if endpoint exists
$sql = "SELECT id FROM " . $db_config['prefix'] . "_" . NV_LANG_DATA . "_" . $module_data . "_subscriptions WHERE endpoint=" . $db->quote($endpoint);
$result = $db->query($sql);
if ($result->rowCount() > 0) {
    // Update
    $id = $result->fetchColumn();
    $sql = "UPDATE " . $db_config['prefix'] . "_" . NV_LANG_DATA . "_" . $module_data . "_subscriptions SET userid=" . $userId . ", auth_keys=" . $db->quote($jsonKeys) . ", time=" . NV_CURRENTTIME . " WHERE id=" . $id;
    $db->query($sql);
} else {
    // Insert
    $sql = "INSERT INTO " . $db_config['prefix'] . "_" . NV_LANG_DATA . "_" . $module_data . "_subscriptions (userid, endpoint, auth_keys, time) VALUES (" . $userId . ", " . $db->quote($endpoint) . ", " . $db->quote($jsonKeys) . ", " . NV_CURRENTTIME . ")";
    $db->query($sql);
}

header('Content-Type: application/json');
echo json_encode(['status' => 'success']);
die();
