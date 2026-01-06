<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Tên Của Bạn <email@domain.com>
 * @Copyright (C) 2024. All rights reserved
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
    $row['icon'] = $nv_Request->get_string('icon', 'post', '');
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
                $sql = "UPDATE `" . NV_PREFIXLANG . "_" . $module_data . "_templates` SET catid=:catid, title=:title, alias=:alias, icon=:icon, description=:description, prompt_body=:prompt_body, input_config=:input_config, status=:status, edit_time=:edit_time WHERE id=:id";
                $data_insert = array(
                    ':catid' => $row['catid'],
                    ':title' => $row['title'],
                    ':alias' => $row['alias'],
                    ':icon' => $row['icon'],
                    ':description' => $row['description'],
                    ':prompt_body' => $row['prompt_body'],
                    ':input_config' => $row['input_config'],
                    ':status' => $row['status'],
                    ':edit_time' => NV_CURRENTTIME,
                    ':id' => $id
                );
                $db->prepare($sql)->execute($data_insert);
            } else {
                // Determine max weight
                $sql_weight = "SELECT max(weight) FROM `" . NV_PREFIXLANG . "_" . $module_data . "_templates`";
                $result_weight = $db->query($sql_weight);
                $weight = $result_weight->fetchColumn();
                $weight = intval($weight) + 1;

                $sql = "INSERT INTO `" . NV_PREFIXLANG . "_" . $module_data . "_templates` (catid, title, alias, icon, description, prompt_body, input_config, status, weight, add_time, edit_time) VALUES (:catid, :title, :alias, :icon, :description, :prompt_body, :input_config, :status, :weight, :add_time, :edit_time)";
                $data_insert = array(
                    ':catid' => $row['catid'],
                    ':title' => $row['title'],
                    ':alias' => $row['alias'],
                    ':icon' => $row['icon'],
                    ':description' => $row['description'],
                    ':prompt_body' => $row['prompt_body'],
                    ':input_config' => $row['input_config'],
                    ':status' => $row['status'],
                    ':weight' => $weight,
                    ':add_time' => NV_CURRENTTIME,
                    ':edit_time' => NV_CURRENTTIME
                );
                $db->prepare($sql)->execute($data_insert);
            }
            // Redirect to Main List after save
            Header('Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=main');
            die();
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
        'icon' => '',
        'description' => '',
        'prompt_body' => '',
        'input_config' => '[]',
        'status' => 1
    );
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
        $conf['checked_required'] = (isset($conf['required']) && $conf['required']) ? 'checked="checked"' : '';
        $conf['options_text'] = (isset($conf['options']) && is_array($conf['options'])) ? implode("\n", $conf['options']) : '';

        $conf['sel_text'] = ($conf['type'] == 'text') ? 'selected="selected"' : '';
        $conf['sel_textarea'] = ($conf['type'] == 'textarea') ? 'selected="selected"' : '';
        $conf['sel_number'] = ($conf['type'] == 'number') ? 'selected="selected"' : '';
        $conf['sel_select'] = ($conf['type'] == 'select') ? 'selected="selected"' : '';
        $conf['sel_checkbox'] = ($conf['type'] == 'checkbox') ? 'selected="selected"' : '';
        $conf['sel_radio'] = ($conf['type'] == 'radio') ? 'selected="selected"' : '';
        $conf['sel_section'] = ($conf['type'] == 'section') ? 'selected="selected"' : '';
        $conf['sel_group'] = ($conf['type'] == 'group') ? 'selected="selected"' : '';

        $xtpl->assign('CONF', $conf);
        $xtpl->parse('main.config_row');
    }
}

if (!empty($error)) {
    $xtpl->parse('main.error');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
