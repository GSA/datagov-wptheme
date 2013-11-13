<?php

if (!array_key_exists('PATH_INFO', $_SERVER)) {
	throw new SimpleSAML_Error_BadRequest('Missing authentication source id in metadata URL');
}

$config = SimpleSAML_Configuration::getInstance();
$sourceId = substr($_SERVER['PATH_INFO'], 1);
$source = SimpleSAML_Auth_Source::getById($sourceId);
if ($source === NULL) {
	throw new SimpleSAML_Error_NotFound('Could not find authentication source with id ' . $sourceId);
}

if (!($source instanceof sspmod_saml_Auth_Source_SP)) {
	throw new SimpleSAML_Error_NotFound('Source isn\'t a SAML SP: ' . var_export($sourceId, TRUE));
}

$entityId = $source->getEntityId();
$spconfig = $source->getMetadata();

$metaArray20 = array(
	'AssertionConsumerService' => SimpleSAML_Module::getModuleURL('saml/sp/saml2-acs.php/' . $sourceId),
	'SingleLogoutService' => SimpleSAML_Module::getModuleURL('saml/sp/saml2-logout.php/' . $sourceId),
);

$ed = new SAML2_XML_md_EntityDescriptor();
$ed->entityID = $entityId;

$sp = new SAML2_XML_md_SPSSODescriptor();
$ed->RoleDescriptor[] = $sp;
$sp->protocolSupportEnumeration = array(
	'urn:oasis:names:tc:SAML:1.1:protocol',
	'urn:oasis:names:tc:SAML:2.0:protocol'
);

$slo = new SAML2_XML_md_EndpointType();
$slo->Binding = SAML2_Const::BINDING_HTTP_REDIRECT;
$slo->Location = SimpleSAML_Module::getModuleURL('saml/sp/saml2-logout.php/' . $sourceId);
$sp->SingleLogoutService[] = $slo;

$store = SimpleSAML_Store::getInstance();
if ($store instanceof SimpleSAML_Store_SQL) {
	/* We can properly support SOAP logout. */
	$slo = new SAML2_XML_md_EndpointType();
	$slo->Binding = SAML2_Const::BINDING_SOAP;
	$slo->Location = SimpleSAML_Module::getModuleURL('saml/sp/saml2-logout.php/' . $sourceId);
	$sp->SingleLogoutService[] = $slo;
}

$assertionsconsumerservicesdefault = array(
	'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
	'urn:oasis:names:tc:SAML:1.0:profiles:browser-post',
	'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact',
	'urn:oasis:names:tc:SAML:1.0:profiles:artifact-01',
	'urn:oasis:names:tc:SAML:2.0:profiles:holder-of-key:SSO:browser',
);

$assertionsconsumerservices = $spconfig->getArray('acs.Bindings', $assertionsconsumerservicesdefault);

$index = 0;
foreach ($assertionsconsumerservices as $services) {

	$acs = new SAML2_XML_md_IndexedEndpointType();
	$acs->index = $index;
	switch ($services) {
	case 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST':
		$acs->Binding = SAML2_Const::BINDING_HTTP_POST;
		$acs->Location = SimpleSAML_Module::getModuleURL('saml/sp/saml2-acs.php/' . $sourceId);
		break;
	case 'urn:oasis:names:tc:SAML:1.0:profiles:browser-post':
		$acs->Binding = 'urn:oasis:names:tc:SAML:1.0:profiles:browser-post';
		$acs->Location = SimpleSAML_Module::getModuleURL('saml/sp/saml1-acs.php/' . $sourceId);
		break;
	case 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact':
		$acs->Binding = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact';
		$acs->Location = SimpleSAML_Module::getModuleURL('saml/sp/saml2-acs.php/' . $sourceId);
		break;
	case 'urn:oasis:names:tc:SAML:1.0:profiles:artifact-01':
		$acs->Binding = 'urn:oasis:names:tc:SAML:1.0:profiles:artifact-01';
		$acs->Location = SimpleSAML_Module::getModuleURL('saml/sp/saml1-acs.php/' . $sourceId . '/artifact');
		break;
	case 'urn:oasis:names:tc:SAML:2.0:profiles:holder-of-key:SSO:browser':
		$acs->Binding = 'urn:oasis:names:tc:SAML:2.0:profiles:holder-of-key:SSO:browser';
		$acs->ProtocolBinding = SAML2_Const::BINDING_HTTP_POST;
		$acs->Location = SimpleSAML_Module::getModuleURL('saml/sp/saml2-acs.php/' . $sourceId);
		break;
	}
	$sp->AssertionConsumerService[] = $acs;
	$index++;
}


$keys = array();
$certInfo = SimpleSAML_Utilities::loadPublicKey($spconfig, FALSE, 'new_');
if ($certInfo !== NULL && array_key_exists('certData', $certInfo)) {
	$hasNewCert = TRUE;

	$certData = $certInfo['certData'];
	$kd = SAML2_Utils::createKeyDescriptor($certData);
	$kd->use = 'signing';
	$sp->KeyDescriptor[] = $kd;

	$kd = SAML2_Utils::createKeyDescriptor($certData);
	$kd->use = 'encryption';
	$sp->KeyDescriptor[] = $kd;

	$keys[] = array(
		'type' => 'X509Certificate',
		'signing' => TRUE,
		'encryption' => TRUE,
		'X509Certificate' => $certInfo['certData'],
	);
} else {
	$hasNewCert = FALSE;
}

$certInfo = SimpleSAML_Utilities::loadPublicKey($spconfig);
if ($certInfo !== NULL && array_key_exists('certData', $certInfo)) {
	$certData = $certInfo['certData'];
	$kd = SAML2_Utils::createKeyDescriptor($certData);
	$kd->use = 'signing';
	$sp->KeyDescriptor[] = $kd;

	if (!$hasNewCert) {
		/* Don't include the old certificate for encryption when we have a newer certificate. */
		$kd = SAML2_Utils::createKeyDescriptor($certData);
		$kd->use = 'encryption';
		$sp->KeyDescriptor[] = $kd;
	}

	$keys[] = array(
		'type' => 'X509Certificate',
		'signing' => TRUE,
		'encryption' => ($hasNewCert ? FALSE : TRUE),
		'X509Certificate' => $certInfo['certData'],
	);
} else {
	$certData = NULL;
}

$name = $spconfig->getLocalizedString('name', NULL);
$attributes = $spconfig->getArray('attributes', array());

if ($name !== NULL && !empty($attributes)) {

	$attributesrequired = $spconfig->getArray('attributes.required', array());

	/* We have everything necessary to add an AttributeConsumingService. */
	$acs = new SAML2_XML_md_AttributeConsumingService();
	$sp->AttributeConsumingService[] = $acs;

	$acs->index = 0;
	$acs->ServiceName = $name;

	$description = $spconfig->getLocalizedString('description', NULL);
	if ($description !== NULL) {
		$acs->ServiceDescription = $description;
	}

	$nameFormat = $spconfig->getString('attributes.NameFormat', NULL);
	foreach ($attributes as $attribute) {
		$a = new SAML2_XML_md_RequestedAttribute();
		$a->Name = $attribute;
		$a->NameFormat = $nameFormat;
		// Is the attribute required
		if (in_array($attribute, $attributesrequired))
			$a->isRequired = true;

		$acs->RequestedAttribute[] = $a;
	}

	$metaArray20['name'] = $name;
	if ($description !== NULL) {
		$metaArray20['description'] = $description;
	}

	$metaArray20['attributes'] = $attributes;
	if ($nameFormat !== NULL) {
		$metaArray20['attributes.NameFormat'] = $nameFormat;
	}
}


$orgName = $spconfig->getLocalizedString('OrganizationName', NULL);
if ($orgName !== NULL) {
	$o = new SAML2_XML_md_Organization();
	$o->OrganizationName = $orgName;

	$o->OrganizationDisplayName = $spconfig->getLocalizedString('OrganizationDisplayName', NULL);
	if ($o->OrganizationDisplayName === NULL) {
		$o->OrganizationDisplayName = $orgName;
	}

	$o->OrganizationURL = $spconfig->getLocalizedString('OrganizationURL', NULL);
	if ($o->OrganizationURL === NULL) {
		throw new SimpleSAML_Error_Exception('If OrganizationName is set, OrganizationURL must also be set.');
	}

	$ed->Organization = $o;

	$metaArray20['OrganizationName'] = $orgName;
	$metaArray20['OrganizationDisplayName'] = $o->OrganizationDisplayName;
	$metaArray20['OrganizationURL'] = $o->OrganizationURL;
}

$c = new SAML2_XML_md_ContactPerson();
$c->contactType = 'technical';

$email = $config->getString('technicalcontact_email', NULL);
if ($email !== NULL) {
	$c->EmailAddress = array($email);
}

$name = $config->getString('technicalcontact_name', NULL);
if ($name === NULL) {
	/* Nothing to do here... */
} elseif (preg_match('@^(.*?)\s*,\s*(.*)$@D', $name, $matches)) {
	$c->SurName = $matches[1];
	$c->GivenName = $matches[2];
} elseif (preg_match('@^(.*?)\s+(.*)$@D', $name, $matches)) {
	$c->GivenName = $matches[1];
	$c->SurName = $matches[2];
} else {
	$c->GivenName = $name;
}
$ed->ContactPerson[] = $c;

$xml = $ed->toXML();
SimpleSAML_Utilities::formatDOMElement($xml);
$xml = $xml->ownerDocument->saveXML($xml);

if (count($keys) === 1) {
	$metaArray20['certData'] = $keys[0]['X509Certificate'];
} elseif (count($keys) > 1) {
	$metaArray20['keys'] = $keys;
}

/* Sign the metadata if enabled. */
$xml = SimpleSAML_Metadata_Signer::sign($xml, $sp, 'SAML 2 SP');

if (array_key_exists('output', $_REQUEST) && $_REQUEST['output'] == 'xhtml') {

	$t = new SimpleSAML_XHTML_Template($config, 'metadata.php', 'admin');

	$t->data['header'] = 'saml20-sp';
	$t->data['metadata'] = htmlspecialchars($xml);
	$t->data['metadataflat'] = '$metadata[' . var_export($entityId, TRUE) . '] = ' . var_export($metaArray20, TRUE) . ';';
	$t->data['metaurl'] = $source->getMetadataURL();
	$t->show();
} else {
	header('HTTP/1.0 200 OK',true,200);
	header('Content-Type: text/xml');
	echo($xml);
}
?>
