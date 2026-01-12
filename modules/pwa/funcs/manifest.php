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
$iconSrc = '';

// Helper to normalize path
$checkPath = isset($module_config['icon_path']) ? trim($module_config['icon_path'], '/') : '';

if (!empty($checkPath)) {
    // We assume the user-provided path is correct relative to the site root.
    // We skip strict file_exists checking to avoid issues with restrictive hosting environments (open_basedir)
    // or complex path structures where NV_ROOTDIR resolution might fail.
    $iconSrc = $checkPath;
} else {
    // Fallback to site logo
    $logo = isset($global_config['site_logo']) ? $global_config['site_logo'] : '';
    if (!empty($logo)) {
        $iconSrc = $logo;
    }
}

if (!empty($iconSrc)) {
    $iconUrl = NV_BASE_SITEURL . $iconSrc;
    $ext = strtolower(pathinfo($iconSrc, PATHINFO_EXTENSION));
    $mime = 'image/png';
    if ($ext == 'jpg' || $ext == 'jpeg') $mime = 'image/jpeg';
    if ($ext == 'webp') $mime = 'image/webp';
    if ($ext == 'gif') $mime = 'image/gif';
    if ($ext == 'svg') $mime = 'image/svg+xml';

    // PWA requires 192 and 512.
    // We use "purpose: any" as default because most user uploaded logos have transparency and are not "maskable".
    // "maskable" requires solid background.
    $icons[] = [
        'src' => $iconUrl,
        'sizes' => '192x192',
        'type' => $mime,
        'purpose' => 'any'
    ];
    $icons[] = [
        'src' => $iconUrl,
        'sizes' => '512x512',
        'type' => $mime,
        'purpose' => 'any'
    ];
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
