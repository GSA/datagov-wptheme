<?php

require_once('../../_include.php');

/* Load simpleSAMLphp, configuration and metadata */
$config = SimpleSAML_Configuration::getInstance();
$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();


if (!$config->getBoolean('enable.shib13-sp', false))
	throw new SimpleSAML_Error_Error('NOACCESS');

/* Check if valid local session exists.. */
if ($config->getBoolean('admin.protectmetadata', false)) {
	SimpleSAML_Utilities::requireAdmin();
}


try {

	$spentityid = isset($_GET['spentityid']) ? $_GET['spentityid'] : $metadata->getMetaDataCurrentEntityID('shib13-sp-hosted');
	$spmeta = $metadata->getMetaDataConfig($spentityid, 'shib13-sp-hosted');

	$metaArray = array(
		'metadata-set' => 'shib13-sp-remote',
		'entityid' => $spentityid,
		'AssertionConsumerService' => $metadata->getGenerated('AssertionConsumerService', 'shib13-sp-hosted'),
	);

	$certInfo = SimpleSAML_Utilities::loadPublicKey($spmeta);
	if ($certInfo !== NULL && array_key_exists('certData', $certInfo)) {
		$metaArray['certData'] = $certInfo['certData'];
	}

	$metaArray['NameIDFormat'] = $spmeta->getString('NameIDFormat', 'urn:mace:shibboleth:1.0:nameIdentifier');

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


	$metaflat = '$metadata[' . var_export($spentityid, TRUE) . '] = ' . var_export($metaArray, TRUE) . ';';

	if ($spmeta->hasValue('certificate')) {
		$metaArray['certificate'] = $spmeta->getString('certificate');
	}
	$metaBuilder = new SimpleSAML_Metadata_SAMLBuilder($spentityid);
	$metaBuilder->addMetadataSP11($metaArray);
	$metaBuilder->addOrganizationInfo($metaArray);
	$metaBuilder->addContact('technical', array(
		'emailAddress' => $config->getString('technicalcontact_email', NULL),
		'name' => $config->getString('technicalcontact_name', NULL),
		));
	$metaxml = $metaBuilder->getEntityDescriptorText();

	/* Sign the metadata if enabled. */
	$metaxml = SimpleSAML_Metadata_Signer::sign($metaxml, $spmeta->toArray(), 'Shib 1.3 SP');

	if (array_key_exists('output', $_GET) && $_GET['output'] == 'xhtml') {
		$t = new SimpleSAML_XHTML_Template($config, 'metadata.php', 'admin');
		$t->data['header'] = 'shib13-sp';
		$t->data['metadata'] = htmlspecialchars($metaxml);
		$t->data['metadataflat'] = htmlspecialchars($metaflat);
		$t->data['metaurl'] = SimpleSAML_Utilities::addURLparameter(SimpleSAML_Utilities::selfURLNoQuery(), array('output' => 'xml'));
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