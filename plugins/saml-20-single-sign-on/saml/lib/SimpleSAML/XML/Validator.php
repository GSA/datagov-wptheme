<?php

/**
 * This class implements helper functions for XML validation.
 *
 * @author Olav Morken, UNINETT AS. 
 * @package simpleSAMLphp
 * @version $Id: Validator.php 1598 2009-08-03 12:44:42Z olavmrk $
 */
class SimpleSAML_XML_Validator {

	/**
	 * This variable contains the X509 certificate the XML document
	 * was signed with, or NULL if it wasn't signed with an X509 certificate.
	 */
	private $x509Certificate;

	/**
	 * This variable contains the nodes which are signed.
	 */
	private $validNodes = null;


	/**
	 * This function initializes the validator.
	 *
	 * This function accepts an optional parameter $publickey, which is the public key
	 * or certificate which should be used to validate the signature. This parameter can
	 * take the following values:
	 * - NULL/FALSE: No validation will be performed. This is the default.
	 * - A string: Assumed to be a PEM-encoded certificate / public key.
	 * - An array: Assumed to be an array returned by SimpleSAML_Utilities::loadPublicKey.
	 *
	 * @param DOMNode $xmlNode  The XML node which contains the Signature element.
	 * @param string|array $idAttribute  The ID attribute which is used in node references. If
	 *          this attribute is NULL (the default), then we will use whatever is the default
	 *          ID. Can be eigther a string with one value, or an array with multiple ID
	 *          attrbute names.
	 * @param array $publickey  The public key / certificate which should be used to validate the XML node.
	 */
	public function __construct($xmlNode, $idAttribute = NULL, $publickey = FALSE) {
		assert('$xmlNode instanceof DOMNode');

		if ($publickey === NULL) {
			$publickey = FALSE;
		} elseif(is_string($publickey)) {
			$publickey = array(
				'PEM' => $publickey,
			);
		} else {
			assert('$publickey === FALSE || is_array($publickey)');
		}

		/* Create an XML security object. */
		$objXMLSecDSig = new XMLSecurityDSig();

		/* Add the id attribute if the user passed in an id attribute. */
		if($idAttribute !== NULL) {
			if (is_string($idAttribute)) {
				$objXMLSecDSig->idKeys[] = $idAttribute;
			} elseif (is_array($idAttribute)) {
				foreach ($idAttribute AS $ida) 
					$objXMLSecDSig->idKeys[] = $ida;
			}
		}

		/* Locate the XMLDSig Signature element to be used. */
		$signatureElement = $objXMLSecDSig->locateSignature($xmlNode);
		if (!$signatureElement) {
			throw new Exception('Could not locate XML Signature element.');
		}

		/* Canonicalize the XMLDSig SignedInfo element in the message. */
		$objXMLSecDSig->canonicalizeSignedInfo();

		/* Validate referenced xml nodes. */
		if (!$objXMLSecDSig->validateReference()) {
			throw new Exception('XMLsec: digest validation failed');
		}


		/* Find the key used to sign the document. */
		$objKey = $objXMLSecDSig->locateKey();
		if (empty($objKey)) {
			throw new Exception('Error loading key to handle XML signature');
		}

		/* Load the key data. */
		if ($publickey !== FALSE && array_key_exists('PEM', $publickey)) {
			/* We have PEM data for the public key / certificate. */
			$objKey->loadKey($publickey['PEM']);
		} else {
			/* No PEM data. Search for key in signature. */

			if (!XMLSecEnc::staticLocateKeyInfo($objKey, $signatureElement)) {
				throw new Exception('Error finding key data for XML signature validation.');
			}

			if ($publickey !== FALSE) {
				/* $publickey is set, and should therefore contain one or more fingerprints.
				 * Check that the response contains a certificate with a matching
				 * fingerprint.
				 */
				assert('is_array($publickey["certFingerprint"])');

				$certificate = $objKey->getX509Certificate();
				if ($certificate === NULL) {
					/* Wasn't signed with an X509 certificate. */
					throw new Exception('Message wasn\'t signed with an X509 certificate,' .
						' and no public key was provided in the metadata.');
				}

				self::validateCertificateFingerprint($certificate, $publickey['certFingerprint']);
				/* Key OK. */
			}
		}

		/* Check the signature. */
		if (! $objXMLSecDSig->verify($objKey)) {
			throw new Exception("Unable to validate Signature");
		}

		/* Extract the certificate. */
		$this->x509Certificate = $objKey->getX509Certificate();

		/* Find the list of validated nodes. */
		$this->validNodes = $objXMLSecDSig->getValidatedNodes();
	}


	/**
	 * Retrieve the X509 certificate which was used to sign the XML.
	 *
	 * This function will return the certificate as a PEM-encoded string. If the XML
	 * wasn't signed by an X509 certificate, NULL will be returned.
	 *
	 * @return The certificate as a PEM-encoded string, or NULL if not signed with an X509 certificate.
	 */
	public function getX509Certificate() {
		return $this->x509Certificate;
	}


	/**
	 * Calculates the fingerprint of an X509 certificate.
	 *
	 * @param $x509cert  The certificate as a base64-encoded string. The string may optionally
	 *                   be framed with '-----BEGIN CERTIFICATE-----' and '-----END CERTIFICATE-----'.
	 * @return  The fingerprint as a 40-character lowercase hexadecimal number. NULL is returned if the
	 *          argument isn't an X509 certificate.
	 */
	private static function calculateX509Fingerprint($x509cert) {
		assert('is_string($x509cert)');

		$lines = explode("\n", $x509cert);

		$data = '';

		foreach($lines as $line) {
			/* Remove '\r' from end of line if present. */
			$line = rtrim($line);
			if($line === '-----BEGIN CERTIFICATE-----') {
				/* Delete junk from before the certificate. */
				$data = '';
			} elseif($line === '-----END CERTIFICATE-----') {
				/* Ignore data after the certificate. */
				break;
			} elseif($line === '-----BEGIN PUBLIC KEY-----') {
				/* This isn't an X509 certificate. */
				return NULL;
			} else {
				/* Append the current line to the certificate data. */
				$data .= $line;
			}
		}

		/* $data now contains the certificate as a base64-encoded string. The fingerprint
		 * of the certificate is the sha1-hash of the certificate.
		 */
		return strtolower(sha1(base64_decode($data)));
	}


	/**
	 * Helper function for validating the fingerprint.
	 *
	 * Checks the fingerprint of a certificate against an array of valid fingerprints.
	 * Will throw an exception if none of the fingerprints matches.
	 *
	 * @param string $certificate  The X509 certificate we should validate.
	 * @param array $fingerprints  The valid fingerprints.
	 */
	private static function validateCertificateFingerprint($certificate, $fingerprints) {
		assert('is_string($certificate)');
		assert('is_array($fingerprints)');

		$certFingerprint = self::calculateX509Fingerprint($certificate);
		if ($certFingerprint === NULL) {
			/* Couldn't calculate fingerprint from X509 certificate. Should not happen. */
			throw new Exception('Unable to calculate fingerprint from X509' .
				' certificate. Maybe it isn\'t an X509 certificate?');
		}

		foreach ($fingerprints as $fp) {
			assert('is_string($fp)');

			if ($fp === $certFingerprint) {
				/* The fingerprints matched. */
				return;
			}

		}

		/* None of the fingerprints matched. Throw an exception describing the error. */
		throw new Exception('Invalid fingerprint of certificate. Expected one of [' .
			implode('], [', $fingerprints) . '], but got [' . $certFingerprint . ']');
	}


	/**
	 * Validate the fingerprint of the certificate which was used to sign this document.
	 *
	 * This function accepts either a string, or an array of strings as a parameter. If this
	 * is an array, then any string (certificate) in the array can match. If this is a string,
	 * then that string must match,
	 *
	 * @param $fingerprints  The fingerprints which should match. This can be a single string,
	 *                       or an array of fingerprints.
	 */
	public function validateFingerprint($fingerprints) {
		assert('is_string($fingerprints) || is_array($fingerprints)');

		if($this->x509Certificate === NULL) {
			throw new Exception('Key used to sign the message was not an X509 certificate.');
		}

		if(!is_array($fingerprints)) {
			$fingerprints = array($fingerprints);
		}

		/* Normalize the fingerprints. */
		foreach($fingerprints as &$fp) {
			assert('is_string($fp)');

			/* Make sure that the fingerprint is in the correct format. */
			$fp = strtolower(str_replace(":", "", $fp));
		}

		self::validateCertificateFingerprint($this->x509Certificate, $fingerprints);
	}


	/**
	 * This function checks if the given XML node was signed.
	 *
	 * @param $node   The XML node which we should verify that was signed.
	 *
	 * @return TRUE if this node (or a parent node) was signed. FALSE if not.
	 */
	public function isNodeValidated($node) {
		assert('$node instanceof DOMNode');

		while($node !== NULL) {
			if(in_array($node, $this->validNodes)) {
				return TRUE;
			}

			$node = $node->parentNode;
		}

		/* Neither this node nor any of the parent nodes could be found in the list of
		 * signed nodes.
		 */
		return FALSE;
	}


	/**
	 * Validate the certificate used to sign the XML against a CA file.
	 *
	 * This function throws an exception if unable to validate against the given CA file.
	 *
	 * @param $caFile  File with trusted certificates, in PEM-format.
	 */
	public function validateCA($caFile) {

		assert('is_string($caFile)');

		if($this->x509Certificate === NULL) {
			throw new Exception('Key used to sign the message was not an X509 certificate.');
		}

		SimpleSAML_Utilities::validateCA($this->x509Certificate, $caFile);
	}

}

?>