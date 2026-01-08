<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Phạm Ngọc Tú <ngoctu.dnkd@gmail.com>
 * @Copyright (C) 2024 Phạm Ngọc Tú. All rights reserved
 * @License: Not free source, more information contact ngoctu.dnkd@gmail.com
 * @Createdate Thu, 01 Jan 2024 00:00:00 GMT
 */

if (!defined('NV_MAINFILE')) {
    die('Stop!!!');
}

// Load Local Config
$sql = "SELECT config_name, config_value FROM " . NV_PREFIXLANG . "_" . $module_data . "_config";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    $module_config[$module_name][$row['config_name']] = $row['config_value'];
}

// Check permission
$who_view = isset($module_config[$module_name]['who_view']) ? explode(',', $module_config[$module_name]['who_view']) : ['0'];
$allowed = false;
if (in_array(0, $who_view)) { // All visitors
    $allowed = true;
} elseif (defined('NV_IS_USER')) {
    // Check user groups
    if (array_intersect($user_info['in_groups'], $who_view)) {
        $allowed = true;
    }
}

if (!$allowed) {
    $contents = '<div class="alert alert-danger text-center">Bạn không có quyền truy cập chức năng này! Vui lòng đăng nhập hoặc liên hệ BQT.</div>';
    include NV_ROOTDIR . '/includes/header.php';
    echo nv_site_theme($contents);
    include NV_ROOTDIR . '/includes/footer.php';
    exit();
}

$page_title = $lang_module['main'];
$error = '';
$result_data = [];

// Check captcha config
$active_captcha = isset($module_config[$module_name]['active_captcha']) ? $module_config[$module_name]['active_captcha'] : 1;

// Handle Search
if ($nv_Request->isset_request('search', 'post')) {
    $cert_number = $nv_Request->get_title('cert_number', 'post', '');
    $second_factor = $nv_Request->get_title('second_factor', 'post', '');
    $captcha = $nv_Request->get_title('captcha', 'post', '');

    if ($active_captcha == 1 && !nv_capcha_txt($captcha)) {
        $error = $lang_module['captcha_error'];
    } elseif (empty($cert_number) || empty($second_factor)) {
        $error = $lang_module['input_required'];
    } else {
        // Logic: Cert Number AND (Fullname OR Birthdate)
        // Note: Fullname might be fuzzy or exact. User asked for exact match for storage, but search is usually strict for cert apps.
        // However, "Second factor" logic usually implies exact match or Contains.
        // Given the requirement "Exact Match" in DB design section, I should probably stick to exact match or 'LIKE' with high precision.
        // For birthdate (string), exact match is best.
        // For name, maybe Case Insensitive.

        $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows
                WHERE cert_number = :cert_number
                AND (LOWER(fullname) = LOWER(:name) OR birthdate = :birthdate)
                AND status=1";

        $sth = $db->prepare($sql);
        $sth->bindValue(':cert_number', $cert_number);
        $sth->bindValue(':name', $second_factor);
        $sth->bindValue(':birthdate', $second_factor);
        $sth->execute();

        $result_data = $sth->fetchAll();

        if (empty($result_data)) {
            $error = $lang_module['no_result'];
        }
    }
}

$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('TEMPLATE', $module_info['template']);
$xtpl->assign('NV_BASE_SITEURL', NV_BASE_SITEURL);

if (!empty($error)) {
    $xtpl->assign('ERROR', $error);
    $xtpl->parse('main.error');
}

// Display Form
$xtpl->assign('ACTION', NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
if ($active_captcha == 1) {
    $xtpl->assign('CAPTCHA_URL', NV_BASE_SITEURL . 'index.php?scaptcha=captcha&t=' . NV_CURRENTTIME);
    $xtpl->assign('GFX_NUM', NV_GFX_NUM);
    $xtpl->parse('main.form.captcha');
}

if (!empty($result_data)) {
    // Get Categories
    $sql = "SELECT catid, title, image FROM " . NV_PREFIXLANG . "_" . $module_data . "_cat";
    $result = $db->query($sql);
    $cats = [];
    $cat_images = [];
    while ($row = $result->fetch()) {
        $cats[$row['catid']] = $row['title'];
        $cat_images[$row['catid']] = $row['image'];
    }

    // Get Custom Fields
    $fields_q = $db->query("SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_fields WHERE status=1 ORDER BY weight ASC");
    $custom_fields_def = [];
    while($field = $fields_q->fetch()) {
         $custom_fields_def[] = $field;
    }

    foreach ($result_data as $row) {
        $row['cat_title'] = isset($cats[$row['catid']]) ? $cats[$row['catid']] : '';
        $row['issue_date_str'] = ($row['issue_date'] > 0) ? date('d/m/Y', $row['issue_date']) : '';

        // Image from category
        $cat_image = isset($cat_images[$row['catid']]) ? $cat_images[$row['catid']] : '';
        if (!empty($cat_image) && file_exists(NV_ROOTDIR . '/' . $cat_image)) {
            $row['image'] = NV_BASE_SITEURL . $cat_image;
            $xtpl->parse('main.result_box.loop.image');
        }

        if (!empty($row['classification_en'])) {
            $xtpl->parse('main.result_box.loop.class_en');
        }

        // Render custom fields for this row
        foreach ($custom_fields_def as $field) {
            $val = isset($row[$field['field']]) ? $row[$field['field']] : '';
            if ($val != '') {
                // If select, get label
                 if ($field['field_type'] == 'select') {
                     $choices = explode("\n", $field['field_choices']);
                     foreach ($choices as $choice) {
                         $parts = explode('|', trim($choice));
                         if ($parts[0] == $val) {
                             $val = isset($parts[1]) ? $parts[1] : $val;
                             break;
                         }
                     }
                 }

                $xtpl->assign('FIELD', [
                    'title' => $field['title'],
                    'value' => $val
                ]);
                $xtpl->parse('main.result_box.loop.custom_field');
            }
        }

        $xtpl->assign('RESULT', $row);
        $xtpl->parse('main.result_box.loop');
    }
    $xtpl->parse('main.result_box');
} else {
    $xtpl->parse('main.form');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
