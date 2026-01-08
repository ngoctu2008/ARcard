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

$sql_create_module = $sql_drop_module;

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_cat (
  catid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(250) NOT NULL,
  alias varchar(250) NOT NULL,
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
  status tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (id),
  UNIQUE KEY cert_number (cert_number),
  KEY catid (catid),
  KEY fullname (fullname)
) ENGINE=MyISAM";

$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_cat (catid, title, alias, status) VALUES
(1, 'Bằng Tốt nghiệp THPT', 'bang-tot-nghiep-thpt', 1),
(2, 'Chứng chỉ Tin học', 'chung-chi-tin-hoc', 1),
(3, 'Chứng chỉ Ngoại ngữ', 'chung-chi-ngoai-ngu', 1)";
