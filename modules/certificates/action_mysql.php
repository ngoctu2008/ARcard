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

$sql_drop_module = [];
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_rows";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_cat";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_fields";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config";

$sql_create_module = $sql_drop_module;

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_cat (
  catid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(250) NOT NULL,
  alias varchar(250) NOT NULL,
  description varchar(250) DEFAULT '',
  image varchar(255) DEFAULT '',
  status tinyint(1) unsigned NOT NULL DEFAULT '1',
  weight mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (catid),
  UNIQUE KEY alias (alias)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_rows (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  catid mediumint(8) unsigned NOT NULL,
  fullname varchar(250) NOT NULL,
  birthdate varchar(20) NOT NULL DEFAULT '',
  cert_number varchar(50) NOT NULL,
  reg_number varchar(50) NOT NULL,
  issue_date int(11) unsigned NOT NULL DEFAULT '0',
  classification varchar(50) DEFAULT '',
  classification_en varchar(50) DEFAULT '',
  status tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (id),
  UNIQUE KEY cert_number (cert_number),
  KEY catid (catid),
  KEY fullname (fullname)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_fields (
  fid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  field varchar(50) NOT NULL,
  title varchar(250) NOT NULL,
  description varchar(250) DEFAULT '',
  required tinyint(1) unsigned NOT NULL DEFAULT '0',
  weight mediumint(8) unsigned NOT NULL DEFAULT '0',
  field_type varchar(50) NOT NULL DEFAULT 'textbox',
  field_choices text,
  sql_choices text,
  match_type varchar(20) DEFAULT 'none',
  match_regex varchar(250) DEFAULT '',
  func_callback varchar(75) DEFAULT '',
  class varchar(75) DEFAULT '',
  default_value varchar(250) DEFAULT '',
  catids varchar(255) DEFAULT '0',
  status tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (fid),
  UNIQUE KEY field (field)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config (
  config_name varchar(30) NOT NULL,
  config_value varchar(255) NOT NULL,
  UNIQUE KEY config_name (config_name)
) ENGINE=MyISAM";

$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config VALUES
('workgroup', 1),
('groupmanager', 1),
('name_center', 'Trung tâm Giáo dục nghề nghiệp - Giáo dục thường xuyên huyện Đăk Tô'),
('name_center_en', 'Dak To district vocational and continuing education center'),
('location_districts', 'Đăk Tô'),
('location_provincial', 'Kon Tum')";

$sql_create_module[] = "INSERT INTO " . NV_CONFIG_GLOBALTABLE . " (lang, module, config_name, config_value) VALUES ('" . $lang . "', '" . $module_name . "', 'captcha_type', 'captcha')";
