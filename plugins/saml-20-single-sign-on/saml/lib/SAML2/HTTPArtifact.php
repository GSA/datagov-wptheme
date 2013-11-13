<?php


/**
 * Class which implements the HTTP-Redirect binding.
 *
 * @author  Danny Bollaert, UGent AS. <danny.bollaert@ugent.be>
 * @package simpleSAMLphp
 * @version $Id$
 */
class SAML2_HTTPArtifact extends SAML2_Binding {

	private $spMetadata;

	/**
	 * Create the redirect URL for a message.
	 *
	 * @param SAML2_Message $message  The message.
	 * @return string  The URL the user should be redirected to in order to send a message.
	 */
	public function getRedirectURL(SAML2_Message $message) {

		$store = SimpleSAML_Store::getInstance();
		if ($store === FALSE) {
			throw new Exception('Unable to send artifact without a datastore configured.');
		}

		$generatedId = pack('H*', ((string)  SimpleSAML_Utilities::stringToHex(SimpleSAML_Utilities::generateRandomBytes(20))));
		$artifact = base64_encode("\x00\x04\x00\x00" . sha1($message->getIssuer(), TRUE) . $generatedId) ;
		$artifactData = $message->toUnsignedXML();
		$artifactDataString = $artifactData->ownerDocument->saveXML($artifactData);

		$store->set('artifact', $artifact, $artifactDataString, time() + 15*60);

		$params = array(
			'SAMLart' => $artifact,
		);
		$relayState = $message->getRelayState();
		if ($relayState !== NULL) {
			$params['RelayState'] = $relayState;
		}

		return SimpleSAML_Utilities::addURLparameter($message->getDestination(), $params);
	}


	/**
	 * Send a SAML 2 message using the HTTP-Redirect binding.
	 *
	 * Note: This function never returns.
	 *
	 * @param SAML2_Message $message  The message we should send.
	 */
	public function send(SAML2_Message $message) {

		$destination = $this->getRedirectURL($message);
		SimpleSAML_Utilities::redirect($destination);
	}


	/**
	 * Receive a SAML 2 message sent using the HTTP-Artifact binding.
	 *
	 * Throws an exception if it is unable receive the message.
	 *
	 * @return SAML2_Message  The received message.
	 */
	public function receive() {

		if (array_key_exists('SAMLart', $_REQUEST)) {
			$artifact = base64_decode($_REQUEST['SAMLart']);
			$endpointIndex =  bin2hex(substr($artifact,2,2));
			$sourceId = bin2hex(substr($artifact,4,20));

		}else{
			throw new Execption('Missing SAMLArt parameter.');
		}

		$metadataHandler = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();

		$idpmetadata = $metadataHandler->getMetaDataConfigForSha1($sourceId, 'saml20-idp-remote');

		if ($idpmetadata === NULL) {
			throw new Exception('No metadata found for remote provider with SHA1 ID: ' . var_export($sourceId, TRUE));
		}

		$endpoint = NULL;
		foreach ($idpmetadata->getEndpoints('ArtifactResolutionService') as $ep) {
			if ($ep['index'] ===  hexdec($endpointIndex)) {
				$endpoint = $ep;
				break;
			}
		}

		if ($endpoint === NULL) {
			throw new Exception('No ArtifactResolutionService with the correct index.');
		}

		SimpleSAML_Logger::debug("ArtifactResolutionService endpoint being used is := " . $endpoint['Location']);

		//Construct the ArtifactResolve Request
		$ar = new SAML2_ArtifactResolve();

		/* Set the request attributes */

		$ar->setIssuer($this->spMetadata->getString('entityid'));
		$ar->setArtifact($_REQUEST['SAMLart']);
		$ar->setDestination($endpoint['Location']);

		/* Sign the request */
		sspmod_saml_Message::addSign($this->spMetadata, $idpmetadata, $ar); // Shoaib - moved from the SOAPClient.

		$soap = new SAML2_SOAPClient();

		// Send message through SoapClient
		$artifactResponse = $soap->send($ar, $this->spMetadata);

		if (!$artifactResponse->isSuccess()) {
			throw new Exception('Received error from ArtifactResolutionService.');
		}

		$xml = $artifactResponse->getAny();
		if ($xml === NULL) {
			/* Empty ArtifactResponse - possibly because of Artifact replay? */
			return NULL;
		}

		$samlresponse = SAML2_Message::fromXML($xml);
		$samlresponse->addValidator(array(get_class($this), 'validateSignature'), $artifactResponse);


		if (isset($_REQUEST['RelayState'])) {
			$samlresponse->setRelayState($_REQUEST['RelayState']);
		}

		return $samlresponse;
	}


	public function setSPMetadata($sp){
		$this->spMetadata = $sp;
	}


	/**
	 * A validator which returns TRUE if the ArtifactResponse was signed with the given key
	 *
	 * @return TRUE
	 */
	public static function validateSignature(SAML2_ArtifactResponse $message, XMLSecurityKey $key) {

		return $message->validate($key);
	}

}
