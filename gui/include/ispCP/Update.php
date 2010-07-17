<?php
/**
 * ispCP ω (OMEGA) a Virtual Hosting Control System
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
 * The Original Code is "ispCP - ISP Control Panel".
 *
 * The Initial Developer of the Original Code is ispCP Team.
 * Portions created by Initial Developer are Copyright (C) 2006-2010 by
 * isp Control Panel. All Rights Reserved.
 *
 * @category    ispCP
 * @package     ispCP_Update
 * @copyright   2006-2010 by ispCP | http://isp-control.net
 * @author      ispCP Team
 * @version     SVN: $Id$
 * @link        http://isp-control.net ispCP Home Site
 * @license     http://www.mozilla.org/MPL/ MPL 1.1
 */

/**
 * Abstract class to implement update functions
 *
 * @package     ispCP_Update
 * @author      Jochen Manz <zothos@zothos.net>
 * @author      Daniel Andreca <sci2tech@gmail.com>
 * @author      Laurent Declercq <l.declercq@nuxwin.com>
 * @version     1.0.3
 * @since		r1355
 */
abstract class ispCP_Update {

	/**
	 * Version of the last update that was applied
	 *
	 * @var int
	 */
	protected $_currentVersion = 0;

	/**
	 * Error messages for updates that have failed
	 *
	 * @var string
	 */
	protected $_errorMessages = '';

	/**
	 * Database variable name for the update version
	 *
	 * @var string
	 */
	protected $_databaseVariableName = '';

	/**
	 * Update functions prefix
	 *
	 * @var string
	 */
	protected $_functionName = '';

	/**
	 * Error message for updates that have failed
	 *
	 * @var string
	 */
	protected $_errorMessage = '';

	/**
	 * This class implements the sigleton design pattern
	 *
	 * @return void
	 */
	protected function __construct() {

		$this->_currentVersion = $this->_getCurrentVersion();
	}

	/**
	 * This class implements the sigleton design pattern
	 *
	 * @return void
	 */
	protected function __clone() {}

	/**
	 * Returns the version of the last update that was applied
	 *
	 * @return int Last update that was applied
	 */
	protected function _getCurrentVersion() {

		$dbConfig = ispCP_Registry::get('Db_Config');

		return (int) $dbConfig->get($this->_databaseVariableName);
	}

	/**
	 * Returns the version of the next update
	 *
	 * @return int The version of the next update
	 */
	protected function _getNextVersion() {

		return $this->_currentVersion + 1;
	}

	/**
	 * Checks if a new update is available
	 *
	 * @return boolean TRUE if a new update is available, FALSE otherwise
	 */
	public function checkUpdateExists() {

		$functionName = $this->_returnFunctionName($this->_getNextVersion());

		return (method_exists($this, $functionName)) ? true : false;
	}

	/**
	 * Returns the name of the function that provides the update
	 *
	 * @return string Update function name
	 */
	protected function _returnFunctionName($version) {

		return $this->_functionName . $version;
	}

	/**
	 * Sends a request to the ispCP daemon
	 *
	 * @return void
	 */
	protected function _sendEngineRequest() {

		send_request();
	}

	/**
	 * Adds a new message in the errors messages cache
	 *
	 * @return void
	 */
	protected function _addErrorMessage($message) {

		$this->_errorMessages .= $message;
	}

	/**
	 * Accessor for error messages
	 *
	 * @return string Error messages
	 */
	public function getErrorMessage() {

		return $this->_errorMessages;
	}

	/**
	 * Apply all available updates
	 *
	 * @return boolean TRUE on sucess, FALSE otherwise
	 * @todo Should be more generic (Only the database variable should be
	 * updated here. Other stuff should be implemented by the concrete class
	 */
	public function executeUpdates() {

		$cfg = ispCP_Registry::get('Config');
		$sql = ispCP_Registry::get('Pdo');
		$dbConfig = ispCP_Registry::get('Db_Config');

		$engine_run_request = false;

		while ($this->checkUpdateExists()) {

			// Get the next database update Version
			$newVersion = $this->_getNextVersion();

			// Get the needed function name
			$functionName = $this->_returnFunctionName($newVersion);

			// Pull the query from the update function using a variable function
			$queryArray = $this->$functionName($engine_run_request);

			// First, switch to exception mode for errors managment
			$sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// We start a transaction (autocommit disabled)
			// NXW: Not needed at this moment (We don't use innoDB engine)
			// $sql->startTransaction();

			try {
				// We execute every Sql statements
				foreach ($queryArray as $query) {
					$sql->query($query);
				}

				// If all SQL statements are executed correctly, commits
				// the changes
				// NXW: Not needed at this moment (We don't use innoDB engine)
				// $sql->completeTransaction();

				// Update database revision
				$dbConfig->set($this->_databaseVariableName, $newVersion);

			} catch (PDOException $e) {

				// Perform a rollback if a SQL statement was failed
				// NXW: Not needed at this moment (We don't use innoDB engine)
				// $sql->rollbackTransaction();

				// Prepare and display an error message
				$errorMessage =  sprintf($this->_errorMessage, $newVersion);

				// Extended error message
				if ($cfg->DEBUG) {
					if(PHP_SAPI != 'cli') {
						$errorMessage .= "<br />" . $e->getMessage();
						$errorMessage .= "<br />Query: $query";
					} else {
						$errorMessage .= "\n" . $e->getMessage();
						$errorMessage .= "\nQuery: $query";
					}
				}

				$this->_addErrorMessage($errorMessage);

				// An error was occured, we stop here !
				return false;
			}

			$this->_currentVersion = $newVersion;

		} // End while

		// We should never run the backend scripts from the CLI update script
		if(PHP_SAPI != 'cli' && $engine_run_request) {
			$this->_sendEngineRequest();
		}

		return true;
	}
}