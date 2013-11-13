<?php

/**
 * This page shows a username/password login form, and passes information from it
 * to the sspmod_core_Auth_UserPassBase class, which is a generic class for
 * username/password authentication.
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
$state = SimpleSAML_Auth_State::loadState($authStateId, sspmod_core_Auth_UserPassBase::STAGEID);


$source = SimpleSAML_Auth_Source::getById($state[sspmod_core_Auth_UserPassBase::AUTHID]);
if ($source === NULL) {
	throw new Exception('Could not find authentication source with id ' . $state[sspmod_core_Auth_UserPassBase::AUTHID]);
}


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

if (!empty($_REQUEST['username']) || !empty($password)) {
	/* Either username or password set - attempt to log in. */

	if (array_key_exists('forcedUsername', $state)) {
		$username = $state['forcedUsername'];
	}

	if ($source->getRememberUsernameEnabled()) {
		$sessionHandler = SimpleSAML_SessionHandler::getSessionHandler();
		$params = $sessionHandler->getCookieParams();
		$params['expire'] = time();
		$params['expire'] += (isset($_REQUEST['remember_username']) && $_REQUEST['remember_username'] == 'Yes' ? 31536000 : -300);
		setcookie($source->getAuthId() . '-username', $username, $params['expire'], $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	}

	$errorCode = sspmod_core_Auth_UserPassBase::handleLogin($authStateId, $username, $password);
} else {
	$errorCode = NULL;
}

$globalConfig = SimpleSAML_Configuration::getInstance();
$t = new SimpleSAML_XHTML_Template($globalConfig, 'core:loginuserpass.php');
$t->data['stateparams'] = array('AuthState' => $authStateId);
if (array_key_exists('forcedUsername', $state)) {
	$t->data['username'] = $state['forcedUsername'];
	$t->data['forceUsername'] = TRUE;
	$t->data['rememberUsernameEnabled'] = FALSE;
	$t->data['rememberUsernameChecked'] = FALSE;
} else {
	$t->data['username'] = $username;
	$t->data['forceUsername'] = FALSE;
	$t->data['rememberUsernameEnabled'] = $source->getRememberUsernameEnabled();
	$t->data['rememberUsernameChecked'] = $source->getRememberUsernameChecked();
	if (isset($_COOKIE[$source->getAuthId() . '-username'])) $t->data['rememberUsernameChecked'] = TRUE;
}
$t->data['links'] = $source->getLoginLinks();
$t->data['errorcode'] = $errorCode;

if (isset($state['SPMetadata'])) {
	$t->data['SPMetadata'] = $state['SPMetadata'];
} else {
	$t->data['SPMetadata'] = NULL;
}

$t->show();
exit();


?>