--
-- i-MSCP a internet Multi Server Control Panel
--
-- Copyright (C) 2006-2010 by isp Control Panel - http://ispcp.net
-- Copyright (C) 2010-2012 by internet Multi Server Control Panel - http://i-mscp.net
--
-- Version: $Id$
--
-- The contents of this file are subject to the Mozilla Public License
-- Version 1.1 (the "License"); you may not use this file except in
-- compliance with the License. You may obtain a copy of the License at
-- http://www.mozilla.org/MPL/
--
-- Software distributed under the License is distributed on an "AS IS"
-- basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
-- License for the specific language governing rights and limitations
-- under the License.
--
-- The Original Code is "ispCP ω (OMEGA) a Virtual Hosting Control Panel".
--
-- The Initial Developer of the Original Code is ispCP Team.
-- Portions created by Initial Developer are Copyright (C) 2006-2010 by
-- isp Control Panel. All Rights Reserved.
--
-- Portions created by the i-MSCP Team are Copyright (C) 2010-2012 by
-- internet Multi Server Control Panel. All Rights Reserved.
--
-- The i-MSCP Home Page is:
--
--    http://i-mscp.net
--
-- --------------------------------------------------------

--
-- Structure for table `access`
--

CREATE TABLE IF NOT EXISTS `access` (
  `login` varchar(100) NOT NULL,
  `idsite` int(10) unsigned NOT NULL,
  `access` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`login`,`idsite`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `goal`
--

CREATE TABLE IF NOT EXISTS `goal` (
  `idsite` int(11) NOT NULL,
  `idgoal` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `match_attribute` varchar(20) NOT NULL,
  `pattern` varchar(255) NOT NULL,
  `pattern_type` varchar(10) NOT NULL,
  `case_sensitive` tinyint(4) NOT NULL,
  `allow_multiple` tinyint(4) NOT NULL,
  `revenue` float NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idsite`,`idgoal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `logger_api_call`
--

CREATE TABLE IF NOT EXISTS `logger_api_call` (
  `idlogger_api_call` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class_name` varchar(255) DEFAULT NULL,
  `method_name` varchar(255) DEFAULT NULL,
  `parameter_names_default_values` text,
  `parameter_values` text,
  `execution_time` float DEFAULT NULL,
  `caller_ip` varbinary(16) NOT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `returned_value` text,
  PRIMARY KEY (`idlogger_api_call`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `logger_error`
--

CREATE TABLE IF NOT EXISTS `logger_error` (
  `idlogger_error` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NULL DEFAULT NULL,
  `message` text,
  `errno` int(10) unsigned DEFAULT NULL,
  `errline` int(10) unsigned DEFAULT NULL,
  `errfile` varchar(255) DEFAULT NULL,
  `backtrace` text,
  PRIMARY KEY (`idlogger_error`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `logger_exception`
--

CREATE TABLE IF NOT EXISTS `logger_exception` (
  `idlogger_exception` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NULL DEFAULT NULL,
  `message` text,
  `errno` int(10) unsigned DEFAULT NULL,
  `errline` int(10) unsigned DEFAULT NULL,
  `errfile` varchar(255) DEFAULT NULL,
  `backtrace` text,
  PRIMARY KEY (`idlogger_exception`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `logger_message`
--

CREATE TABLE IF NOT EXISTS `logger_message` (
  `idlogger_message` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NULL DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`idlogger_message`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `log_action`
--

CREATE TABLE IF NOT EXISTS `log_action` (
  `idaction` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` text,
  `hash` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`idaction`),
  KEY `index_type_hash` (`type`,`hash`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `log_conversion`
--

CREATE TABLE IF NOT EXISTS `log_conversion` (
  `idvisit` int(10) unsigned NOT NULL,
  `idsite` int(10) unsigned NOT NULL,
  `idvisitor` binary(8) NOT NULL,
  `server_time` datetime NOT NULL,
  `idaction_url` int(11) DEFAULT NULL,
  `idlink_va` int(11) DEFAULT NULL,
  `referer_visit_server_date` date DEFAULT NULL,
  `referer_type` int(10) unsigned DEFAULT NULL,
  `referer_name` varchar(70) DEFAULT NULL,
  `referer_keyword` varchar(255) DEFAULT NULL,
  `visitor_returning` tinyint(1) NOT NULL,
  `visitor_count_visits` smallint(5) unsigned NOT NULL,
  `visitor_days_since_first` smallint(5) unsigned NOT NULL,
  `visitor_days_since_order` smallint(5) unsigned NOT NULL,
  `location_country` char(3) NOT NULL,
  `location_continent` char(3) NOT NULL,
  `url` text NOT NULL,
  `idgoal` int(10) NOT NULL,
  `buster` int(10) unsigned NOT NULL,
  `idorder` varchar(100) DEFAULT NULL,
  `items` smallint(5) unsigned DEFAULT NULL,
  `revenue` float DEFAULT NULL,
  `revenue_subtotal` float DEFAULT NULL,
  `revenue_tax` float DEFAULT NULL,
  `revenue_shipping` float DEFAULT NULL,
  `revenue_discount` float DEFAULT NULL,
  `custom_var_k1` varchar(200) DEFAULT NULL,
  `custom_var_v1` varchar(200) DEFAULT NULL,
  `custom_var_k2` varchar(200) DEFAULT NULL,
  `custom_var_v2` varchar(200) DEFAULT NULL,
  `custom_var_k3` varchar(200) DEFAULT NULL,
  `custom_var_v3` varchar(200) DEFAULT NULL,
  `custom_var_k4` varchar(200) DEFAULT NULL,
  `custom_var_v4` varchar(200) DEFAULT NULL,
  `custom_var_k5` varchar(200) DEFAULT NULL,
  `custom_var_v5` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`idvisit`,`idgoal`,`buster`),
  UNIQUE KEY `unique_idsite_idorder` (`idsite`,`idorder`),
  KEY `index_idsite_datetime` (`idsite`,`server_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `log_conversion_item`
--

CREATE TABLE IF NOT EXISTS `log_conversion_item` (
  `idsite` int(10) unsigned NOT NULL,
  `idvisitor` binary(8) NOT NULL,
  `server_time` datetime NOT NULL,
  `idvisit` int(10) unsigned NOT NULL,
  `idorder` varchar(100) NOT NULL,
  `idaction_sku` int(10) unsigned NOT NULL,
  `idaction_name` int(10) unsigned NOT NULL,
  `idaction_category` int(10) unsigned NOT NULL,
  `idaction_category2` int(10) unsigned NOT NULL,
  `idaction_category3` int(10) unsigned NOT NULL,
  `idaction_category4` int(10) unsigned NOT NULL,
  `idaction_category5` int(10) unsigned NOT NULL,
  `price` float NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`idvisit`,`idorder`,`idaction_sku`),
  KEY `index_idsite_servertime` (`idsite`,`server_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `log_link_visit_action`
--

CREATE TABLE IF NOT EXISTS `log_link_visit_action` (
  `idlink_va` int(11) NOT NULL AUTO_INCREMENT,
  `idsite` int(10) unsigned NOT NULL,
  `idvisitor` binary(8) NOT NULL,
  `server_time` datetime NOT NULL,
  `idvisit` int(10) unsigned NOT NULL,
  `idaction_url` int(10) unsigned NOT NULL,
  `idaction_url_ref` int(10) unsigned NOT NULL,
  `idaction_name` int(10) unsigned DEFAULT NULL,
  `idaction_name_ref` int(10) unsigned NOT NULL,
  `time_spent_ref_action` int(10) unsigned NOT NULL,
  `custom_var_k1` varchar(200) DEFAULT NULL,
  `custom_var_v1` varchar(200) DEFAULT NULL,
  `custom_var_k2` varchar(200) DEFAULT NULL,
  `custom_var_v2` varchar(200) DEFAULT NULL,
  `custom_var_k3` varchar(200) DEFAULT NULL,
  `custom_var_v3` varchar(200) DEFAULT NULL,
  `custom_var_k4` varchar(200) DEFAULT NULL,
  `custom_var_v4` varchar(200) DEFAULT NULL,
  `custom_var_k5` varchar(200) DEFAULT NULL,
  `custom_var_v5` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`idlink_va`),
  KEY `index_idvisit` (`idvisit`),
  KEY `index_idsite_servertime` (`idsite`,`server_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `log_profiling`
--

CREATE TABLE IF NOT EXISTS `log_profiling` (
  `query` text NOT NULL,
  `count` int(10) unsigned DEFAULT NULL,
  `sum_time_ms` float DEFAULT NULL,
  UNIQUE KEY `query` (`query`(100))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `log_visit`
--

CREATE TABLE IF NOT EXISTS `log_visit` (
  `idvisit` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idsite` int(10) unsigned NOT NULL,
  `idvisitor` binary(8) NOT NULL,
  `visitor_localtime` time NOT NULL,
  `visitor_returning` tinyint(1) NOT NULL,
  `visitor_count_visits` smallint(5) unsigned NOT NULL,
  `visitor_days_since_last` smallint(5) unsigned NOT NULL,
  `visitor_days_since_order` smallint(5) unsigned NOT NULL,
  `visitor_days_since_first` smallint(5) unsigned NOT NULL,
  `visit_first_action_time` datetime NOT NULL,
  `visit_last_action_time` datetime NOT NULL,
  `visit_exit_idaction_url` int(11) unsigned NOT NULL,
  `visit_exit_idaction_name` int(11) unsigned NOT NULL,
  `visit_entry_idaction_url` int(11) unsigned NOT NULL,
  `visit_entry_idaction_name` int(11) unsigned NOT NULL,
  `visit_total_actions` smallint(5) unsigned NOT NULL,
  `visit_total_time` smallint(5) unsigned NOT NULL,
  `visit_goal_converted` tinyint(1) NOT NULL,
  `visit_goal_buyer` tinyint(1) NOT NULL,
  `referer_type` tinyint(1) unsigned DEFAULT NULL,
  `referer_name` varchar(70) DEFAULT NULL,
  `referer_url` text NOT NULL,
  `referer_keyword` varchar(255) DEFAULT NULL,
  `config_id` binary(8) NOT NULL,
  `config_os` char(3) NOT NULL,
  `config_browser_name` varchar(10) NOT NULL,
  `config_browser_version` varchar(20) NOT NULL,
  `config_resolution` varchar(9) NOT NULL,
  `config_pdf` tinyint(1) NOT NULL,
  `config_flash` tinyint(1) NOT NULL,
  `config_java` tinyint(1) NOT NULL,
  `config_director` tinyint(1) NOT NULL,
  `config_quicktime` tinyint(1) NOT NULL,
  `config_realplayer` tinyint(1) NOT NULL,
  `config_windowsmedia` tinyint(1) NOT NULL,
  `config_gears` tinyint(1) NOT NULL,
  `config_silverlight` tinyint(1) NOT NULL,
  `config_cookie` tinyint(1) NOT NULL,
  `location_ip` varbinary(16) NOT NULL,
  `location_browser_lang` varchar(20) NOT NULL,
  `location_country` char(3) NOT NULL,
  `location_continent` char(3) NOT NULL,
  `custom_var_k1` varchar(200) DEFAULT NULL,
  `custom_var_v1` varchar(200) DEFAULT NULL,
  `custom_var_k2` varchar(200) DEFAULT NULL,
  `custom_var_v2` varchar(200) DEFAULT NULL,
  `custom_var_k3` varchar(200) DEFAULT NULL,
  `custom_var_v3` varchar(200) DEFAULT NULL,
  `custom_var_k4` varchar(200) DEFAULT NULL,
  `custom_var_v4` varchar(200) DEFAULT NULL,
  `custom_var_k5` varchar(200) DEFAULT NULL,
  `custom_var_v5` varchar(200) DEFAULT NULL,
  `location_provider` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idvisit`),
  KEY `index_idsite_config_datetime` (`idsite`,`config_id`,`visit_last_action_time`),
  KEY `index_idsite_datetime` (`idsite`,`visit_last_action_time`),
  KEY `index_idsite_idvisitor` (`idsite`,`idvisitor`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `option`
--

CREATE TABLE IF NOT EXISTS `option` (
  `option_name` varchar(255) NOT NULL,
  `option_value` longtext NOT NULL,
  `autoload` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`option_name`),
  KEY `autoload` (`autoload`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `report`
--

CREATE TABLE IF NOT EXISTS `report` (
  `idreport` int(11) NOT NULL AUTO_INCREMENT,
  `idsite` int(11) NOT NULL,
  `login` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `period` varchar(10) NOT NULL,
  `type` varchar(10) NOT NULL,
  `format` varchar(10) NOT NULL,
  `reports` text NOT NULL,
  `parameters` text,
  `ts_created` timestamp NULL DEFAULT NULL,
  `ts_last_sent` timestamp NULL DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idreport`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `id` char(32) NOT NULL,
  `modified` int(11) DEFAULT NULL,
  `lifetime` int(11) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `site`
--

CREATE TABLE IF NOT EXISTS `site` (
  `idsite` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(90) NOT NULL,
  `main_url` varchar(255) NOT NULL,
  `ts_created` timestamp NULL DEFAULT NULL,
  `ecommerce` tinyint(4) DEFAULT '0',
  `timezone` varchar(50) NOT NULL,
  `currency` char(3) NOT NULL,
  `excluded_ips` text NOT NULL,
  `excluded_parameters` text NOT NULL,
  `group` varchar(250) NOT NULL,
  `feedburnerName` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idsite`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `site_url`
--

CREATE TABLE IF NOT EXISTS `site_url` (
  `idsite` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`idsite`,`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `login` varchar(100) NOT NULL,
  `password` char(32) NOT NULL,
  `alias` varchar(45) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token_auth` char(32) NOT NULL,
  `date_registered` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`login`),
  UNIQUE KEY `uniq_keytoken` (`token_auth`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `user_dashboard`
--

CREATE TABLE IF NOT EXISTS `user_dashboard` (
  `login` varchar(100) NOT NULL,
  `iddashboard` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `layout` text NOT NULL,
  PRIMARY KEY (`login`,`iddashboard`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for table `user_language`
--

CREATE TABLE IF NOT EXISTS `user_language` (
  `login` varchar(100) NOT NULL,
  `language` varchar(10) NOT NULL,
  PRIMARY KEY (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
