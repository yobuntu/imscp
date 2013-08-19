<?php
/**
 * i-MSCP - internet Multi Server Control Panel
 *
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * The Original Code is "VHCS - Virtual Hosting Control System".
 *
 * The Initial Developer of the Original Code is moleSoftware GmbH.
 * Portions created by Initial Developer are Copyright (C) 2001-2006
 * by moleSoftware GmbH. All Rights Reserved.
 *
 * Portions created by the ispCP Team are Copyright (C) 2006-2010 by
 * isp Control Panel. All Rights Reserved.
 *
 * Portions created by the i-MSCP Team are Copyright (C) 2010-2013 by
 * i-MSCP - internet Multi Server Control Panel. All Rights Reserved.
 *
 * @category    i-MSCP
 * @package     iMSCP_Core
 * @subpackage  Client
 * @copyright   2001-2006 by moleSoftware GmbH
 * @copyright   2006-2010 by ispCP | http://isp-control.net
 * @copyright   2010-2013 by i-MSCP | http://i-mscp.net
 * @author      ispCP Team
 * @author      i-MSCP Team
 * @link        http://i-mscp.net
 */

/***********************************************************************************************************************
 * Functions
 */

/**
 * Generates database sql users list.
 *
 * @access private
 * @param iMSCP_pTemplate $tpl Template engine
 * @param int $databaseId Database unique identifier
 * @return void
 */
function _client_generateDatabaseSqlUserList($tpl, $databaseId)
{
	$query = "SELECT `sqlu_id`, `sqlu_name` FROM `sql_user` WHERE `sqld_id` = ? ORDER BY `sqlu_name`";
	$stmt = exec_query($query, $databaseId);

	if (!$stmt->rowCount()) {
		$tpl->assign('SQL_USERS_LIST', '');
	} else {
		$tpl->assign('SQL_USERS_LIST', '');

		/** @var  $cfg iMSCP_Config_Handler_File */
		$cfg = iMSCP_Registry::get('config');
		$sqlUsersHostname = $cfg->DATABASE_USER_HOST;

		while ($sqlUser = $stmt->fetchRow(PDO::FETCH_ASSOC)) {
			$sqlUserName = $sqlUser['sqlu_name'];

			$tpl->assign(
				array(
					'DB_USER' => tohtml($sqlUserName . '@' . $sqlUsersHostname),
					'USER_ID' => $sqlUser['sqlu_id']
				)
			);

			$tpl->parse('SQL_USERS_LIST', '.sql_users_list');
		}
	}
}

/**
 * Generates databases list.
 *
 * @param iMSCP_pTemplate $tpl Template engine
 * @param int $domainId Domain unique identifier
 * @return void
 */
function client_databasesList($tpl, $domainId)
{
	$query = "SELECT `sqld_id`, `sqld_name`, `sqld_status` FROM `sql_database` WHERE `domain_id` = ? ORDER BY `sqld_name`";
	$stmt = exec_query($query, $domainId);

	if (!$stmt->rowCount()) {
		set_page_message(tr('You do not have databases.'), 'info');
		$tpl->assign('SQL_DATABASES_USERS_LIST', '');
	} else {
		/** @var iMSCP_Config_Handler_File $cfg */
		$cfg = iMSCP_Registry::get('config');

		$okStatus = $cfg->ITEM_OK_STATUS;

		while ($database = $stmt->fetchRow(PDO::FETCH_ASSOC)) {
			$databaseId = $database['sqld_id'];

			$tpl->assign(
				array(
					'DB_NAME' => tohtml($database['sqld_name']),
					'DB_ID' => $databaseId,
					'DB_STATUS' => translate_dmn_status($database['sqld_status'])
				)
			);

			if($database['sqld_status'] != $okStatus) {
				$tpl->assign('SQL_DATABASE_ACTIONS', translate_dmn_status($database['sqld_status']));
				$tpl->assign('SQL_USERS', '');
			} else {
				$tpl->parse('SQL_DATABASE_ACTIONS', 'sql_database_actions');
				_client_generateDatabaseSqlUserList($tpl, $databaseId);
			}

			$tpl->parse('SQL_DATABASES_LIST', '.sql_databases_list');
		}
	}
}

/***********************************************************************************************************************
 * Main script
 */

// Include core library
require_once 'imscp-lib.php';

iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onClientScriptStart);

check_login('user');

customerHasFeature('sql') or showBadRequestErrorPage();

$domainProperties = get_domain_default_props($_SESSION['user_id']);

/** @var $cfg iMSCP_Config_Handler_File */
$cfg = iMSCP_Registry::get('config');

$tpl = new iMSCP_pTemplate();
$tpl->define_dynamic('layout', 'shared/layouts/ui.tpl');
$tpl->define_dynamic(
	array(
		'page' => 'client/sql_manage.tpl',
		'page_message' => 'layout',
		'sql_databases_users_list' => 'page',
		'sql_databases_list' => 'sql_databases_users_list',
		'sql_database_actions' => 'sql_databases_list',
		'sql_users' => 'sql_databases_list',
		'sql_users_list' => 'sql_users',
		'sql_user_actions' => 'sql_users_list'
	)
);

$tpl->assign(
	array(
		'TR_PAGE_TITLE' => tr('Client / Databases / Overview'),
		'ISP_LOGO' => layout_getUserLogo(),
		'TR_MANAGE_SQL' => tr('Manage SQL'),
		'TR_DELETE' => tr('Delete'),
		'TR_DATABASE' => tr('Database Name and Users'),
		'TR_CHANGE_PASSWORD' => tr('Change password'),
		'TR_STATUS' => tr('Status'),
		'TR_PHPMYADMIN' => tr('phpMyAdmin'),
		'TR_DATABASE_USERS' => tr('Database users'),
		'TR_ADD_USER' => tr('Add SQL user'),
		'TR_LOGIN_PMA' => tr('Login into phpMyAdmin'),
		'TR_DATABASE_MESSAGE_DELETE' => tr("This database will be permanently deleted. This process cannot be recovered. All users linked to this database will also be deleted if not linked to another database. Are you sure you want to delete the '%s' database?", true, '%s'),
		'TR_USER_MESSAGE_DELETE' => tr("Are you sure you want delete the '%s' SQL user?", true, '%s'),
		'PMA_TARGET' => $cfg->PMA_TARGET
	)
);

generateNavigation($tpl);
client_databasesList($tpl, $domainProperties['domain_id']);

generatePageMessage($tpl);

$tpl->parse('LAYOUT_CONTENT', 'page');

iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onClientScriptEnd, array('templateEngine' => $tpl));

$tpl->prnt();

unsetMessages();
