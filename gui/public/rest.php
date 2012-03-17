<?php

/**
 * i-MSCP - internet Multi Server Control Panel
 * Copyright (C) 2010-2012 by i-MSCP team
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @category	iMSCP
 * @package		iMSCP_Core
 * @subpackage	Rest
 * @copyright	2010-2012 by i-MSCP team
 * @author		Laurent Declercq <l.declercq@nuxwin.com>
 * @version		0.0.2
 * @link		http://www.i-mscp.net i-MSCP Home Site
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/***********************************************************************************************************************
 * Script functions
 */

/**
 * Exception handler for Rest action script
 *
 * @param Exception $exception
 * @return void
 */
function rest_exceptionHandler($exception)
{
	$dom = new DOMDocument('1.0', 'UTF-8');
	$xml = $dom->createElement('rest');
	$xml->setAttribute('generator', 'iMSCP Rest Server');
	$xml->setAttribute('version', '1.0');
	$dom->appendChild($xml);
	$xmlResponse = $dom->createElement('response');
	$xml->appendChild($xmlResponse);
	$element = $dom->createElement('message');
	$element->appendChild($dom->createTextNode($exception->getMessage()));
	$xmlResponse->appendChild($element);
	$xml->appendChild($xmlResponse);
	$xml->appendChild($dom->createElement('status', 'failed'));

	$code = $exception->getCode();

	switch($code) {
		case 401:
			header('WWW-Authenticate: Basic realm="i-MSCP Rest Server - Authentication required"');
			$header = 'HTTP/1.0 401 Unauthorized';
		break;
		case 403:
			$header = 'HTTP/1.0 403 Forbidden';
		break;
		case 404:
			$header ='HTTP/1.0 404 Not Found';
		break;
		case 500:
			$header ='HTTP/1.0 500 Internal error';
		break;
		default:
			$header = 'HTTP/1.0 400 Bad Request';
	}

	header('Content-Type: text/xml');
	header($header);
	echo $dom->saveXML();
	exit;
}

/***********************************************************************************************************************
 * Main script
 */

//die('Development in progress...');

// Set exception handler to handle uncaught exceptions (override the default one used by i-MSCP UI)
set_exception_handler('rest_exceptionHandler');

// Set error handler
set_error_handler(
	function($errno, $errstr)
	{
		throw new Exception(sprintf('An internal error occured (%s - %d)', $errstr, $errno), 500);
	}
);

if (!empty($_REQUEST['wsid'])) { // Any Web service must have an unique identifier
	require_once 'imscp-lib.php'; // Include core library
	restore_exception_handler(); // Back to the REST exception handller

	if(iMSCP_Registry::isRegistered('bufferFilter')) { // Do not show any compression information in output
		iMSCP_Registry::get('bufferFilter')->compressionInformation = false;
	}

	// Trigger the onRestRequest event (only plugins that provides a Web Service controller should listen this event)
	$responseCollection = iMSCP_Events_Manager::getInstance()->dispatch(
		iMSCP_Events::onRestRequest, array('webServiceId' => clean_input($_REQUEST['wsid']))
	);

	foreach ($responseCollection as $response) {
		// When a plugin that listen on the onRestRequest event don't provide the requested Web service, it must return NULL
		if ($response !== null) {
			// A plugin that provides a Web service can return a string representing the class name of the Web Service
			// controller or an array where the first index contains a string representing the class name of the Web
			// Service controller and the second, an array that contains constructor arguments.
			if (is_array($response)) {
				$controller = $response[0]; // Web Service controller class name
				$constructorArgs = (array) $response[1]; // Constructor arguments for $controller
			} else {
				$controller = $response;
				$constructorArgs = null;
			}

			// Any Web Service controller must extends the iMSCP_WebService class
			if(class_exists($controller, false) && in_array('iMSCP_WebService', class_parents($controller), false)) {
				/** @var $controller iMSCP_WebService */
				$controller::authenticate($_REQUEST); // Process authentication for remote application

				$restServer = new iMSCP_Rest_Server($controller, $constructorArgs);
				$restServer->handle(); // Handle the REST request and sent output to the client
				exit;
			} else { // Controller not found or bad implementation
				write_log(sprintf(
						"Class for the %s Web Service not found or doesn't extends the iMSCP_WebService class",
						clean_input($_REQUEST['wsid'])
					), E_USER_ERROR
				);

				throw new Exception('An internal error occured', 500);
			}

			break;
		}
	}

	throw new iMSCP_WebService_Exception('Web service no found or not activated', 404);
} else {
	throw new Exception('Only REST requests with a valid Web service identifier are allowed', 403);
}
