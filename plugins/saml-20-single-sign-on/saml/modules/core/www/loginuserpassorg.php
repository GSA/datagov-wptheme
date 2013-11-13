<?php

/**
 * This page shows a username/password/organization login form, and passes information from
 * itto the sspmod_core_Auth_UserPassBase class, which is a generic class for
 * username/password/organization authentication.
 *
 * @author Olav Morken, UNINETT AS.
 * @package simpleSAMLphp
 * @version $Id$
 */

if (!array_key_exists('AuthState', $_REQUEST)) {
	throw new SimpleSAML_Error_BadRequest('Missing AuthState parameter.');
}
$authStateId = $_REQUEST['AuthState'];

/* Retrieve the authentication state. */
$state = SimpleSAML_Auth_State::loadState($authStateId, sspmod_core_Auth_UserPassOrgBase::STAGEID);

$source = SimpleSAML_Auth_Source::getById($state[sspmod_core_Auth_UserPassOrgBase::AUTHID]);
if ($source === NULL) {
	throw new Exception('Could not find authentication source with id ' . $state[sspmod_core_Auth_UserPassOrgBase::AUTHID]);
}

$organizations = sspmod_core_Auth_UserPassOrgBase::listOrganizations($authStateId);

if (array_key_exists('username', $_REQUEST)) {
	$username = $_REQUEST['username'];
} elseif ($source->getRememberUsernameEnabled() && array_key_exists($source->getAuthId() . '-username', $_COOKIE)) {
	$username = $_COOKIE[$source->getAuthId() . '-username'];
} elseif (isset($state['core:username'])) {
	$username = (string)$state['core:username'];
} else {
	$username = '';
}

if (array_key_exists('password', $_REQUEST)) {
	$password = $_REQUEST['password'];
} else {
	$password = '';
}

if (array_key_exists('organization', $_REQUEST)) {
	$organization = $_REQUEST['organization'];
} elseif (isset($state['core:organization'])) {
	$organization = (string)$state['core:organization'];
} else {
	$organization = '';
}

$errorCode = NULL;
if ($organizations === NULL || !empty($organization)) {
	if (!empty($username) && !empty($password)) {

		if ($source->getRememberUsernameEnabled()) {
			$sessionHandler = SimpleSAML_SessionHandler::getSessionHandler();
			$params = $sessionHandler->getCookieParams();
			$params['expire'] = time();
			$params['expire'] += (isset($_REQUEST['remember_username']) && $_REQUEST['remember_username'] == 'Yes' ? 31536000 : -300);
			setcookie($source->getAuthId() . '-username', $username, $params['expire'], $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		}

		$errorCode = sspmod_core_Auth_UserPassOrgBase::handleLogin($authStateId, $username, $password, $organization);
	}
}

$globalConfig = SimpleSAML_Configuration::getInstance();
$t = new SimpleSAML_XHTML_Template($globalConfig, 'core:loginuserpass.php');
$t->data['stateparams'] = array('AuthState' => $authStateId);
$t->data['username'] = $username;
$t->data['forceUsername'] = FALSE;
$t->data['rememberUsernameEnabled'] = $source->getRememberUsernameEnabled();
$t->data['rememberUsernameChecked'] = $source->getRememberUsernameChecked();
if (isset($_COOKIE[$source->getAuthId() . '-username'])) $t->data['rememberUsernameChecked'] = TRUE;
$t->data['errorcode'] = $errorCode;

if ($organizations !== NULL) {
	$t->data['selectedOrg'] = $organization;
	$t->data['organizations'] = $organizations;
}

if (isset($state['SPMetadata'])) {
	$t->data['SPMetadata'] = $state['SPMetadata'];
} else {
	$t->data['SPMetadata'] = NULL;
}

$t->show();
exit();


?>