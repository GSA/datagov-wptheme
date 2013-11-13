<?php
/**
 * WS-Federation/ADFS PRP protocol support for simpleSAMLphp.
 *
 * The AssertionConsumerService handler accepts responses from a WS-Federation
 * Account Partner using the Passive Requestor Profile (PRP) and handles it as
 * a Resource Partner.  It receives a response, parses it and passes on the
 * authentication+attributes.
 *
 * @author Hans Zandbelt, SURFnet BV. <hans.zandbelt@surfnet.nl>
 * @package simpleSAMLphp
 * @version $Id$
 */

require_once('../../_include.php');

$config = SimpleSAML_Configuration::getInstance();
$session = SimpleSAML_Session::getInstance();
$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();

SimpleSAML_Logger::info('WS-Fed - SP.AssertionConsumerService: Accessing WS-Fed SP endpoint AssertionConsumerService');

if (!$config->getBoolean('enable.wsfed-sp', false))
	throw new SimpleSAML_Error_Error('NOACCESS');

if (!empty($_GET['wa']) and ($_GET['wa'] == 'wsignoutcleanup1.0')) {
	print 'Logged Out';
	exit;
}

/* Make sure that the correct query parameters are passed to this script. */
try {
	if (empty($_POST['wresult'])) {
		throw new Exception('Missing wresult parameter');
	}
	if (empty($_POST['wa'])) {
		throw new Exception('Missing wa parameter');
	}
	if (empty($_POST['wctx'])) {
		throw new Exception('Missing wctx parameter');
	}
} catch(Exception $exception) {
	throw new SimpleSAML_Error_Error('ACSPARAMS', $exception);
}


try {

	$wa = $_POST['wa'];
	$wresult = $_POST['wresult'];
	$wctx = $_POST['wctx'];

	/* Load and parse the XML. */
	$dom = new DOMDocument();
	/* Accommodate for MS-ADFS escaped quotes */
	$wresult = str_replace('\"', '"', $wresult);
	$dom->loadXML(str_replace ("\r", "", $wresult));	

	$xpath = new DOMXpath($dom);
	$xpath->registerNamespace('wst', 'http://schemas.xmlsoap.org/ws/2005/02/trust');
	$xpath->registerNamespace('saml', 'urn:oasis:names:tc:SAML:1.0:assertion');

	/* Find the saml:Assertion element in the response. */
	$assertions = $xpath->query('/wst:RequestSecurityTokenResponse/wst:RequestedSecurityToken/saml:Assertion');
	if ($assertions->length === 0) {
		throw new Exception('Received a response without an assertion on the WS-Fed PRP handler.');
	}
	if ($assertions->length > 1) {
		throw new Exception('The WS-Fed PRP handler currently only supports a single assertion in a response.');
	}
	$assertion = $assertions->item(0);

	/* Find the entity id of the issuer. */
	$idpEntityId = $assertion->getAttribute('Issuer');

	/* Load the IdP metadata. */
	$idpMetadata = $metadata->getMetaData($idpEntityId, 'wsfed-idp-remote');

	/* Find the certificate used by the IdP. */
	if(array_key_exists('certificate', $idpMetadata)) {
		$certFile = SimpleSAML_Utilities::resolveCert($idpMetadata['certificate']);
	} else {
		throw new Exception('Missing \'certificate\' metadata option in the \'wsfed-idp-remote\' metadata' .
			' for the IdP \'' .  $idpEntityId . '\'.');
	}

	/* Load the certificate. */
	$certData = file_get_contents($certFile);
	if($certData === FALSE) {
		throw new Exception('Unable to load certificate file \'' . $certFile . '\' for wsfed-idp \'' .
			$idpEntityId . '\'.');
	}

	/* Verify that the assertion is signed by the issuer. */
	$validator = new SimpleSAML_XML_Validator($assertion, 'AssertionID', $certData);
	if(!$validator->isNodeValidated($assertion)) {
		throw new Exception('The assertion was not correctly signed by the WS-Fed IdP \'' .
			$idpEntityId . '\'.');
	}

	/* Check time constraints of contitions (if present). */
	foreach($xpath->query('./saml:Conditions', $assertion) as $condition) {
		$notBefore = $condition->getAttribute('NotBefore');
		$notOnOrAfter = $condition->getAttribute('NotOnOrAfter');
		if(!SimpleSAML_Utilities::checkDateConditions($notBefore, $notOnOrAfter)) {
			throw new Exception('The response has expired.');
		}
	}


	/* Extract the name identifier from the response. */
	$nameid = $xpath->query('./saml:AuthenticationStatement/saml:Subject/saml:NameIdentifier', $assertion);
	if ($nameid->length === 0) {
		throw new Exception('Could not find the name identifier in the response from the WS-Fed IdP \'' .
			$idpEntityId . '\'.');
	}
	$nameid = array(
		'Format' => $nameid->item(0)->getAttribute('Format'),
		'Value' => $nameid->item(0)->textContent,
		);


	/* Extract the attributes from the response. */
	$attributes = array();
	$attributeValues = $xpath->query('./saml:AttributeStatement/saml:Attribute/saml:AttributeValue', $assertion);
	foreach($attributeValues as $attribute) {
		$name = $attribute->parentNode->getAttribute('AttributeName');
		$value = $attribute->textContent;
		if(!array_key_exists($name, $attributes)) {
			$attributes[$name] = array();
		}
		$attributes[$name][] = $value;
	}


	/* Mark the user as logged in. */
	$authData = array(
		'Attributes' => $attributes,
		'saml:sp:NameID' => $nameid,
		'saml:sp:IdP' => $idpEntityId,
	);
	$session->doLogin('wsfed', $authData);

	/* Redirect the user back to the page which requested the login. */
	SimpleSAML_Utilities::redirect($wctx);

} catch(Exception $exception) {		
	throw new SimpleSAML_Error_Error('PROCESSASSERTION', $exception);
}

?>