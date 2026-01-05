<?php

/**
 * @Project NUKEVIET 4.x
 * @Author Tên Của Bạn <email@domain.com>
 * @Copyright (C) 2024. All rights reserved
 * @Createdate Sat, 25 May 2024 10:00:00 GMT
 */

if (!defined('NV_MAINFILE')) {
    die('Stop!!!');
}

$sql_drop_module = array();
$sql_drop_module[] = "DROP TABLE IF EXISTS " . NV_PREFIXLANG . "_" . $module_data . "_cat";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . NV_PREFIXLANG . "_" . $module_data . "_templates";

$sql_create_module = $sql_drop_module;

// Table: Categories
$sql_create_module[] = "CREATE TABLE " . NV_PREFIXLANG . "_" . $module_data . "_cat (
  catid mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(250) NOT NULL,
  alias varchar(250) NOT NULL,
  description mediumtext NOT NULL,
  weight smallint(4) NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (catid),
  UNIQUE KEY alias (alias)
) ENGINE=" . $db_config['engine'] . " DEFAULT CHARSET=utf8mb4";

// Table: Templates
$sql_create_module[] = "CREATE TABLE " . NV_PREFIXLANG . "_" . $module_data . "_templates (
  id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  catid mediumint(8) unsigned NOT NULL DEFAULT '0',
  title varchar(250) NOT NULL,
  alias varchar(250) NOT NULL,
  description mediumtext NOT NULL,
  prompt_body text NOT NULL,
  input_config mediumtext NOT NULL,
  weight smallint(4) NOT NULL DEFAULT '0',
  status tinyint(1) NOT NULL DEFAULT '1',
  add_time int(11) unsigned NOT NULL DEFAULT '0',
  edit_time int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY catid (catid),
  UNIQUE KEY alias (alias)
) ENGINE=" . $db_config['engine'] . " DEFAULT CHARSET=utf8mb4";
