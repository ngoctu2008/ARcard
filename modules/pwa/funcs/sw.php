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

header('Content-Type: application/javascript; charset=utf-8');

// Get offline page URL if any
$offlineUrl = NV_BASE_SITEURL . 'index.php?nv=' . $module_name . '&op=offline';

// Load module config for icon
$sql = "SELECT config_value FROM " . NV_CONFIG_GLOBALTABLE . " WHERE lang='" . $lang . "' AND module='" . $module_name . "' AND config_name='icon_path'";
$result = $db->query($sql);
$icon_path = $result->fetchColumn();
$notification_icon = NV_BASE_SITEURL . $global_config['site_logo'];

if (!empty($icon_path)) {
    $checkPath = trim($icon_path, '/');
    if (file_exists(NV_ROOTDIR . '/' . $checkPath)) {
        $notification_icon = NV_BASE_SITEURL . $checkPath;
    }
}

?>
const CACHE_NAME = 'pwa-cache-v1';
const OFFLINE_URL = '<?php echo $offlineUrl; ?>';

self.addEventListener('install', (event) => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            // Cache critical assets
            const assets = [
                OFFLINE_URL,
                '<?php echo NV_BASE_SITEURL; ?>themes/default/css/bootstrap.min.css',
                '<?php echo NV_BASE_SITEURL; ?>themes/default/css/font-awesome.min.css'
            ];

            // Add Logo if exists in config, otherwise ignore to prevent cache failure
            <?php if (!empty($global_config['site_logo']) && file_exists(NV_ROOTDIR . '/' . $global_config['site_logo'])): ?>
            assets.push('<?php echo NV_BASE_SITEURL . $global_config['site_logo']; ?>');
            <?php endif; ?>

            return cache.addAll(assets).catch(err => {
                console.log('Failed to cache some assets', err);
            });
        })
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keyList) => {
            return Promise.all(keyList.map((key) => {
                if (key !== CACHE_NAME) {
                    return caches.delete(key);
                }
            }));
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .catch(() => {
                    return caches.open(CACHE_NAME)
                        .then((cache) => {
                            return cache.match(OFFLINE_URL);
                        });
                })
        );
    } else {
        event.respondWith(
            caches.match(event.request).then((response) => {
                return response || fetch(event.request);
            })
        );
    }
});

self.addEventListener('push', function(event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    // "Pull-on-Push" Strategy for simplified VAPID (no payload encryption)
    // If event.data is empty, we fetch the latest notification from server.
    const showNoti = (data) => {
        const title = data.title || '<?php echo $global_config['site_name']; ?>';
        const options = {
            body: data.body || '',
            icon: data.icon || '<?php echo $notification_icon; ?>',
            badge: data.badge || '<?php echo $notification_icon; ?>',
            data: {
                url: data.url || '<?php echo NV_BASE_SITEURL; ?>'
            }
        };
        return self.registration.showNotification(title, options);
    };

    if (event.data) {
        let data = {};
        try {
            data = event.data.json();
        } catch (e) {
            data = { title: 'Notification', body: event.data.text() };
        }
        event.waitUntil(showNoti(data));
    } else {
        // Fetch latest message
        event.waitUntil(
            fetch('<?php echo NV_BASE_SITEURL; ?>index.php?nv=<?php echo $module_name; ?>&op=latest_push')
            .then(response => {
                if (response.status !== 200) {
                     console.log('Problem with latest_push: ' + response.status);
                     throw new Error();
                }
                return response.json();
            })
            .then(data => {
                if (data && data.title) {
                    return showNoti(data);
                }
            })
            .catch(err => {
                console.log('Error fetching notification', err);
                // Fallback
                return showNoti({ title: 'New Notification', body: 'You have a new update.' });
            })
        );
    }
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then( windowClients => {
            // Check if there is already a window/tab open with the target URL
            for (var i = 0; i < windowClients.length; i++) {
                var client = windowClients[i];
                // If so, just focus it.
                if (client.url === event.notification.data.url && 'focus' in client) {
                    return client.focus();
                }
            }
            // If not, then open the target URL in a new window/tab.
            if (clients.openWindow) {
                return clients.openWindow(event.notification.data.url);
            }
        })
    );
});
