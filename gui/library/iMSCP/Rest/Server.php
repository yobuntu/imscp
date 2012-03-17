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
 * @package		iMSCP_Rest
 * @subpackage	Server
 * @copyright	2010 - 2012 by i-MSCP Team
 * @author		Laurent Declercq <l.declercq@nuxwin.com>
 * @link		http://www.i-mscp.net i-MSCP Home Site
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPL v2
 */

/**
 * iMSCP Rest Server.
 *
 * Upon based on Zend Rest server implementation.
 *
 * @category	iMSCP
 * @package		iMSCP_Rest
 * @subpackage	Server
 * @author		Laurent Declercq <l.declercq@nuxwin.com>
 * @version		0.0.1
 */
class iMSCP_Rest_Server
{
	/**
	 * @var array Arguments passed to constructor of the the Web service controller
	 */
	protected $_arguments;

	/**
	 * @var ReflectionClass Information about the Web service controller
	 */
	protected $_reflection;

	/**
	 * @var ReflectionMethod[] Information about each available method (only public methods excluding magics)
	 */
	protected $_methods;

	/**
	 * @var string Current method
	 */
	protected $_method;

	/**
	 * @var array Array of headers to send
	 */
	protected $_headers = array();

	/**
	 * Constructor.
	 *
	 * @param string $className Classname of the Web service controller
	 * @param array|null arguments OPTIONAL optional array of constructor arguments for $controller
	 */
	public function __construct($className, array $arguments = null)
	{
		// Set exception handler
		set_exception_handler(array($this, 'raiseError'));

		$this->_arguments = $arguments;
		$this->setControllerClass($className);
	}

	/**
	 * Handle a request.
	 *
	 * Requests may be passed in, or the server may automagically determine the request based on defaults. Dispatches
	 * server request to the appropriate Web service controller method and returns a response to the Web service client.
	 *
	 * @param array $request
	 */
	public function handle(array $request = null)
	{
		$this->_headers = array('Content-Type: text/xml');

		if (!$request) {
			$request = $_REQUEST;
		}

		// Retrieves method called by the client
		if (isset($request['method'])) {
			$this->_method = $request['method'];

			// Is a method provided by the Web service controller?
			if (isset($this->_methods[$this->_method])) {
				$requestKeys = array_keys($request);
				array_walk($requestKeys, array(__CLASS__, 'lowerCase'));
				$request = array_combine($requestKeys, $request);

				/** @var $methodParameters ReflectionParameter[] */
				$methodParameters = $this->_methods[$this->_method]->getParameters();
				$callingParameters = array();
				$missingParameters = array();

				// Retrieve parameters
				foreach ($methodParameters as $parameter) {
					if (isset($request[strtolower($parameter->getName())])) {
						// TODO Checks if parameter expects an array
						$callingParameters[] = $request[strtolower($parameter->getName())];
					} elseif ($parameter->isOptional()) {
						$callingParameters[] = $parameter->getDefaultValue();
					} else {
						$missingParameters[] = $parameter->getName();
					}
				}

				foreach ($request as $key => $value) {
					if (substr($key, 0, 3) == 'arg') {
						$key = str_replace('arg', '', $key);
						$callingParameters[$key] = $value;

						if (($index = array_search($key, $missingParameters)) !== false) {
							unset($missingParameters[$index]);
						}
					}
				}

				// Sort arguments by key
				ksort($callingParameters);

				$result = false;

				// Check for missing argument
				if (count($callingParameters) < count($methodParameters)) {
					require_once 'iMSCP/Rest/Exception.php';
					$result = $this->raiseError(
						new iMSCP_Rest_Exception(sprintf(
								'Invalid Method Call to %s. Missing argument(s): %s',
								$this->_method , implode(', ', $missingParameters)
							)
						),
						400
					);
				}

				if (!$result) {
					$class = $this->_reflection->getName();

					if ($this->_methods[$this->_method]->isStatic()) { // Static method
						$result = $this->_callStaticMethod($class, $callingParameters);
					} else { // Object method
						$result = $this->_callObjectMethod($class, $callingParameters);
					}
				}

			} else { // Method is not provided by the Web service controller
				require_once 'iMSCP/Rest/Exception.php';
				$result = $this->raiseError(new iMSCP_Rest_Exception(sprintf("Unknown Method %s", $this->_method)), 404);
			}
		} else {
			require_once 'iMSCP/Rest/Exception.php';
			$result = $this->raiseError(new iMSCP_Rest_Exception('No Method Specified'), 404);
		}

		/** @var $result  SimpleXMLElement*/
		if ($result instanceof SimpleXMLElement) {
			$response = $result->asXML();
		} elseif ($result instanceof DOMDocument) {
			$response = $result->saveXML();
		} elseif ($result instanceof DOMNode) {
			$response = $result->ownerDocument->saveXML($result);
		} elseif (is_array($result) || is_object($result)) {
			$response = $this->_handleStruct($result);
		} else {
			$response = $this->_handleScalar($result);
		}

		if (!headers_sent()) {
			foreach ($this->_headers as $header) {
				header($header);
			}
		}

		echo $response;
	}

	/**
	 * Set controller that provide the Web Service and retrieve all available methods.
	 *
	 * Note: Only public methods from the Web Service controller (excluding magics) are exposed to the remote
	 * application.
	 *
	 * @param string $className Class name of the Web service controller
	 * @param array|null arguments OPTIONAL array of constructor arguments for $className
	 * @return void
	 */
	protected function setControllerClass($className)
	{
		if (is_string($className) && class_exists($className, true)) {
			$this->_reflection = new ReflectionClass($className);

			/** @var $reflectionMethod ReflectionMethod */
			foreach ($this->_reflection->getMethods() as $reflectionMethod) {
				$methodName = $reflectionMethod->getName();

				// Don't aggregate magic methods
				if ('__' == substr($methodName, 0, 2)) {
					continue;
				}

				if ($reflectionMethod->isPublic()) {
					$this->_methods[$reflectionMethod->getName()] = $reflectionMethod;
				}
			}
		}
	}

	/**
	 * Creates XML error response, returning DOMDocument with response.
	 *
	 * @param string|Exception $exception Message
	 * @param int $code Error Code
	 * @return DOMDocument
	 */
	public function raiseError($exception = null, $code = null)
	{
		if (isset($this->_methods[$this->_method])) {
			$method = $this->_methods[$this->_method];
		} elseif (isset($this->_method)) {
			$method = $this->_method;
		} else {
			$method = 'rest';
		}

		if ($method instanceof ReflectionMethod) {
			$class = $this->_reflection->getName();
			$method = $method->getName();
		} else {
			$class = false;
		}

		$dom = new DOMDocument('1.0', 'UTF-8');

		if ($class) {
			$xml = $dom->createElement($class);
			$xmlMethod = $dom->createElement($method);
			$xml->appendChild($xmlMethod);
		} else {
			$xml = $dom->createElement($method);
			$xmlMethod = $xml;
		}

		$xml->setAttribute('generator', 'i-MSCP Rest Server');
		$xml->setAttribute('version', '1.0');
		$dom->appendChild($xml);

		$xmlResponse = $dom->createElement('response');
		$xmlMethod->appendChild($xmlResponse);

		if ($exception instanceof Exception) {
			$element = $dom->createElement('message');
			$element->appendChild($dom->createTextNode($exception->getMessage()));
			$xmlResponse->appendChild($element);
			$code = $exception->getCode();
		} elseif (($exception !== null) || 'rest' == $method) {
			$xmlResponse->appendChild($dom->createElement('message', 'An unknown error occured. Please try again.'));
		} else {
			$xmlResponse->appendChild($dom->createElement('message', 'Call to ' . $method . ' failed.'));
		}

		$xmlMethod->appendChild($xmlResponse);
		$xmlMethod->appendChild($dom->createElement('status', 'failed'));

		// Headers to send
		if ($code === null || (404 != $code)) {
			$this->_headers[] = 'HTTP/1.0 400 Bad Request';
		} else {
			$this->_headers[] = 'HTTP/1.0 404 File Not Found';
		}

		return $dom;
	}

	/**
	 * Lowercase a string.
	 *
	 * Lowercase's a string by reference
	 *
	 * @param string $value
	 * @param string $key
	 * @return string Lower cased string
	 */
	protected static function lowerCase(&$value, &$key)
	{
		return $value = strtolower($value);
	}

	/**
	 * Call a static class method and return the result.
	 *
	 * @param  string $class Classname of the Web Service controller
	 * @param  array $arguments Arguments to pass to the method
	 * @return mixed
	 */
	protected function _callStaticMethod($class, array $arguments)
	{
		try {
			$result = call_user_func_array(array($class, $this->_methods[$this->_method]->getName()), $arguments);
		} catch (Exception $e) {
			$result = $this->raiseError($e);
		}

		return $result;
	}

	/**
	 * Call an instance method of a web service controller object.
	 *
	 * @throws iMSCP_Rest_Exception For invalid class name
	 * @param  string $class class name to instanciate
	 * @param  array $arguments arguments to pass to the method
	 * @return mixed
	 */
	protected function _callObjectMethod($class, array $arguments)
	{
		try {
			if ($this->_reflection->getConstructor()) {
				$object = $this->_reflection->newInstanceArgs($arguments);
			} else {
				$object = $this->_reflection->newInstance();
			}
		} catch (Exception $e) {
			require_once "iMSCP/Rest/Exception.php";
			return $this->raiseError(new iMSCP_Rest_Exception(sprintf(
						"Error instantiating Web service controller class %s to invoke method %s (%s)",
						$class, $this->_methods[$this->_method]->getName(), $e->getMessage()
					), 500, $e
				)
			);
		}

		try {
			$result = $this->_methods[$this->_method]->invokeArgs($object, $arguments);
		} catch (Exception $e) {
			$result = $this->raiseError($e);
		}

		return $result;
	}

	/**
	 * Handle an array or object result.
	 *
	 * @param array|object $struct Result Value
	 * @return string XML Response
	 */
	protected function _handleStruct($struct)
	{
		$method = $this->_methods[$this->_method];

		if ($method instanceof ReflectionMethod) {
			$class = $method->getDeclaringClass()->getName();
		} else {
			$class = false;
		}

		$method = $method->getName();

		$dom = new DOMDocument('1.0', 'UTF-8');

		if ($class) {
			$root = $dom->createElement($class);
			$method = $dom->createElement($method);
			$root->appendChild($method);
		} else {
			$root = $dom->createElement($method);
			$method = $root;
		}

		$root->setAttribute('generator', 'i-MSCP Rest Server');
		$root->setAttribute('version', '1.0');
		$dom->appendChild($root);

		$this->_structValue($struct, $dom, $method);

		$struct = (array)$struct;

		if (!isset($struct['status'])) {
			$status = $dom->createElement('status', 'success');
			$method->appendChild($status);
		}

		return $dom->saveXML();
	}

	/**
	 * Recursively iterate through a struct.
	 *
	 * Recursively iterates through an associative array or object's properties
	 * to build XML response.
	 *
	 * @param mixed $struct
	 * @param DOMDocument $dom
	 * @param DOMElement $parent
	 * @return void
	 */
	protected function _structValue($struct, DOMDocument $dom, DOMElement $parent)
	{
		$struct = (array)$struct;

		foreach ($struct as $key => $value) {
			if ($value === false) {
				$value = 0;
			} elseif ($value === true) {
				$value = 1;
			}

			if (ctype_digit((string)$key)) {
				$key = 'key_' . $key;
			}

			if (is_array($value) || is_object($value)) {
				$element = $dom->createElement($key);
				$this->_structValue($value, $dom, $element);
			} else {
				$element = $dom->createElement($key);
				$element->appendChild($dom->createTextNode($value));
			}

			$parent->appendChild($element);
		}
	}

	/**
	 * Handle a single value.
	 *
	 * @param string|int|boolean $value Result value
	 * @return string XML Response
	 */
	protected function _handleScalar($value)
	{
		$method = $this->_methods[$this->_method];
		$class = $method->getDeclaringClass()->getName();
		$method = $method->getName();

		$dom = new DOMDocument('1.0', 'UTF-8');
		$xml = $dom->createElement($class);
		$methodNode = $dom->createElement($method);
		$xml->appendChild($methodNode);
		$xml->setAttribute('generator', 'i-MSCP Rest Server');
		$xml->setAttribute('version', '1.0');
		$dom->appendChild($xml);

		if ($value === false) {
			$value = 0;
		} elseif ($value === true) {
			$value = 1;
		}

		if (isset($value)) {
			$element = $dom->createElement('response');
			$element->appendChild($dom->createTextNode($value));
			$methodNode->appendChild($element);
		} else {
			$methodNode->appendChild($dom->createElement('response'));
		}

		$methodNode->appendChild($dom->createElement('status', 'success'));

		return $dom->saveXML();
	}
}
