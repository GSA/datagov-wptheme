<?php

/**
 * This is a helper class for the Auth MemCookie module.
 * It handles the configuration, and implements the logout handler.
 *
 * @author Olav Morken, UNINETT AS.
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_AuthMemCookie {

	/**
	 * This is the singleton instance of this class.
	 */
	private static $instance = NULL;


	/**
	 * The configuration for Auth MemCookie.
	 */
	private $amcConfig;

	/**
	 * This function is used to retrieve the singleton instance of this class.
	 *
	 * @return The singleton instance of this class.
	 */
	public static function getInstance() {
		if(self::$instance === NULL) {
			self::$instance = new SimpleSAML_AuthMemCookie();
		}

		return self::$instance;
	}


	/**
	 * This function implements the constructor for this class. It loads the Auth MemCookie configuration.
	 */
	private function __construct() {
		/* Load Auth MemCookie configuration. */
		$this->amcConfig = SimpleSAML_Configuration::getConfig('authmemcookie.php');
	}


	/**
	 * Retrieve the login method which should be used to authenticate the user.
	 *
	 * @return string  The login type which should be used for Auth MemCookie.
	 */
	public function getLoginMethod() {
		$loginMethod = $this->amcConfig->getString('loginmethod', 'saml2');
		$supportedLogins = array(
			'authsource',
			'saml2',
			'shib13',
			);
		if(!in_array($loginMethod, $supportedLogins, TRUE)) {
			throw new Exception('Configuration option \'loginmethod\' contains an invalid value.');
		}

		return $loginMethod;
	}


	/**
	 * Retrieve the authentication source that should be used to authenticate the user.
	 *
	 * @return string  The login type which should be used for Auth MemCookie.
	 */
	public function getAuthSource() {

		return $this->amcConfig->getString('authsource');
	}


	/**
	 * This function retrieves the name of the cookie from the configuration.
	 *
	 * @return string  The name of the cookie.
	 */
	public function getCookieName() {
		$cookieName = $this->amcConfig->getString('cookiename', 'AuthMemCookie');
		if(!is_string($cookieName) || strlen($cookieName) === 0) {
			throw new Exception('Configuration option \'cookiename\' contains an invalid value. This option should be a string.');
		}

		return $cookieName;
	}


	/**
	 * This function retrieves the name of the attribute which contains the username from the configuration.
	 *
	 * @return string  The name of the attribute which contains the username.
	 */
	public function getUsernameAttr() {
		$usernameAttr = $this->amcConfig->getString('username', NULL);

		return $usernameAttr;
	}


	/**
	 * This function retrieves the name of the attribute which contains the groups from the configuration.
	 *
	 * @return string  The name of the attribute which contains the groups.
	 */
	public function getGroupsAttr() {
		$groupsAttr = $this->amcConfig->getString('groups', NULL);

		return $groupsAttr;
	}


	/**
	 * This function creates and initializes a Memcache object from our configuration.
	 *
	 * @return Memcache  A Memcache object initialized from our configuration.
	 */
	public function getMemcache() {

		$memcacheHost = $this->amcConfig->getString('memcache.host', '127.0.0.1');
		$memcachePort = $this->amcConfig->getInteger('memcache.port', 11211);

		$memcache = new Memcache;

		foreach (explode(',', $memcacheHost) as $memcacheHost) {
			$memcache->addServer($memcacheHost, $memcachePort);
		}

		return $memcache;
	}


	/**
	 * This function logs the user out by deleting the session information from memcache.
	 */
	private function doLogout() {

		$cookieName = $this->getCookieName();

		/* Check if we have a valid cookie. */
		if(!array_key_exists($cookieName, $_COOKIE)) {
			return;
		}

		$sessionID = $_COOKIE[$cookieName];

		/* Delete the session from memcache. */
		$memcache = $this->getMemcache();
		$memcache->delete($sessionID);

		/* Delete the session cookie. */
		$sessionHandler = SimpleSAML_SessionHandler::getSessionHandler();
		$sessionHandler->setCookie($cookieName, NULL);
	}


	/**
	 * This function implements the logout handler. It deletes the information from Memcache.
	 */
	public static function logoutHandler() {
		self::getInstance()->doLogout();
	}
}

?>