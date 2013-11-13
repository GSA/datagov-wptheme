<?php

/**
 * Session storage in the datastore.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_SessionHandlerStore extends SimpleSAML_SessionHandlerCookie {

	/**
	 * The datastore we save the session to.
	 */
	private $store;

	/**
	 * Initialize the session handlerstore.
	 */
	protected function __construct(SimpleSAML_Store $store) {
		parent::__construct();

		$this->store = $store;
	}


	/**
	 * Load the session from the datastore.
	 *
	 * @param string|NULL $sessionId  The ID of the session we should load, or NULL to use the default.
	 * @return SimpleSAML_Session|NULL  The session object, or NULL if it doesn't exist.
	 */
	public function loadSession($sessionId = NULL) {
		assert('is_string($sessionId) || is_null($sessionId)');

		if ($sessionId === NULL) {
			$sessionId = $this->getCookieSessionId();
		}

		$session = $this->store->get('session', $sessionId);
		if ($session !== NULL) {
			assert('$session instanceof SimpleSAML_Session');
			return $session;
		}

		if (!($this->store instanceof SimpleSAML_Store_Memcache)) {
			return NULL;
		}

		/* For backwards compatibility, check the MemcacheStore object. */
		$store = SimpleSAML_MemcacheStore::find($sessionId);
		if ($store === NULL) {
			return NULL;
		}

		$session = $store->get('SimpleSAMLphp_SESSION');
		if ($session === NULL) {
			return NULL;
		}

		assert('is_string($session)');

		$session = unserialize($session);
		assert('$session instanceof SimpleSAML_Session');

		return $session;
	}


	/**
	 * Save the current session to the datastore.
	 *
	 * @param SimpleSAML_Session $session  The session object we should save.
	 */
	public function saveSession(SimpleSAML_Session $session) {

		$sessionId = $session->getSessionId();

		$config = SimpleSAML_Configuration::getInstance();
		$sessionDuration = $config->getInteger('session.duration', 8*60*60);
		$expire = time() + $sessionDuration;

		$this->store->set('session', $sessionId, $session, $expire);
	}

}
