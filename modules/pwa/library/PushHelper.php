<?php

namespace NukeViet\Module\Pwa\Library;

class PushHelper
{
    private $auth;
    private $webPush;
    private $db;
    private $module_name;

    public function __construct($db, $module_config)
    {
        $this->db = $db;
        $this->module_name = 'pwa'; // Default, or pass as arg if dynamic

        if (!empty($module_config['vapid_public_key']) && !empty($module_config['vapid_private_key'])) {
            $this->auth = [
                'VAPID' => [
                    'subject' => NV_BASE_SITEURL,
                    'publicKey' => $module_config['vapid_public_key'],
                    'privateKey' => $module_config['vapid_private_key']
                ]
            ];

            require_once NV_ROOTDIR . '/modules/pwa/library/Vapid.php';
            require_once NV_ROOTDIR . '/modules/pwa/library/WebPush.php';

            $this->webPush = new WebPush($this->auth);
        }
    }

    public function sendPush($title, $message, $url, $image = '')
    {
        global $db_config, $global_config;

        if (!$this->webPush) {
            return ['status' => 'error', 'message' => 'VAPID keys not configured'];
        }

        // 1. Save Payload to DB (Pull-on-Push strategy)
        $pushData = [
            'title' => $title,
            'body' => $message,
            'url' => $url,
            'icon' => $image,
            'timestamp' => time()
        ];

        // Save to ALL languages to ensure retrieval regardless of user's current lang context (Cron might run as 'vi', user might be 'en')
        foreach ($global_config['allow_sitelangs'] as $site_lang) {
             $this->db->query("REPLACE INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $site_lang . "', '" . $this->module_name . "', 'last_push_payload', " . $this->db->quote(json_encode($pushData)) . ")");
        }

        // 2. Send Signal
        $sql = "SELECT endpoint, auth_keys FROM " . $db_config['prefix'] . "_" . NV_LANG_DATA . "_" . $this->module_name . "_subscriptions";
        // Note: NV_LANG_DATA in Cron might be default lang. We might need to iterate ALL subscription tables if PWA is localized?
        // Usually PWA module data is shared or specific. subscriptions table is usually `prefix_lang_pwa_subscriptions`.
        // If we want to notify ALL users, we might need to loop languages.
        // For now, let's assume we notify users of the CURRENT language (or default).
        // A better approach for Cron is to query all tables: `prefix_en_pwa_subscriptions`, `prefix_vi_pwa_subscriptions`.

        // Let's stick to NV_LANG_DATA for now, but in Cron we should set NV_LANG_DATA to the module's lang or loop.
        // If this helper is called from Admin (Language X), it notifies users of Language X.

        $result = $this->db->query($sql);
        $count = 0;

        while ($row = $result->fetch()) {
            $keys = json_decode($row['auth_keys'], true);
            $sub = [
                'endpoint' => $row['endpoint'],
                'keys' => $keys
            ];

            $res = $this->webPush->sendNotification($sub, null); // Signal only

            if (isset($res['success']) && $res['success']) {
                $count++;
            } else {
                if (isset($res['code']) && $res['code'] == 410) {
                     $this->db->query("DELETE FROM " . $db_config['prefix'] . "_" . NV_LANG_DATA . "_" . $this->module_name . "_subscriptions WHERE endpoint=" . $this->db->quote($row['endpoint']));
                }
            }
        }

        return ['status' => 'success', 'count' => $count];
    }
}
