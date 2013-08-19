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
 * Check SQL permissions (limit)
 *
 * @param iMSCP_pTemplate $tpl
 * @param int $customerId Customer unique identifier
 * @param int $databaseId Database unique identifier
 * @param array $sqlUserList
 */
function check_sql_permissions($tpl, $customerId, $databaseId, $sqlUserList)
{
	$domainProperties = get_domain_default_props($customerId);
	$domainSqlUsersLimit = $domainProperties['domain_sqlu_limit'];

	$limits = get_domain_running_sql_acc_cnt($domainProperties['domain_id']);

	if ($domainSqlUsersLimit != 0 && $limits[1] >= $domainSqlUsersLimit) {
		if (!$sqlUserList) {
			set_page_message(tr('SQL user limit reached.'), 'error');
			redirectTo('sql_manage.php');
		} else {
			$tpl->assign('CREATE_SQLUSER', '');
		}
	}

	$query = "
		SELECT
			`t1`.`domain_id`
		FROM
			`domain` `t1`
		INNER JOIN
			`sql_database` `t2` ON(t2.domain_id = t1.domain_id)
		WHERE
			`t1`.`domain_id` = ?
		AND
			`t2`.`sqld_id` = ?
		LIMIT 1
	";
	$stmt = exec_query($query, array($domainProperties['domain_id'], $databaseId));

	if (!$stmt->rowCount()) {
		showBadRequestErrorPage();
	}
}

/**
 * Generate list of SQL user assigned to the database
 *
 * @param int $databaseId Database unique identifier
 * @return array
 */
function get_sqluser_list_of_current_db($databaseId)
{
	$query = "SELECT `sqlu_name` FROM `sql_user` WHERE `sqld_id` = ?";
	$stmt = exec_query($query, $databaseId);

	$userlist = array();

	while (!$stmt->EOF) {
		$userlist[] = $stmt->fields['sqlu_name'];
		$stmt->moveNext();
	}

	return $userlist;
}

/**
 * Generate list of available SQL users
 *
 * @param iMSCP_pTemplate $tpl
 * @param int $sqlUserId Sql user unique identifier
 * @param int $databaseId Database unique identifier
 * @return bool
 */
function gen_sql_user_list($tpl, $sqlUserId, $databaseId)
{

	/** @var $cfg iMSCP_Config_Handler_File */
	$cfg = iMSCP_Registry::get('config');

	$firstPassed = true;
	$sqlUserFound = false;
	$oldrsName = '';
	$userlist = get_sqluser_list_of_current_db($databaseId);
	$domainId = get_user_domain_id($sqlUserId);

	// Let's select all sqlusers of the current domain except the users of the current database
	$query = "
		SELECT
			t1.`sqlu_name`, t1.`sqlu_id`
		FROM
			`sql_user` AS t1, `sql_database` AS t2
		WHERE
			t1.`sqld_id` = t2.`sqld_id`
		AND
			t2.`domain_id` = ?
		AND
			t1.`sqld_id` <> ?
		ORDER BY
			t1.`sqlu_name`
	";
	$stmt = exec_query($query, array($domainId, $databaseId));

	while (!$stmt->EOF) {
		// Checks if it's the first element of the combobox and set it as selected
		if ($firstPassed) {
			$select = $cfg->HTML_SELECTED;
			$firstPassed = false;
		} else {
			$select = '';
		}

		// 1. Compares the sqluser name with the record before (Is set as '' at the first time, see above)
		// 2. Compares the sqluser name with the userlist of the current database
		if ($oldrsName != $stmt->fields['sqlu_name'] && !in_array($stmt->fields['sqlu_name'], $userlist)) {
			$sqlUserFound = true;
			$oldrsName = $stmt->fields['sqlu_name'];

			$tpl->assign(
				array(
					'SQLUSER_ID' => $stmt->fields['sqlu_id'],
					'SQLUSER_SELECTED' => $select,
					'SQLUSER_NAME' => tohtml($stmt->fields['sqlu_name'])
				)
			);

			$tpl->parse('SQLUSER_LIST', '.sqluser_list');
		}

		$stmt->moveNext();
	}

	// let's hide the combobox in case there are no other sqlusers
	if (!$sqlUserFound) {
		$tpl->assign('SHOW_SQLUSER_LIST', '');
		return false;
	} else {
		return true;
	}
}

/**
 * Check for existence of the given SQL user
 * @param string $sqlUsername SQL username
 * @return mixed
 */
function check_db_user($sqlUsername)
{
	$query = "SELECT COUNT(`User`) `cnt` FROM `mysql`.`user` WHERE `User` = ?";
	$stmt = exec_query($query, $sqlUsername);

	return $stmt->fields['cnt'];
}

/**
 * Assign an SQL user to the given database
 *
 * This can be an existing user or a new user as filled out in input data
 *
 * @param int $customerId Customer unique identifier
 * @param int $databaseId Database unique identifier
 * @return mixed
 */
function add_sql_user($customerId, $databaseId)
{
	if (!empty($_POST)) {
		if (!isset($_POST['uaction'])) {
			showBadRequestErrorPage();
		}

		iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onBeforeAddSqlUser);

		/** @var $cfg iMSCP_Config_Handler_File */
		$cfg = iMSCP_Registry::get('config');

		$domainId = get_user_domain_id($customerId);

		if (!isset($_POST['Add_Exist'])) { // Add new SQL user as specified in input data
			if (empty($_POST['user_name'])) {
				set_page_message(tr('Please enter a username.'), 'error');
				return;
			}

			if (empty($_POST['pass'])) {
				set_page_message(tr('Please enter a password.'), 'error');
				return;
			}

			if (!isset($_POST['pass_rep']) || $_POST['pass'] !== $_POST['pass_rep']) {
				set_page_message(tr("Passwords do not match."), 'error');
				return;
			}

			if (!preg_match('/^[[:alnum:]:!*+#_.-]+$/', $_POST['pass'])) {
				set_page_message(tr("Please don't use special chars such as '@, $, %...' in the password."), 'error');
				return;
			}

			if (!checkPasswordSyntax($_POST['pass'])) {
				return;
			}

			$sqlUserPassword = $_POST['pass'];

			// we'll use domain_id in the name of the database;
			if (
				isset($_POST['use_dmn_id']) && $_POST['use_dmn_id'] == 'on' && isset($_POST['id_pos'])
				&& $_POST['id_pos'] == 'start'
			) {
				$sqlUser = $domainId . '_' . clean_input($_POST['user_name']);
			} elseif (
				isset($_POST['use_dmn_id']) && $_POST['use_dmn_id'] == 'on' && isset($_POST['id_pos'])
				&& $_POST['id_pos'] == 'end'
			) {
				$sqlUser = clean_input($_POST['user_name']) . '_' . $domainId;
			} else {
				$sqlUser = clean_input($_POST['user_name']);
			}
		} else { // Using existing SQL user as specified in input data
			$stmt = exec_query("SELECT `sqlu_name`, `sqlu_pass` FROM `sql_user` WHERE `sqlu_id` = ?", $_POST['sqluser_id']);

			if (!$stmt->rowCount()) {
				set_page_message(tr('SQL user not found.'), 'error');
				return;
			}

			$sqlUser = $stmt->fields['sqlu_name'];
			$sqlUserPassword = $stmt->fields['sqlu_pass'];
		}

		# Check for username length
		if (strlen($sqlUser) > $cfg->MAX_SQL_USER_LENGTH) {
			set_page_message(tr('User name too long!'), 'error');
			return;
		}

		// Check for unallowed character in username
		if (preg_match('/[%|\?]+/', $sqlUser)) {
			set_page_message(tr('Wildcards such as %% and ? are not allowed.'), 'error');
			return;
		}

		// Ensure that SQL user doesn't already exists
		if (!isset($_POST['Add_Exist']) && check_db_user($sqlUser)) {
			set_page_message(tr('SQL username already in use.'), 'error');
			return;
		}

		# Retrieve database to which SQL user should be assigned
		$stmt = exec_query(
			"SELECT `sqld_name` FROM `sql_database` WHERE `sqld_id` = ? AND `domain_id` = ?",
			array($databaseId, $domainId)
		);

		if (!$stmt->rowCount()) {
			showBadRequestErrorPage();
		} else {
			try {
				$query = "INSERT INTO `sql_user` (`sqld_id`, `sqlu_name`, `sqlu_pass`, `sqlu_status`) VALUES (?, ?, ?, ?)";
				exec_query(
					$query,
					array(
						$databaseId,
						$sqlUser,
						$sqlUserPassword,
						(!isset($_POST['Add_Exist'])) ? $cfg->ITEM_TOADD_STATUS : $cfg->ITEM_OK_STATUS
					)
				);

				iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onAfterAddSqlUser);

				set_page_message(tr('SQL user successfully scheduled for addition.'), 'success');

				write_log(
					sprintf("%s scheduled addition of SQL user: %s", $_SESSION['user_logged'], tohtml($sqlUser)),
					E_USER_NOTICE
				);
			} catch (iMSCP_Exception_Database $e) {
				set_page_message(tr('System was unable to schedule addition of new SQL user.'), 'error');
				write_log(
					sprintf(
						"System was unable to schedule addition of the '%s' SQL user. Message was: %s",
						$sqlUser,
						$e->getMessage()
					),
					E_USER_ERROR
				);
			}
		}

		redirectTo('sql_manage.php');
	}
}

/**
 * Generate page post data
 *
 * @param iMSCP_pTemplate $tpl
 * @param int $databaseId Dtabase unique identifier
 * @return void
 */
function gen_page_post_data($tpl, $databaseId)
{
	/** @var $cfg iMSCP_Config_Handler_File */
	$cfg = iMSCP_Registry::get('config');

	if ($cfg->MYSQL_PREFIX == 'yes') {
		$tpl->assign('MYSQL_PREFIX_YES', '');
		if ($cfg->MYSQL_PREFIX_TYPE == 'behind') {
			$tpl->assign('MYSQL_PREFIX_INFRONT', '');
			$tpl->parse('MYSQL_PREFIX_BEHIND', 'mysql_prefix_behind');
			$tpl->assign('MYSQL_PREFIX_ALL', '');
		} else {
			$tpl->parse('MYSQL_PREFIX_INFRONT', 'mysql_prefix_infront');
			$tpl->assign(
				array(
					'MYSQL_PREFIX_BEHIND' => '',
					'MYSQL_PREFIX_ALL' => ''
				)
			);
		}
	} else {
		$tpl->assign(
			array(
				'MYSQL_PREFIX_NO' => '',
				'MYSQL_PREFIX_INFRONT' => '',
				'MYSQL_PREFIX_BEHIND' => ''
			)
		);
		$tpl->parse('MYSQL_PREFIX_ALL', 'mysql_prefix_all');
	}

	if (isset($_POST['uaction']) && $_POST['uaction'] === 'add_user') {
		$tpl->assign(
			array(
				'USER_NAME' => (isset($_POST['user_name'])) ? clean_html($_POST['user_name'], true) : '',
				'USE_DMN_ID' => (isset($_POST['use_dmn_id']) && $_POST['use_dmn_id'] === 'on') ? $cfg->HTML_CHECKED : '',
				'START_ID_POS_CHECKED' => (isset($_POST['id_pos']) && $_POST['id_pos'] !== 'end') ? $cfg->HTML_CHECKED : '',
				'END_ID_POS_CHECKED' => (isset($_POST['id_pos']) && $_POST['id_pos'] === 'end') ? $cfg->HTML_CHECKED : ''));
	} else {
		$tpl->assign(
			array(
				'USER_NAME' => '',
				'USE_DMN_ID' => '',
				'START_ID_POS_CHECKED' => '',
				'END_ID_POS_CHECKED' => $cfg->HTML_CHECKED));
	}

	$tpl->assign('ID', $databaseId);
}

/***********************************************************************************************************************
 * Main
 */

// Include core library
require_once 'imscp-lib.php';

iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onClientScriptStart);

check_login('user');

if (isset($_GET['id'])) {
	$databaseId = $_GET['id'];
} else if (isset($_POST['id'])) {
	$databaseId = $_POST['id'];
} elseif (!customerHasFeature('sql')) {
	showBadRequestErrorPage();
}

/** @var $cfg iMSCP_Config_Handler_File */
$cfg = iMSCP_Registry::get('config');

$tpl = new iMSCP_pTemplate();
$tpl->define_dynamic(
	array(
		'layout' => 'shared/layouts/ui.tpl',
		'page' => 'client/sql_user_add.tpl',
		'page_message' => 'layout',
		'mysql_prefix_no' => 'page',
		'mysql_prefix_yes' => 'page',
		'mysql_prefix_infront' => 'page',
		'mysql_prefix_behind' => 'page',
		'mysql_prefix_all' => 'page',
		'sqluser_list' => 'page',
		'show_sqluser_list' => 'page',
		'create_sqluser' => 'page'
	)
);

$tpl->assign(
	array(
		'TR_PAGE_TITLE' => tr('Client / Databases / Overview / Add SQL User'),
		'ISP_LOGO' => layout_getUserLogo(),
		'TR_ADD_SQL_USER' => tr('Add SQL user'),
		'TR_USER_NAME' => tr('SQL user name'),
		'TR_USE_DMN_ID' => tr('Use numeric ID'),
		'TR_START_ID_POS' => tr('In front the name'),
		'TR_END_ID_POS' => tr('Behind the name'),
		'TR_ADD' => tr('Add'),
		'TR_CANCEL' => tr('Cancel'),
		'TR_ADD_EXIST' => tr('Assign'),
		'TR_PASS' => tr('Password'),
		'TR_PASS_REP' => tr('Repeat password'),
		'TR_SQL_USER_NAME' => tr('SQL users'),
		'TR_ASSIGN_EXISTING_SQL_USER' => tr('Assign existing SQL user'),
		'TR_NEW_SQL_USER_DATA' => tr('New SQL user data'),
		'SQL_USERS_HOSTNAME' => $cfg->DATABASE_USER_HOST
	)
);

$sqlUserList = gen_sql_user_list($tpl, $_SESSION['user_id'], $databaseId);
check_sql_permissions($tpl, $_SESSION['user_id'], $databaseId, $sqlUserList);
gen_page_post_data($tpl, $databaseId);
add_sql_user($_SESSION['user_id'], $databaseId);
generateNavigation($tpl);
generatePageMessage($tpl);

$tpl->parse('LAYOUT_CONTENT', 'page');

iMSCP_Events_Manager::getInstance()->dispatch(iMSCP_Events::onClientScriptEnd, array('templateEngine' => $tpl));

$tpl->prnt();

unsetMessages();
