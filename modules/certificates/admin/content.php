<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Phạm Ngọc Tú <ngoctu.dnkd@gmail.com>
 * @Copyright (C) 2024 Phạm Ngọc Tú. All rights reserved
 * @License: Not free source, more information contact ngoctu.dnkd@gmail.com
 * @Createdate Thu, 01 Jan 2024 00:00:00 GMT
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) {
    die('Stop!!!');
}

$page_title = $lang_module['main_manage'];

$id = $nv_Request->get_int('id', 'get,post', 0);
$error = '';

if ($nv_Request->isset_request('save', 'post')) {
    $row = [];
    $row['catid'] = $nv_Request->get_int('catid', 'post', 0);
    $row['fullname'] = $nv_Request->get_title('fullname', 'post', '');
    $row['birthdate'] = $nv_Request->get_title('birthdate', 'post', '');
    $row['cert_number'] = $nv_Request->get_title('cert_number', 'post', '');
    $row['reg_number'] = $nv_Request->get_title('reg_number', 'post', '');
    $issue_date = $nv_Request->get_title('issue_date', 'post', '');
    $row['classification'] = $nv_Request->get_title('classification', 'post', '');
    $row['classification_en'] = $nv_Request->get_title('classification_en', 'post', '');
    $row['image'] = $nv_Request->get_string('image', 'post', ''); // Input from text field (browse) or handled upload below

    // Handle File Upload if provided
    if (isset($_FILES['image_file']) && is_uploaded_file($_FILES['image_file']['tmp_name'])) {
        $cat_alias = 'uncategorized';
        if ($row['catid'] > 0) {
            $cat_alias = $db->query("SELECT alias FROM " . NV_PREFIXLANG . "_" . $module_data . "_cat WHERE catid=" . $row['catid'])->fetchColumn();
        }

        $upload_dir = NV_UPLOADS_REAL_DIR . '/' . $module_upload . '/' . $cat_alias . '/' . date('Y_m');
        if (!is_dir($upload_dir)) {
            nv_mkdir($upload_dir, $cat_alias . '/' . date('Y_m'), true);
        }

        $filename = $_FILES['image_file']['name'];
        $filename = nv_string_to_filename(pathinfo($filename, PATHINFO_FILENAME)) . '.' . pathinfo($filename, PATHINFO_EXTENSION);
        $full_path = $upload_dir . '/' . $filename;

        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $full_path)) {
            $row['image'] = NV_BASE_SITEURL . NV_UPLOADS_DIR . '/' . $module_upload . '/' . $cat_alias . '/' . date('Y_m') . '/' . $filename;
        }
    }

    // Custom Fields processing
    $custom_fields = [];
    $fields_q = $db->query("SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_fields WHERE status=1 ORDER BY weight ASC");
    $custom_data = [];
    while($field = $fields_q->fetch()) {
        $val = $nv_Request->get_string($field['field'], 'post', '');
        $custom_data[$field['field']] = $val;
    }

    if (preg_match('/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{4})$/', $issue_date, $m)) {
        $row['issue_date'] = mktime(0, 0, 0, $m[2], $m[1], $m[3]);
    } else {
        $row['issue_date'] = 0;
    }

    if (empty($row['fullname']) || empty($row['cert_number'])) {
        $error = $lang_module['error_required'];
    } else {
        // Check duplicate cert_number
        $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE cert_number=:cert_number";
        if ($id > 0) {
            $sql .= " AND id!=" . $id;
        }
        $sth = $db->prepare($sql);
        $sth->bindParam(':cert_number', $row['cert_number'], PDO::PARAM_STR);
        $sth->execute();
        if ($sth->fetchColumn() > 0) {
             $error = "Error: Certificate Number exists!";
        } else {
            if ($id > 0) {
                $sql_extra = "";
                $params_extra = [];
                foreach ($custom_data as $fname => $fval) {
                    $sql_extra .= ", `" . $fname . "`=:" . $fname;
                    $params_extra[':'.$fname] = $fval;
                }

                $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_rows SET
                    catid=:catid, fullname=:fullname, birthdate=:birthdate, cert_number=:cert_number, reg_number=:reg_number, issue_date=:issue_date, classification=:classification, classification_en=:classification_en, image=:image" . $sql_extra . "
                    WHERE id=" . $id;

                $sth = $db->prepare($sql);
                $sth->bindValue(':catid', $row['catid']);
                $sth->bindValue(':fullname', $row['fullname']);
                $sth->bindValue(':birthdate', $row['birthdate']);
                $sth->bindValue(':cert_number', $row['cert_number']);
                $sth->bindValue(':reg_number', $row['reg_number']);
                $sth->bindValue(':issue_date', $row['issue_date']);
                $sth->bindValue(':classification', $row['classification']);
                $sth->bindValue(':classification_en', $row['classification_en']);
                $sth->bindValue(':image', $row['image']);

                foreach ($params_extra as $k => $v) {
                    $sth->bindValue($k, $v);
                }
                $sth->execute();
            } else {
                $cols_extra = "";
                $vals_extra = "";
                $params_extra = [];
                 foreach ($custom_data as $fname => $fval) {
                    $cols_extra .= ", `" . $fname . "`";
                    $vals_extra .= ", :" . $fname;
                    $params_extra[':'.$fname] = $fval;
                }

                $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_rows
                    (catid, fullname, birthdate, cert_number, reg_number, issue_date, classification, classification_en, image" . $cols_extra . ") VALUES
                    (:catid, :fullname, :birthdate, :cert_number, :reg_number, :issue_date, :classification, :classification_en, :image" . $vals_extra . ")";

                $sth = $db->prepare($sql);
                $sth->bindValue(':catid', $row['catid']);
                $sth->bindValue(':fullname', $row['fullname']);
                $sth->bindValue(':birthdate', $row['birthdate']);
                $sth->bindValue(':cert_number', $row['cert_number']);
                $sth->bindValue(':reg_number', $row['reg_number']);
                $sth->bindValue(':issue_date', $row['issue_date']);
                $sth->bindValue(':classification', $row['classification']);
                $sth->bindValue(':classification_en', $row['classification_en']);
                $sth->bindValue(':image', $row['image']);

                foreach ($params_extra as $k => $v) {
                    $sth->bindValue($k, $v);
                }
                $sth->execute();
            }
            $nv_Cache->delMod($module_name);
            Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
            die();
        }
    }
} else {
    if ($id > 0) {
        $row = $db->query("SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_rows WHERE id=" . $id)->fetch();
        if (empty($row)) {
             Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name);
             die();
        }
        $row['issue_date'] = ($row['issue_date'] > 0) ? date('d/m/Y', $row['issue_date']) : '';
    } else {
        $row = [
            'catid' => 0,
            'fullname' => '',
            'birthdate' => '',
            'cert_number' => '',
            'reg_number' => '',
            'issue_date' => date('d/m/Y'),
            'classification' => '',
            'classification_en' => '',
            'image' => ''
        ];
    }
}

$xtpl = new XTemplate('content.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('UPLOADS_DIR_USER', NV_UPLOADS_DIR . '/' . $module_upload);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', 'content');
$xtpl->assign('ROW', $row);

// Render Custom Fields
$fields_q = $db->query("SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_fields WHERE status=1 ORDER BY weight ASC");
while($field = $fields_q->fetch()) {
    $field['value'] = isset($row[$field['field']]) ? $row[$field['field']] : $field['default_value'];

    // Add catids attribute to field row for JS filtering
    $field['data_catids'] = $field['catids'];

    if ($field['field_type'] == 'select') {
        $choices = explode("\n", $field['field_choices']);
        foreach ($choices as $choice) {
            $parts = explode('|', trim($choice));
            $key = $parts[0];
            $label = isset($parts[1]) ? $parts[1] : $key;
            $xtpl->assign('OPTION', [
                'key' => $key,
                'title' => $label,
                'selected' => ($key == $field['value']) ? 'selected' : ''
            ]);
            $xtpl->parse('main.field.select.option');
        }
        $xtpl->parse('main.field.select');
    } else {
         $xtpl->parse('main.field.textbox');
    }

    $xtpl->assign('FIELD', $field);
    $xtpl->parse('main.field');
}

if (!empty($error)) {
    $xtpl->assign('ERROR', $error);
    $xtpl->parse('main.error');
}

// Categories select
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_cat ORDER BY weight ASC";
$result = $db->query($sql);
while ($cat = $result->fetch()) {
    $cat['selected'] = ($cat['catid'] == $row['catid']) ? 'selected="selected"' : '';
    $xtpl->assign('CAT', $cat);
    $xtpl->parse('main.cat');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
