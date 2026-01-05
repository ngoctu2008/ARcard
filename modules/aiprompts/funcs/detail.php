<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Tên Của Bạn <email@domain.com>
 * @Copyright (C) 2024. All rights reserved
 * @Createdate Sat, 25 May 2024 10:00:00 GMT
 */

if (!defined('NV_IS_MOD_AIPROMPTS')) {
    die('Stop!!!');
}

// URL format: detail/alias-id
$alias = isset($array_op[1]) ? $array_op[1] : '';
$array_page = explode('-', $alias);
$id = intval(end($array_page));

if ($id > 0) {
    $sql = "SELECT * FROM " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_templates WHERE id=" . $id . " AND status=1";
    $result = $db->query($sql);
    $row = $result->fetch();
}

if (empty($row)) {
    Header('Location: ' . NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . $lang . '&' . NV_NAME_VARIABLE . '=' . $module_name);
    die();
}

$page_title = $row['title'];
$key_words = $row['title'];

$xtpl = new XTemplate('detail.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('ROW', $row);

// Fetch Sibling Templates for Tabs
$sql_siblings = "SELECT id, title, alias FROM " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_templates WHERE catid=" . $row['catid'] . " AND status=1 ORDER BY weight ASC";
$result_siblings = $db->query($sql_siblings);
while ($sib = $result_siblings->fetch()) {
    $sib['link'] = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . $lang . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=detail/' . $sib['alias'] . '-' . $sib['id'];
    $sib['active'] = ($sib['id'] == $row['id']) ? 'active' : '';
    $xtpl->assign('TAB', $sib);
    $xtpl->parse('main.tab');
}

// Parse JSON config and restructure for View
$input_config = json_decode($row['input_config'], true);
$sections = array();

// Default Section/Group
$default_section = array('label' => '', 'groups' => array());
$default_group = array('label' => '', 'icon' => '', 'inputs' => array());

$current_section = &$default_section;
$current_group = &$default_group;

// Flag to check if we have any explicit sections
$has_sections = false;

if (!empty($input_config)) {
    foreach ($input_config as $conf) {
        $conf['required_star'] = (isset($conf['required']) && $conf['required']) ? ' <span class="text-danger">(*)</span>' : '';
        $conf['required_attr'] = (isset($conf['required']) && $conf['required']) ? 'required="required"' : '';

        if ($conf['type'] == 'section') {
            // If we were working on default section and it has content, save it?
            // Actually, just start a new section
            $has_sections = true;
            // Save previous group to previous section
            if (!empty($current_group['inputs']) || !empty($current_group['label'])) {
                $current_section['groups'][] = $current_group;
            }
            // Save previous section to list
            if ($current_section !== $default_section || !empty($current_section['groups'])) {
                 $sections[] = $current_section;
            }

            // New Section
            $current_section = array('label' => $conf['label'], 'groups' => array());
            // Reset group
            $current_group = array('label' => '', 'icon' => '', 'inputs' => array());

        } elseif ($conf['type'] == 'group') {
            // Save previous group
            if (!empty($current_group['inputs']) || !empty($current_group['label'])) {
                $current_section['groups'][] = $current_group;
            }
            // New Group
            $current_group = array('label' => $conf['label'], 'icon' => isset($conf['icon']) ? $conf['icon'] : '', 'inputs' => array());

        } else {
            // Regular input, add to current group
            // Process Options
            $conf['options_list'] = array();
            if (!empty($conf['options'])) {
                foreach ($conf['options'] as $opt) {
                    $conf['options_list'][] = array('value' => trim($opt), 'title' => trim($opt));
                }
            }
            $current_group['inputs'][] = $conf;
        }
    }
}

// Finish up: Save last group and last section
if (!empty($current_group['inputs']) || !empty($current_group['label'])) {
    $current_section['groups'][] = $current_group;
}
if (!empty($current_section['groups']) || !empty($current_section['label'])) {
    $sections[] = $current_section;
}

// Pass to View
foreach ($sections as $sec) {
    $xtpl->assign('SEC', $sec);

    foreach ($sec['groups'] as $grp) {
        $grp['has_icon'] = !empty($grp['icon']) ? '' : 'display:none';
        $xtpl->assign('GRP', $grp);

        foreach ($grp['inputs'] as $inp) {
            $xtpl->assign('INP', $inp);

            if ($inp['type'] == 'text') {
                $xtpl->parse('main.section.group.input.text');
            } elseif ($inp['type'] == 'number') {
                $xtpl->parse('main.section.group.input.number');
            } elseif ($inp['type'] == 'textarea') {
                $xtpl->parse('main.section.group.input.textarea');
            } elseif ($inp['type'] == 'select') {
                foreach ($inp['options_list'] as $opt) {
                    $xtpl->assign('OPT', $opt);
                    $xtpl->parse('main.section.group.input.select.option');
                }
                $xtpl->parse('main.section.group.input.select');
            } elseif ($inp['type'] == 'checkbox') {
                foreach ($inp['options_list'] as $opt) {
                    $xtpl->assign('OPT', $opt);
                    $xtpl->parse('main.section.group.input.checkbox.option');
                }
                $xtpl->parse('main.section.group.input.checkbox');
            } elseif ($inp['type'] == 'radio') {
                foreach ($inp['options_list'] as $opt) {
                    $xtpl->assign('OPT', $opt);
                    $xtpl->parse('main.section.group.input.radio.option');
                }
                $xtpl->parse('main.section.group.input.radio');
            }

            $xtpl->parse('main.section.group.input');
        }
        $xtpl->parse('main.section.group');
    }

    if (!empty($sec['label'])) {
        $xtpl->parse('main.section.has_label');
    }
    $xtpl->parse('main.section');
}


$xtpl->assign('PROMPT_BODY', htmlspecialchars($row['prompt_body']));

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
