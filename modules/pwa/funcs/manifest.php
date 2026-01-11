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

// Set Content-Type
header('Content-Type: application/manifest+json; charset=utf-8');

// Load module config
$sql = "SELECT config_name, config_value FROM " . NV_CONFIG_GLOBALTABLE . " WHERE lang='" . $lang . "' AND module='" . $module_name . "'";
$result = $db->query($sql);
$module_config = [];
while ($row = $result->fetch()) {
    $module_config[$row['config_name']] = $row['config_value'];
}

// Defaults
$name = !empty($module_config['manifest_name']) ? $module_config['manifest_name'] : $global_config['site_name'];
$short_name = !empty($module_config['manifest_short_name']) ? $module_config['manifest_short_name'] : $global_config['site_name'];
$theme_color = !empty($module_config['theme_color']) ? $module_config['theme_color'] : '#000000';
$background_color = !empty($module_config['background_color']) ? $module_config['background_color'] : '#ffffff';

// Icon handling
$icons = [];
if (!empty($module_config['icon_path']) && file_exists(NV_ROOTDIR . '/' . $module_config['icon_path'])) {
    $iconUrl = NV_BASE_SITEURL . $module_config['icon_path'];
    // We assume the user uploads a high res icon. PWA usually wants 192 and 512.
    // Since we don't have dynamic resizing on the fly easily without creating cache files,
    // we will just serve the same image for both sizes or check if resizing is possible.
    // For simplicity/reliability in "One Click", we point both to the uploaded image.
    // Ideally, we would have resized it in Admin Config.
    $icons[] = [
        'src' => $iconUrl,
        'sizes' => '192x192',
        'type' => 'image/png' // Assuming PNG, or we detect mime
    ];
    $icons[] = [
        'src' => $iconUrl,
        'sizes' => '512x512',
        'type' => 'image/png'
    ];
} else {
    // Fallback to site logo if possible, or a default placeholder
    // NukeViet stores site logo in $global_config['site_logo'] usually
    $logo = isset($global_config['site_logo']) ? $global_config['site_logo'] : '';
    if ($logo && file_exists(NV_ROOTDIR . '/' . $logo)) {
         $iconUrl = NV_BASE_SITEURL . $logo;
         $icons[] = [
            'src' => $iconUrl,
            'sizes' => '192x192',
            'type' => 'image/png'
        ];
        $icons[] = [
            'src' => $iconUrl,
            'sizes' => '512x512',
            'type' => 'image/png'
        ];
    } else {
        // Absolute fallback (1x1 pixel or similar, to avoid 404 in console)
        // We will create a default asset in assets/ folder later if needed
    }
}

$manifest = [
    'name' => $name,
    'short_name' => $short_name,
    'theme_color' => $theme_color,
    'background_color' => $background_color,
    'display' => 'standalone',
    'start_url' => NV_BASE_SITEURL,
    'scope' => NV_BASE_SITEURL,
    'icons' => $icons
];

// If VAPID public key exists, some implementations might look for it here (gcm_sender_id for legacy, but standards use separate mechanism)
// We stick to standard web manifest.

echo json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
die();
