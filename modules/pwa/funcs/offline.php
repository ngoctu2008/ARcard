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

$contents = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>' . $lang_module['offline_title'] . '</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; background-color: #f4f4f4; color: #333; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { font-size: 24px; margin-bottom: 20px; color: #d9534f; }
        p { color: #666; font-size: 16px; line-height: 1.5; }
        .icon { font-size: 64px; color: #ccc; margin-bottom: 20px; display: block; }
    </style>
</head>
<body>
    <div class="container">
        <span class="icon">&#9888;</span>
        <h1>' . $lang_module['offline_title'] . '</h1>
        <p>' . $lang_module['offline_message'] . '</p>
    </div>
</body>
</html>';

echo $contents;
die();
