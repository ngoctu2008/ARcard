<?php

/**
 * @version 4.x
 * @author Phạm Ngọc Tú <ngoctu.dnkd@gmail.com>
 * @copyright (C) 2009-2021 Phạm Ngọc Tú. All rights reserved
 * @license GNU/GPL version 2 or any later version
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    exit('Stop!!!');
}

if (isset($lang_module['send_notification'])) {
    $page_title = $lang_module['send_notification'];
}

// Load config for VAPID keys
$sql = "SELECT config_name, config_value FROM " . NV_CONFIG_GLOBALTABLE . " WHERE lang='" . NV_LANG_DATA . "' AND module='" . $module_name . "'";
$result = $db->query($sql);
$module_config = [];
while ($row = $result->fetch()) {
    $module_config[$row['config_name']] = $row['config_value'];
}

if (empty($module_config['vapid_public_key']) || empty($module_config['vapid_private_key'])) {
    $xtpl = new XTemplate('error.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
    $xtpl->assign('ERROR', $lang_module['error_vapid']);
    $xtpl->parse('main');
    $contents = $xtpl->text('main');
} else {
    $xtpl = new XTemplate('notification.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
    $xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
    $xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
    $xtpl->assign('MODULE_NAME', $module_name);
    $xtpl->assign('OP', $op);

    // Check if form submitted
    if ($nv_Request->isset_request('send', 'post')) {
        $title = $nv_Request->get_title('title', 'post', '');
        $message = $nv_Request->get_string('message', 'post', '');
        $url = $nv_Request->get_string('url', 'post', '');

        // Prepare payload
        // NOTE: Since our WebPush.php currently only sends the "wake up" signal (empty payload) because we didn't implement full AES128GCM,
        // the Service Worker `push` event handler in `sw.php` needs to be smart.
        // Wait, the `sw.php` I wrote expects JSON:
        // `try { data = event.data.json(); } ...`
        // If I send empty body, `event.data` is null or empty.
        // So the `sw.php` will default to: `title = 'Notification', body = event.data.text()`.

        // LIMITATION: Without AES128GCM, we CANNOT send data securely to the browser.
        // The browser will receive a push event, but no data.
        // It must show a generic notification or fetch the data from the server.

        // Workaround for "One Click" without external deps:
        // 1. Save this notification to a DB table `pwa_latest_notification`.
        // 2. In SW, when `push` event fires (and data is empty), perform `fetch('/index.php?nv=pwa&op=get_latest_notification')`.
        // 3. Show that data.
        // This effectively bypasses the complex encryption requirement by using a pull-on-push mechanism.
        // It is slightly slower but works 100% without encryption libs.

        // Let's implement this "Pull-on-Push" strategy to ensure the user gets the custom title/message.

        // Save to DB (We need a table for this? Or just a config value? A config value is enough for "Latest global message")
        // But if multiple messages are sent?
        // Let's use a config value for simplicity for now, or a file.
        // "pwa_last_push"
        $pushData = [
            'title' => $title,
            'body' => $message,
            'url' => $url,
            'timestamp' => time()
        ];
        $db->query("REPLACE INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . NV_LANG_DATA . "', '" . $module_name . "', 'last_push_payload', " . $db->quote(json_encode($pushData)) . ")");

        // Now send the signal to all subscribers
        require_once NV_ROOTDIR . '/modules/' . $module_file . '/library/Vapid.php';
        require_once NV_ROOTDIR . '/modules/' . $module_file . '/library/WebPush.php';

        $auth = [
            'VAPID' => [
                'subject' => NV_BASE_SITEURL, // Should be mailto: or URL
                'publicKey' => $module_config['vapid_public_key'],
                'privateKey' => $module_config['vapid_private_key']
            ]
        ];

        $webPush = new \NukeViet\Module\Pwa\Library\WebPush($auth);

        // Fetch all subscriptions
        $sql = "SELECT endpoint, auth_keys FROM " . $db_config['prefix'] . "_" . NV_LANG_DATA . "_" . $module_data . "_subscriptions";
        $result = $db->query($sql);

        $count = 0;
        while ($row = $result->fetch()) {
            $keys = json_decode($row['auth_keys'], true);
            $sub = [
                'endpoint' => $row['endpoint'],
                'keys' => $keys
            ];

            // Send empty payload (signal)
            $res = $webPush->sendNotification($sub, null);
            if ($res['success']) {
                $count++;
            } else {
                // Handle 410 Gone (remove subscription)
                if ($res['code'] == 410) {
                     $db->query("DELETE FROM " . $db_config['prefix'] . "_" . NV_LANG_DATA . "_" . $module_data . "_subscriptions WHERE endpoint=" . $db->quote($row['endpoint']));
                }
            }
        }

        $xtpl->assign('SUCCESS_MSG', sprintf($lang_module['send_success'], $count));
        $xtpl->parse('main.success_msg');
    }

    $xtpl->parse('main');
    $contents = $xtpl->text('main');
}

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
