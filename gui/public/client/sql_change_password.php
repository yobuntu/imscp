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
 * Update password of the given SQL user
 *
 * @throws iMSCP_Exception_Database
 * @param int $sqlUserId SQL user unique identifier
 * @param int $sqlUsername SQL username
 * @return bool true on succes
 */
function updateSqlUserPassword($sqlUserId, $sqlUsername)
{
	if (!isset($_POST['uaction'])) {
		return false;
	}

	iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onBeforeEditSqlUser, array('sqlUserId' => $sqlUserId));

	/** @var $cfg iMSCP_Config_Handler_File */
	$cfg = iMSCP_Registry::get('config');

	if ($_POST['pass'] === '' && $_POST['pass_rep'] === '') {
		set_page_message(tr('Please type user password.'), 'error');
		return false;
	}

	if ($_POST['pass'] !== $_POST['pass_rep']) {
		set_page_message(tr("Passwords do not match."), 'error');
		return false;
	}

	if (strlen($_POST['pass']) > $cfg->MAX_SQL_PASS_LENGTH) {
		set_page_message(tr('Too long user password.'), 'error');
		return false;
	}

	if (isset($_POST['pass']) && !preg_match('/^[[:alnum:]:!\*\+\#_.-]+$/', $_POST['pass'])) {
		set_page_message(tr("Don't use special chars like '@, $, %...' in the password."), 'error');
		return false;
	}

	if (!checkPasswordSyntax($_POST['pass'])) {
		return false;
	}

	$password = $_POST['pass'];

	try {
		$query = "UPDATE `sql_user` SET `sql_password` = ?, `sqlu_status` = ? WHERE `sqlu_name` = ? AND `sqlu_status` = ?";
		$stmt = exec_query($query, array($password, $cfg->ITEM_TOCHANGE_STATUS, $sqlUsername, $cfg->ITEM_OK_STATUS));

		if(!$stmt->rowCount()) {
			showBadRequestErrorPage();
		}

		iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onAfterEditSqlUser, array('sqlUserId' => $sqlUserId));

		set_page_message(tr('SQL user password successfully scheduled for update.'), 'success');

		write_log(
			sprintf("%s scheduled password update for the '%s' SQL user.", $_SESSION['user_logged'], tohtml($sqlUsername)),
			E_USER_NOTICE
		);
	} catch (iMSCP_Exception_Database $e) {
		set_page_message(tr('System was unable to schedule update of your SQL user.'), 'error');

		write_log(
			sprintf(
				"System was unable to update password for the '%s' SQL user. Message was: %s",
				tohtml($sqlUsername),
				$e->getMessage()
			),
			E_USER_ERROR
		);
	}

	return true;
}

/**
 * Generate page data.
 *
 * @param iMSCP_pTemplate $tpl
 * @param int $sqlUserId Sql user unique identifier
 * @return string SQL username
 */
function gen_page_data($tpl, $sqlUserId)
{
	/** @var iMSCP_Config_Handler_File $cfg */
	$cfg = iMSCP_Registry::get('config');

	$query = "SELECT `sqlu_name` FROM `sql_user` WHERE `sqlu_id` = ?";
	$stmt = exec_query($query, $sqlUserId);

	$sqlUsername = $stmt->fetchRow(PDO::FETCH_COLUMN);

	$tpl->assign(
		array(
			'USER_NAME' => tohtml($sqlUsername),
			'SQL_USERS_HOSTNAME' => tohtml($cfg->DATABASE_USER_HOST),
			'ID' => $sqlUserId
		)
	);

	return $sqlUsername;
}

/***********************************************************************************************************************
 * Main
 */

// Include core library
require_once 'imscp-lib.php';

iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onClientScriptStart);

check_login('user');

if (isset($_GET['id'])) {
	$sqlUserId = clean_input($_GET['id']);
} else if (isset($_POST['id'])) {
	$sqlUserId = clean_input($_POST['id']);
} elseif(! customerHasFeature('sql') || ! check_user_sql_perms($sqlUserId)) {
	showBadRequestErrorPage();
}

/** @var $cfg iMSCP_Config_Handler_File */
$cfg = iMSCP_Registry::get('config');

$tpl = new iMSCP_pTemplate();
$tpl->define_dynamic(
	array(
		'layout' => 'shared/layouts/ui.tpl',
		'page' => 'client/sql_change_password.tpl',
		'page_message' => 'layout'
	)
);

if(updateSqlUserPassword($sqlUserId, gen_page_data($tpl, $sqlUserId))) {
	redirectTo('sql_manage.php');
}

generateNavigation($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE' => tr('Client / Databases / Overview / Update SQL User Password'),
		'ISP_LOGO' => layout_getUserLogo(),
		 'TR_CHANGE_SQL_USER_PASSWORD' => tr('Update SQL user password'),
		 'TR_USER_NAME' => tr('User name'),
		 'TR_PASS' => tr('Password'),
		 'TR_PASS_REP' => tr('Confirm password'),
		 'TR_CHANGE' => tr('Update')
	)
);

generatePageMessage($tpl);
$tpl->parse('LAYOUT_CONTENT', 'page');

iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onClientScriptEnd, array('templateEngine' => $tpl));

$tpl->prnt();

unsetMessages();
