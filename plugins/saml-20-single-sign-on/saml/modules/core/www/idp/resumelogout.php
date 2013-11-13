<?php

if (!isset($_REQUEST['id'])) {
	throw new SimpleSAML_Error_BadRequest('Missing id-parameter.');
}
$id = (string)$_REQUEST['id'];

$state = SimpleSAML_Auth_State::loadState($id, 'core:Logout:afterbridge');
$idp = SimpleSAML_IdP::getByState($state);

$assocId = $state['core:TerminatedAssocId'];

$handler = $idp->getLogoutHandler();
$handler->startLogout($state, $assocId);
assert('FALSE');
