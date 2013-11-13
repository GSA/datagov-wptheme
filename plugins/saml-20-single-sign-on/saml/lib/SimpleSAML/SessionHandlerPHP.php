<?php

/**
 * This file is part of SimpleSAMLphp. See the file COPYING in the
 * root of the distribution for licence information.
 *
 * This file defines a session handler which uses the default php
 * session handler for storage.
 *
 * @author Olav Morken, UNINETT AS. <andreas.solberg@uninett.no>
 * @package simpleSAMLphp
 * @version $Id: SessionHandlerPHP.php 3025 2012-01-30 07:35:49Z olavmrk $
 */
class SimpleSAML_SessionHandlerPHP extends SimpleSAML_SessionHandler {

	/* Initialize the PHP session handling. This constructor is protected
	 * because it should only be called from
	 * SimpleSAML_SessionHandler::createSessionHandler(...).
	 */
	protected function __construct() {

		/* Call the parent constructor in case it should become
		 * necessary in the future.
		 */
		parent::__construct();

		/* Initialize the php session handling.
		 *
		 * If session_id() returns a blank string, then we need
		 * to call session start. Otherwise the session is already
		 * started, and we should avoid calling session_start().
		 */
		if(session_id() === '') {
			$config = SimpleSAML_Configuration::getInstance();

			$params = $this->getCookieParams();

			$version = explode('.', PHP_VERSION);
			if ((int)$version[0] === 5 && (int)$version[1] < 2) {
				session_set_cookie_params($params['lifetime'], $params['path'], $params['domain'], $params['secure']);
			} else {
				session_set_cookie_params($params['lifetime'], $params['path'], $params['domain'], $params['secure'], $params['httponly']);
			}

			$cookiename = $config->getString('session.phpsession.cookiename', NULL);
			if (!empty($cookiename)) session_name($cookiename);

			$savepath = $config->getString('session.phpsession.savepath', NULL);
			if(!empty($savepath)) {
				session_save_path($savepath);
			}
		}
	}


	/**
	 * Retrieve the session id of saved in the session cookie.
	 *
	 * @return string  The session id saved in the cookie.
	 */
	public function getCookieSessionId() {
		if(session_id() === '') {
			$session_cookie_params = session_get_cookie_params();

			if ($session_cookie_params['secure'] && !SimpleSAML_Utilities::isHTTPS()) {
				throw new SimpleSAML_Error_Exception('Session start with secure cookie not allowed on http.');
			}

			if(!self::hasSessionCookie()) {

				if (headers_sent()) {
					throw new SimpleSAML_Error_Exception('Cannot create new session - headers already sent.');
				}

				/* Session cookie unset - session id not set. Generate new (secure) session id. */
				$sessionId = SimpleSAML_Utilities::stringToHex(SimpleSAML_Utilities::generateRandomBytes(16));
				SimpleSAML_Session::createSession($sessionId);
				session_id($sessionId);
			}
			
			session_start();
		}

		return session_id();
	}


	/**
	 * Save the current session to the PHP session array.
	 *
	 * @param SimpleSAML_Session $session  The session object we should save.
	 */
	public function saveSession(SimpleSAML_Session $session) {

		$_SESSION['SimpleSAMLphp_SESSION'] = serialize($session);
	}


	/**
	 * Load the session from the PHP session array.
	 *
	 * @param string|NULL $sessionId  The ID of the session we should load, or NULL to use the default.
	 * @return SimpleSAML_Session|NULL  The session object, or NULL if it doesn't exist.
	 */
	public function loadSession($sessionId = NULL) {
		assert('is_string($sessionId) || is_null($sessionId)');

		if ($sessionId !== NULL) {
			if (session_id() === '') {
				/* session not initiated with getCookieSessionId(), start session without setting cookie */
				$ret = ini_set('session.use_cookies', '0');
				if ($ret === FALSE) {
					throw new SimpleSAML_Error_Exception('Disabling PHP option session.use_cookies failed.');
				}

				session_id($sessionId);
				session_start();
			} elseif ($sessionId !== session_id()) {
				throw new SimpleSAML_Error_Exception('Cannot load PHP session with a specific ID.');
			}
		} elseif (session_id() === '') {
			$sessionId = self::getCookieSessionId();
		}

		if (!isset($_SESSION['SimpleSAMLphp_SESSION'])) {
			return NULL;
		}

		$session = $_SESSION['SimpleSAMLphp_SESSION'];
		assert('is_string($session)');

		$session = unserialize($session);
		assert('$session instanceof SimpleSAML_Session');

		return $session;
	}


	/**
	 * Check whether the session cookie is set.
	 *
	 * This function will only return FALSE if is is certain that the cookie isn't set.
	 *
	 * @return bool  TRUE if it was set, FALSE if not.
	 */
	public function hasSessionCookie() {

		$cookieName = session_name();
		return array_key_exists($cookieName, $_COOKIE);
	}


	/**
	 * Get the cookie parameters that should be used for session cookies.
	 *
	 * This function contains some adjustments from the default to provide backwards-compatibility.
	 *
	 * @return array
	 * @link http://www.php.net/manual/en/function.session-get-cookie-params.php
	 */
	public function getCookieParams() {

		$config = SimpleSAML_Configuration::getInstance();

		$ret = parent::getCookieParams();

		if ($config->hasValue('session.phpsession.limitedpath') && $config->hasValue('session.cookie.path')) {
			throw new SimpleSAML_Error_Exception('You cannot set both the session.phpsession.limitedpath and session.cookie.path options.');
		} elseif ($config->hasValue('session.phpsession.limitedpath')) {
			$ret['path'] = $config->getBoolean('session.phpsession.limitedpath', FALSE) ? '/' . $config->getBaseURL() : '/';
		}

		$ret['httponly'] = $config->getBoolean('session.phpsession.httponly', FALSE);

		return $ret;
	}

}
