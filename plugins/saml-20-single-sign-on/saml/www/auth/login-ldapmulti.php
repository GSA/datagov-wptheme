<?php


require_once('../_include.php');

$config = SimpleSAML_Configuration::getInstance();
$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();
$session = SimpleSAML_Session::getInstance();

$ldapconfigfile = $config->getBaseDir() . 'config/ldapmulti.php';
require_once($ldapconfigfile);


SimpleSAML_Logger::info('AUTH - ldap-multi: Accessing auth endpoint login-ldapmulti');

$error = null;
$attributes = array();

/* Load the RelayState argument. The RelayState argument contains the address
 * we should redirect the user to after a successful authentication.
 */
if (!array_key_exists('RelayState', $_REQUEST)) {
	throw new SimpleSAML_Error_Error('NORELAYSTATE');
}

if (isset($_POST['username'])) {

	try {
	
		$ldapconfig = $ldapmulti[$_POST['org']];

		if ($ldapconfig['search.enable'] === TRUE) {
			if(!$ldap->bind($ldapconfig['search.username'], $ldapconfig['search.password'])) {
				throw new Exception('Error authenticating using search username & password.');
			}
			$dn = $ldap->searchfordn($ldapconfig['search.base'], $ldapconfig['search.attributes'], $_POST['username']);
		} else {
			$dn = str_replace('%username%', $_POST['username'], $ldapconfig['dnpattern'] );
		}
		
		$pwd = $_POST['password'];
	
		$ldap = new SimpleSAML_Auth_LDAP($ldapconfig['hostname'], $ldapconfig['enable_tls']);
		
		if (($pwd == "") or (!$ldap->bind($dn, $pwd))) {
			SimpleSAML_Logger::info('AUTH - ldap-multi: '. $_POST['username'] . ' failed to authenticate. DN=' . $dn);
			throw new Exception('Wrong username or password');
		}
						
		$attributes = $ldap->getAttributes($dn, $ldapconfig['attributes']);
						
		SimpleSAML_Logger::info('AUTH - ldap-multi: '. $_POST['username'] . ' successfully authenticated');
				
				
		$session->doLogin('login-ldapmulti');
		$session->setAttributes($attributes);
				
		$session->setNameID(array(
			'value' => SimpleSAML_Utilities::generateID(),
			'Format' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient'));
				
		/**
		 * Create a statistics log entry for every successfull login attempt.
		 * Also log a specific attribute as set in the config: statistics.authlogattr
		 */
		$authlogattr = $config->getValue('statistics.authlogattr', null);
		if ($authlogattr && array_key_exists($authlogattr, $attributes)) 
			SimpleSAML_Logger::stats('AUTH-login-ldapmulti OK ' . $attributes[$authlogattr][0]);
		else 
			SimpleSAML_Logger::stats('AUTH-login-ldapmulti OK');
				
				
		$returnto = $_REQUEST['RelayState'];
		SimpleSAML_Utilities::redirect($returnto);

	} catch (Exception $e) {
		
		$error = $e->getMessage();
		
	}	
}


$t = new SimpleSAML_XHTML_Template($config, 'login-ldapmulti.php', 'login');

$t->data['header'] = 'simpleSAMLphp: Enter username and password';	
$t->data['relaystate'] = $_REQUEST['RelayState'];
$t->data['ldapconfig'] = $ldapmulti;
$t->data['org'] = $_REQUEST['org'];
$t->data['error'] = $error;
if (isset($error)) {
	$t->data['username'] = $_POST['username'];
}

$t->show();


?>
