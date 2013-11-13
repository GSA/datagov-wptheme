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


$links_auth[] = array(
	'href' => 'authenticate.php',
	'text' => '{core:frontpage:authtest}',
);


if ($config->getValue('enable.saml20-sp') === true)
	$links_auth[] = array(
		'href' => SimpleSAML_Utilities::getBaseURL() . 'example-simple/saml2-example.php',
		'text' => '{core:frontpage:link_saml2example}',
		'deprecated' => TRUE,
	);

if ($config->getValue('enable.shib13-sp') === true)
	$links_auth[] = array(
		'href' => SimpleSAML_Utilities::getBaseURL() . 'example-simple/shib13-example.php',
		'text' => '{core:frontpage:link_shib13example}',
		'deprecated' => TRUE,
	);


$allLinks = array(
	'links'      => &$links,
	'welcome'    => &$links_welcome,
	'config'     => &$links_config,
	'auth'       => &$links_auth,
	'federation' => &$links_federation,
);
SimpleSAML_Module::callHooks('frontpage', $allLinks);



$t = new SimpleSAML_XHTML_Template($config, 'core:frontpage_auth.tpl.php');
$t->data['pageid'] = 'frontpage_auth';
$t->data['isadmin'] = $isadmin;
$t->data['loginurl'] = $loginurl;

$t->data['links'] = $links;
$t->data['links_welcome'] = $links_welcome;
$t->data['links_config'] = $links_config;
$t->data['links_auth'] = $links_auth;
$t->data['links_federation'] = $links_federation;



$t->show();


