<?php

require_once('../../_include.php');

/* Load simpleSAMLphp, configuration and metadata */
$config = SimpleSAML_Configuration::getInstance();
$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();


if (!$config->getValue('enable.saml20-sp', TRUE))
	throw new SimpleSAML_Error_Error('NOACCESS');

/* Check if valid local session exists.. */
if ($config->getBoolean('admin.protectmetadata', false)) {
	SimpleSAML_Utilities::requireAdmin();
}

try {
	

	$spentityid = isset($_GET['spentityid']) ? $_GET['spentityid'] : $metadata->getMetaDataCurrentEntityID();
	$spmeta = $metadata->getMetaDataConfig($spentityid, 'saml20-sp-hosted');
	
	$metaArray = array(
		'metadata-set' => 'saml20-sp-remote',
		'entityid' => $spentityid,
		'AssertionConsumerService' => $metadata->getGenerated('AssertionConsumerService', 'saml20-sp-hosted'),
		'SingleLogoutService' => $metadata->getGenerated('SingleLogoutService', 'saml20-sp-hosted'),
	);

	$metaArray['NameIDFormat'] = $spmeta->getString('NameIDFormat', 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient');

	if ($spmeta->hasValue('OrganizationName')) {
		$metaArray['OrganizationName'] = $spmeta->getLocalizedString('OrganizationName');
		$metaArray['OrganizationDisplayName'] = $spmeta->getLocalizedString('OrganizationDisplayName', $metaArray['OrganizationName']);

		if (!$spmeta->hasValue('OrganizationURL')) {
			throw new SimpleSAML_Error_Exception('If OrganizationName is set, OrganizationURL must also be set.');
		}
		$metaArray['OrganizationURL'] = $spmeta->getLocalizedString('OrganizationURL');
	}


	if ($spmeta->hasValue('attributes')) {
		$metaArray['attributes'] = $spmeta->getArray('attributes');
	}
	if ($spmeta->hasValue('attributes.NameFormat')) {
		$metaArray['attributes.NameFormat'] = $spmeta->getString('attributes.NameFormat');
	}
	if ($spmeta->hasValue('name')) {
		$metaArray['name'] = $spmeta->getLocalizedString('name');
	}
	if ($spmeta->hasValue('description')) {
		$metaArray['description'] = $spmeta->getLocalizedString('description');
	}

	$certInfo = SimpleSAML_Utilities::loadPublicKey($spmeta);
	if ($certInfo !== NULL && array_key_exists('certData', $certInfo)) {
		$metaArray['certData'] = $certInfo['certData'];
	}

	$metaflat = '$metadata[' . var_export($spentityid, TRUE) . '] = ' . var_export($metaArray, TRUE) . ';';

	$metaBuilder = new SimpleSAML_Metadata_SAMLBuilder($spentityid);
	$metaBuilder->addMetadataSP20($metaArray);
	$metaBuilder->addOrganizationInfo($metaArray);
	$metaBuilder->addContact('technical', array(
		'emailAddress' => $config->getString('technicalcontact_email', NULL),
		'name' => $config->getString('technicalcontact_name', NULL),
		));
	$metaxml = $metaBuilder->getEntityDescriptorText();

	/* Sign the metadata if enabled. */
	$metaxml = SimpleSAML_Metadata_Signer::sign($metaxml, $spmeta->toArray(), 'SAML 2 SP');

	if (array_key_exists('output', $_REQUEST) && $_REQUEST['output'] == 'xhtml') {
		$t = new SimpleSAML_XHTML_Template($config, 'metadata.php', 'admin');
		$t->data['header'] = 'saml20-sp';
		$t->data['metadata'] = htmlspecialchars($metaxml);
		$t->data['metadataflat'] = htmlspecialchars($metaflat);
		$t->data['metaurl'] = SimpleSAML_Utilities::selfURLNoQuery();
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