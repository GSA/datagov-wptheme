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


$warnings = array();

if (!SimpleSAML_Utilities::isHTTPS()) {
	$warnings[] = '{core:frontpage:warnings_https}';
}

if ($config->getValue('secretsalt') === 'defaultsecretsalt') {
	$warnings[] = '{core:frontpage:warnings_secretsalt}';
}

if (extension_loaded('suhosin')) {
	$suhosinLength = ini_get('suhosin.get.max_value_length');
	if (empty($suhosinLength) || (int)$suhosinLength < 2048) {
		$warnings[] = '{core:frontpage:warnings_suhosin_url_length}';
	}
}





$links = array();
$links_welcome = array();
$links_config = array();
$links_auth = array();
$links_federation = array();



$links_config[] = array(
	'href' => SimpleSAML_Utilities::getBaseURL() . 'example-simple/hostnames.php?dummy=1',
	'text' => '{core:frontpage:link_diagnostics}'
);

$links_config[] = array(
	'href' => SimpleSAML_Utilities::getBaseURL() . 'admin/phpinfo.php',
	'text' => '{core:frontpage:link_phpinfo}'
);





$allLinks = array(
	'links'      => &$links,
	'welcome'    => &$links_welcome,
	'config'     => &$links_config,
	'auth'       => &$links_auth,
	'federation' => &$links_federation,
);
SimpleSAML_Module::callHooks('frontpage', $allLinks);







$enablematrix = array(
	'saml20-idp' => $config->getBoolean('enable.saml20-idp', false),
	'shib13-idp' => $config->getBoolean('enable.shib13-idp', false),
);


$functionchecks = array(
	'hash'             => array('required',  'Hashing function'),
	'gzinflate'        => array('required',  'ZLib'),
	'openssl_sign'     => array('required',  'OpenSSL'),
	'simplexml_import_dom' => array('required', 'SimpleXML'),
	'dom_import_simplexml' => array('required', 'XML DOM'),
	'preg_match'       => array('required',  'RegEx support'),
	'mcrypt_module_open'=> array('optional',  'MCrypt'),
	'mysql_connect'    => array('optional',  'MySQL support'),
);
if (SimpleSAML_Module::isModuleEnabled('ldap')) {
	$functionchecks['ldap_bind'] = array('required_ldap',  'LDAP Extension');
}
if (SimpleSAML_Module::isModuleEnabled('radius')) {
        $functionchecks['radius_auth_open'] = array('required_radius',  'Radius Extension');
}

$funcmatrix = array();
$funcmatrix[] = array(
	'required' => 'required', 
	'descr' => 'PHP Version >= 5.2. You run: ' . phpversion(),
	'enabled' => version_compare(phpversion(), '5.2', '>='));
foreach ($functionchecks AS $func => $descr) {
	$funcmatrix[] = array('descr' => $descr[1], 'required' => $descr[0], 'enabled' => function_exists($func));
}


/* Some basic configuration checks */

if($config->getString('technicalcontact_email', 'na@example.org') === 'na@example.org') {
	$mail_ok = FALSE;
} else {
	$mail_ok = TRUE;
}
$funcmatrix[] = array(
	'required' => 'reccomended',
	'descr' => 'technicalcontact_email option set',
	'enabled' => $mail_ok
	);
if($config->getString('auth.adminpassword', '123') === '123') {
	$password_ok = FALSE;
} else {
	$password_ok = TRUE;
}
$funcmatrix[] = array(
	'required' => 'required',
	'descr' => 'auth.adminpassword option set',
	'enabled' => $password_ok
);

$funcmatrix[] = array(
	'required' => 'reccomended',
	'descr' => 'Magic Quotes should be turned off',
	'enabled' => (get_magic_quotes_runtime() === 0)
);


$t = new SimpleSAML_XHTML_Template($config, 'core:frontpage_config.tpl.php');
$t->data['pageid'] = 'frontpage_config';
$t->data['isadmin'] = $isadmin;
$t->data['loginurl'] = $loginurl;
$t->data['warnings'] = $warnings;


$t->data['links'] = $links;
$t->data['links_welcome'] = $links_welcome;
$t->data['links_config'] = $links_config;
$t->data['links_auth'] = $links_auth;
$t->data['links_federation'] = $links_federation;



$t->data['enablematrix'] = $enablematrix;
$t->data['funcmatrix'] = $funcmatrix;
$t->data['version'] = $config->getVersion();
$t->data['directory'] = dirname(dirname(dirname(dirname(__FILE__))));

$t->show();


