<?php


require_once('../_include.php');

$config = SimpleSAML_Configuration::getInstance();
$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();
$session = SimpleSAML_Session::getInstance();

SimpleSAML_Logger::info('AUTH -admin: Accessing auth endpoint login-admin');

$error = null;
$attributes = array();
$username = null;

/* Load the RelayState argument. The RelayState argument contains the address
 * we should redirect the user to after a successful authentication.
 */
if (!array_key_exists('RelayState', $_REQUEST)) {
	throw new SimpleSAML_Error_Error('NORELAYSTATE');
}

$relaystate = $_REQUEST['RelayState'];

$correctpassword = $config->getString('auth.adminpassword', '123');

if (empty($correctpassword) or $correctpassword === '123') {
	throw new SimpleSAML_Error_Error('NOTSET');
}


if (isset($_POST['password'])) {

	/* Validate and sanitize form data. */

	if (SimpleSAML_Utils_Crypto::pwValid($correctpassword, $_POST['password'])) {
		$username = 'admin';
		$password = $_POST['password'];
	
	
		$attributes = array('user' => array('admin'));
	
		$session->doLogin('login-admin');
		$session->setAttributes($attributes);

		$session->setNameID(array(
			'value' => SimpleSAML_Utilities::generateID(),
			'Format' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient'));
		
		SimpleSAML_Logger::info('AUTH - admin: '. $username . ' successfully authenticated');

		/**
		 * Create a statistics log entry for every successfull login attempt.
		 * Also log a specific attribute as set in the config: statistics.authlogattr
		 */
		$authlogattr = $config->getValue('statistics.authlogattr', null);
		if ($authlogattr && array_key_exists($authlogattr, $attributes)) 
			SimpleSAML_Logger::stats('AUTH-login-admin OK ' . $attributes[$authlogattr][0]);
		else 
			SimpleSAML_Logger::stats('AUTH-login-admin OK');
		
		SimpleSAML_Utilities::redirect($relaystate);
		exit(0);
	} else {
		SimpleSAML_Logger::stats('AUTH-login-admin Failed');
		$error = 'error_wrongpassword';
		SimpleSAML_Logger::info($error);
	}
	
}


$t = new SimpleSAML_XHTML_Template($config, 'login.php', 'login');

$t->data['header'] = 'simpleSAMLphp: Enter username and password';	
$t->data['relaystate'] = $relaystate;
$t->data['admin'] = TRUE;
$t->data['autofocus'] = 'password';
$t->data['error'] = $error;
if (isset($error)) {
	$t->data['username'] = $username;
}

$t->show();


?>
