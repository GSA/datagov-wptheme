<?php

/**
 * Class which implements the HTTP-POST binding.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SAML2_HTTPPost extends SAML2_Binding {

	/**
	 * Send a SAML 2 message using the HTTP-POST binding.
	 *
	 * Note: This function never returns.
	 *
	 * @param SAML2_Message $message  The message we should send.
	 */
	public function send(SAML2_Message $message) {

		if ($this->destination === NULL) {
			$destination = $message->getDestination();
		} else {
			$destination = $this->destination;
		}
		$relayState = $message->getRelayState();

		$msgStr = $message->toSignedXML();
		$msgStr = $msgStr->ownerDocument->saveXML($msgStr);

		SimpleSAML_Utilities::debugMessage($msgStr, 'out');

		$msgStr = base64_encode($msgStr);

		if ($message instanceof SAML2_Request) {
			$msgType = 'SAMLRequest';
		} else {
			$msgType = 'SAMLResponse';
		}

		$post = array();
		$post[$msgType] = $msgStr;

		if ($relayState !== NULL) {
			$post['RelayState'] = $relayState;
		}

		SimpleSAML_Utilities::postRedirect($destination, $post);
	}


	/**
	 * Receive a SAML 2 message sent using the HTTP-POST binding.
	 *
	 * Throws an exception if it is unable receive the message.
	 *
	 * @return SAML2_Message  The received message.
	 */
	public function receive() {

		if (array_key_exists('SAMLRequest', $_POST)) {
			$msg = $_POST['SAMLRequest'];
		} elseif (array_key_exists('SAMLResponse', $_POST)) {
			$msg = $_POST['SAMLResponse'];
		} else {
			throw new Exception('Missing SAMLRequest or SAMLResponse parameter.');
		}

		$msg = base64_decode($msg);

		SimpleSAML_Utilities::debugMessage($msg, 'in');

		$document = new DOMDocument();
		$document->loadXML($msg);
		$xml = $document->firstChild;

		$msg = SAML2_Message::fromXML($xml);

		if (array_key_exists('RelayState', $_POST)) {
			$msg->setRelayState($_POST['RelayState']);
		}

		return $msg;
	}

}

?>