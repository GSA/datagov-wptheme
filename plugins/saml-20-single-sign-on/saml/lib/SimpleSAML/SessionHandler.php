<?php

/**
 * This file is part of SimpleSAMLphp. See the file COPYING in the
 * root of the distribution for licence information.
 *
 * This file defines a base class for session handling.
 * Instantiation of session handler objects should be done through
 * the class method getSessionHandler().
 *
 * @author Olav Morken, UNINETT AS. <andreas.solberg@uninett.no>
 * @package simpleSAMLphp
 * @version $Id: SessionHandler.php 3060 2012-03-29 09:02:39Z olavmrk $
 */
abstract class SimpleSAML_SessionHandler {


	/* This static variable contains a reference to the current
	 * instance of the session handler. This variable will be NULL if
	 * we haven't instantiated a session handler yet.
	 */
	private static $sessionHandler = NULL;



	/* This function retrieves the current instance of the session handler.
	 * The session handler will be instantiated if this is the first call
	 * to this fuunction.
	 *
	 * Returns:
	 *  The current session handler.
	 */
	public static function getSessionHandler() {
		if(self::$sessionHandler === NULL) {
			self::createSessionHandler();
		}

		return self::$sessionHandler;
	}


	/* This constructor is included in case it is needed in the the
	 * future. Including it now allows us to write parent::__construct() in
	 * the subclasses of this class.
	 */
	protected function __construct() {
	}


	/**
	 * Retrieve the session id of saved in the session cookie.
	 *
	 * @return string  The session id saved in the cookie.
	 */
	abstract public function getCookieSessionId();


	/**
	 * Save the session.
	 *
	 * @param SimpleSAML_Session $session  The session object we should save.
	 */
	abstract public function saveSession(SimpleSAML_Session $session);


	/**
	 * Load the session.
	 *
	 * @param string|NULL $sessionId  The ID of the session we should load, or NULL to use the default.
	 * @return SimpleSAML_Session|NULL  The session object, or NULL if it doesn't exist.
	 */
	abstract public function loadSession($sessionId = NULL);


	/**
	 * Initialize the session handler.
	 *
	 * This function creates an instance of the session handler which is
	 * selected in the 'session.handler' configuration directive. If no
	 * session handler is selected, then we will fall back to the default
	 * PHP session handler.
	 */
	private static function createSessionHandler() {

		$store = SimpleSAML_Store::getInstance();
		if ($store === FALSE) {
			self::$sessionHandler = new SimpleSAML_SessionHandlerPHP();
		} else {
			self::$sessionHandler = new SimpleSAML_SessionHandlerStore($store);
		}
	}


	/**
	 * Check whether the session cookie is set.
	 *
	 * This function will only return FALSE if is is certain that the cookie isn't set.
	 *
	 * @return bool  TRUE if it was set, FALSE if not.
	 */
	public function hasSessionCookie() {

		return TRUE;
	}


	/**
	 * Get the cookie parameters that should be used for session cookies.
	 *
	 * @return array
	 * @link http://www.php.net/manual/en/function.session-get-cookie-params.php
	 */
	public function getCookieParams() {

		$config = SimpleSAML_Configuration::getInstance();

		return array(
			'lifetime' => $config->getInteger('session.cookie.lifetime', 0),
			'path' => $config->getString('session.cookie.path', '/'),
			'domain' => $config->getString('session.cookie.domain', NULL),
			'secure' => $config->getBoolean('session.cookie.secure', FALSE),
			'httponly' => TRUE,
		);
	}


	/**
	 * Set a session cookie.
	 *
	 * @param string $name  The name of the session cookie.
	 * @param string|NULL $value  The value of the cookie. Set to NULL to delete the cookie.
	 */
	public function setCookie($name, $value) {
		assert('is_string($name)');
		assert('is_string($value) || is_null($value)');

		$params = $this->getCookieParams();

		// Do not set secure cookie if not on HTTPS
		if ($params['secure'] && !SimpleSAML_Utilities::isHTTPS()) {
			SimpleSAML_Logger::warning('Setting secure cookie on http not allowed.');
			return;
		}

		if ($value === NULL) {
			$expire = time() - 365*24*60*60;
		} elseif ($params['lifetime'] === 0) {
			$expire = 0;
		} else {
			$expire = time() + $params['lifetime'];;
		}

		if (!setcookie($name, $value, $expire, $params['path'], $params['domain'], $params['secure'], $params['httponly'])) {
			throw new SimpleSAML_Error_Exception('Error setting cookie - headers already sent.');
		}
	}

}
