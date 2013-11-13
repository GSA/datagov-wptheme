<?php

/**
 * Implementation of the Shibboleth 1.3 HTTP-POST binding.
 *
 * @author Andreas Åkre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @package simpleSAMLphp
 * @version $Id: HTTPPost.php 2542 2010-09-10 13:14:09Z olavmrk $
 */
class SimpleSAML_Bindings_Shib13_HTTPPost {

	private $configuration = null;
	private $metadata = null;

	function __construct(SimpleSAML_Configuration $configuration, SimpleSAML_Metadata_MetaDataStorageHandler $metadatastore) {
		$this->configuration = $configuration;
		$this->metadata = $metadatastore;
	}

	/**
	 * Send an authenticationResponse using HTTP-POST.
	 *
	 * @param string $response  The response which should be sent.
	 * @param SimpleSAML_Configuration $idpmd  The metadata of the IdP which is sending the response.
	 * @param SimpleSAML_Configuration $spmd  The metadata of the SP which is receiving the response.
	 * @param string|NULL $relayState  The relaystate for the SP.
	 * @param string $shire  The shire which should receive the response.
	 */
	public function sendResponse($response, SimpleSAML_Configuration $idpmd, SimpleSAML_Configuration $spmd, $relayState, $shire) {

		SimpleSAML_Utilities::validateXMLDocument($response, 'saml11');

		$privatekey = SimpleSAML_Utilities::loadPrivateKey($idpmd, TRUE);
		$publickey = SimpleSAML_Utilities::loadPublicKey($idpmd, TRUE);

		$responsedom = new DOMDocument();
		$responsedom->loadXML(str_replace ("\r", "", $response));

		$responseroot = $responsedom->getElementsByTagName('Response')->item(0);
		$firstassertionroot = $responsedom->getElementsByTagName('Assertion')->item(0);

		/* Determine what we should sign - either the Response element or the Assertion. The default
		 * is to sign the Assertion, but that can be overridden by the 'signresponse' option in the
		 * SP metadata or 'saml20.signresponse' in the global configuration.
		 */
		$signResponse = FALSE;
		if ($spmd->hasValue('signresponse')) {
			$signResponse = $spmd->getBoolean['signresponse'];
		} else {
			$signResponse = $this->configuration->getBoolean('shib13.signresponse', TRUE);
		}

		/* Check if we have an assertion to sign. Force to sign the response if not. */
		if ($firstassertionroot === NULL) {
			$signResponse = TRUE;
		}

		$signer = new SimpleSAML_XML_Signer(array(
			'privatekey_array' => $privatekey,
			'publickey_array' => $publickey,
			'id' => ($signResponse ? 'ResponseID' : 'AssertionID') ,
			));

		if ($idpmd->hasValue('certificatechain')) {
			$signer->addCertificate($idpmd->getString('certificatechain'));
		}

		if ($signResponse) {
			/* Sign the response - this must be done after encrypting the assertion. */
			/* We insert the signature before the saml2p:Status element. */
			$statusElements = SimpleSAML_Utilities::getDOMChildren($responseroot, 'Status', '@saml1p');
			assert('count($statusElements) === 1');
			$signer->sign($responseroot, $responseroot, $statusElements[0]);

		} else {
			/* Sign the assertion */
			$signer->sign($firstassertionroot, $firstassertionroot);
		}

		$response = $responsedom->saveXML();

		SimpleSAML_Utilities::debugMessage($response, 'out');

		SimpleSAML_Utilities::postRedirect($shire, array(
			'TARGET' => $relayState,
			'SAMLResponse' => base64_encode($response),
		));

	}


	/**
	 * Decode a received response.
	 *
	 * @param array $post  POST data received.
	 * @return SimpleSAML_XML_Shib13_AuthnResponse  Response.
	 */
	public function decodeResponse($post) {
		assert('is_array($post)');

		if (!array_key_exists('SAMLResponse', $post)) {
			throw new Exception('Missing required SAMLResponse parameter.');
		}
		$rawResponse = $post['SAMLResponse'];
		$samlResponseXML = base64_decode($rawResponse);

		SimpleSAML_Utilities::debugMessage($samlResponseXML, 'in');

		SimpleSAML_Utilities::validateXMLDocument($samlResponseXML, 'saml11');

		$samlResponse = new SimpleSAML_XML_Shib13_AuthnResponse();
		$samlResponse->setXML($samlResponseXML);

		if (array_key_exists('TARGET', $post)) {
			$samlResponse->setRelayState($post['TARGET']);
		}

		return $samlResponse;
	}

}

?>