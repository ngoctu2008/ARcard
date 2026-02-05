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

// NOTE: This file is intended to be run by the NukeViet Cronjob System
// You should register this file in AdminCP -> Tools -> Cronjobs -> Add New
// File path: modules/pwa/cronjobs/auto_news.php

function pwa_cron_auto_news()
{
    global $db, $db_config, $global_config, $module_name; // We need $module_name of PWA

    // Hardcode PWA module name if we are in global context,
    // BUT usually cron function doesn't set $module_name to 'pwa'.
    // We need to find the PWA module name.
    $pwa_module_name = 'pwa'; // Default
    // Check real name if installed differently
    $sql = "SELECT module_file FROM " . NV_MODULES_TABLE . " WHERE module_file='pwa'";
    $result = $db->query($sql);
    if ($result->rowCount() > 0) {
        // It exists.
    } else {
        // Maybe named differently?
        // Let's assume 'pwa' for now as per structure.
    }

    // Load PWA config
    $sql = "SELECT config_name, config_value FROM " . NV_CONFIG_GLOBALTABLE . " WHERE lang='" . NV_LANG_DATA . "' AND module='" . $pwa_module_name . "'";
    $result = $db->query($sql);
    $module_config = [];
    while ($row = $result->fetch()) {
        $module_config[$row['config_name']] = $row['config_value'];
    }

    // Check if automation is active
    if (empty($module_config['auto_active']) || $module_config['auto_active'] != 1) {
        return false;
    }

    // Check active modules
    if (empty($module_config['auto_modules'])) {
        return false;
    }
    $watch_modules = explode(',', $module_config['auto_modules']);

    // Load PushHelper
    if (!file_exists(NV_ROOTDIR . '/modules/' . $pwa_module_name . '/library/PushHelper.php')) {
        return false;
    }
    require_once NV_ROOTDIR . '/modules/' . $pwa_module_name . '/library/PushHelper.php';
    $pushHelper = new \NukeViet\Module\Pwa\Library\PushHelper($db, $module_config);

    $has_sent = false;

    foreach ($watch_modules as $mod) {
        // 1. Get module info to know the table prefix (usually just prefix_lang_modulename_rows)
        // Check "Last Checked Time" for this module. We store it in pwa config as 'auto_last_check_{mod}'
        $last_check_key = 'auto_last_check_' . $mod;
        $last_check = isset($module_config[$last_check_key]) ? intval($module_config[$last_check_key]) : 0;

        // Query latest post
        // Table: {prefix}_{lang}_{mod}_rows
        // Status = 1 (Active), publtime <= time() (Published), publtime > last_check
        $table = $db_config['prefix'] . "_" . NV_LANG_DATA . "_" . $mod . "_rows";

        // Verify table exists to avoid crash
        // $sql = "SHOW TABLES LIKE '$table'"; ... (Optional but safer)

        $sql = "SELECT id, title, alias, catid, publtime, homeimgfile, homeimgthumb FROM " . $table . " WHERE status=1 AND publtime <= " . time() . " AND publtime > " . $last_check . " ORDER BY publtime DESC LIMIT 1";

        try {
            $result = $db->query($sql);
            $row = $result->fetch();

            if ($row) {
                // Found new post!

                // Construct URL
                // News URL: index.php?nv={mod}&op={cat_alias}/{alias}-{id} or similar.
                // We need to construct it properly.
                // Standard News: nv_url_rewrite(NV_BASE_SITEURL . 'index.php?nv=' . $mod . '&op=' . $alias . '-' . $id . $global_config['rewrite_exturl'], $mod);
                // But we don't have cat alias here easily without join.
                // News module detail link is usually: module/alias-id.html
                // Let's rely on standard NukeViet URL construction if possible, or simple format.

                $url = NV_BASE_SITEURL . 'index.php?nv=' . $mod . '&op=detail&id=' . $row['id'];
                // Wait, standard news usually uses alias in op or clean url.
                // Let's use the standard "alias-id" format if we can.
                // We don't have access to `nv_url_rewrite` context for that module easily inside a generic cron?
                // Actually `nv_url_rewrite` works globally if configured.
                // Let's try to build the native link:
                // $link = NV_BASE_SITEURL . "index.php?nv=" . $mod . "&op=" . $row['alias'] . "-" . $row['id'];

                // Better: Check if `alias` is valid.
                $link = NV_BASE_SITEURL . "index.php?nv=" . $mod . "&op=" . $row['alias'] . "-" . $row['id'] . (isset($global_config['rewrite_exturl']) ? $global_config['rewrite_exturl'] : '');

                // Prepare Image
                $image = '';
                if (!empty($row['homeimgfile']) && file_exists(NV_ROOTDIR . '/' . $row['homeimgfile'])) {
                     $image = NV_BASE_SITEURL . $row['homeimgfile'];
                } elseif (!empty($row['homeimgthumb']) && strpos($row['homeimgthumb'], '|') !== false) {
                     // Thumb might be "path|width|height"
                     $parts = explode('|', $row['homeimgthumb']);
                     if (file_exists(NV_ROOTDIR . '/' . $parts[0])) {
                         $image = NV_BASE_SITEURL . $parts[0];
                     }
                }

                // Send Push
                $pushHelper->sendPush($row['title'], 'Tin mới cập nhật!', $link, $image);

                // Update Last Check Time
                // We set it to the publtime of the post we just found.
                // Next time we look for > this time.
                $new_check_time = $row['publtime'];

                $db->query("REPLACE INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . NV_LANG_DATA . "', '" . $pwa_module_name . "', '" . $last_check_key . "', " . $new_check_time . ")");

                $has_sent = true;

                // Break to avoid sending multiple notifications in one cron run (avoid spam)
                // If the user has 3 active modules, and all have new posts, we only send one per 5 mins?
                // Or one per module?
                // Let's send one per module to be safe, but typically 1 total is better to avoid annoyance.
                // User requirement: "Tự động lấy thông tin mới... tạo thông báo đẩy".
                // Let's stick to 1 notification per cron run max.
                break;
            }
        } catch (\PDOException $e) {
            // Table might not exist or error
            continue;
        }
    }

    return $has_sent;
}

// Check if run directly or via NV Cron
if (defined('NV_IS_CRON')) {
    pwa_cron_auto_news();
}
