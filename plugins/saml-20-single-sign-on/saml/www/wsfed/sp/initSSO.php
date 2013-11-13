<?php
/**
 * WS-Federation/ADFS PRP protocol support for simpleSAMLphp.
 *
 * The initSSO handler relays an internal request from a simpleSAMLphp
 * Service Provider as a WS-Federation Resource Partner using the Passive
 * Requestor Profile (PRP) to an Account Partner.
 *
 * @author Hans Zandbelt, SURFnet BV. <hans.zandbelt@surfnet.nl>
 * @package simpleSAMLphp
 * @version $Id$
 */

require_once('../../_include.php');

$config = SimpleSAML_Configuration::getInstance();
$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();

SimpleSAML_Logger::info('WS-Fed - SP.initSSO: Accessing WS-Fed SP initSSO script');

if (!$config->getBoolean('enable.wsfed-sp', false))
	throw new SimpleSAML_Error_Error('NOACCESS');

if (empty($_GET['RelayState'])) {
	throw new SimpleSAML_Error_Error('NORELAYSTATE');
}

try {

	$idpentityid = isset($_GET['idpentityid']) ? $_GET['idpentityid'] : $config->getString('default-wsfed-idp', NULL);
	$spentityid = isset($_GET['spentityid']) ? $_GET['spentityid'] : $metadata->getMetaDataCurrentEntityID('wsfed-sp-hosted');

} catch (Exception $exception) {
	throw new SimpleSAML_Error_Error('METADATA', $exception);
}

if ($idpentityid == null) {

	SimpleSAML_Logger::info('WS-Fed - SP.initSSO: No chosen or default IdP, go to WSFeddisco');

	SimpleSAML_Utilities::redirect('/' . $config->getBaseURL() . 'wsfed/sp/idpdisco.php', array(
		'entityID' => $spentityid,
		'return' => SimpleSAML_Utilities::selfURL(),
		'returnIDParam' => 'idpentityid')
	);
}

try {
	$relaystate = $_GET['RelayState'];
	
	$idpmeta = $metadata->getMetaData($idpentityid, 'wsfed-idp-remote');
	$spmeta = $metadata->getMetaData($spentityid, 'wsfed-sp-hosted');

	SimpleSAML_Utilities::redirect($idpmeta['prp'], array(
		'wa' => 'wsignin1.0',
		'wct' =>  gmdate('Y-m-d\TH:i:s\Z', time()),
		'wtrealm' => $spentityid,
		'wctx' => $relaystate
		));
	
} catch (Exception $exception) {
	throw new SimpleSAML_Error_Error('CREATEREQUEST', $exception);
}

?>