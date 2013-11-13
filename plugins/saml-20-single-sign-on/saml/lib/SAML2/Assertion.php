<?php

/**
 * Class representing a SAML 2 assertion.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SAML2_Assertion implements SAML2_SignedElement {

	/**
	 * The identifier of this assertion.
	 *
	 * @var string
	 */
	private $id;


	/**
	 * The issue timestamp of this assertion, as an UNIX timestamp.
	 *
	 * @var int
	 */
	private $issueInstant;


	/**
	 * The entity id of the issuer of this assertion.
	 *
	 * @var string
	 */
	private $issuer;


	/**
	 * The NameId of the subject in the assertion.
	 *
	 * If the NameId is NULL, no subject was included in the assertion.
	 *
	 * @var array|NULL
	 */
	private $nameId;


	/**
	 * The encrypted NameId of the subject.
	 *
	 * If this is not NULL, the NameId needs decryption before it can be accessed.
	 *
	 * @var DOMElement|NULL
	 */
	private $encryptedNameId;


	/**
	 * The encrypted Attributes.
	 *
	 * If this is not NULL, the Attributes needs decryption before it can be accessed.
	 *
	 * @var array|NULL
	 */
	 private $encryptedAttribute;


	 /**
	 * The earliest time this assertion is valid, as an UNIX timestamp.
	 *
	 * @var int
	 */
	private $notBefore;


	/**
	 * The time this assertion expires, as an UNIX timestamp.
	 *
	 * @var int
	 */
	private $notOnOrAfter;


	/**
	 * The set of audiences that are allowed to receive this assertion.
	 *
	 * This is an array of valid service providers.
	 *
	 * If no restrictions on the audience are present, this variable contains NULL.
	 *
	 * @var array|NULL
	 */
	private $validAudiences;


	/**
	 * The session expiration timestamp.
	 *
	 * @var int|NULL
	 */
	private $sessionNotOnOrAfter;


	/**
	 * The session index for this user on the IdP.
	 *
	 * Contains NULL if no session index is present.
	 *
	 * @var string|NULL
	 */
	private $sessionIndex;


	/**
	 * The timestamp the user was authenticated, as an UNIX timestamp.
	 *
	 * @var int
	 */
	private $authnInstant;


	/**
	 * The authentication context for this assertion.
	 *
	 * @var string|NULL
	 */
	private $authnContext;

	/**
	 * The list of AuthenticatingAuthorities for this assertion.
	 *
	 * @var array
	 */
	private $AuthenticatingAuthority;


	/**
	 * The attributes, as an associative array.
	 *
	 * @var array
	 */
	private $attributes;


	/**
	 * The NameFormat used on all attributes.
	 *
	 * If more than one NameFormat is used, this will contain
	 * the unspecified nameformat.
	 *
	 * @var string
	 */
	private $nameFormat;


	/**
	 * The private key we should use to sign the assertion.
	 *
	 * The private key can be NULL, in which case the assertion is sent unsigned.
	 *
	 * @var XMLSecurityKey|NULL
	 */
	private $signatureKey;


	/**
	 * List of certificates that should be included in the assertion.
	 *
	 * @var array
	 */
	private $certificates;


	/**
	 * The data needed to verify the signature.
	 *
	 * @var array|NULL
	 */
	private $signatureData;


	/**
	 * Boolean that indicates if attributes are encrypted in the
	 * assertion or not.
	 *
	 * @var boolean
	 */
	private $requiredEncAttributes;


	/**
	 * The SubjectConfirmation elements of the Subject in the assertion.
	 *
	 * @var array  Array of SAML2_XML_saml_SubjectConfirmation elements.
	 */
	private $SubjectConfirmation;


	/**
	 * Constructor for SAML 2 assertions.
	 *
	 * @param DOMElement|NULL $xml  The input assertion.
	 */
	public function __construct(DOMElement $xml = NULL) {

		$this->id = SimpleSAML_Utilities::generateID();
		$this->issueInstant = time();
		$this->issuer = '';
		$this->authnInstant = time();
		$this->attributes = array();
		$this->nameFormat = SAML2_Const::NAMEFORMAT_UNSPECIFIED;
		$this->certificates = array();
		$this->AuthenticatingAuthority = array();
		$this->SubjectConfirmation = array();

		if ($xml === NULL) {
			return;
		}

		if (!$xml->hasAttribute('ID')) {
			throw new Exception('Missing ID attribute on SAML assertion.');
		}
		$this->id = $xml->getAttribute('ID');

		if ($xml->getAttribute('Version') !== '2.0') {
			/* Currently a very strict check. */
			throw new Exception('Unsupported version: ' . $xml->getAttribute('Version'));
		}

		$this->issueInstant = SimpleSAML_Utilities::parseSAML2Time($xml->getAttribute('IssueInstant'));

		$issuer = SAML2_Utils::xpQuery($xml, './saml_assertion:Issuer');
		if (empty($issuer)) {
			throw new Exception('Missing <saml:Issuer> in assertion.');
		}
		$this->issuer = trim($issuer[0]->textContent);

		$this->parseSubject($xml);
		$this->parseConditions($xml);
		$this->parseAuthnStatement($xml);
		$this->parseAttributes($xml);
		$this->parseEncryptedAttributes($xml);
		$this->parseSignature($xml);
	}


	/**
	 * Parse subject in assertion.
	 *
	 * @param DOMElement $xml  The assertion XML element.
	 */
	private function parseSubject(DOMElement $xml) {

		$subject = SAML2_Utils::xpQuery($xml, './saml_assertion:Subject');
		if (empty($subject)) {
			/* No Subject node. */
			return;
		} elseif (count($subject) > 1) {
			throw new Exception('More than one <saml:Subject> in <saml:Assertion>.');
		}
		$subject = $subject[0];

		$nameId = SAML2_Utils::xpQuery($subject, './saml_assertion:NameID | ./saml_assertion:EncryptedID/xenc:EncryptedData');
		if (empty($nameId)) {
			throw new Exception('Missing <saml:NameID> or <saml:EncryptedID> in <saml:Subject>.');
		} elseif (count($nameId) > 1) {
			throw new Exception('More than one <saml:NameID> or <saml:EncryptedD> in <saml:Subject>.');
		}
		$nameId = $nameId[0];
		if ($nameId->localName === 'EncryptedData') {
			/* The NameID element is encrypted. */
			$this->encryptedNameId = $nameId;
		} else {
			$this->nameId = SAML2_Utils::parseNameId($nameId);
		}

		$subjectConfirmation = SAML2_Utils::xpQuery($subject, './saml_assertion:SubjectConfirmation');
		if (empty($subjectConfirmation)) {
			throw new Exception('Missing <saml:SubjectConfirmation> in <saml:Subject>.');
		}

		foreach ($subjectConfirmation as $sc) {
			$this->SubjectConfirmation[] = new SAML2_XML_saml_SubjectConfirmation($sc);
		}
	}


	/**
	 * Parse conditions in assertion.
	 *
	 * @param DOMElement $xml  The assertion XML element.
	 */
	private function parseConditions(DOMElement $xml) {

		$conditions = SAML2_Utils::xpQuery($xml, './saml_assertion:Conditions');
		if (empty($conditions)) {
			/* No <saml:Conditions> node. */
			return;
		} elseif (count($conditions) > 1) {
			throw new Exception('More than one <saml:Conditions> in <saml:Assertion>.');
		}
		$conditions = $conditions[0];

		if ($conditions->hasAttribute('NotBefore')) {
			$notBefore = SimpleSAML_Utilities::parseSAML2Time($conditions->getAttribute('NotBefore'));
			if ($this->notBefore === NULL || $this->notBefore < $notBefore) {
				$this->notBefore = $notBefore;
			}
		}
		if ($conditions->hasAttribute('NotOnOrAfter')) {
			$notOnOrAfter = SimpleSAML_Utilities::parseSAML2Time($conditions->getAttribute('NotOnOrAfter'));
			if ($this->notOnOrAfter === NULL || $this->notOnOrAfter > $notOnOrAfter) {
				$this->notOnOrAfter = $notOnOrAfter;
			}
		}


		for ($node = $conditions->firstChild; $node !== NULL; $node = $node->nextSibling) {
			if ($node instanceof DOMText) {
				continue;
			}
			if ($node->namespaceURI !== SAML2_Const::NS_SAML) {
				throw new Exception('Unknown namespace of condition: ' . var_export($node->namespaceURI, TRUE));
			}
			switch ($node->localName) {
			case 'AudienceRestriction':
				$audiences = SAML2_Utils::extractStrings($node, SAML2_Const::NS_SAML, 'Audience');
				if ($this->validAudiences === NULL) {
					/* The first (and probably last) AudienceRestriction element. */
					$this->validAudiences = $audiences;

				} else {
					/*
					 * The set of AudienceRestriction are ANDed together, so we need
					 * the subset that are present in all of them.
					 */
					$this->validAudiences = array_intersect($this->validAudiences, $audiences);
				}
				break;
			case 'OneTimeUse':
				/* Currently ignored. */
				break;
			case 'ProxyRestriction':
				/* Currently ignored. */
				break;
			default:
				throw new Exception('Unknown condition: ' . var_export($node->localName, TRUE));
			}
		}

	}


	/**
	 * Parse AuthnStatement in assertion.
	 *
	 * @param DOMElement $xml  The assertion XML element.
	 */
	private function parseAuthnStatement(DOMElement $xml) {

		$as = SAML2_Utils::xpQuery($xml, './saml_assertion:AuthnStatement');
		if (empty($as)) {
			$this->authnInstant = NULL;
			return;
		} elseif (count($as) > 1) {
			throw new Exception('More that one <saml:AuthnStatement> in <saml:Assertion> not supported.');
		}
		$as = $as[0];
		$this->authnStatement = array();

		if (!$as->hasAttribute('AuthnInstant')) {
			throw new Exception('Missing required AuthnInstant attribute on <saml:AuthnStatement>.');
		}
		$this->authnInstant = SimpleSAML_Utilities::parseSAML2Time($as->getAttribute('AuthnInstant'));

		if ($as->hasAttribute('SessionNotOnOrAfter')) {
			$this->sessionNotOnOrAfter = SimpleSAML_Utilities::parseSAML2Time($as->getAttribute('SessionNotOnOrAfter'));
		}

		if ($as->hasAttribute('SessionIndex')) {
			$this->sessionIndex = $as->getAttribute('SessionIndex');
		}

		$ac = SAML2_Utils::xpQuery($as, './saml_assertion:AuthnContext');
		if (empty($ac)) {
			throw new Exception('Missing required <saml:AuthnContext> in <saml:AuthnStatement>.');
		} elseif (count($ac) > 1) {
			throw new Exception('More than one <saml:AuthnContext> in <saml:AuthnStatement>.');
		}
		$ac = $ac[0];

		$accr = SAML2_Utils::xpQuery($ac, './saml_assertion:AuthnContextClassRef');
		if (empty($accr)) {
			$acdr = SAML2_Utils::xpQuery($ac, './saml_assertion:AuthnContextDeclRef');
			if (empty($acdr)) {
				throw new Exception('Neither <saml:AuthnContextClassRef> nor <saml:AuthnContextDeclRef> found in <saml:AuthnContext>.');
			} elseif (count($accr) > 1) {
				throw new Exception('More than one <saml:AuthnContextDeclRef> in <saml:AuthnContext>.');
			}
			$this->authnContext = trim($acdr[0]->textContent);
		} elseif (count($accr) > 1) {
			throw new Exception('More than one <saml:AuthnContextClassRef> in <saml:AuthnContext>.');
		} else {
			$this->authnContext = trim($accr[0]->textContent);
		}
		
		$this->AuthenticatingAuthority = SAML2_Utils::extractStrings($ac, SAML2_Const::NS_SAML, 'AuthenticatingAuthority');
	}


	/**
	 * Parse attribute statements in assertion.
	 *
	 * @param DOMElement $xml  The XML element with the assertion.
	 */
	private function parseAttributes(DOMElement $xml) {

		$firstAttribute = TRUE;
		$attributes = SAML2_Utils::xpQuery($xml, './saml_assertion:AttributeStatement/saml_assertion:Attribute');
		foreach ($attributes as $attribute) {
			if (!$attribute->hasAttribute('Name')) {
				throw new Exception('Missing name on <saml:Attribute> element.');
			}
			$name = $attribute->getAttribute('Name');

			if ($attribute->hasAttribute('NameFormat')) {
				$nameFormat = $attribute->getAttribute('NameFormat');
			} else {
				$nameFormat = SAML2_Const::NAMEFORMAT_UNSPECIFIED;
			}

			if ($firstAttribute) {
				$this->nameFormat = $nameFormat;
				$firstAttribute = FALSE;
			} else {
				if ($this->nameFormat !== $nameFormat) {
					$this->nameFormat = SAML2_Const::NAMEFORMAT_UNSPECIFIED;
				}
			}

			if (!array_key_exists($name, $this->attributes)) {
				$this->attributes[$name] = array();
			}

			$values = SAML2_Utils::xpQuery($attribute, './saml_assertion:AttributeValue');
			foreach ($values as $value) {
				$this->attributes[$name][] = trim($value->textContent);
			}
		}
	}


	/**
	 * Parse encrypted attribute statements in assertion.
	 *
	 * @param DOMElement $xml  The XML element with the assertion.
	 */
	private function parseEncryptedAttributes(DOMElement $xml) {

		$this->encryptedAttribute = SAML2_Utils::xpQuery($xml, './saml_assertion:AttributeStatement/saml_assertion:EncryptedAttribute');
	}


	/**
	 * Parse signature on assertion.
	 *
	 * @param DOMElement $xml  The assertion XML element.
	 */
	private function parseSignature(DOMElement $xml) {

		/* Validate the signature element of the message. */
		$sig = SAML2_Utils::validateElement($xml);
		if ($sig !== FALSE) {
			$this->certificates = $sig['Certificates'];
			$this->signatureData = $sig;
		}
	}


	/**
	 * Validate this assertion against a public key.
	 *
	 * If no signature was present on the assertion, we will return FALSE.
	 * Otherwise, TRUE will be returned. An exception is thrown if the
	 * signature validation fails.
	 *
	 * @param XMLSecurityKey $key  The key we should check against.
	 * @return boolean  TRUE if successful, FALSE if it is unsigned.
	 */
	public function validate(XMLSecurityKey $key) {
		assert('$key->type === XMLSecurityKey::RSA_SHA1');

		if ($this->signatureData === NULL) {
			return FALSE;
		}

		SAML2_Utils::validateSignature($this->signatureData, $key);

		return TRUE;
	}


	/**
	 * Retrieve the identifier of this assertion.
	 *
	 * @return string  The identifier of this assertion.
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * Set the identifier of this assertion.
	 *
	 * @param string $id  The new identifier of this assertion.
	 */
	public function setId($id) {
		assert('is_string($id)');

		$this->id = $id;
	}


	/**
	 * Retrieve the issue timestamp of this assertion.
	 *
	 * @return int  The issue timestamp of this assertion, as an UNIX timestamp.
	 */
	public function getIssueInstant() {
		return $this->issueInstant;
	}


	/**
	 * Set the issue timestamp of this assertion.
	 *
	 * @param int $issueInstant  The new issue timestamp of this assertion, as an UNIX timestamp.
	 */
	public function setIssueInstant($issueInstant) {
		assert('is_int($issueInstant)');

		$this->issueInstant = $issueInstant;
	}


	/**
	 * Retrieve the issuer if this assertion.
	 *
	 * @return string  The issuer of this assertion.
	 */
	public function getIssuer() {
		return $this->issuer;
	}


	/**
	 * Set the issuer of this message.
	 *
	 * @param string $issuer  The new issuer of this assertion.
	 */
	public function setIssuer($issuer) {
		assert('is_string($issuer)');

		$this->issuer = $issuer;
	}


	/**
	 * Retrieve the NameId of the subject in the assertion.
	 *
	 * The returned NameId is in the format used by SAML2_Utils::addNameId().
	 *
	 * @see SAML2_Utils::addNameId()
	 * @return array|NULL  The name identifier of the assertion.
	 */
	public function getNameId() {

		if ($this->encryptedNameId !== NULL) {
			throw new Exception('Attempted to retrieve encrypted NameID without decrypting it first.');
		}

		return $this->nameId;
	}


	/**
	 * Set the NameId of the subject in the assertion.
	 *
	 * The NameId must be in the format accepted by SAML2_Utils::addNameId().
	 *
	 * @see SAML2_Utils::addNameId()
	 * @param array|NULL $nameId  The name identifier of the assertion.
	 */
	public function setNameId($nameId) {
		assert('is_array($nameId) || is_null($nameId)');

		$this->nameId = $nameId;
	}


	/**
	 * Check whether the NameId is encrypted.
	 *
	 * @return TRUE if the NameId is encrypted, FALSE if not.
	 */
	public function isNameIdEncrypted() {

		if ($this->encryptedNameId !== NULL) {
			return TRUE;
		}

		return FALSE;
	}


	/**
	 * Encrypt the NameID in the Assertion.
	 *
	 * @param XMLSecurityKey $key  The encryption key.
	 */
	public function encryptNameId(XMLSecurityKey $key) {

		/* First create a XML representation of the NameID. */
		$doc = new DOMDocument();
		$root = $doc->createElement('root');
		$doc->appendChild($root);
		SAML2_Utils::addNameId($root, $this->nameId);
		$nameId = $root->firstChild;

		SimpleSAML_Utilities::debugMessage($nameId, 'encrypt');

		/* Encrypt the NameID. */
		$enc = new XMLSecEnc();
		$enc->setNode($nameId);
		$enc->type = XMLSecEnc::Element;

		$symmetricKey = new XMLSecurityKey(XMLSecurityKey::AES128_CBC);
		$symmetricKey->generateSessionKey();
		$enc->encryptKey($key, $symmetricKey);

		$this->encryptedNameId = $enc->encryptNode($symmetricKey);
		$this->nameId = NULL;
	}


	/**
	 * Decrypt the NameId of the subject in the assertion.
	 *
	 * @param XMLSecurityKey $key  The decryption key.
	 * @param array $blacklist  Blacklisted decryption algorithms.
	 */
	public function decryptNameId(XMLSecurityKey $key, array $blacklist = array()) {

		if ($this->encryptedNameId === NULL) {
			/* No NameID to decrypt. */
			return;
		}

		$nameId = SAML2_Utils::decryptElement($this->encryptedNameId, $key, $blacklist);
		SimpleSAML_Utilities::debugMessage($nameId, 'decrypt');
		$this->nameId = SAML2_Utils::parseNameId($nameId);

		$this->encryptedNameId = NULL;
	}


	public function decryptAttributes($key, array $blacklist = array()){
		if($this->encryptedAttribute === null){
			return;
		}
		$attributes = $this->encryptedAttribute;
		foreach ($attributes as $attributeEnc) {
			/*Decrypt node <EncryptedAttribute>*/
			$attribute = SAML2_Utils::decryptElement($attributeEnc->getElementsByTagName('EncryptedData')->item(0), $key, $blacklist);

			if (!$attribute->hasAttribute('Name')) {
				throw new Exception('Missing name on <saml:Attribute> element.');
			}
			$name = $attribute->getAttribute('Name');

			if ($attribute->hasAttribute('NameFormat')) {
				$nameFormat = $attribute->getAttribute('NameFormat');
			} else {
				$nameFormat = SAML2_Const::NAMEFORMAT_UNSPECIFIED;
			}

			if ($firstAttribute) {
				$this->nameFormat = $nameFormat;
				$firstAttribute = FALSE;
			} else {
				if ($this->nameFormat !== $nameFormat) {
					$this->nameFormat = SAML2_Const::NAMEFORMAT_UNSPECIFIED;
				}
			}

			if (!array_key_exists($name, $this->attributes)) {
				$this->attributes[$name] = array();
			}

			$values = SAML2_Utils::xpQuery($attribute, './saml_assertion:AttributeValue');
			foreach ($values as $value) {
				$this->attributes[$name][] = trim($value->textContent);
			}
		}
	}


	/**
	 * Retrieve the earliest timestamp this assertion is valid.
	 *
	 * This function returns NULL if there are no restrictions on how early the
	 * assertion can be used.
	 *
	 * @return int|NULL  The earliest timestamp this assertion is valid.
	 */
	public function getNotBefore() {

		return $this->notBefore;
	}


	/**
	 * Set the earliest timestamp this assertion can be used.
	 *
	 * Set this to NULL if no limit is required.
	 *
	 * @param int|NULL $notBefore  The earliest timestamp this assertion is valid.
	 */
	public function setNotBefore($notBefore) {
		assert('is_int($notBefore) || is_null($notBefore)');

		$this->notBefore = $notBefore;
	}


	/**
	 * Retrieve the expiration timestamp of this assertion.
	 *
	 * This function returns NULL if there are no restrictions on how
	 * late the assertion can be used.
	 *
	 * @return int|NULL  The latest timestamp this assertion is valid.
	 */
	public function getNotOnOrAfter() {

		return $this->notOnOrAfter;
	}


	/**
	 * Set the expiration timestamp of this assertion.
	 *
	 * Set this to NULL if no limit is required.
	 *
	 * @param int|NULL $notOnOrAfter  The latest timestamp this assertion is valid.
	 */
	public function setNotOnOrAfter($notOnOrAfter) {
		assert('is_int($notOnOrAfter) || is_null($notOnOrAfter)');

		$this->notOnOrAfter = $notOnOrAfter;
	}


	/**
	 * Set $EncryptedAttributes if attributes will send encrypted
	 *
	 * @param boolean $ea  TRUE to encrypt attributes in the assertion.
	 */
	public function setEncryptedAttributes($ea) {
		$this->requiredEncAttributes = $ea;
	}


	/**
	 * Retrieve the audiences that are allowed to receive this assertion.
	 *
	 * This may be NULL, in which case all audiences are allowed.
	 *
	 * @return array|NULL  The allowed audiences.
	 */
	public function getValidAudiences() {

		return $this->validAudiences;
	}


	/**
	 * Set the audiences that are allowed to receive this assertion.
	 *
	 * This may be NULL, in which case all audiences are allowed.
	 *
	 * @param array|NULL $validAudiences  The allowed audiences.
	 */
	public function setValidAudiences(array $validAudiences = NULL) {

		$this->validAudiences = $validAudiences;
	}


	/**
	 * Retrieve the AuthnInstant of the assertion.
	 *
	 * @return int|NULL  The timestamp the user was authenticated, or NULL if the user isn't authenticated.
	 */
	public function getAuthnInstant() {

		return $this->authnInstant;
	}


	/**
	 * Set the AuthnInstant of the assertion.
	 *
	 * @param int|NULL $authnInstant  The timestamp the user was authenticated, or NULL if we don't want an AuthnStatement.
	 */
	public function setAuthnInstant($authnInstant) {
		assert('is_int($authnInstant) || is_null($authnInstant)');

		$this->authnInstant = $authnInstant;
	}


	/**
	 * Retrieve the session expiration timestamp.
	 *
	 * This function returns NULL if there are no restrictions on the
	 * session lifetime.
	 *
	 * @return int|NULL  The latest timestamp this session is valid.
	 */
	public function getSessionNotOnOrAfter() {

		return $this->sessionNotOnOrAfter;
	}


	/**
	 * Set the session expiration timestamp.
	 *
	 * Set this to NULL if no limit is required.
	 *
	 * @param int|NULL $sessionLifetime  The latest timestamp this session is valid.
	 */
	public function setSessionNotOnOrAfter($sessionNotOnOrAfter) {
		assert('is_int($sessionNotOnOrAfter) || is_null($sessionNotOnOrAfter)');

		$this->sessionNotOnOrAfter = $sessionNotOnOrAfter;
	}


	/**
	 * Retrieve the session index of the user at the IdP.
	 *
	 * @return string|NULL  The session index of the user at the IdP.
	 */
	public function getSessionIndex() {

		return $this->sessionIndex;
	}


	/**
	 * Set the session index of the user at the IdP.
	 *
	 * Note that the authentication context must be set before the
	 * session index can be inluded in the assertion.
	 *
	 * @param string|NULL $sessionIndex  The session index of the user at the IdP.
	 */
	public function setSessionIndex($sessionIndex) {
		assert('is_string($sessionIndex) || is_null($sessionIndex)');

		$this->sessionIndex = $sessionIndex;
	}


	/**
	 * Retrieve the authentication method used to authenticate the user.
	 *
	 * This will return NULL if no authentication statement was
	 * included in the assertion.
	 *
	 * @return string|NULL  The authentication method.
	 */
	public function getAuthnContext() {

		return $this->authnContext;
	}


	/**
	 * Set the authentication method used to authenticate the user.
	 *
	 * If this is set to NULL, no authentication statement will be
	 * included in the assertion. The default is NULL.
	 *
	 * @param string|NULL $authnContext  The authentication method.
	 */
	public function setAuthnContext($authnContext) {
		assert('is_string($authnContext) || is_null($authnContext)');

		$this->authnContext = $authnContext;
	}


	/**
	 * Retrieve the AuthenticatingAuthority.
	 *
	 *
	 * @return array
	 */
	public function getAuthenticatingAuthority() {

		return $this->AuthenticatingAuthority;
	}


	/**
	 * Set the AuthenticatingAuthority
	 *
	 *
	 * @param array.
	 */
	public function setAuthenticatingAuthority($AuthenticatingAuthority) {
		$this->AuthenticatingAuthority = $AuthenticatingAuthority;
	}


	/**
	 * Retrieve all attributes.
	 *
	 * @return array  All attributes, as an associative array.
	 */
	public function getAttributes() {

		return $this->attributes;
	}


	/**
	 * Replace all attributes.
	 *
	 * @param array $attributes  All new attributes, as an associative array.
	 */
	public function setAttributes(array $attributes) {

		$this->attributes = $attributes;
	}


	/**
	 * Retrieve the NameFormat used on all attributes.
	 *
	 * If more than one NameFormat is used in the received attributes, this
	 * returns the unspecified NameFormat.
	 *
	 * @return string  The NameFormat used on all attributes.
	 */
	public function getAttributeNameFormat() {
		return $this->nameFormat;
	}


	/**
	 * Set the NameFormat used on all attributes.
	 *
	 * @param string $nameFormat  The NameFormat used on all attributes.
	 */
	public function setAttributeNameFormat($nameFormat) {
		assert('is_string($nameFormat)');

		$this->nameFormat = $nameFormat;
	}


	/**
	 * Retrieve the SubjectConfirmation elements we have in our Subject element.
	 *
	 * @return array  Array of SAML2_XML_saml_SubjectConfirmation elements.
	 */
	public function getSubjectConfirmation() {
		return $this->SubjectConfirmation;
	}


	/**
	 * Set the SubjectConfirmation elements that should be included in the assertion.
	 *
	 * @param array $SubjectConfirmation Array of SAML2_XML_saml_SubjectConfirmation elements.
	 */
	public function setSubjectConfirmation(array $SubjectConfirmation) {

		$this->SubjectConfirmation = $SubjectConfirmation;
	}


	/**
	 * Retrieve the private key we should use to sign the assertion.
	 *
	 * @return XMLSecurityKey|NULL The key, or NULL if no key is specified.
	 */
	public function getSignatureKey() {
		return $this->signatureKey;
	}


	/**
	 * Set the private key we should use to sign the assertion.
	 *
	 * If the key is NULL, the assertion will be sent unsigned.
	 *
	 * @param XMLSecurityKey|NULL $key
	 */
	public function setSignatureKey(XMLsecurityKey $signatureKey = NULL) {
		$this->signatureKey = $signatureKey;
	}


	/**
	 * Return the key we should use to encrypt the assertion.
	 *
	 * @return XMLSecurityKey|NULL The key, or NULL if no key is specified..
	 *
	 */


	public function getEncryptionKey() {
		return $this->encryptionKey;
	}


	/**
	 * Set the private key we should use to encrypt the attributes.
	 *
	 * @param XMLSecurityKey|NULL $key
	 */
	public function setEncryptionKey(XMLSecurityKey $Key = NULL) {
		$this->encryptionKey = $Key;
	}

	/**
	 * Set the certificates that should be included in the assertion.
	 *
	 * The certificates should be strings with the PEM encoded data.
	 *
	 * @param array $certificates  An array of certificates.
	 */
	public function setCertificates(array $certificates) {
		$this->certificates = $certificates;
	}


	/**
	 * Retrieve the certificates that are included in the assertion.
	 *
	 * @return array  An array of certificates.
	 */
	public function getCertificates() {
		return $this->certificates;
	}


	/**
	 * Convert this assertion to an XML element.
	 *
	 * @param DOMNode|NULL $parentElement  The DOM node the assertion should be created in.
	 * @return DOMElement  This assertion.
	 */
	public function toXML(DOMNode $parentElement = NULL) {

		if ($parentElement === NULL) {
			$document = new DOMDocument();
			$parentElement = $document;
		} else {
			$document = $parentElement->ownerDocument;
		}

		$root = $document->createElementNS(SAML2_Const::NS_SAML, 'saml:' . 'Assertion');
		$parentElement->appendChild($root);

		/* Ugly hack to add another namespace declaration to the root element. */
		$root->setAttributeNS(SAML2_Const::NS_SAMLP, 'samlp:tmp', 'tmp');
		$root->removeAttributeNS(SAML2_Const::NS_SAMLP, 'tmp');
		$root->setAttributeNS(SAML2_Const::NS_XSI, 'xsi:tmp', 'tmp');
		$root->removeAttributeNS(SAML2_Const::NS_XSI, 'tmp');
		$root->setAttributeNS(SAML2_Const::NS_XS, 'xs:tmp', 'tmp');
		$root->removeAttributeNS(SAML2_Const::NS_XS, 'tmp');

		$root->setAttribute('ID', $this->id);
		$root->setAttribute('Version', '2.0');
		$root->setAttribute('IssueInstant', gmdate('Y-m-d\TH:i:s\Z', $this->issueInstant));

		$issuer = SAML2_Utils::addString($root, SAML2_Const::NS_SAML, 'saml:Issuer', $this->issuer);

		$this->addSubject($root);
		$this->addConditions($root);
		$this->addAuthnStatement($root);
		if($this->requiredEncAttributes == false)
			$this->addAttributeStatement($root);
		else
			$this->addEncryptedAttributeStatement($root);

		if ($this->signatureKey !== NULL) {
			SAML2_Utils::insertSignature($this->signatureKey, $this->certificates, $root, $issuer->nextSibling);
		}

		return $root;
	}


	/**
	 * Add a Subject-node to the assertion.
	 *
	 * @param DOMElement $root  The assertion element we should add the subject to.
	 */
	private function addSubject(DOMElement $root) {

		if ($this->nameId === NULL && $this->encryptedNameId === NULL) {
			/* We don't have anything to create a Subject node for. */
			return;
		}

		$subject = $root->ownerDocument->createElementNS(SAML2_Const::NS_SAML, 'saml:Subject');
		$root->appendChild($subject);

		if ($this->encryptedNameId === NULL) {
			SAML2_Utils::addNameId($subject, $this->nameId);
		} else {
			$eid = $subject->ownerDocument->createElementNS(SAML2_Const::NS_SAML, 'saml:' . 'EncryptedID');
			$subject->appendChild($eid);
			$eid->appendChild($subject->ownerDocument->importNode($this->encryptedNameId, TRUE));
		}

		foreach ($this->SubjectConfirmation as $sc) {
			$sc->toXML($subject);
		}
	}


	/**
	 * Add a Conditions-node to the assertion.
	 *
	 * @param DOMElement $root  The assertion element we should add the conditions to.
	 */
	private function addConditions(DOMElement $root) {

		$document = $root->ownerDocument;

		$conditions = $document->createElementNS(SAML2_Const::NS_SAML, 'saml:Conditions');
		$root->appendChild($conditions);

		if ($this->notBefore !== NULL) {
			$conditions->setAttribute('NotBefore', gmdate('Y-m-d\TH:i:s\Z', $this->notBefore));
		}
		if ($this->notOnOrAfter !== NULL) {
			$conditions->setAttribute('NotOnOrAfter', gmdate('Y-m-d\TH:i:s\Z', $this->notOnOrAfter));
		}

		if ($this->validAudiences !== NULL) {
			$ar = $document->createElementNS(SAML2_Const::NS_SAML, 'saml:AudienceRestriction');
			$conditions->appendChild($ar);

			SAML2_Utils::addStrings($ar, SAML2_Const::NS_SAML, 'saml:Audience', FALSE, $this->validAudiences);
		}
	}


	/**
	 * Add a AuthnStatement-node to the assertion.
	 *
	 * @param DOMElement $root  The assertion element we should add the authentication statement to.
	 */
	private function addAuthnStatement(DOMElement $root) {

		if ($this->authnContext === NULL || $this->authnInstant === NULL) {
			/* No authentication context or AuthnInstant => no authentication statement. */
			return;
		}

		$document = $root->ownerDocument;

		$as = $document->createElementNS(SAML2_Const::NS_SAML, 'saml:AuthnStatement');
		$root->appendChild($as);

		$as->setAttribute('AuthnInstant', gmdate('Y-m-d\TH:i:s\Z', $this->authnInstant));

		if ($this->sessionNotOnOrAfter !== NULL) {
			$as->setAttribute('SessionNotOnOrAfter', gmdate('Y-m-d\TH:i:s\Z', $this->sessionNotOnOrAfter));
		}
		if ($this->sessionIndex !== NULL) {
			$as->setAttribute('SessionIndex', $this->sessionIndex);
		}

		$ac = $document->createElementNS(SAML2_Const::NS_SAML, 'saml:AuthnContext');
		$as->appendChild($ac);

		SAML2_Utils::addString($ac, SAML2_Const::NS_SAML, 'saml:AuthnContextClassRef', $this->authnContext);
		SAML2_Utils::addStrings($ac, SAML2_Const::NS_SAML, 'saml:AuthenticatingAuthority', false, $this->AuthenticatingAuthority);
	}


	/**
	 * Add an AttributeStatement-node to the assertion.
	 *
	 * @param DOMElement $root  The assertion element we should add the subject to.
	 */
	private function addAttributeStatement(DOMElement $root) {

		if (empty($this->attributes)) {
			return;
		}

		$document = $root->ownerDocument;

		$attributeStatement = $document->createElementNS(SAML2_Const::NS_SAML, 'saml:AttributeStatement');
		$root->appendChild($attributeStatement);

		foreach ($this->attributes as $name => $values) {
			$attribute = $document->createElementNS(SAML2_Const::NS_SAML, 'saml:Attribute');
			$attributeStatement->appendChild($attribute);
			$attribute->setAttribute('Name', $name);

			if ($this->nameFormat !== SAML2_Const::NAMEFORMAT_UNSPECIFIED) {
				$attribute->setAttribute('NameFormat', $this->nameFormat);
			}

			foreach ($values as $value) {
				if (is_string($value)) {
					$type = 'xs:string';
				} elseif (is_int($value)) {
					$type = 'xs:integer';
				} else {
					$type = NULL;
				}

				$attributeValue = $document->createElementNS(SAML2_Const::NS_SAML, 'saml:AttributeValue');
				$attribute->appendChild($attributeValue);
				if ($type !== NULL) {
					$attributeValue->setAttributeNS(SAML2_Const::NS_XSI, 'xsi:type', $type);
				}

				if ($value instanceof DOMNodeList) {
					for ($i = 0; $i < $value->length; $i++) {
						$node = $document->importNode($value->item($i), TRUE);
						$attributeValue->appendChild($node);
					}
				} else {
					$attributeValue->appendChild($document->createTextNode($value));
				}
			}
		}
	}


	/**
	 * Add an EncryptedAttribute Statement-node to the assertion.
	 *
	 * @param DOMElement $root  The assertion element we should add the Encrypted Attribute Statement to.
	 */
	private function addEncryptedAttributeStatement(DOMElement $root) {

		if ($this->requiredEncAttributes == FALSE)
			return;

		$document = $root->ownerDocument;

		$attributeStatement = $document->createElementNS(SAML2_Const::NS_SAML, 'saml:AttributeStatement');
		$root->appendChild($attributeStatement);

		foreach ($this->attributes as $name => $values) {
			$document2 = new DOMDocument();
			$attribute = $document2->createElementNS(SAML2_Const::NS_SAML, 'saml:Attribute');
			$attribute->setAttribute('Name', $name);
			$document2->appendChild($attribute);

			if ($this->nameFormat !== SAML2_Const::NAMEFORMAT_UNSPECIFIED) {
				$attribute->setAttribute('NameFormat', $this->nameFormat);
			}

			foreach ($values as $value) {
				if (is_string($value)) {
					$type = 'xs:string';
				} elseif (is_int($value)) {
					$type = 'xs:integer';
				} else {
					$type = NULL;
				}

				$attributeValue = $document2->createElementNS(SAML2_Const::NS_SAML, 'saml:AttributeValue');
				$attribute->appendChild($attributeValue);
				if ($type !== NULL) {
					$attributeValue->setAttributeNS(SAML2_Const::NS_XSI, 'xsi:type', $type);
				}

				if ($value instanceof DOMNodeList) {
					for ($i = 0; $i < $value->length; $i++) {
						$node = $document2->importNode($value->item($i), TRUE);
						$attributeValue->appendChild($node);
					}
				} else {
					$attributeValue->appendChild($document2->createTextNode($value));
				}
			}
			/*Once the attribute nodes are built, the are encrypted*/
			$EncAssert = new XMLSecEnc();
			$EncAssert->setNode($document2->documentElement);
			$EncAssert->type = 'http://www.w3.org/2001/04/xmlenc#Element';
			/*
			 * Attributes are encrypted with a session key and this one with
			 * $EncryptionKey
			 */
			$symmetricKey = new XMLSecurityKey(XMLSecurityKey::AES256_CBC);
			$symmetricKey->generateSessionKey();
			$EncAssert->encryptKey($this->encryptionKey, $symmetricKey);
			$EncrNode = $EncAssert->encryptNode($symmetricKey);

			$EncAttribute = $document->createElementNS(SAML2_Const::NS_SAML, 'saml:EncryptedAttribute');
			$attributeStatement->appendChild($EncAttribute);
			$n = $document->importNode($EncrNode,true);
			$EncAttribute->appendChild($n);
		}
	}

}
