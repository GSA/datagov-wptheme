<?php

require_once('../../_include.php');

/* Load simpleSAMLphp, configuration and metadata */
$config = SimpleSAML_Configuration::getInstance();
$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();

if (!$config->getBoolean('enable.shib13-idp', false))
	throw new SimpleSAML_Error_Error('NOACCESS');

/* Check if valid local session exists.. */
if ($config->getBoolean('admin.protectmetadata', false)) {
	SimpleSAML_Utilities::requireAdmin();
}


try {

	$idpentityid = isset($_GET['idpentityid']) ? $_GET['idpentityid'] : $metadata->getMetaDataCurrentEntityID('shib13-idp-hosted');
	$idpmeta = $metadata->getMetaDataConfig($idpentityid, 'shib13-idp-hosted');

	$keys = array();
	$certInfo = SimpleSAML_Utilities::loadPublicKey($idpmeta, FALSE, 'new_');
	if ($certInfo !== NULL) {
		$keys[] = array(
			'type' => 'X509Certificate',
			'signing' => TRUE,
			'encryption' => FALSE,
			'X509Certificate' => $certInfo['certData'],
		);
	}

	$certInfo = SimpleSAML_Utilities::loadPublicKey($idpmeta, TRUE);
	$keys[] = array(
		'type' => 'X509Certificate',
		'signing' => TRUE,
		'encryption' => FALSE,
		'X509Certificate' => $certInfo['certData'],
	);

	$metaArray = array(
		'metadata-set' => 'shib13-idp-remote',
		'entityid' => $idpentityid,
		'SingleSignOnService' => $metadata->getGenerated('SingleSignOnService', 'shib13-idp-hosted'),
	);

	if (count($keys) === 1) {
		$metaArray['certData'] = $keys[0]['X509Certificate'];
	} else {
		$metaArray['keys'] = $keys;
	}

	$metaArray['NameIDFormat'] = $idpmeta->getString('NameIDFormat', 'urn:mace:shibboleth:1.0:nameIdentifier');

	if ($idpmeta->hasValue('OrganizationName')) {
		$metaArray['OrganizationName'] = $idpmeta->getLocalizedString('OrganizationName');
		$metaArray['OrganizationDisplayName'] = $idpmeta->getLocalizedString('OrganizationDisplayName', $metaArray['OrganizationName']);

		if (!$idpmeta->hasValue('OrganizationURL')) {
			throw new SimpleSAML_Error_Exception('If OrganizationName is set, OrganizationURL must also be set.');
		}
		$metaArray['OrganizationURL'] = $idpmeta->getLocalizedString('OrganizationURL');
	}


	$metaflat = '$metadata[' . var_export($idpentityid, TRUE) . '] = ' . var_export($metaArray, TRUE) . ';';
	
	$metaBuilder = new SimpleSAML_Metadata_SAMLBuilder($idpentityid);
	$metaBuilder->addMetadataIdP11($metaArray);
	$metaBuilder->addOrganizationInfo($metaArray);
	$metaBuilder->addContact('technical', array(
		'emailAddress' => $config->getString('technicalcontact_email', NULL),
		'name' => $config->getString('technicalcontact_name', NULL),
		));
	$metaxml = $metaBuilder->getEntityDescriptorText();

	/* Sign the metadata if enabled. */
	$metaxml = SimpleSAML_Metadata_Signer::sign($metaxml, $idpmeta->toArray(), 'Shib 1.3 IdP');
	
	
	if (array_key_exists('output', $_GET) && $_GET['output'] == 'xhtml') {
		$defaultidp = $config->getString('default-shib13-idp', NULL);
		
		$t = new SimpleSAML_XHTML_Template($config, 'metadata.php', 'admin');
	
		$t->data['header'] = 'shib13-idp';
		
		$t->data['metaurl'] = SimpleSAML_Utilities::addURLparameter(SimpleSAML_Utilities::selfURLNoQuery(), array('output' => 'xml'));
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