<?php

require_once('../../_include.php');

/* Load simpleSAMLphp, configuration and metadata */
$config = SimpleSAML_Configuration::getInstance();
$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();

if (!$config->getBoolean('enable.saml20-idp', false))
	throw new SimpleSAML_Error_Error('NOACCESS');

/* Check if valid local session exists.. */
if ($config->getBoolean('admin.protectmetadata', false)) {
	SimpleSAML_Utilities::requireAdmin();
}


try {
	$idpentityid = isset($_GET['idpentityid']) ? $_GET['idpentityid'] : $metadata->getMetaDataCurrentEntityID('saml20-idp-hosted');
	$idpmeta = $metadata->getMetaDataConfig($idpentityid, 'saml20-idp-hosted');

	$availableCerts = array();

	$keys = array();
	$certInfo = SimpleSAML_Utilities::loadPublicKey($idpmeta, FALSE, 'new_');
	if ($certInfo !== NULL) {
		$availableCerts['new_idp.crt'] = $certInfo;
		$keys[] = array(
			'type' => 'X509Certificate',
			'signing' => TRUE,
			'encryption' => TRUE,
			'X509Certificate' => $certInfo['certData'],
		);
		$hasNewCert = TRUE;
	} else {
		$hasNewCert = FALSE;
	}

	$certInfo = SimpleSAML_Utilities::loadPublicKey($idpmeta, TRUE);
	$availableCerts['idp.crt'] = $certInfo;
	$keys[] = array(
		'type' => 'X509Certificate',
		'signing' => TRUE,
		'encryption' => ($hasNewCert ? FALSE : TRUE),
		'X509Certificate' => $certInfo['certData'],
	);

	if ($idpmeta->hasValue('https.certificate')) {
		$httpsCert = SimpleSAML_Utilities::loadPublicKey($idpmeta, TRUE, 'https.');
		assert('isset($httpsCert["certData"])');
		$availableCerts['https.crt'] = $httpsCert;
		$keys[] = array(
			'type' => 'X509Certificate',
			'signing' => TRUE,
			'encryption' => FALSE,
			'X509Certificate' => $httpsCert['certData'],
		);
	}

	$metaArray = array(
		'metadata-set' => 'saml20-idp-remote',
		'entityid' => $idpentityid,
		'SingleSignOnService' => array(0 => array(
					'Binding' => SAML2_Const::BINDING_HTTP_REDIRECT,
					'Location' => $metadata->getGenerated('SingleSignOnService', 'saml20-idp-hosted'))),
		'SingleLogoutService' => $metadata->getGenerated('SingleLogoutService', 'saml20-idp-hosted'),
	);

	if (count($keys) === 1) {
		$metaArray['certData'] = $keys[0]['X509Certificate'];
	} else {
		$metaArray['keys'] = $keys;
	}

	if ($idpmeta->getBoolean('saml20.sendartifact', FALSE)) {
		/* Artifact sending enabled. */
		$metaArray['ArtifactResolutionService'][] = array(
			'index' => 0,
			'Location' => SimpleSAML_Utilities::getBaseURL() . 'saml2/idp/ArtifactResolutionService.php',
			'Binding' => SAML2_Const::BINDING_SOAP,
		);
	}

	if ($idpmeta->getBoolean('saml20.hok.assertion', FALSE)) {
		/* Prepend HoK SSO Service endpoint. */
		array_unshift($metaArray['SingleSignOnService'], array(
			'hoksso:ProtocolBinding' => SAML2_Const::BINDING_HTTP_REDIRECT,
			'Binding' => SAML2_Const::BINDING_HOK_SSO,
			'Location' => SimpleSAML_Utilities::getBaseURL() . 'saml2/idp/SSOService.php'));
	}

	$metaArray['NameIDFormat'] = $idpmeta->getString('NameIDFormat', 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient');

	if ($idpmeta->hasValue('OrganizationName')) {
		$metaArray['OrganizationName'] = $idpmeta->getLocalizedString('OrganizationName');
		$metaArray['OrganizationDisplayName'] = $idpmeta->getLocalizedString('OrganizationDisplayName', $metaArray['OrganizationName']);

		if (!$idpmeta->hasValue('OrganizationURL')) {
			throw new SimpleSAML_Error_Exception('If OrganizationName is set, OrganizationURL must also be set.');
		}
		$metaArray['OrganizationURL'] = $idpmeta->getLocalizedString('OrganizationURL');
	}

	if ($idpmeta->hasValue('scope')) {
		$metaArray['scope'] = $idpmeta->getArray('scope');
	}

	if ($idpmeta->hasValue('EntityAttributes')) {
		$metaArray['EntityAttributes'] = $idpmeta->getArray('EntityAttributes');
	}

	if ($idpmeta->hasValue('UIInfo')) {
		$metaArray['UIInfo'] = $idpmeta->getArray('UIInfo');
	}

	if ($idpmeta->hasValue('DiscoHints')) {
		$metaArray['DiscoHints'] = $idpmeta->getArray('DiscoHints');
	}

	$metaflat = '$metadata[' . var_export($idpentityid, TRUE) . '] = ' . var_export($metaArray, TRUE) . ';';

	$metaBuilder = new SimpleSAML_Metadata_SAMLBuilder($idpentityid);
	$metaBuilder->addMetadataIdP20($metaArray);
	$metaBuilder->addOrganizationInfo($metaArray);
	$technicalContactEmail = $config->getString('technicalcontact_email', NULL);
	if ($technicalContactEmail && $technicalContactEmail !== 'na@example.org') {
		$metaBuilder->addContact('technical', array(
			'emailAddress' => $technicalContactEmail,
			'name' => $config->getString('technicalcontact_name', NULL),
		));
	}
	$metaxml = $metaBuilder->getEntityDescriptorText();

	/* Sign the metadata if enabled. */
	$metaxml = SimpleSAML_Metadata_Signer::sign($metaxml, $idpmeta->toArray(), 'SAML 2 IdP');

	if (array_key_exists('output', $_GET) && $_GET['output'] == 'xhtml') {
		$defaultidp = $config->getString('default-saml20-idp', NULL);

		$t = new SimpleSAML_XHTML_Template($config, 'metadata.php', 'admin');

		$t->data['available_certs'] = $availableCerts;
		$t->data['header'] = 'saml20-idp';
		$t->data['metaurl'] = SimpleSAML_Utilities::selfURLNoQuery();
		$t->data['metadata'] = htmlspecialchars($metaxml);
		$t->data['metadataflat'] = htmlspecialchars($metaflat);
		$t->data['defaultidp'] = $defaultidp;
		$t->show();

	} else {

		header('Content-Type: application/xml');

		echo $metaxml;
		exit(0);

	}



} catch(Exception $exception) {

	throw new SimpleSAML_Error_Error('METADATA', $exception);

}

?>