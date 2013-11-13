<?php

/* TODO: Delete this file in version 1.8. */

if (!isset($_REQUEST['RequestID'])) {
	throw new SimpleSAML_Error_BadRequest('Missing required URL parameter.');
}

/* Backwards-compatibility with old authentication pages. */
$session = SimpleSAML_Session::getInstance();
$requestcache = $session->getAuthnRequest('saml2', (string)$_REQUEST['RequestID']);
if (!$requestcache) {
	throw new Exception('Could not retrieve cached RequestID = ' . $authId);
}

if ($requestcache['ForceAuthn'] && $requestcache['core:prevSession'] === $session->getAuthnInstant()) {
	throw new Exception('ForceAuthn set, but timestamp not updated.');
}

$state = $requestcache['State'];
SimpleSAML_IdP::postAuth($state);
