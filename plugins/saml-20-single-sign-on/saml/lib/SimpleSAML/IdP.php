<?php

/**
 * IdP class.
 *
 * This class implements the various functions used by IdP.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_IdP {

	/**
	 * A cache for resolving IdP id's.
	 *
	 * @var array
	 */
	private static $idpCache = array();


	/**
	 * The identifier for this IdP.
	 *
	 * @var string
	 */
	private $id;


	/**
	 * The "association group" for this IdP.
	 *
	 * We use this to support cross-protocol logout until
	 * we implement a cross-protocol IdP.
	 *
	 * @var string
	 */
	private $associationGroup;


	/**
	 * The configuration for this IdP.
	 *
	 * @var SimpleSAML_Configuration
	 */
	private $config;


	/**
	 * Our authsource.
	 *
	 * @var SimpleSAML_Auth_Simple
	 */
	private $authSource;


	/**
	 * Initialize an IdP.
	 *
	 * @param string $id  The identifier of this IdP.
	 */
	private function __construct($id) {
		assert('is_string($id)');

		$this->id = $id;

		$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();
		$globalConfig = SimpleSAML_Configuration::getInstance();

		if (substr($id, 0, 6) === 'saml2:') {
			if (!$globalConfig->getBoolean('enable.saml20-idp', FALSE)) {
				throw new SimpleSAML_Error_Exception('enable.saml20-idp disabled in config.php.');
			}
			$this->config = $metadata->getMetaDataConfig(substr($id, 6), 'saml20-idp-hosted');
		} elseif (substr($id, 0, 6) === 'saml1:') {
			if (!$globalConfig->getBoolean('enable.shib13-idp', FALSE)) {
				throw new SimpleSAML_Error_Exception('enable.shib13-idp disabled in config.php.');
			}
			$this->config = $metadata->getMetaDataConfig(substr($id, 6), 'shib13-idp-hosted');
		} elseif (substr($id, 0, 5) === 'adfs:') {
			if (!$globalConfig->getBoolean('enable.adfs-idp', FALSE)) {
				throw new SimpleSAML_Error_Exception('enable.adfs-idp disabled in config.php.');
			}
			$this->config = $metadata->getMetaDataConfig(substr($id, 5), 'adfs-idp-hosted');

			try {
				/* This makes the ADFS IdP use the same SP associations as the SAML 2.0 IdP. */
				$saml2EntityId = $metadata->getMetaDataCurrentEntityID('saml20-idp-hosted');
				$this->associationGroup = 'saml2:' . $saml2EntityId;
								
			} catch (Exception $e) {
				/* Probably no SAML 2 IdP configured for this host. Ignore the error. */
			}
		} else {
			assert(FALSE);
		}

		if ($this->associationGroup === NULL) {
			$this->associationGroup = $this->id;
		}


		$auth = $this->config->getString('auth');
		if (SimpleSAML_Auth_Source::getById($auth) !== NULL) {
			$this->authSource = new SimpleSAML_Auth_Simple($auth);
		} else {
			$this->authSource = new SimpleSAML_Auth_BWC($auth, $this->config->getString('authority', NULL));
		}
	}


	/**
	 * Retrieve the ID of this IdP.
	 *
	 * @return string  The ID of this IdP.
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * Retrieve an IdP by ID.
	 *
	 * @param string $id  The identifier of the IdP.
	 * @return SimpleSAML_IdP  The IdP.
	 */
	public static function getById($id) {
		assert('is_string($id)');

		if (isset(self::$idpCache[$id])) {
			return self::$idpCache[$id];
		}

		$idp = new self($id);
		self::$idpCache[$id] = $idp;
		return $idp;
	}


	/**
	 * Retrieve the IdP "owning" the state.
	 *
	 * @param array &$state  The state array.
	 * @return SimpleSAML_IdP  The IdP.
	 */
	public static function getByState(array &$state) {
		assert('isset($state["core:IdP"])');

		return self::getById($state['core:IdP']);
	}


	/**
	 * Retrieve the configuration for this IdP.
	 *
	 * @return SimpleSAML_Configuration  The configuration object.
	 */
	public function getConfig() {

		return $this->config;
	}


	/**
	 * Get SP name.
	 *
	 * @param string $assocId  The association identifier.
	 * @return array|NULL  The name of the SP, as an associative array of language=>text, or NULL if this isn't an SP.
	 */
	public function getSPName($assocId) {
		assert('is_string($assocId)');

		$prefix = substr($assocId, 0, 4);
		$spEntityId = substr($assocId, strlen($prefix) + 1);
		$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();

		if ($prefix === 'saml') {
			try {
				$spMetadata = $metadata->getMetaDataConfig($spEntityId, 'saml20-sp-remote');
			} catch (Exception $e) {
				try {
					$spMetadata = $metadata->getMetaDataConfig($spEntityId, 'shib13-sp-remote');
				} catch (Exception $e) {
					return NULL;
				}
			}
		} else if ($prefix === 'adfs') {
			$spMetadata = $metadata->getMetaDataConfig($spEntityId, 'adfs-sp-remote');
		} else {
			return NULL;
		}

		if ($spMetadata->hasValue('name')) {
			return $spMetadata->getLocalizedString('name');
		} elseif ($spMetadata->hasValue('OrganizationDisplayName')) {
			return $spMetadata->getLocalizedString('OrganizationDisplayName');
		} else {
			return array('en' => $spEntityId);
		}
	}


	/**
	 * Add an SP association.
	 *
	 * @param array  The SP association.
	 */
	public function addAssociation(array $association) {
		assert('isset($association["id"])');
		assert('isset($association["Handler"])');

		$association['core:IdP'] = $this->id;
		
		$session = SimpleSAML_Session::getInstance();
		$session->addAssociation($this->associationGroup, $association);
	}


	/**
	 * Retrieve list of SP associations.
	 *
	 * @return array  List of SP associations.
	 */
	public function getAssociations() {

		$session = SimpleSAML_Session::getInstance();
		return $session->getAssociations($this->associationGroup);
	}


	/**
	 * Remove an SP association.
	 *
	 * @param string $assocId  The association id.
	 */
	public function terminateAssociation($assocId) {
		assert('is_string($assocId)');

		$session = SimpleSAML_Session::getInstance();
		$session->terminateAssociation($this->associationGroup, $assocId);
	}


	/**
	 * Is the current user authenticated?
	 *
	 * @return bool  TRUE if the user is authenticated, FALSE if not.
	 */
	public function isAuthenticated() {
		return $this->authSource->isAuthenticated();
	}


	/**
	 * Called after authproc has run.
	 *
	 * @param array $state  The authentication request state array.
	 */
	public static function postAuthProc(array $state) {
		assert('is_callable($state["Responder"])');

		if (isset($state['core:SP'])) {
			$session = SimpleSAML_Session::getInstance();
			$session->setData('core:idp-ssotime', $state['core:IdP'] . ';' . $state['core:SP'],
				time(), SimpleSAML_Session::DATA_TIMEOUT_LOGOUT);
		}

		call_user_func($state['Responder'], $state);
		assert('FALSE');
	}


	/**
	 * The user is authenticated.
	 *
	 * @param array $state  The authentication request state arrray.
	 */
	public static function postAuth(array $state) {

		$idp = SimpleSAML_IdP::getByState($state);

		if (!$idp->isAuthenticated()) {
			throw new SimpleSAML_Error_Exception('Not authenticated.');
		}

		$state['Attributes'] = $idp->authSource->getAttributes();

		if (isset($state['SPMetadata'])) {
			$spMetadata = $state['SPMetadata'];
		} else {
			$spMetadata = array();
		}

		if (isset($state['core:SP'])) {
			$session = SimpleSAML_Session::getInstance();
			$previousSSOTime = $session->getData('core:idp-ssotime', $state['core:IdP'] . ';' . $state['core:SP']);
			if ($previousSSOTime !== NULL) {
				$state['PreviousSSOTimestamp'] = $previousSSOTime;
			}
		}

		$idpMetadata = $idp->getConfig()->toArray();

		$pc = new SimpleSAML_Auth_ProcessingChain($idpMetadata, $spMetadata, 'idp');

		$state['ReturnCall'] = array('SimpleSAML_IdP', 'postAuthProc');
		$state['Destination'] = $spMetadata;
		$state['Source'] = $idpMetadata;

		$pc->processState($state);

		self::postAuthProc($state);
	}


	/**
	 * Authenticate the user.
	 *
	 * This function authenticates the user.
	 *
	 * @param array &$state  The authentication request state.
	 */
	private function authenticate(array &$state) {

		if (isset($state['isPassive']) && (bool)$state['isPassive']) {
			throw new SimpleSAML_Error_NoPassive('Passive authentication not supported.');
		}

		$this->authSource->login($state);
	}


	/**
	 * Reuthenticate the user.
	 *
	 * This function reauthenticates an user with an existing session. This
	 * gives the authentication source a chance to do additional work when
	 * reauthenticating for SSO.
	 *
	 * Note: This function is not used when ForceAuthn=true.
	 *
	 * @param array &$state  The authentication request state.
	 */
	private function reauthenticate(array &$state) {

		$sourceImpl = $this->authSource->getAuthSource();
		if ($sourceImpl === NULL) {
			/* Backwards-compatibility with non-authsource IdP. */
			foreach ($this->authSource->getAuthDataArray() as $k => $v) {
				$state[$k] = $v;
			}
			return;
		}

		$sourceImpl->reauthenticate($state);
	}


	/**
	 * Process authentication requests.
	 *
	 * @param array &$state  The authentication request state.
	 */
	public function handleAuthenticationRequest(array &$state) {
		assert('isset($state["Responder"])');

		$state['core:IdP'] = $this->id;

		if (isset($state['SPMetadata']['entityid'])) {
			$spEntityId = $state['SPMetadata']['entityid'];
		} elseif (isset($state['SPMetadata']['entityID'])) {
			$spEntityId = $state['SPMetadata']['entityID'];
		} else {
			$spEntityId = NULL;
		}
		$state['core:SP'] = $spEntityId;

		/* First, check whether we need to authenticate the user. */
		if (isset($state['ForceAuthn']) && (bool)$state['ForceAuthn']) {
			/* Force authentication is in effect. */
			$needAuth = TRUE;
		} elseif (isset($state['saml:IDPList']) && sizeof($state['saml:IDPList']) > 0) {
			$needAuth = !in_array($this->authSource->getAuthData('saml:sp:IdP'), $state['saml:IDPList'], TRUE);
		} else {
			$needAuth = !$this->isAuthenticated();
		}

		$state['IdPMetadata'] = $this->getConfig()->toArray();
		$state['ReturnCallback'] = array('SimpleSAML_IdP', 'postAuth');

		try {
			if ($needAuth) {
				$this->authenticate($state);
				assert('FALSE');
			} else {
				$this->reauthenticate($state);
			}
			$this->postAuth($state);
		} catch (SimpleSAML_Error_Exception $e) {
			SimpleSAML_Auth_State::throwException($state, $e);
		} catch (Exception $e) {
			$e = new SimpleSAML_Error_UnserializableException($e);
			SimpleSAML_Auth_State::throwException($state, $e);
		}
	}


	/**
	 * Find the logout handler of this IdP.
	 *
	 * @return string  The logout handler class.
	 */
	public function getLogoutHandler() {

		/* Find the logout handler. */
		$logouttype = $this->getConfig()->getString('logouttype', 'traditional');
		switch ($logouttype) {
		case 'traditional':
			$handler = 'SimpleSAML_IdP_LogoutTraditional';
			break;
		case 'iframe':
			$handler = 'SimpleSAML_IdP_LogoutIFrame';
			break;
		default:
			throw new SimpleSAML_Error_Exception('Unknown logout handler: ' . var_export($logouttype, TRUE));
		}

		return new $handler($this);

	}


	/**
	 * Finish the logout operation.
	 *
	 * This function will never return.
	 *
	 * @param array &$state  The logout request state.
	 */
	public function finishLogout(array &$state) {
		assert('isset($state["Responder"])');

		$idp = SimpleSAML_IdP::getByState($state);
		call_user_func($state['Responder'], $idp, $state);
		assert('FALSE');
	}


	/**
	 * Process a logout request.
	 *
	 * This function will never return.
	 *
	 * @param array &$state  The logout request state.
	 * @param string|NULL $assocId  The association we received the logout request from, or NULL if there was no association.
	 */
	public function handleLogoutRequest(array &$state, $assocId) {
		assert('isset($state["Responder"])');
		assert('is_string($assocId) || is_null($assocId)');

		$state['core:IdP'] = $this->id;
		$state['core:TerminatedAssocId'] = $assocId;

		if ($assocId !== NULL) {
			$this->terminateAssociation($assocId);
		}

		/* Terminate the local session. */
		$id = SimpleSAML_Auth_State::saveState($state, 'core:Logout:afterbridge');
		$returnTo = SimpleSAML_Module::getModuleURL('core/idp/resumelogout.php',
			array('id' => $id)
		);

		$this->authSource->logout($returnTo);

		$handler = $this->getLogoutHandler();
		$handler->startLogout($state, $assocId);
		assert('FALSE');
	}


	/**
	 * Process a logout response.
	 *
	 * This function will never return.
	 *
	 * @param string $assocId  The association that is terminated.
	 * @param string|NULL $relayState  The RelayState from the start of the logout.
	 * @param SimpleSAML_Error_Exception|NULL $error  The error that occurred during session termination (if any).
	 */
	public function handleLogoutResponse($assocId, $relayState, SimpleSAML_Error_Exception $error = NULL) {
		assert('is_string($assocId)');
		assert('is_string($relayState) || is_null($relayState)');

		$handler = $this->getLogoutHandler();
		$handler->onResponse($assocId, $relayState, $error);

		assert('FALSE');
	}


	/**
	 * Log out, then redirect to an URL.
	 *
	 * This function never returns.
	 *
	 * @param string $url  The URL the user should be returned to after logout.
	 */
	public function doLogoutRedirect($url) {
		assert('is_string($url)');

		$state = array(
			'Responder' => array('SimpleSAML_IdP', 'finishLogoutRedirect'),
			'core:Logout:URL' => $url,
		);

		$this->handleLogoutRequest($state, NULL);
		assert('FALSE');
	}


	/**
	 * Redirect to an URL after logout.
	 *
	 * This function never returns.
	 *
	 * @param array &$state  The logout state from doLogoutRedirect().
	 */
	public static function finishLogoutRedirect(SimpleSAML_IdP $idp, array $state) {
		assert('isset($state["core:Logout:URL"])');

		SimpleSAML_Utilities::redirect($state['core:Logout:URL']);
		assert('FALSE');
	}

}
