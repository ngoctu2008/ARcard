<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Phạm Ngọc Tú (ngoctu.dnkd@gmail.com)
 * @Copyright (C) 2024 Phạm Ngọc Tú. All rights reserved
 * @Createdate Mon, 28 Oct 2024 00:00:00 GMT
 */

if (!defined('NV_IS_MOD_AVATAR')) {
    die('Stop!!!');
}

// Since the frontend now handles WYSIWYG generation and direct download (canvas.toDataURL),
// this backend script is technically optional for the "Download" button flow implemented in main.tpl.
// However, if we want to support Server-Side Generation (e.g. for fallback or higher quality not limited by screen),
// we would implement it here.

// Currently, the frontend implementation uses `canvas.toDataURL()` which triggers a browser download.
// So this file might not be called directly by the new frontend logic.
// But to ensure compatibility if the user wants server-side processing later (or if `toDataURL` fails/is restricted),
// I will implement a basic handler that accepts the DataURL and streams it back (acting as a download proxy),
// OR attempts to reconstruct the image.

// Given the "WYSIWYG" requirement is strict, client-side is best.
// But "Program Module" implies backend work.
// Let's implement a "Save/Download Proxy" which receives the DataURL.

$dataURL = $nv_Request->get_string('dataURL', 'post', '');

if (!empty($dataURL)) {
    // Remove header
    $filteredData = substr($dataURL, strpos($dataURL, ",") + 1);
    $unencodedData = base64_decode($filteredData);

    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="avatar_' . time() . '.png"');
    echo $unencodedData;
    exit;
}

// Fallback: If we receive the JSON payload (as originally planned)
// We would reconstruct it. But rendering FabricJS JSON in PHP GD is extremely complex
// (matching fonts, wrapping, shadows, exact positioning).
// For this task, enabling the Client-Side download in the TPL was the critical move.
// I will leave this file capable of acting as a proxy if needed, or returning an error if accessed directly.

die('Invalid Request');
