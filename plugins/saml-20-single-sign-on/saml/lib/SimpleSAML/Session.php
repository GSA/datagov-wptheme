<?php

/**
 * The Session class holds information about a user session, and everything attached to it.
 *
 * The session will have a duration, and validity, and also cache information about the different
 * federation protocols, as Shibboleth and SAML 2.0. On the IdP side the Session class holds 
 * information about all the currently logged in SPs. This is used when the user initiate a 
 * Single-Log-Out.
 *
 * @author Andreas Åkre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @package simpleSAMLphp
 * @version $Id: Session.php 3105 2012-05-24 06:08:23Z olavmrk $
 */
class SimpleSAML_Session {

	/**
	 * This is a timeout value for setData, which indicates that the data should be deleted
	 * on logout.
	 */
	const DATA_TIMEOUT_LOGOUT = 'logoutTimeout';


	/**
	 * The list of loaded session objects.
	 *
	 * This is an associative array indexed with the session id.
	 *
	 * @var array
	 */
	private static $sessions = array();


	/**
	 * This variable holds the instance of the session - Singleton approach.
	 */
	private static $instance = null;
	

	/**
	 * The session ID of this session.
	 *
	 * @var string|NULL
	 */
	private $sessionId;


	/**
	 * Transient session flag.
	 *
	 * @var boolean|FALSE
	 */
	private $transient = FALSE;


	/**
	 * The track id is a new random unique identifier that is generate for each session.
	 * This is used in the debug logs and error messages to easily track more information
	 * about what went wrong.
	 */
	private $trackid = 0;
	
	private $idp = null;
	
	private $authenticated = null;
	private $attributes = null;
	
	private $sessionindex = null;
	private $nameid = null;
	
	private $authority = null;
	
	// Session duration parameters
	private $sessionstarted = null;
	private $sessionduration = null;
	
	// Track whether the session object is modified or not.
	private $dirty = false;
		

	/**
	 * This is an array of registered logout handlers.
	 * All registered logout handlers will be called on logout.
	 */
	private $logout_handlers = array();


	/**
	 * This is an array of objects which will autoexpire after a set time. It is used
	 * where one needs to store some information - for example a logout request, but doesn't
	 * want it to be stored forever.
	 *
	 * The data store contains three levels of nested associative arrays. The first is the data type, the
	 * second is the identifier, and the third contains the expire time of the data and the data itself.
	 */
	private $dataStore = null;


	/**
	 * Current NameIDs for sessions.
	 *
	 * Stored as a two-level associative array: $sessionNameId[<entityType>][<entityId>]
	 */
	private $sessionNameId;


	/**
	 * Logout state when authenticated with authentication sources.
	 */
	private $logoutState;


	/**
	 * Persistent authentication state.
	 *
	 * @array
	 */
	private $authState;


	/**
	 * The list of IdP-SP associations.
	 *
	 * This is an associative array with the IdP id as the key, and the list of
	 * associations as the value.
	 *
	 * @var array
	 */
	private $associations = array();


	/**
	 * The authentication token.
	 *
	 * This token is used to prevent session fixation attacks.
	 *
	 * @var string|NULL
	 */
	private $authToken;


	/**
	 * Authentication data.
	 *
	 * This is an array with authentication data for the various authsources.
	 *
	 * @var array|NULL  Associative array of associative arrays.
	 */
	private $authData;


	/**
	 * private constructor restricts instantiaton to getInstance()
	 */
	private function __construct($transient = FALSE) {

		$this->authData = array();

		if ($transient) {
			$this->trackid = 'XXXXXXXXXX';
			$this->transient = TRUE;
			return;
		}

		$sh = SimpleSAML_SessionHandler::getSessionHandler();
		$this->sessionId = $sh->getCookieSessionId();

		$this->trackid = substr(md5(uniqid(rand(), true)), 0, 10);

		$this->dirty = TRUE;
		$this->addShutdownFunction();
	}


	/**
	 * Upgrade this session object to use the $authData property.
	 *
	 * TODO: Remove in version 1.8.
	 */
	private function upgradeAuthData() {
		$this->authData = array();

		if ($this->authority === NULL || !$this->authenticated) {
			return;
		}

		if ($this->authState !== NULL) {
			$data = $this->authState;
		} else {
			$data = array();
		}

		if ($this->attributes !== NULL) {
			$data['Attributes'] = $this->attributes;
		} else {
			$data['Attributes'] = array();
		}

		if ($this->idp !== NULL) {
			$data['saml:sp:IdP'] = $this->idp;
		}

		if ($this->sessionindex !== NULL) {
			$data['saml:sp:SessionIndex'] = $this->sessionindex;
		}

		if ($this->nameid !== NULL) {
			$data['saml:sp:NameID'] = $this->nameid;
		}

		$data['AuthnInstant'] = $this->sessionstarted;
		$data['Expire'] = $this->sessionstarted + $this->sessionduration;
		$this->sessionstarted = NULL;
		$this->sessionduration = NULL;

		if ($this->logoutState !== NULL) {
			$data['LogoutState'] = $this->logoutState;
		}


		if (!empty($this->logout_handlers)) {
			$data['LogoutHandlers'] = $this->logout_handlers;
		}

		$this->authData[$this->authority] = $data;
	}


	/**
	 * This function is called after this class has been deserialized.
	 */
	public function __wakeup() {
		$this->addShutdownFunction();

		/* TODO: Remove for version 1.8. */
		if ($this->authData === NULL) {
			$this->upgradeAuthData();
		}
	}


	/**
	 * Retrieves the current session. Will create a new session if there isn't a session.
	 *
	 * @return The current session.
	 */
	public static function getInstance() {

		/* Check if we already have initialized the session. */
		if (isset(self::$instance)) {
			return self::$instance;
		}


		/* Check if we have stored a session stored with the session
		 * handler.
		 */
		try {
			self::$instance = self::getSession();
		} catch (Exception $e) {
			/* For some reason, we were unable to initialize this session. Use a transient session instead. */
			self::useTransientSession();

			$globalConfig = SimpleSAML_Configuration::getInstance();
			if ($globalConfig->getBoolean('session.disable_fallback', FALSE) === TRUE) {
				throw $e;
			}

			if ($e instanceof SimpleSAML_Error_Exception) {
				SimpleSAML_Logger::error('Error loading session:');
				$e->logError();
			} else {
				SimpleSAML_Logger::error('Error loading session: ' . $e->getMessage());
			}

			return self::$instance;
		}

		if(self::$instance !== NULL) {
			return self::$instance;
		}


		/* Create a new session. */
		self::$instance = new SimpleSAML_Session();
		return self::$instance;
	}


	/**
	 * Use a transient session.
	 *
	 * Create a session that should not be saved at the end of the request.
	 * Subsequent calls to getInstance() will return this transient session.
	 */
	public static function useTransientSession() {

		if (isset(self::$instance)) {
			/* We already have a session. Don't bother with a transient session. */
			return;
		}

		self::$instance = new SimpleSAML_Session(TRUE);
	}


	/**
	 * Retrieve the session ID of this session.
	 *
	 * @return string|NULL  The session ID, or NULL if this is a transient session.
	 */
	public function getSessionId() {

		return $this->sessionId;
	}


	/**
	 * Retrieve if session is transient.
	 *
	 * @return boolean  The session transient flag.
	 */
	public function isTransient() {
		return $this->transient;
	}


	/**
	 * Get a unique ID that will be permanent for this session.
	 * Used for debugging and tracing log files related to a session.
	 */
	public function getTrackID() {
		return $this->trackid;
	}


	/**
	 * Who authorized this session. could be in example saml2, shib13, login,login-admin etc.
	 */
	public function getAuthority() {
		return $this->authority;
	}


	/**
	 * This method retrieves from session a cache of a specific Authentication Request
	 * The complete request is not stored, instead the values that will be needed later
	 * are stored in an assoc array.
	 *
	 * @param $protocol 		saml2 or shib13
	 * @param $requestid 		The request id used as a key to lookup the cache.
	 *
	 * @return Returns an assoc array of cached variables associated with the
	 * authentication request.
	 */
	public function getAuthnRequest($protocol, $requestid) {


		SimpleSAML_Logger::debug('Library - Session: Get authnrequest from cache ' . $protocol . ' time:' . time() . '  id: '. $requestid );

		$type = 'AuthnRequest-' . $protocol;
		$authnRequest = $this->getData($type, $requestid);

		if($authnRequest === NULL) {
			/*
			 * Could not find requested ID. Throw an error. Could be that it is never set, or that it is deleted due to age.
			 */
			throw new Exception('Could not find cached version of authentication request with ID ' . $requestid . ' (' . $protocol . ')');
		}

		return $authnRequest;
	}


	/**
	 * This method sets a cached assoc array to the authentication request cache storage.
	 *
	 * @param $protocol 		saml2 or shib13
	 * @param $requestid 		The request id used as a key to lookup the cache.
	 * @param $cache			The assoc array that will be stored.
	 */
	public function setAuthnRequest($protocol, $requestid, array $cache) {

		SimpleSAML_Logger::debug('Library - Session: Set authnrequest ' . $protocol . ' time:' . time() . ' size:' . count($cache) . '  id: '. $requestid );

		$type = 'AuthnRequest-' . $protocol;
		$this->setData($type, $requestid, $cache);
	}


	/**
	 * Set the IdP we are authenticated against.
	 *
	 * @param string|NULL $idp  Our current IdP, or NULL if we aren't authenticated with an IdP.
	 */
	public function setIdP($idp) {
		assert('is_string($idp) || is_null($idp)');
		assert('isset($this->authData[$this->authority])');

		SimpleSAML_Logger::debug('Library - Session: Set IdP to : ' . $idp);
		$this->dirty = true;
		if ($idp !== NULL) {
			$this->authData[$this->authority]['saml:sp:IdP'] = $idp;
		} else {
			unset($this->authData[$this->authority]['saml:sp:IdP']);
		}

	}


	/**
	 * Retrieve the IdP we are currently authenticated against.
	 *
	 * @return string|NULL  Our current IdP, or NULL if we aren't authenticated with an IdP.
	 */
	public function getIdP() {
		if (!isset($this->authData[$this->authority]['saml:sp:IdP'])) {
			return NULL;
		}
		return $this->authData[$this->authority]['saml:sp:IdP'];
	}


	/**
	 * Set the SessionIndex we received from our IdP.
	 *
	 * @param string|NULL $sessionindex  Our SessionIndex.
	 */
	public function setSessionIndex($sessionindex) {
		assert('is_string($sessionindex) || is_null($sessionindex)');
		assert('isset($this->authData[$this->authority])');

		SimpleSAML_Logger::debug('Library - Session: Set sessionindex: ' . $sessionindex);
		$this->dirty = true;
		if ($sessionindex !== NULL) {
			$this->authData[$this->authority]['saml:sp:SessionIndex'] = $sessionindex;
		} else {
			unset($this->authData[$this->authority]['saml:sp:SessionIndex']);
		}
	}


	/**
	 * Retrieve our SessionIndex.
	 *
	 * @return string|NULL  Our SessionIndex.
	 */
	public function getSessionIndex() {
		if (!isset($this->authData[$this->authority]['saml:sp:SessionIndex'])) {
			return NULL;
		}
		return $this->authData[$this->authority]['saml:sp:SessionIndex'];
	}


	/**
	 * Set our current NameID.
	 *
	 * @param array|NULL $nameid  The NameID we received from the IdP
	 */
	public function setNameID($nameid) {
		assert('is_array($nameid) || is_null($nameid)');
		assert('isset($this->authData[$this->authority])');

		SimpleSAML_Logger::debug('Library - Session: Set nameID: ');
		$this->dirty = true;
		if ($nameid !== NULL) {
			$this->authData[$this->authority]['saml:sp:NameID'] = $nameid;
		} else {
			unset($this->authData[$this->authority]['saml:sp:NameID']);
		}
	}


	/**
	 * Get our NameID.
	 *
	 * @return array|NULL The NameID we received from the IdP.
	 */
	public function getNameID() {
		if (!isset($this->authData[$this->authority]['saml:sp:NameID'])) {
			return NULL;
		}
		return $this->authData[$this->authority]['saml:sp:NameID'];
	}


	/**
	 * Marks the user as logged in with the specified authority.
	 *
	 * If the user already has logged in, the user will be logged out first.
	 *
	 * @param string $authority  The authority the user logged in with.
	 * @param array|NULL $data  The authentication data for this authority.
	 */
	public function doLogin($authority, array $data = NULL) {
		assert('is_string($authority)');
		assert('is_array($data) || is_null($data)');

		SimpleSAML_Logger::debug('Session: doLogin("' . $authority . '")');

		$this->dirty = TRUE;

		if (isset($this->authData[$authority])) {
			/* We are already logged in. Log the user out first. */
			$this->doLogout($authority);
		}


		if ($data === NULL) {
			$data = array();
		}

		$globalConfig = SimpleSAML_Configuration::getInstance();
		if (!isset($data['AuthnInstant'])) {
			$data['AuthnInstant'] = time();
		}

		$maxSessionExpire = time() + $globalConfig->getInteger('session.duration', 8*60*60);
		if (!isset($data['Expire']) || $data['Expire'] > $maxSessionExpire) {
			/* Unset, or beyond our session lifetime. Clamp it to our maximum session lifetime. */
			$data['Expire'] = $maxSessionExpire;
		}

		$this->authData[$authority] = $data;
		$this->authority = $authority;

		$this->authToken = SimpleSAML_Utilities::generateID();
		$sessionHandler = SimpleSAML_SessionHandler::getSessionHandler();
		$sessionHandler->setCookie($globalConfig->getString('session.authtoken.cookiename', 'SimpleSAMLAuthToken'), $this->authToken);
	}


	/**
	 * Marks the user as logged out.
	 *
	 * This function will call any registered logout handlers before marking the user as logged out.
	 *
	 * @param string|NULL $authority  The authentication source we are logging out of.
	 */
	public function doLogout($authority = NULL) {

		SimpleSAML_Logger::debug('Session: doLogout(' . var_export($authority, TRUE) . ')');

		if ($authority === NULL) {
			if ($this->authority === NULL) {
				SimpleSAML_Logger::debug('Session: No current authsource - not logging out.');
				return;
			}
			$authority = $this->authority;
		}

		if (!isset($this->authData[$authority])) {
			SimpleSAML_Logger::debug('Session: Already logged out of ' . $authority . '.');
			return;
		}

		$this->dirty = TRUE;

		$this->callLogoutHandlers($authority);
		unset($this->authData[$authority]);
		if ($this->authority === $authority) {
			$this->authority = NULL;
		}

		/* Delete data which expires on logout. */
		$this->expireDataLogout();
	}


	/**
	 * Set the lifetime of our current authentication session.
	 *
	 * @param int $duration  The number of seconds this authentication session is valid.
	 */
	public function setSessionDuration($duration) {
		assert('is_int($duration)');
		assert('isset($this->authData[$this->authority])');

		SimpleSAML_Logger::debug('Library - Session: Set session duration ' . $duration);
		$this->dirty = true;
		$this->sessionduration = $duration;

		$this->authData[$this->authority]['Expire'] = time() + $duration;
	}


	/**
	 * Is the session representing an authenticated user, and is the session still alive.
	 * This function will return false after the user has timed out.
	 *
	 * @param string $authority  The authentication source that the user should be authenticated with.
	 * @return TRUE if the user has a valid session, FALSE if not.
	 */
	public function isValid($authority) {
		assert('is_string($authority)');

		if (!isset($this->authData[$authority])) {
			SimpleSAML_Logger::debug('Session: '. var_export($authority, TRUE) .' not valid because we are not authenticated.');
			return FALSE;
		}

		if ($this->authData[$authority]['Expire'] <= time()) {
			SimpleSAML_Logger::debug('Session: ' . var_export($authority, TRUE) .' not valid because it is expired.');
			return FALSE;
		}

		SimpleSAML_Logger::debug('Session: Valid session found with ' . var_export($authority, TRUE) . '.');

		return TRUE;
	}


	/**
	 * If the user is authenticated, how much time is left of the session.
	 *
	 * @return int  The number of seconds until the session expires.
	 */
	public function remainingTime() {

		if (!isset($this->authData[$this->authority])) {
			/* Not authenticated. */
			return -1;
		}

		assert('isset($this->authData[$this->authority]["Expire"])');
		return $this->authData[$this->authority]['Expire'] - time();
	}

	/**
	 * Is the user authenticated. This function does not check the session duration.
	 *
	 * @return bool  TRUE if the user is authenticated, FALSE otherwise.
	 */
	public function isAuthenticated() {
		return isset($this->authData[$this->authority]);
	}


	/**
	 * Retrieve the time the user was authenticated.
	 *
	 * @return int|NULL  The timestamp for when the user was authenticated. NULL if the user hasn't authenticated.
	 */
	public function getAuthnInstant() {

		if (!isset($this->authData[$this->authority])) {
			/* Not authenticated. */
			return NULL;
		}

		assert('isset($this->authData[$this->authority]["AuthnInstant"])');
		return $this->authData[$this->authority]['AuthnInstant'];
	}


	/**
	 * Retrieve the attributes associated with this session.
	 *
	 * @return array|NULL  The attributes.
	 */
	public function getAttributes() {
		if (!isset($this->authData[$this->authority]['Attributes'])) {
			return NULL;
		}
		return $this->authData[$this->authority]['Attributes'];
	}


	/**
	 * Retrieve a single attribute.
	 *
	 * @param string $name  The name of the attribute.
	 * @return array|NULL  The values of the given attribute.
	 */
	public function getAttribute($name) {
		if (!isset($this->authData[$this->authority]['Attributes'][$name])) {
			return NULL;
		}
		return $this->authData[$this->authority]['Attributes'][$name];
	}


	/**
	 * Set the attributes for this session.
	 *
	 * @param array|NULL $attributes  The attributes of this session.
	 */
	public function setAttributes($attributes) {
		assert('isset($this->authData[$this->authority])');

		$this->dirty = true;
		$this->authData[$this->authority]['Attributes'] = $attributes;
	}


	/**
	 * Set the values of a single attribute.
	 *
	 * @param string $name  The name of the attribute.
	 * @param array $value  The values of the attribute.
	 */
	public function setAttribute($name, $value) {
		assert('isset($this->authData[$this->authority])');

		$this->dirty = true;
		$this->authData[$this->authority]['Attributes'][$name] = $value;
	}


	/**
	 * Calculates the size of the session object after serialization
	 *
	 * @return The size of the session measured in bytes.
	 */
	public function getSize() {
		$s = serialize($this);
		return strlen($s);
	}


	/**
	 * This function registers a logout handler.
	 *
	 * @param $classname  The class which contains the logout handler.
	 * @param $functionname  The logout handler function.
	 */
	public function registerLogoutHandler($classname, $functionname) {
		assert('isset($this->authData[$this->authority])');

		$logout_handler = array($classname, $functionname);

		if(!is_callable($logout_handler)) {
			throw new Exception('Logout handler is not a vaild function: ' . $classname . '::' .
				$functionname);
		}


		$this->authData[$this->authority]['LogoutHandlers'][] = $logout_handler;
		$this->dirty = TRUE;
	}


	/**
	 * This function calls all registered logout handlers.
	 *
	 * @param string $authority  The authentication source we are logging out from.
	 */
	private function callLogoutHandlers($authority) {
		assert('is_string($authority)');
		assert('isset($this->authData[$authority])');

		if (empty($this->authData[$authority]['LogoutHandlers'])) {
			return;
		}
		foreach($this->authData[$authority]['LogoutHandlers'] as $handler) {

			/* Verify that the logout handler is a valid function. */
			if(!is_callable($handler)) {
				$classname = $handler[0];
				$functionname = $handler[1];

				throw new Exception('Logout handler is not a vaild function: ' . $classname . '::' .
					$functionname);
			}

			/* Call the logout handler. */
			call_user_func($handler);

		}

		/* We require the logout handlers to register themselves again if they want to be called later. */
		unset($this->authData[$authority]['LogoutHandlers']);
	}


	/**
	 * This function removes expired data from the data store.
	 *
	 * Note that this function doesn't mark the session object as dirty. This means that
	 * if the only change to the session object is that some data has expired, it will not be
	 * written back to the session store.
	 */
	private function expireData() {

		if(!is_array($this->dataStore)) {
			return;
		}

		$ct = time();

		foreach($this->dataStore as &$typedData) {
			foreach($typedData as $id => $info) {
				if ($info['expires'] === self::DATA_TIMEOUT_LOGOUT) {
					/* This data only expires on logout. */
					continue;
				}

				if($ct > $info['expires']) {
					unset($typedData[$id]);
				}
			}
		}
	}


	/**
	 * This function deletes data which should be deleted on logout from the data store.
	 */
	private function expireDataLogout() {

		if(!is_array($this->dataStore)) {
			return;
		}

		$this->dirty = TRUE;

		foreach ($this->dataStore as &$typedData) {
			foreach ($typedData as $id => $info) {
				if ($info['expires'] === self::DATA_TIMEOUT_LOGOUT) {
					unset($typedData[$id]);
				}
			}
		}
	}


	/**
	 * Delete data from the data store.
	 *
	 * This function immediately deletes the data with the given type and id from the data store.
	 *
	 * @param string $type  The type of the data.
	 * @param string $id  The identifier of the data.
	 */
	public function deleteData($type, $id) {
		assert('is_string($type)');
		assert('is_string($id)');

		if (!is_array($this->dataStore)) {
			return;
		}

		if(!array_key_exists($type, $this->dataStore)) {
			return;
		}

		unset($this->dataStore[$type][$id]);
		$this->dirty = TRUE;
	}


	/**
	 * This function stores data in the data store.
	 *
	 * The timeout value can be SimpleSAML_Session::DATA_TIMEOUT_LOGOUT, which indicates
	 * that the data should be deleted on logout (and not before).
	 *
	 * @param $type     The type of the data. This is checked when retrieving data from the store.
	 * @param $id       The identifier of the data.
	 * @param $data     The data.
	 * @param $timeout  The number of seconds this data should be stored after its last access.
	 *                  This parameter is optional. The default value is set in 'session.datastore.timeout',
	 *                  and the default is 4 hours.
	 */
	public function setData($type, $id, $data, $timeout = NULL) {
		assert('is_string($type)');
		assert('is_string($id)');
		assert('is_int($timeout) || is_null($timeout) || $timeout === self::DATA_TIMEOUT_LOGOUT');

		/* Clean out old data. */
		$this->expireData();

		if($timeout === NULL) {
			/* Use the default timeout. */

			$configuration = SimpleSAML_Configuration::getInstance();

			$timeout = $configuration->getInteger('session.datastore.timeout', NULL);
			if($timeout !== NULL) {
				if ($timeout <= 0) {
					throw new Exception('The value of the session.datastore.timeout' .
						' configuration option should be a positive integer.');
				}
			} else {
				/* For backwards compatibility. */
				$timeout = $configuration->getInteger('session.requestcache', 4*(60*60));
				if ($timeout <= 0) {
					throw new Exception('The value of the session.requestcache' .
						' configuration option should be a positive integer.');
				}
			}
		}

		if ($timeout === self::DATA_TIMEOUT_LOGOUT) {
			$expires = self::DATA_TIMEOUT_LOGOUT;
		} else {
			$expires = time() + $timeout;
		}

		$dataInfo = array(
			'expires' => $expires,
			'timeout' => $timeout,
			'data' => $data
			);

		if(!is_array($this->dataStore)) {
			$this->dataStore = array();
		}

		if(!array_key_exists($type, $this->dataStore)) {
			$this->dataStore[$type] = array();
		}

		$this->dataStore[$type][$id] = $dataInfo;

		$this->dirty = TRUE;
	}


	/**
	 * This function retrieves data from the data store.
	 *
	 * Note that this will not change when the data stored in the data store will expire. If that is required,
	 * the data should be written back with setData.
	 *
	 * @param $type  The type of the data. This must match the type used when adding the data.
	 * @param $id    The identifier of the data. Can be NULL, in which case NULL will be returned.
	 * @return The data of the given type with the given id or NULL if the data doesn't exist in the data store.
	 */
	public function getData($type, $id) {
		assert('is_string($type)');
		assert('$id === NULL || is_string($id)');

		if($id === NULL) {
			return NULL;
		}

		$this->expireData();

		if(!is_array($this->dataStore)) {
			return NULL;
		}

		if(!array_key_exists($type, $this->dataStore)) {
			return NULL;
		}

		if(!array_key_exists($id, $this->dataStore[$type])) {
			return NULL;
		}

		return $this->dataStore[$type][$id]['data'];
	}


	/**
	 * This function retrieves all data of the specified type from the data store.
	 *
	 * The data will be returned as an associative array with the id of the data as the key, and the data
	 * as the value of each key. The value will be stored as a copy of the original data. setData must be
	 * used to update the data.
	 *
	 * An empty array will be returned if no data of the given type is found.
	 *
	 * @param $type  The type of the data.
	 * @return An associative array with all data of the given type.
	 */
	public function getDataOfType($type) {
		assert('is_string($type)');

		if(!is_array($this->dataStore)) {
			return array();
		}

		if(!array_key_exists($type, $this->dataStore)) {
			return array();
		}

		$ret = array();
		foreach($this->dataStore[$type] as $id => $info) {
			$ret[$id] = $info['data'];
		}

		return $ret;
	}

	/**
	 * Create a new session and cache it.
	 *
	 * @param string $sessionId  The new session we should create.
	 */
	public static function createSession($sessionId) {
		assert('is_string($sessionId)');
		self::$sessions[$sessionId] = NULL;
	}

	/**
	 * Load a session from the session handler.
	 *
	 * @param string|NULL $sessionId  The session we should load, or NULL to load the current session.
	 * @return The session which is stored in the session handler, or NULL if the session wasn't found.
	 */
	public static function getSession($sessionId = NULL) {
		assert('is_string($sessionId) || is_null($sessionId)');

		$sh = SimpleSAML_SessionHandler::getSessionHandler();

		if ($sessionId === NULL) {
			$checkToken = TRUE;
			$sessionId = $sh->getCookieSessionId();
		} else {
			$checkToken = FALSE;
		}

		if (array_key_exists($sessionId, self::$sessions)) {
			return self::$sessions[$sessionId];
		}


		$session = $sh->loadSession($sessionId);
		if($session === NULL) {
			return NULL;
		}

		assert('$session instanceof self');

		/* For backwardscompatibility. Remove after 1.7. */
		if ($session->sessionId === NULL) {
			$session->sessionId = $sh->getCookieSessionId();
		}

		if ($checkToken && $session->authToken !== NULL) {
			$globalConfig = SimpleSAML_Configuration::getInstance();
			$authTokenCookieName = $globalConfig->getString('session.authtoken.cookiename', 'SimpleSAMLAuthToken');
			if (!isset($_COOKIE[$authTokenCookieName])) {
				SimpleSAML_Logger::warning('Missing AuthToken cookie.');
				return NULL;
			}
			if ($_COOKIE[$authTokenCookieName] !== $session->authToken) {
				SimpleSAML_Logger::warning('Invalid AuthToken cookie.');
				return NULL;
			}
		}

		self::$sessions[$sessionId] = $session;

		return $session;
	}


	/**
	 * Save the session to the session handler.
	 *
	 * This function will check the dirty-flag to check if the session has changed.
	 */
	public function saveSession() {

		if(!$this->dirty) {
			/* Session hasn't changed - don't bother saving it. */
			return;
		}

		$this->dirty = FALSE;

		$sh = SimpleSAML_SessionHandler::getSessionHandler();

		try {
			$sh->saveSession($this);
		} catch (Exception $e) {
			if (!($e instanceof SimpleSAML_Error_Exception)) {
				$e = new SimpleSAML_Error_UnserializableException($e);
			}
			SimpleSAML_Logger::error('Unable to save session.');
			$e->logError();
		}
	}


	/**
	 * Add a shutdown function for saving this session object on exit.
	 */
	private function addShutdownFunction() {
		register_shutdown_function(array($this, 'saveSession'));
	}


	/**
	 * Set the logout state for this session.
	 *
	 * @param array $state  The state array.
	 */
	public function setLogoutState(array $state) {
		assert('isset($this->authData[$this->authority])');

		$this->dirty = TRUE;
		$this->authData[$this->authority]['LogoutState'] = $state;
	}


	/**
	 * Retrieve the logout state for this session.
	 *
	 * @return array  The logout state. If no logout state is set, an empty array will be returned.
	 */
	public function getLogoutState() {
		assert('isset($this->authData[$this->authority])');

		if (!isset($this->authData[$this->authority]['LogoutState'])) {
			return array();
		}

		return $this->authData[$this->authority]['LogoutState'];
	}


	/**
	 * Get the current persistent authentication state.
	 *
	 * @param string|NULL $authority  The authority to retrieve the data from.
	 * @return array  The current persistent authentication state, or NULL if not authenticated.
	 */
	public function getAuthState($authority = NULL) {
		assert('is_string($authority) || is_null($authority)');

		if ($authority === NULL) {
			$authority = $this->authority;
		}

		if (!isset($this->authData[$authority])) {
			return NULL;
		}

		return $this->authData[$authority];
	}


	/**
	 * Check whether the session cookie is set.
	 *
	 * This function will only return FALSE if is is certain that the cookie isn't set.
	 *
	 * @return bool  TRUE if it was set, FALSE if not.
	 */
	public function hasSessionCookie() {

		$sh = SimpleSAML_SessionHandler::getSessionHandler();
		return $sh->hasSessionCookie();
	}


	/**
	 * Add an SP association for an IdP.
	 *
	 * This function is only for use by the SimpleSAML_IdP class.
	 *
	 * @param string $idp  The IdP id.
	 * @param array $association  The association we should add.
	 */
	public function addAssociation($idp, array $association) {
		assert('is_string($idp)');
		assert('isset($association["id"])');
		assert('isset($association["Handler"])');

		if (!isset($this->associations)) {
			$this->associations = array();
		}

		if (!isset($this->associations[$idp])) {
			$this->associations[$idp] = array();
		}

		$this->associations[$idp][$association['id']] = $association;

		$this->dirty = TRUE;
	}


	/**
	 * Retrieve the associations for an IdP.
	 *
	 * This function is only for use by the SimpleSAML_IdP class.
	 *
	 * @param string $idp  The IdP id.
	 * @return array  The IdP associations.
	 */
	public function getAssociations($idp) {
		assert('is_string($idp)');

		if (substr($idp, 0, 6) === 'saml2:' && !empty($this->sp_at_idpsessions)) {
			/* Remove in 1.7. */
			$this->upgradeAssociations($idp);
		}

		if (!isset($this->associations)) {
			$this->associations = array();
		}

		if (!isset($this->associations[$idp])) {
			return array();
		}

		foreach ($this->associations[$idp] as $id => $assoc) {
			if (!isset($assoc['Expires'])) {
				continue;
			}
			if ($assoc['Expires'] >= time()) {
				continue;
			}

			unset($this->associations[$idp][$id]);
		}

		return $this->associations[$idp];
	}


	/**
	 * Remove an SP association for an IdP.
	 *
	 * This function is only for use by the SimpleSAML_IdP class.
	 *
	 * @param string $idp  The IdP id.
	 * @param string $associationId  The id of the association.
	 */
	public function terminateAssociation($idp, $associationId) {
		assert('is_string($idp)');
		assert('is_string($associationId)');

		if (substr($idp, 0, 6) === 'saml2:' && !empty($this->sp_at_idpsessions)) {
			/* Remove in 1.7. */
			$this->upgradeAssociations($idp);
		}

		if (!isset($this->associations)) {
			return;
		}

		if (!isset($this->associations[$idp])) {
			return;
		}

		unset($this->associations[$idp][$associationId]);

		$this->dirty = TRUE;
	}


	/**
	 * Get a list of associated SAML 2 SPs.
	 *
	 * This function is just for backwards-compatibility. New code should
	 * use the SimpleSAML_IdP::getAssociations()-function.
	 *
	 * @return array  Array of SAML 2 entitiyIDs.
	 * @deprecated  Will be removed in the future.
	 */
	public function get_sp_list() {

		$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();
		try {
			$idpEntityId = $metadata->getMetaDataCurrentEntityID('saml20-idp-hosted');
			$idp = SimpleSAML_IdP::getById('saml2:' . $idpEntityId);
		} catch (Exception $e) {
			/* No SAML 2 IdP configured? */
			return array();
		}

		$ret = array();
		foreach ($idp->getAssociations() as $assoc) {
			if (isset($assoc['saml:entityID'])) {
				$ret[] = $assoc['saml:entityID'];
			}
		}

		return $ret;
	}


	/**
	 * Retrieve authentication data.
	 *
	 * @param string $authority  The authentication source we should retrieve data from.
	 * @param string $name  The name of the data we should retrieve.
	 * @return mixed  The value, or NULL if the value wasn't found.
	 */
	public function getAuthData($authority, $name) {
		assert('is_string($authority)');
		assert('is_string($name)');

		if (!isset($this->authData[$authority][$name])) {
			return NULL;
		}
		return $this->authData[$authority][$name];
	}

}
