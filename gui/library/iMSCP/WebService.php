<?php
/**
 * i-MSCP - internet Multi Server Control Panel
 * Copyright (C) 2010 - 2012 by i-MSCP Team
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @category	iMSCP
 * @package		iMSCP_Core
 * @subpackage	WebService
 * @Copyright (C) 2010 - 2012 by i-MSCP Team
 * @author		Laurent Declercq <l.declercq@nuxwin.com>
 * @version		0.0.1
 * @link		http://www.i-mscp.net i-MSCP Home Site
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * WebService base class.
 *
 * @category	iMSCP
 * @package		iMSCP_Core
 * @subpackage	WebService
 * @author		Laurent Declercq <l.declercq@nuxwin.com>
 * @version		0.0.1
 */
abstract class iMSCP_WebService
{
	/**
	 * Authenticate remote application.
	 *
	 * @param array $request
	 */
	final static public function authenticate($request)
	{
		$auth = iMSCP_Authentication::getInstance();

		if (!$auth->hasIdentity()) {

			// Enable bruteforce if needed
			if (iMSCP_Registry::get('config')->BRUTEFORCE) {
				$bruteforce = new iMSCP_Authentication_Bruteforce();
				$bruteforce->register($auth->events());
			}

			if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
				$result = $auth
					->setUsername(clean_input($_SERVER['PHP_AUTH_USER']))
					->setPassword(clean_input($_SERVER['PHP_AUTH_PW']))
					->authenticate();

				// TODO better is to allow per user access to this service  (ACL)
				if ($result->isValid() && in_array($result->getIdentity()->admin_type, array('admin', 'reseller'))) {
					return;
				}
			}
		}

		$auth->unsetIdentity();
		throw new iMSCP_WebService_Exception('Authentication required', 401);
	}
}
