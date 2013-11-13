<?php

require_once('../../_include.php');

$config = SimpleSAML_Configuration::getInstance();
$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();

// Get the local session
$session = SimpleSAML_Session::getInstance();


SimpleSAML_Logger::info('SAML2.0 - SP.SingleLogoutService: Accessing SAML 2.0 SP endpoint SingleLogoutService');

if (!$config->getBoolean('enable.saml20-sp', TRUE))
	throw new SimpleSAML_Error_Error('NOACCESS');



// Destroy local session if exists.
$session->doLogout('saml2');

$binding = SAML2_Binding::getCurrentBinding();
$message = $binding->receive();

$idpEntityId = $message->getIssuer();
if ($idpEntityId === NULL) {
	/* Without an issuer we have no way to respond to the message. */
	throw new SimpleSAML_Error_BadRequest('Received message on logout endpoint without issuer.');
}

$spEntityId = $metadata->getMetaDataCurrentEntityId('saml20-sp-hosted');

$idpMetadata = $metadata->getMetaDataConfig($idpEntityId, 'saml20-idp-remote');
$spMetadata = $metadata->getMetaDataConfig($spEntityId, 'saml20-sp-hosted');

sspmod_saml_Message::validateMessage($idpMetadata, $spMetadata, $message);

if ($message instanceof SAML2_LogoutRequest) {

	try {
		// Extract some parameters from the logout request
		$requestid = $message->getId();

		SimpleSAML_Logger::info('SAML2.0 - SP.SingleLogoutService: IdP (' . $idpEntityId .
			') is sending logout request to me SP (' . $spEntityId . ') requestid '.$requestid);
		SimpleSAML_Logger::stats('saml20-idp-SLO idpinit ' . $spEntityId . ' ' . $idpEntityId);

		/* Create response. */
		$lr = sspmod_saml_Message::buildLogoutResponse($spMetadata, $idpMetadata);
		$lr->setRelayState($message->getRelayState());
		$lr->setInResponseTo($message->getId());

		SimpleSAML_Logger::info('SAML2.0 - SP.SingleLogoutService: SP me (' . $spEntityId . ') is sending logout response to IdP (' . $idpEntityId . ')');

		/* Send response. */
		$binding = new SAML2_HTTPRedirect();
		$binding->send($lr);
	} catch (Exception $exception) {
		throw new SimpleSAML_Error_Error('LOGOUTREQUEST', $exception);
	}

} elseif ($message instanceof SAML2_LogoutResponse) {

	SimpleSAML_Logger::stats('saml20-sp-SLO spinit ' . $spEntityId . ' ' . $idpEntityId);

	$id = $message->getRelayState();
	if (empty($id)) {
		/* For backwardscompatibility. */
		$id = $message->getInResponseTo();
	}

	$returnTo = $session->getData('spLogoutReturnTo', $id);
	if (empty($returnTo)) {
		throw new SimpleSAML_Error_Error('LOGOUTINFOLOST');
	}

	SimpleSAML_Utilities::redirect($returnTo);

} else {
	throw new SimpleSAML_Error_Error('SLOSERVICEPARAMS');
}



?>