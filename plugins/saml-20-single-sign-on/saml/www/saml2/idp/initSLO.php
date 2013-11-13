<?php

require_once('../../_include.php');

$config = SimpleSAML_Configuration::getInstance();
$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();

SimpleSAML_Logger::info('SAML2.0 - IdP.initSLO: Accessing SAML 2.0 IdP endpoint init Single Logout');

if (!$config->getBoolean('enable.saml20-idp', false)) {
	throw new SimpleSAML_Error_Error('NOACCESS');
}


if (!isset($_GET['RelayState'])) {
	throw new SimpleSAML_Error_Error('NORELAYSTATE');
}

$returnTo = $_GET['RelayState'];

$slo = $metadata->getGenerated('SingleLogoutService', 'saml20-idp-hosted');

/* We turn processing over to the SingleLogoutService script. */
SimpleSAML_Utilities::redirect($slo, array('ReturnTo' => $returnTo));

?>