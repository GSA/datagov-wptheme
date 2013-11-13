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
		throw new Exception('No CAS authentication configuration for this SAML 2.0 entity ID [' . $idpentityid . ']');
	}

	$casconfig = $casldapconfig[$idpentityid]['cas'];
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



function casValidate($cas) {

	$service = SimpleSAML_Utilities::selfURL();
	$service = preg_replace("/(\?|&)?ticket=.*/", "", $service); # always tagged on by cas
	
	/**
	 * Got response from CAS server.
	 */
	if (isset($_GET['ticket'])) {
	
		$ticket = urlencode($_GET['ticket']);
	
		#ini_set('default_socket_timeout', 15);

		if (isset($cas['validate'])) { # cas v1 yes|no\r<username> style
			$paramPrefix = strpos($cas['validate'], '?') ? '&' : '?';
			$result = SimpleSAML_Utilities::fetch($cas['validate'] . $paramPrefix . 'ticket=' . $ticket . '&service=' . urlencode($service) );
			$res = preg_split("/\r?\n/",$result);
			
			if (strcmp($res[0], "yes") == 0) {
				return array($res[1], array());
			} else {
				throw new Exception("Failed to validate CAS service ticket: $ticket");
			}
		} elseif (isset($cas['serviceValidate'])) { # cas v2 xml style
			$paramPrefix = strpos($cas['serviceValidate'], '?') ? '&' : '?';

			$result = SimpleSAML_Utilities::fetch($cas['serviceValidate'] . $paramPrefix . 'ticket=' . $ticket . '&service=' . urlencode($service) );

			$dom = DOMDocument::loadXML($result);
			$xPath = new DOMXpath($dom);
			$xPath->registerNamespace("cas", 'http://www.yale.edu/tp/cas');
			$success = $xPath->query("/cas:serviceResponse/cas:authenticationSuccess/cas:user");
			if ($success->length == 0) {
				$failure = $xPath->evaluate("/cas:serviceResponse/cas:authenticationFailure");
				throw new Exception("Error when validating CAS service ticket: " . $failure->item(0)->textContent);
			} else {
				
				$attributes = array();
				if ($casattributes = $cas['attributes']) { # some has attributes in the xml - attributes is a list of XPath expressions to get them
					foreach ($casattributes as $name => $query) {
						$attrs = $xPath->query($query);
						foreach ($attrs as $attrvalue) $attributes[$name][] = $attrvalue->textContent;
					}
				}
				$casusername = $success->item(0)->textContent;
				
				return array($casusername, $attributes);
			}
		} else {
			throw new Exception("validate or serviceValidate not specified");
		}
	
	/**
	 * First request, will redirect the user to the CAS server for authentication.
	 */
	} else {
		SimpleSAML_Logger::info("AUTH - cas-ldap: redirecting to {$cas['login']}");
		SimpleSAML_Utilities::redirect($cas['login'], array(
			'service' => $service
		));		
	}
}



try {
	
	$relaystate = $_REQUEST['RelayState'];

	list($username, $casattributes) = casValidate($casconfig);
	
	SimpleSAML_Logger::info('AUTH - cas-ldap: '. $username . ' authenticated by ' . $casconfig['validate']);

	$ldapattributes = array();
	if ($ldapconfig['servers']) {
		$ldap = new SimpleSAML_Auth_LDAP($ldapconfig['servers'], $ldapconfig['enable_tls']);
		$ldapattributes = $ldap->validate($ldapconfig, $username);
	}
	$attributes = array_merge_recursive($casattributes, $ldapattributes);
	$session->doLogin('login-cas-ldap');
	$session->setAttributes($attributes);
	
	$session->setNameID(array(
			'value' => SimpleSAML_Utilities::generateID(),
			'Format' => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient'));
	SimpleSAML_Utilities::redirect($relaystate);

} catch(Exception $exception) {
	throw new SimpleSAML_Error_Error('CASERROR', $exception);
}


?>