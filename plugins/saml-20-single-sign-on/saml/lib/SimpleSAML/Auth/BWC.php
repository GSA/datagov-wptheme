<?php

/**
 * Helper class for backwards compatibility with old-style authentication sources.
 *
 * Provides the same interface as Auth_Simple.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_Auth_BWC extends SimpleSAML_Auth_Simple {

	/**
	 * Our authentication handler.
	 *
	 * @var string
	 */
	private $auth;


	/**
	 * Our authority.
	 *
	 * @var string
	 */
	private $authority;


	/**
	 * Initialize a backwards-compatibility authsource for the given authentication page and authority.
	 *
	 * @param string $auth  The authentication page.
	 * @param string|NULL $authority  The authority we should validate the login against.
	 */
	public function __construct($auth, $authority) {
		assert('is_string($auth)');
		assert('is_string($authority) || is_null($authority)');

		if ($authority === NULL) {
			$candidates = array(
				'auth/login-admin.php' => 'login-admin',
				'auth/login-cas-ldap.php' => 'login-cas-ldap',
				'auth/login-ldapmulti.php' => 'login-ldapmulti',
				'auth/login-radius.php' => 'login-radius',
				'auth/login-tlsclient.php' => 'tlsclient',
				'auth/login-wayf-ldap.php' => 'login-wayf-ldap',
				'auth/login.php' => 'login',
			);
			if (!isset($candidates[$auth])) {
				throw new SimpleSAML_Error_Exception('You must provide an authority when using ' . $auth);
			}
			$authority = $candidates[$auth];
		}

		$this->auth = $auth;
		$this->authority = $authority;

		parent::__construct($authority);
	}


	/**
	 * Retrieve the implementing authentication source.
	 *
	 * @return NULL  There is never an authentication source behind this class.
	 */
	public function getAuthSource() {
		return NULL;
	}


	/**
	 * Start a login operation.
	 *
	 * @param array $params  Various options to the authentication request.
	 */
	public function login(array $params = array()) {

		if (array_key_exists('KeepPost', $params)) {
			$keepPost = (bool)$params['KeepPost'];
		} else {
			$keepPost = TRUE;
		}

		if (!isset($params['ReturnTo']) && !isset($params['ReturnCallback'])) {
			$params['ReturnTo'] = SimpleSAML_Utilities::selfURL();
		}

		if (isset($params['ReturnTo']) && $keepPost && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$params['ReturnTo'] = SimpleSAML_Utilities::createPostRedirectLink($params['ReturnTo'], $_POST);
		}

		$session = SimpleSAML_Session::getInstance();

		$authnRequest = array(
			'IsPassive' => isset($params['isPassive']) ? $params['isPassive'] : FALSE,
			'ForceAuthn' => isset($params['ForceAuthn']) ? $params['ForceAuthn'] : FALSE,
			'core:State' => $params,
			'core:prevSession' => $session->getAuthData($this->authority, 'AuthnInstant'),
			'core:authority' => $this->authority,
		);

		if (isset($params['saml:RequestId'])) {
			$authnRequest['RequestID'] = $params['saml:RequestId'];
		}
		if (isset($params['SPMetadata']['entityid'])) {
			$authnRequest['Issuer'] = $params['SPMetadata']['entityid'];
		}
		if (isset($params['saml:RelayState'])) {
			$authnRequest['RelayState'] = $params['saml:RelayState'];
		}
		if (isset($params['saml:IDPList'])) {
			$authnRequest['IDPList'] = $params['saml:IDPList'];
		}

		$authId = SimpleSAML_Utilities::generateID();
		$session->setAuthnRequest('saml2', $authId, $authnRequest);

		$relayState = SimpleSAML_Module::getModuleURL('core/bwc_resumeauth.php', array('RequestID' => $authId));

		$config = SimpleSAML_Configuration::getInstance();
		$authurl = '/' . $config->getBaseURL() . $this->auth;
		SimpleSAML_Utilities::redirect($authurl, array(
			'RelayState' => $relayState,
			'AuthId' => $authId,
			'protocol' => 'saml2',
		));
	}


	/**
	 * Start a logout operation.
	 *
	 * @param string|NULL $url  The url the user should be redirected to after logging out.
	 *                          Defaults to the current page.
	 */
	public function logout($url = NULL) {

		if ($url === NULL) {
			$url = SimpleSAML_Utilities::selfURL();
		}

		$session = SimpleSAML_Session::getInstance();
		if (!$session->isValid($this->authority)) {
			/* Not authenticated to this authentication source. */
			SimpleSAML_Utilities::redirect($url);
			assert('FALSE');
		}

		if ($this->authority === 'saml2') {
			$config = SimpleSAML_Configuration::getInstance();
			SimpleSAML_Utilities::redirect('/' . $config->getBaseURL() . 'saml2/sp/initSLO.php',
				array('RelayState' => $url)
			);
		}

		$session->doLogout($this->authority);

		SimpleSAML_Utilities::redirect($url);
	}

}
