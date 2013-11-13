<?php

require_once('../../_include.php');

$config = SimpleSAML_Configuration::getInstance();
$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();


$session = SimpleSAML_Session::getInstance();
		

/*
 * Incomming URL parameters
 *
 * idpentityid 	optional	The entityid of the wanted IdP to authenticate with. If not provided will use default.
 * spentityid	optional	The entityid of the SP config to use. If not provided will use default to host.
 * RelayState	required	Where to send the user back to after authentication.
 *  
 */

SimpleSAML_Logger::info('Shib1.3 - SP.initSSO: Accessing Shib 1.3 SP initSSO script');

if (!$config->getBoolean('enable.shib13-sp', false))
	throw new SimpleSAML_Error_Error('NOACCESS');


try {

	$idpentityid = isset($_GET['idpentityid']) ? $_GET['idpentityid'] : $config->getString('default-shib13-idp', NULL) ;
	$spentityid = isset($_GET['spentityid']) ? $_GET['spentityid'] : $metadata->getMetaDataCurrentEntityID('shib13-sp-hosted');

	if($idpentityid === NULL) {
		/* We are going to need the SP metadata to determine which IdP discovery service we should use. */
		$spmetadata = $metadata->getMetaDataCurrent('shib13-sp-hosted');
	}


} catch (Exception $exception) {
	throw new SimpleSAML_Error_Error('METADATA', $exception);
}



if (!isset($session) || !$session->isValid('shib13') ) {
	
	if ($idpentityid == null) {
	
		SimpleSAML_Logger::info('Shib1.3 - SP.initSSO: No chosen or default IdP, go to Shib13disco');

		/* Which IdP discovery service should we use? Can be set in SP metadata or in global configuration.
		 * Falling back to builtin discovery service.
		 */
		if(array_key_exists('idpdisco.url', $spmetadata)) {
			$discservice = $spmetadata['idpdisco.url'];
		} elseif($config->getString('idpdisco.url.shib13', NULL) !== NULL) {
			$discservice = $config->getString('idpdisco.url.shib13');
		} else {
			$discservice = '/' . $config->getBaseURL() . 'shib13/sp/idpdisco.php';
		}

		SimpleSAML_Utilities::redirect($discservice, array(
			'entityID' => $spentityid,
			'return' => SimpleSAML_Utilities::selfURL(),
			'returnIDParam' => 'idpentityid',
			));
	}
	
	
	try {
		$ar = new SimpleSAML_XML_Shib13_AuthnRequest();
		$ar->setIssuer($spentityid);	
		if(isset($_GET['RelayState'])) 
			$ar->setRelayState($_GET['RelayState']);

		SimpleSAML_Logger::info('Shib1.3 - SP.initSSO: SP (' . $spentityid . ') is sending AuthNRequest to IdP (' . $idpentityid . ')');

		$url = $ar->createRedirect($idpentityid);
		SimpleSAML_Utilities::redirect($url);
	
	} catch(Exception $exception) {		
		throw new SimpleSAML_Error_Error('CREATEREQUEST', $exception);
	}

} else {

	
	$relaystate = $_GET['RelayState'];
	
	if (isset($relaystate) && !empty($relaystate)) {
		SimpleSAML_Logger::info('Shib1.3 - SP.initSSO: Already Authenticated, Go back to RelayState');
		SimpleSAML_Utilities::redirect($relaystate);
	} else {
		throw new SimpleSAML_Error_Error('NORELAYSTATE');
	}

}




?>