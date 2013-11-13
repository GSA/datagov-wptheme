<?php

/**
 * This file implements an script which can be used to authenticate users with Auth MemCookie.
 * See: http://authmemcookie.sourceforge.net/
 *
 * The configuration for this script is stored in config/authmemcookie.php.
 *
 * The file extra/auth_memcookie.conf contains an example of how Auth Memcookie can be configured
 * to use simpleSAMLphp.
 */

require_once('_include.php');

try {
	/* Load simpleSAMLphp configuration. */
	$globalConfig = SimpleSAML_Configuration::getInstance();

	/* Check if this module is enabled. */
	if(!$globalConfig->getBoolean('enable.authmemcookie', FALSE)) {
		throw new SimpleSAML_Error_Error('NOACCESS');
	}

	/* Load Auth MemCookie configuration. */
	$amc = SimpleSAML_AuthMemCookie::getInstance();

	/* Determine the method we should use to authenticate the user and retrieve the attributes. */
	$loginMethod = $amc->getLoginMethod();
	switch($loginMethod) {
	case 'authsource':
		/* The default now. */
		$sourceId = $amc->getAuthSource();
		$s = new SimpleSAML_Auth_Simple($sourceId);
		break;
	case 'saml2':
		$s = new SimpleSAML_Auth_BWC('saml2/sp/initSSO.php', 'saml2');
		break;
	case 'shib13':
		$s = new SimpleSAML_Auth_BWC('shib13/sp/initSSO.php', 'shib13');
		break;
	default:
		/* Should never happen, as the login method is checked in the AuthMemCookie class. */
		throw new Exception('Invalid login method.');
	}

	/* Check if the user is authorized. We attempt to authenticate the user if not. */
	$s->requireAuth();

	/* Generate session id and save it in a cookie. */
	$sessionID = SimpleSAML_Utilities::generateID();

	$cookieName = $amc->getCookieName();

	$sessionHandler = SimpleSAML_SessionHandler::getSessionHandler();
	$sessionHandler->setCookie($cookieName, $sessionID);


	/* Generate the authentication information. */

	$attributes = $s->getAttributes();

	$authData = array();

	/* Username. */
	$usernameAttr = $amc->getUsernameAttr();
	if(!array_key_exists($usernameAttr, $attributes)) {
		throw new Exception('The user doesn\'t have an attribute named \'' . $usernameAttr .
			'\'. This attribute is expected to contain the username.');
	}
	$authData['UserName'] = $attributes[$usernameAttr];

	/* Groups. */
	$groupsAttr = $amc->getGroupsAttr();
	if($groupsAttr !== NULL) {
		if(!array_key_exists($groupsAttr, $attributes)) {
			throw new Exception('The user doesn\'t have an attribute named \'' . $groupsAttr .
				'\'. This attribute is expected to contain the groups the user is a member of.');
		}
		$authData['Groups'] = $attributes[$groupsAttr];
	} else {
		$authData['Groups'] = array();
	}

	$authData['RemoteIP'] = $_SERVER['REMOTE_ADDR'];

	foreach($attributes as $n => $v) {
		$authData['ATTR_' . $n] = $v;
	}


	/* Store the authentication data in the memcache server. */

	$data = '';
	foreach($authData as $n => $v) {
		if(is_array($v)) {
			$v = implode(':', $v);
		}

		$data .= $n . '=' . $v . "\r\n";
	}


	$memcache = $amc->getMemcache();
	$expirationTime = $s->getAuthData('Expire');
	$memcache->set($sessionID, $data, 0, $expirationTime);

	/* Register logout handler. */
	$session = SimpleSAML_Session::getInstance();
	$session->registerLogoutHandler('SimpleSAML_AuthMemCookie', 'logoutHandler');

	/* Redirect the user back to this page to signal that the login is completed. */
	SimpleSAML_Utilities::redirect(SimpleSAML_Utilities::selfURL());
} catch(Exception $e) {
	throw new SimpleSAML_Error_Error('CONFIG', $e);
}
