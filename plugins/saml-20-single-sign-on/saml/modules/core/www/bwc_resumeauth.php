<?php

if (!isset($_REQUEST['RequestID'])) {
	throw new SimpleSAML_Error_BadRequest('Missing required URL parameter.');
}

/* Backwards-compatibility with old authentication pages. */
$session = SimpleSAML_Session::getInstance();
$requestcache = $session->getAuthnRequest('saml2', (string)$_REQUEST['RequestID']);
if (!$requestcache) {
	throw new Exception('Could not retrieve cached RequestID = ' . $authId);
}

$authority = $requestcache['core:authority'];

$state = $requestcache['core:State'];

if ($requestcache['ForceAuthn'] && $requestcache['core:prevSession'] === $session->getAuthData($authority, 'AuthnInstant')) {
	throw new Exception('ForceAuthn set, but timestamp not updated.');
}

if (isset($state['ReturnTo'])) {
	SimpleSAML_Utilities::redirect($state['ReturnTo']);
}

foreach ($session->getAuthState($authority) as $k => $v) {
	$state[$k] = $v;
}

call_user_func($state['ReturnCallback'], $state);
assert('FALSE');
