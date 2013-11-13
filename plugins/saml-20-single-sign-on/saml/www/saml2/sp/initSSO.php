<?php

require_once('../../_include.php');

$config = SimpleSAML_Configuration::getInstance();
$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();
$session = SimpleSAML_Session::getInstance();


SimpleSAML_Logger::info('SAML2.0 - SP.initSSO: Accessing SAML 2.0 SP initSSO script');

if (!$config->getBoolean('enable.saml20-sp', TRUE))
	throw new SimpleSAML_Error_Error('NOACCESS');

/*
 * Incomming URL parameters
 *
 * idpentityid 	optional	The entityid of the wanted IdP to authenticate with. If not provided will use default.
 * spentityid	optional	The entityid of the SP config to use. If not provided will use default to host.
 * RelayState	required	Where to send the user back to after authentication.
 */		

if (empty($_GET['RelayState'])) {
	throw new SimpleSAML_Error_Error('NORELAYSTATE');
}

$reachableIDPs = array();

try {

	$idpentityid = isset($_GET['idpentityid']) ? $_GET['idpentityid'] : $config->getString('default-saml20-idp', NULL) ;
	$spentityid = isset($_GET['spentityid']) ? $_GET['spentityid'] : $metadata->getMetaDataCurrentEntityID();

	$isPassive  = isset($_GET['IsPassive']) && ($_GET['IsPassive'] === 'true' || $_GET['IsPassive'] === '1');
	$forceAuthn = isset($_GET['ForceAuthn']) && ($_GET['ForceAuthn'] === 'true' || $_GET['ForceAuthn'] === '1');

	/* We are going to need the SP metadata to determine which IdP discovery service we should use.
	   And for checking for scoping parameters. */
	$spmetadata = $metadata->getMetaDataCurrent('saml20-sp-hosted');

	$IDPList = array();

	/* Configured idp overrides one given by Scope */
	if($idpentityid === NULL && array_key_exists('idpentityid', $spmetadata)) {
		$idpentityid = $spmetadata['idpentityid'];
	}

	/* AuthId is set if we are on the sp side on a proxy/bridge */
	$authid = isset($_GET['AuthId']) ? $_GET['AuthId'] : FALSE;
	if ($authid) {
		$authrequestcache = $session->getAuthnRequest('saml2', $authid);
		$isPassive  = $isPassive || $authrequestcache['IsPassive'];
		$forceAuthn = $forceAuthn || $authrequestcache['ForceAuthn'];

		/* keep the IDPList, it MUST be sent it to the next idp,
		   we are only allowed to add idps */
		if (isset($authrequestcache['IDPList']) && is_array($authrequestcache['IDPList'])) {
			$IDPList = $authrequestcache['IDPList'];
		}
		if ($idpentityid === NULL) {
			/* only consider ProviderIDs we know ... */
	
			$reachableIDPs = array_intersect($IDPList, array_keys($metadata->getList()));

			if (sizeof($reachableIDPs) === 1) {
				$idpentityid = array_shift($reachableIDPs);
			}
		}
	}
	

} catch (Exception $exception) {
	throw new SimpleSAML_Error_Error('METADATA', $exception);
}

/*
 * If no IdP can be resolved, send the user to the SAML 2.0 Discovery Service
 */
if ($idpentityid === NULL) {

	SimpleSAML_Logger::info('SAML2.0 - SP.initSSO: No chosen or default IdP, go to SAML2disco');

	/* Which IdP discovery service should we use? Can be set in SP metadata or in global configuration.
	 * Falling back to builtin discovery service.
	 */

	if(array_key_exists('idpdisco.url', $spmetadata)) {
		$discourl = $spmetadata['idpdisco.url'];
	} elseif($config->getString('idpdisco.url.saml20', NULL) !== NULL) {
		$discourl = $config->getString('idpdisco.url.saml20');
	} else {
		$discourl = SimpleSAML_Utilities::getBaseURL() . 'saml2/sp/idpdisco.php';
	}

	if ($config->getBoolean('idpdisco.extDiscoveryStorage', NULL) != NULL) {
		
		$extDiscoveryStorage = $config->getBoolean('idpdisco.extDiscoveryStorage');
		
		SimpleSAML_Utilities::redirect($extDiscoveryStorage, array(
			'entityID' => $spentityid,
			'return' => SimpleSAML_Utilities::addURLparameter($discourl, array(
				'return' => SimpleSAML_Utilities::selfURL(),
				'remember' => 'true',
				'entityID' => $spentityid,
				'returnIDParam' => 'idpentityid',
			)),
			'returnIDParam' => 'idpentityid',
			'isPassive' => 'true')
		);
	}

	$discoparameters = array(
		'entityID' => $spentityid,
		'return' => SimpleSAML_Utilities::selfURL(),
		'returnIDParam' => 'idpentityid');
		
	$discoparameters['isPassive'] = $isPassive;
	
	if (sizeof($reachableIDPs) > 0) {
		$discoparameters['IDPList'] = $reachableIDPs;
	}

	SimpleSAML_Utilities::redirect($discourl, $discoparameters);
}


/*
 * Create and send authentication request to the IdP.
 */
try {

	$spMetadata = $metadata->getMetaDataConfig($spentityid, 'saml20-sp-hosted');
	$idpMetadata = $metadata->getMetaDataConfig($idpentityid, 'saml20-idp-remote');

	$ar = sspmod_saml_Message::buildAuthnRequest($spMetadata, $idpMetadata);

	$assertionConsumerServiceURL = $metadata->getGenerated('AssertionConsumerService', 'saml20-sp-hosted');
	$ar->setAssertionConsumerServiceURL($assertionConsumerServiceURL);
	$ar->setRelayState($_REQUEST['RelayState']);

	if ($isPassive) {
		$ar->setIsPassive(TRUE);
	}
	if ($forceAuthn) {
		$ar->setForceAuthn(TRUE);
	}

	if(array_key_exists('IDPList', $spmetadata)) {
		$IDPList = array_unique(array_merge($IDPList, $spmetadata['IDPList']));
	}
	
	if (isset($_GET['IDPList']) && !empty($_GET['IDPList'])) {
		$providers = $_GET['IDPList'];
		if (!is_array($providers)) $providers = array($providers);
		$IDPList = array_merge($IDPList, $providers);
	};
	$ar->setIDPList($IDPList);

	/* Save request information. */
	$info = array();
	$info['RelayState'] = $_REQUEST['RelayState'];
	if(array_key_exists('OnError', $_REQUEST)) {
		$info['OnError'] = $_REQUEST['OnError'];
	}
	$session->setData('SAML2:SP:SSO:Info', $ar->getId(), $info);

	$b = new SAML2_HTTPRedirect();
	$b->send($ar);

} catch(Exception $exception) {
	throw new SimpleSAML_Error_Error('CREATEREQUEST', $exception);
}

?>