<?php

/**
 * Class which implements the HTTP-Redirect binding.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SAML2_HTTPRedirect extends SAML2_Binding {

	const DEFLATE = 'urn:oasis:names:tc:SAML:2.0:bindings:URL-Encoding:DEFLATE';

	/**
	 * Create the redirect URL for a message.
	 *
	 * @param SAML2_Message $message  The message.
	 * @return string  The URL the user should be redirected to in order to send a message.
	 */
	public function getRedirectURL(SAML2_Message $message) {

		if ($this->destination === NULL) {
			$destination = $message->getDestination();
		} else {
			$destination = $this->destination;
		}

		$relayState = $message->getRelayState();

		$key = $message->getSignatureKey();

		$msgStr = $message->toUnsignedXML();
		$msgStr = $msgStr->ownerDocument->saveXML($msgStr);

		SimpleSAML_Utilities::debugMessage($msgStr, 'out');

		$msgStr = gzdeflate($msgStr);
		$msgStr = base64_encode($msgStr);

		/* Build the query string. */

		if ($message instanceof SAML2_Request) {
			$msg = 'SAMLRequest=';
		} else {
			$msg = 'SAMLResponse=';
		}
		$msg .= urlencode($msgStr);

		if ($relayState !== NULL) {
			$msg .= '&RelayState=' . urlencode($relayState);
		}

		if ($key !== NULL) {
			/* Add the signature. */
			$msg .= '&SigAlg=' . urlencode(XMLSecurityKey::RSA_SHA1);

			$signature = $key->signData($msg);
			$msg .= '&Signature=' . urlencode(base64_encode($signature));
		}

		if (strpos($destination, '?') === FALSE) {
			$destination .= '?' . $msg;
		} else {
			$destination .= '&' . $msg;
		}

		return $destination;
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
		SimpleSAML_Logger::debug('Redirect to ' . strlen($destination) . ' byte URL: ' . $destination);
		SimpleSAML_Utilities::redirect($destination);
	}


	/**
	 * Receive a SAML 2 message sent using the HTTP-Redirect binding.
	 *
	 * Throws an exception if it is unable receive the message.
	 *
	 * @return SAML2_Message  The received message.
	 */
	public function receive() {

		$data = self::parseQuery();

		if (array_key_exists('SAMLRequest', $data)) {
			$msg = $data['SAMLRequest'];
		} elseif (array_key_exists('SAMLResponse', $data)) {
			$msg = $data['SAMLResponse'];
		} else {
			throw new Exception('Missing SAMLRequest or SAMLResponse parameter.');
		}

		if (array_key_exists('SAMLEncoding', $data)) {
			$encoding = $data['SAMLEncoding'];
		} else {
			$encoding = self::DEFLATE;
		}

		$msg = base64_decode($msg);
		switch ($encoding) {
		case self::DEFLATE:
			$msg = gzinflate($msg);
			break;
		default:
			throw new Exception('Unknown SAMLEncoding: ' . var_export($encoding, TRUE));
		}

		SimpleSAML_Utilities::debugMessage($msg, 'in');

		$document = new DOMDocument();
		$document->loadXML($msg);
		$xml = $document->firstChild;

		$msg = SAML2_Message::fromXML($xml);

		if (array_key_exists('Signature', $data)) {
			/* Save the signature validation data until we need it. */
			$signatureValidationData = array(
				'Signature' => $data['Signature'],
				'Query' => $data['SignedQuery'],
				);
		}


		if (array_key_exists('RelayState', $data)) {
			$msg->setRelayState($data['RelayState']);
		}

		if (array_key_exists('Signature', $data)) {
			if (!array_key_exists('SigAlg', $data)) {
				throw new Exception('Missing signature algorithm.');
			}

			$signData = array(
				'Signature' => $data['Signature'],
				'SigAlg' => $data['SigAlg'],
				'Query' => $data['SignedQuery'],
				);
			$msg->addValidator(array(get_class($this), 'validateSignature'), $signData);
		}

		return $msg;
	}


	/**
	 * Helper function to parse query data.
	 *
	 * This function returns the query string split into key=>value pairs.
	 * It also adds a new parameter, SignedQuery, which contains the data that is
	 * signed.
	 *
	 * @return string  The query data that is signed.
	 */
	private static function parseQuery() {
		/*
		 * Parse the query string. We need to do this ourself, so that we get access
		 * to the raw (urlencoded) values. This is required because different software
		 * can urlencode to different values.
		 */
		$data = array();
		$relayState = '';
		$sigAlg = '';
		foreach (explode('&', $_SERVER['QUERY_STRING']) as $e) {
			list($name, $value) = explode('=', $e, 2);
			$name = urldecode($name);
			$data[$name] = urldecode($value);

			switch ($name) {
			case 'SAMLRequest':
			case 'SAMLResponse':
				$sigQuery = $name . '=' . $value;
				break;
			case 'RelayState':
				$relayState = '&RelayState=' . $value;
				break;
			case 'SigAlg':
				$sigAlg = '&SigAlg=' . $value;
				break;
			}
		}

		$data['SignedQuery'] = $sigQuery . $relayState . $sigAlg;

		return $data;
	}


	/**
	 * Validate the signature on a HTTP-Redirect message.
	 *
	 * Throws an exception if we are unable to validate the signature.
	 *
	 * @param array $data  The data we need to validate the query string.
	 * @param XMLSecurityKey $key  The key we should validate the query against.
	 */
	public static function validateSignature(array $data, XMLSecurityKey $key) {
		assert('array_key_exists("Query", $data)');
		assert('array_key_exists("SigAlg", $data)');
		assert('array_key_exists("Signature", $data)');

		$query = $data['Query'];
		$sigAlg = $data['SigAlg'];
		$signature = $data['Signature'];

		$signature = base64_decode($signature);

		switch ($sigAlg) {
		case XMLSecurityKey::RSA_SHA1:
			if ($key->type !== XMLSecurityKey::RSA_SHA1) {
				throw new Exception('Invalid key type for validating signature on query string.');
			}
			if (!$key->verifySignature($query,$signature)) {
				throw new Exception('Unable to validate signature on query string.');
			}
			break;
		default:
			throw new Exception('Unknown signature algorithm: ' . var_export($sigAlg, TRUE));
		}
	}

}

?>