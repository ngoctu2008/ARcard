<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Tên Của Bạn <email@domain.com>
 * @Copyright (C) 2024. All rights reserved
 * @Createdate Sat, 25 May 2024 10:00:00 GMT
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    die('Stop!!!');
}

$page_title = $lang_module['template_manage'];

$sql = "SELECT catid, title FROM `" . NV_PREFIXLANG . "_" . $module_data . "_cat` ORDER BY weight ASC";
$result = $db->query($sql);
$array_cat = array();
while ($row = $result->fetch()) {
    $array_cat[$row['catid']] = $row;
}

if (empty($array_cat)) {
    Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=cat');
    die();
}

$id = $nv_Request->get_int('id', 'get,post', 0);
$error = '';

if ($nv_Request->isset_request('save', 'post')) {
    $row = array();
    $row['catid'] = $nv_Request->get_int('catid', 'post', 0);
    $row['title'] = $nv_Request->get_title('title', 'post', '');
    $row['alias'] = $nv_Request->get_title('alias', 'post', '');
    $row['description'] = $nv_Request->get_string('description', 'post', '');
    $row['prompt_body'] = $nv_Request->get_string('prompt_body', 'post', '');
    $row['status'] = $nv_Request->isset_request('status', 'post') ? 1 : 0;

    // Process Input Config
    $input_labels = $nv_Request->get_array('input_label', 'post', array());
    $input_keys = $nv_Request->get_array('input_key', 'post', array());
    $input_types = $nv_Request->get_array('input_type', 'post', array());
    $input_icons = $nv_Request->get_array('input_icon', 'post', array());
    $input_required = $nv_Request->get_array('input_required', 'post', array());
    $input_options = $nv_Request->get_array('input_options', 'post', array());

    $input_config = array();
    foreach ($input_labels as $key => $label) {
        // Allow empty keys for Sections (Visual separators)
        if (!empty($label) && (!empty($input_keys[$key]) || $input_types[$key] == 'section')) {
            $input_config[] = array(
                'label' => $label,
                'key' => $input_keys[$key],
                'type' => $input_types[$key],
                'icon' => isset($input_icons[$key]) ? $input_icons[$key] : '',
                'required' => isset($input_required[$key]) ? true : false,
                'options' => isset($input_options[$key]) ? explode("\n", trim($input_options[$key])) : array()
            );
        }
    }
    $row['input_config'] = json_encode($input_config, JSON_UNESCAPED_UNICODE);

    if (empty($row['alias'])) {
        $row['alias'] = change_alias($row['title']);
    }

    if (empty($row['title'])) {
        $error = $lang_module['error_title'];
    } elseif ($row['catid'] == 0) {
        $error = $lang_module['error_catid'];
    } else {
        // Check alias unique
        $sql = "SELECT id FROM `" . NV_PREFIXLANG . "_" . $module_data . "_templates` WHERE alias= :alias AND id != :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':alias', $row['alias'], PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->fetch()) {
             $error = $lang_module['error_alias'];
        } else {
            if ($id > 0) {
                $sql = "UPDATE `" . NV_PREFIXLANG . "_" . $module_data . "_templates` SET catid=:catid, title=:title, alias=:alias, description=:description, prompt_body=:prompt_body, input_config=:input_config, status=:status, edit_time=:edit_time WHERE id=:id";
                $data_insert = array(
                    ':catid' => $row['catid'],
                    ':title' => $row['title'],
                    ':alias' => $row['alias'],
                    ':description' => $row['description'],
                    ':prompt_body' => $row['prompt_body'],
                    ':input_config' => $row['input_config'],
                    ':status' => $row['status'],
                    ':edit_time' => NV_CURRENTTIME,
                    ':id' => $id
                );
                $db->prepare($sql)->execute($data_insert);
                Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=content');
                die();
            } else {
                $sql = "INSERT INTO `" . NV_PREFIXLANG . "_" . $module_data . "_templates` (catid, title, alias, description, prompt_body, input_config, status, add_time, edit_time) VALUES (:catid, :title, :alias, :description, :prompt_body, :input_config, :status, :add_time, :edit_time)";
                $data_insert = array(
                    ':catid' => $row['catid'],
                    ':title' => $row['title'],
                    ':alias' => $row['alias'],
                    ':description' => $row['description'],
                    ':prompt_body' => $row['prompt_body'],
                    ':input_config' => $row['input_config'],
                    ':status' => $row['status'],
                    ':add_time' => NV_CURRENTTIME,
                    ':edit_time' => NV_CURRENTTIME
                );

                 if ($db->prepare($sql)->execute($data_insert)) {
                    Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=content');
                    die();
                 }
            }
        }
    }
} elseif ($id > 0) {
    $sql = "SELECT * FROM `" . NV_PREFIXLANG . "_" . $module_data . "_templates` WHERE id=" . $id;
    $result = $db->query($sql);
    $row = $result->fetch();
} else {
    $row = array(
        'id' => 0,
        'catid' => 0,
        'title' => '',
        'alias' => '',
        'description' => '',
        'prompt_body' => '',
        'input_config' => '[]',
        'status' => 1
    );
}

// Delete
if ($nv_Request->isset_request('delete', 'post')) {
    $id_del = $nv_Request->get_int('delete', 'post', 0);
    if ($id_del > 0) {
        $db->query("DELETE FROM `" . NV_PREFIXLANG . "_" . $module_data . "_templates` WHERE id=" . $id_del);
        die('OK');
    }
    die('NO');
}

$xtpl = new XTemplate('content.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);
$xtpl->assign('ERROR', $error);

// Assign Cats
foreach ($array_cat as $cat) {
    $cat['selected'] = ($cat['catid'] == $row['catid']) ? 'selected="selected"' : '';
    $xtpl->assign('CAT', $cat);
    $xtpl->parse('main.cat');
}

// Process Input Config for View
$input_config = json_decode($row['input_config'], true);
if (empty($input_config)) {
    // Add default empty row if new
    $xtpl->parse('main.config_row');
} else {
    foreach ($input_config as $idx => $conf) {
        $conf['index'] = $idx;
        $conf['checked_required'] = ($conf['required']) ? 'checked="checked"' : '';
        $conf['options_text'] = implode("\n", $conf['options']);

        $conf['sel_text'] = ($conf['type'] == 'text') ? 'selected="selected"' : '';
        $conf['sel_textarea'] = ($conf['type'] == 'textarea') ? 'selected="selected"' : '';
        $conf['sel_select'] = ($conf['type'] == 'select') ? 'selected="selected"' : '';
        $conf['sel_number'] = ($conf['type'] == 'number') ? 'selected="selected"' : '';
        $conf['sel_checkbox'] = ($conf['type'] == 'checkbox') ? 'selected="selected"' : '';
        $conf['sel_radio'] = ($conf['type'] == 'radio') ? 'selected="selected"' : '';
        $conf['sel_section'] = ($conf['type'] == 'section') ? 'selected="selected"' : '';
        $conf['sel_group'] = ($conf['type'] == 'group') ? 'selected="selected"' : '';

        $xtpl->assign('CONF', $conf);
        $xtpl->parse('main.config_row');
    }
}


// List of existing templates
$sql = "SELECT t.*, c.title as cat_title FROM `" . NV_PREFIXLANG . "_" . $module_data . "_templates` t LEFT JOIN `" . NV_PREFIXLANG . "_" . $module_data . "_cat` c ON t.catid = c.catid ORDER BY t.id DESC";
$result = $db->query($sql);
while ($item = $result->fetch()) {
    $xtpl->assign('ITEM', $item);
    $xtpl->parse('main.list');
}

if (!empty($error)) {
    $xtpl->parse('main.error');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
