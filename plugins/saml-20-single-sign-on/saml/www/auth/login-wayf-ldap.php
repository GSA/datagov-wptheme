<?php

/**
 * This file is part of SimpleSAMLphp. See the file COPYING in the
 * root of the distribution for licence information.
 *
 * This file implements authentication of users using CAS.
 *
 * @author Mads Freek, RUC. 
 * @package simpleSAMLphp
 * @version $Id$
 */
 
require_once('../_include.php');

$config = SimpleSAML_Configuration::getInstance();
$session = SimpleSAML_Session::getInstance();

try {
	$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();
	// TODO: Make this authentication module independent from SAML 2.0
	$idpentityid = $metadata->getMetaDataCurrentEntityID('saml20-idp-hosted');
	
	$ldapconfigfile = $config->getBaseDir() . 'config/cas-ldap.php';
	require_once($ldapconfigfile);
	
	if (!array_key_exists($idpentityid, $casldapconfig)) {
		throw new Exception('No LDAP authentication configuration for this SAML 2.0 entity ID [' . $idpentityid . ']');
	}

	$ldapconfig = $casldapconfig[$idpentityid]['ldap'];
	
} catch (Exception $exception) {
	throw new SimpleSAML_Error_Error('METADATA', $exception);
}

/*
 * Load the RelayState argument. The RelayState argument contains the address
 * we should redirect the user to after a successful authentication.
 */
if (!array_key_exists('RelayState', $_REQUEST)) {
	throw new SimpleSAML_Error_Error('NORELAYSTATE');
}

$relaystate = $_REQUEST['RelayState'];

if ($username = $_POST['username']) {
	try {
		$ldap = new SimpleSAML_Auth_LDAP($ldapconfig['servers'], $ldapconfig['enable_tls']);
			 
		$attributes = $ldap->validate($ldapconfig, $username, $_POST['password']);
		
		if ($attributes === FALSE) {
			$error = "LDAP_INVALID_CREDENTIALS";
		} else {
			$session->doLogin('login-wayf-ldap');
			$session->setAttributes($attributes);
			
			$session->setNameID(array(
					'value' => SimpleSAML_Utilities::generateID(),
					'Format' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient'));
			SimpleSAML_Utilities::redirect($relaystate);
		}
	} catch(Exception $e) {
			throw new SimpleSAML_Error_Error('LDAPERROR', $e);
	}
}

$t = new SimpleSAML_XHTML_Template($config, $ldapconfig['template']);

$t->data['header'] = 'simpleSAMLphp: Enter username and password';	
$t->data['relaystate'] = htmlspecialchars($relaystate);
$t->data['error'] = $error;
if (isset($error)) {
	$t->data['username'] = htmlspecialchars($username);
}

$t->show();

?>