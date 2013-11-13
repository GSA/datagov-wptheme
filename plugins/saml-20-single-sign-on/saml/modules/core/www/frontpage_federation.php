<?php



/* Load simpleSAMLphp, configuration */
$config = SimpleSAML_Configuration::getInstance();
$session = SimpleSAML_Session::getInstance();

/* Check if valid local session exists.. */
if ($config->getBoolean('admin.protectindexpage', false)) {
	SimpleSAML_Utilities::requireAdmin();
}
$loginurl = SimpleSAML_Utilities::getAdminLoginURL();
$isadmin = SimpleSAML_Utilities::isAdmin();




	
	
	
$links = array();
$links_welcome = array();
$links_config = array();
$links_auth = array();
$links_federation = array();




if($config->getBoolean('idpdisco.enableremember', FALSE)) {
	$links_federation[] = array(
		'href' => 'cleardiscochoices.php',
		'text' => '{core:frontpage:link_cleardiscochoices}',
	);
}


$links_federation[] = array(
	'href' => SimpleSAML_Utilities::getBaseURL() . 'admin/metadata-converter.php',
	'text' => '{core:frontpage:link_xmlconvert}',
);




$allLinks = array(
	'links'      => &$links,
	'welcome'    => &$links_welcome,
	'config'     => &$links_config,
	'auth'       => &$links_auth,
	'federation' => &$links_federation,
);
SimpleSAML_Module::callHooks('frontpage', $allLinks);


$metadataHosted = array();
SimpleSAML_Module::callHooks('metadata_hosted', $metadataHosted);









$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();

$metaentries = array('hosted' => $metadataHosted, 'remote' => array() );


if ($isadmin) {
	$metaentries['remote']['saml20-idp-remote'] = $metadata->getList('saml20-idp-remote');
	$metaentries['remote']['shib13-idp-remote'] = $metadata->getList('shib13-idp-remote');
}

if ($config->getBoolean('enable.saml20-sp', TRUE) === true) {
	try {
		$metaentries['hosted']['saml20-sp'] = $metadata->getMetaDataCurrent('saml20-sp-hosted');
		$metaentries['hosted']['saml20-sp']['deprecated'] = TRUE;
		$metaentries['hosted']['saml20-sp']['metadata-url'] = '/' . $config->getBaseURL() . 'saml2/sp/metadata.php?output=xhtml';
	} catch(Exception $e) {}
}
if ($config->getBoolean('enable.saml20-idp', FALSE) === true) {
	try {
		$metaentries['hosted']['saml20-idp'] = $metadata->getMetaDataCurrent('saml20-idp-hosted');
		$metaentries['hosted']['saml20-idp']['metadata-url'] = '/' . $config->getBaseURL() . 'saml2/idp/metadata.php?output=xhtml';
		if ($isadmin)
			$metaentries['remote']['saml20-sp-remote'] = $metadata->getList('saml20-sp-remote');
	} catch(Exception $e) {}
}
if ($config->getBoolean('enable.shib13-sp', FALSE) === true) {
	try {
		$metaentries['hosted']['shib13-sp'] = $metadata->getMetaDataCurrent('shib13-sp-hosted');
		$metaentries['hosted']['shib13-sp']['deprecated'] = TRUE;
		$metaentries['hosted']['shib13-sp']['metadata-url'] = '/' . $config->getBaseURL() . 'shib13/sp/metadata.php?output=xhtml';
	} catch(Exception $e) {}
}
if ($config->getBoolean('enable.shib13-idp', FALSE) === true) {
	try {
		$metaentries['hosted']['shib13-idp'] = $metadata->getMetaDataCurrent('shib13-idp-hosted');
		$metaentries['hosted']['shib13-idp']['metadata-url'] = '/' . $config->getBaseURL() . 'shib13/idp/metadata.php?output=xhtml';
		if ($isadmin)
			$metaentries['remote']['shib13-sp-remote'] = $metadata->getList('shib13-sp-remote');
	} catch(Exception $e) {}
}





$t = new SimpleSAML_XHTML_Template($config, 'core:frontpage_federation.tpl.php');
$t->data['pageid'] = 'frontpage_federation';
$t->data['isadmin'] = $isadmin;
$t->data['loginurl'] = $loginurl;


$t->data['links'] = $links;
$t->data['links_welcome'] = $links_welcome;
$t->data['links_config'] = $links_config;
$t->data['links_auth'] = $links_auth;
$t->data['links_federation'] = $links_federation;



$t->data['metaentries'] = $metaentries;


$t->show();

