<?php

require_once('../../_include.php');

$config = SimpleSAML_Configuration::getInstance();

$session = SimpleSAML_Session::getInstance();

SimpleSAML_Logger::info('WS-Fed - SP.initSLO: Accessing WS-Fed SP initSLO script');

if (!$config->getBoolean('enable.wsfed-sp', false))
	throw new SimpleSAML_Error_Error('NOACCESS');


if (isset($_REQUEST['RelayState'])) {
	$returnTo = $_REQUEST['RelayState'];
} else {
	throw new SimpleSAML_Error_Error('NORELAYSTATE');
}

	
if (isset($session) ) {
	
	try {
	
		$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();
	
		$idpentityid = $session->getAuthData('wsfed', 'saml:sp:IdP');
		$spentityid = isset($_GET['spentityid']) ? $_GET['spentityid'] : $metadata->getMetaDataCurrentEntityID();
	
		/**
		 * Create a logout request
		 */		
		
		$session->doLogout('wsfed');
		
		SimpleSAML_Logger::info('WS-Fed - SP.initSLO: SP (' . $spentityid . ') is sending logout request to IdP (' . $idpentityid . ')');
			
		$idpmeta = $metadata->getMetaData($idpentityid, 'wsfed-idp-remote');
		
		SimpleSAML_Utilities::redirect($idpmeta['prp'], array(
			'wa' => 'wsignout1.0',
			'wct' =>  gmdate('Y-m-d\TH:i:s\Z', time()),
			'wtrealm' => $spentityid,
			'wctx' => $returnTo
		));
		

	} catch(Exception $exception) {
		throw new SimpleSAML_Error_Error('CREATEREQUEST', $exception);
	}

} else {

	SimpleSAML_Logger::info('WS-Fed - SP.initSLO: User is already logged out. Go back to relaystate');
	SimpleSAML_Utilities::redirect($returnTo);
	
}


?>