<?php

/**
 * Class for SAML 2 authentication request messages.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SAML2_AuthnRequest extends SAML2_Request {

	/**
	 * The options for what type of name identifier should be returned.
	 *
	 * @var array
	 */
	private $nameIdPolicy;

	/**
	 * Whether the Identity Provider must authenticate the user again.
	 *
	 * @var bool
	 */
	private $forceAuthn;


	/**
	 * Set to TRUE if this request is passive.
	 *
	 * @var bool.
	 */
	private $isPassive;

	/**
	 * The list of providerIDs in this request's scoping element
	 *
	 * @var array
	*/
	private $IDPList = array();

	/**
	 * The ProxyCount in this request's scoping element
	 *
	 * @var int
	*/
	private $ProxyCount = null;

	/**
	 * The RequesterID list in this request's scoping element
	 *
	 * @var array
	*/

	private $RequesterID = array();
	
	/**
	 * The URL of the asertion consumer service where the response should be delivered.
	 *
	 * @var string|NULL
	 */
	private $assertionConsumerServiceURL;


	/**
	 * What binding should be used when sending the response.
	 *
	 * @var string|NULL
	 */
	private $protocolBinding;


	/**
	 * The index of the AssertionConsumerService.
	 *
	 * @var int|NULL
	 */
	private $assertionConsumerServiceIndex;


	/**
	 * What authentication context was requested.
	 *
	 * Array with the following elements.
	 * - AuthnContextClassRef (required)
	 * - Comparison (optinal)
	 *
	 * @var array
	 */
	private $requestedAuthnContext;

	/**
	 * Request extensions.
	 *
	 * @var array
	 */
	private $extensions;

	/**
	 * Constructor for SAML 2 authentication request messages.
	 *
	 * @param DOMElement|NULL $xml  The input message.
	 */
	public function __construct(DOMElement $xml = NULL) {
		parent::__construct('AuthnRequest', $xml);

		$this->nameIdPolicy = array();
		$this->forceAuthn = FALSE;
		$this->isPassive = FALSE;

		if ($xml === NULL) {
			return;
		}

		$this->forceAuthn = SAML2_Utils::parseBoolean($xml, 'ForceAuthn', FALSE);
		$this->isPassive = SAML2_Utils::parseBoolean($xml, 'IsPassive', FALSE);

		if ($xml->hasAttribute('AssertionConsumerServiceURL')) {
			$this->assertionConsumerServiceURL = $xml->getAttribute('AssertionConsumerServiceURL');
		}

		if ($xml->hasAttribute('ProtocolBinding')) {
			$this->protocolBinding = $xml->getAttribute('ProtocolBinding');
		}

		if ($xml->hasAttribute('AssertionConsumerServiceIndex')) {
			$this->assertionConsumerServiceIndex = (int)$xml->getAttribute('AssertionConsumerServiceIndex');
		}

		$nameIdPolicy = SAML2_Utils::xpQuery($xml, './saml_protocol:NameIDPolicy');

		if (!empty($nameIdPolicy)) {
			$nameIdPolicy = $nameIdPolicy[0];
			if ($nameIdPolicy->hasAttribute('Format')) {
				$this->nameIdPolicy['Format'] = $nameIdPolicy->getAttribute('Format');
			}
			if ($nameIdPolicy->hasAttribute('SPNameQualifier')) {
				$this->nameIdPolicy['SPNameQualifier'] = $nameIdPolicy->getAttribute('SPNameQualifier');
			}
			if ($nameIdPolicy->hasAttribute('AllowCreate')) {
				$this->nameIdPolicy['AllowCreate'] = SAML2_Utils::parseBoolean($nameIdPolicy, 'AllowCreate', FALSE);
			}
		}

		$requestedAuthnContext = SAML2_Utils::xpQuery($xml, './saml_protocol:RequestedAuthnContext');
		if (!empty($requestedAuthnContext)) {
			$requestedAuthnContext = $requestedAuthnContext[0];

			$rac = array(
				'AuthnContextClassRef' => array(),
				'Comparison' => 'exact',
			);

			$accr = SAML2_Utils::xpQuery($requestedAuthnContext, './saml_assertion:AuthnContextClassRef');
			foreach ($accr as $i) {
				$rac['AuthnContextClassRef'][] = trim($i->textContent);
			}

			if ($requestedAuthnContext->hasAttribute('Comparison')) {
				$rac['Comparison'] = $requestedAuthnContext->getAttribute('Comparison');
			}

			$this->requestedAuthnContext = $rac;
		}

		$scoping = SAML2_Utils::xpQuery($xml, './saml_protocol:Scoping');
		if (!empty($scoping)) {
			$scoping =$scoping[0];
			
			if ($scoping->hasAttribute('ProxyCount')) {
				$this->ProxyCount = (int)$scoping->getAttribute('ProxyCount');
			}
			$idpEntries = SAML2_Utils::xpQuery($scoping, './saml_protocol:IDPList/saml_protocol:IDPEntry');

			foreach($idpEntries as $idpEntry) {
				if (!$idpEntry->hasAttribute('ProviderID')) {
					throw new Exception("Could not get ProviderID from Scoping/IDPEntry element in AuthnRequest object");
				}
				$this->IDPList[] = $idpEntry->getAttribute('ProviderID');
			}
		
			$requesterIDs = SAML2_Utils::xpQuery($scoping, './saml_protocol:RequesterID');
			foreach ($requesterIDs as $requesterID) {
				$this->RequesterID[] = trim($requesterID->textContent);
			}

		}

		$this->extensions = SAML2_XML_samlp_Extensions::getList($xml);
	}


	/**
	 * Retrieve the NameIdPolicy.
	 *
	 * @see SAML2_AuthnRequest::setNameIdPolicy()
	 * @return array  The NameIdPolicy.
	 */
	public function getNameIdPolicy() {
		return $this->nameIdPolicy;
	}


	/**
	 * Set the NameIDPolicy.
	 *
	 * This function accepts an array with the following options:
	 *  - 'Format'
	 *  - 'SPNameQualifier'
	 *  - 'AllowCreate'
	 *
	 * @param array $nameIdPolicy  The NameIDPolicy.
	 */
	public function setNameIdPolicy(array $nameIdPolicy) {

		$this->nameIdPolicy = $nameIdPolicy;
	}


	/**
	 * Retrieve the value of the ForceAuthn attribute.
	 *
	 * @return bool  The ForceAuthn attribute.
	 */
	public function getForceAuthn() {
		return $this->forceAuthn;
	}


	/**
	 * Set the value of the ForceAuthn attribute.
	 *
	 * @param bool $forceAuthn  The ForceAuthn attribute.
	 */
	public function setForceAuthn($forceAuthn) {
		assert('is_bool($forceAuthn)');

		$this->forceAuthn = $forceAuthn;
	}


	/**
	 * Retrieve the value of the IsPassive attribute.
	 *
	 * @return bool  The IsPassive attribute.
	 */
	public function getIsPassive() {
		return $this->isPassive;
	}


	/**
	 * Set the value of the IsPassive attribute.
	 *
	 * @param bool $isPassive  The IsPassive attribute.
	 */
	public function setIsPassive($isPassive) {
		assert('is_bool($isPassive)');

		$this->isPassive = $isPassive;
	}


	/**
	 * This function sets the scoping for the request
	 * See Core 3.4.1.2 for the definition of scoping
	 * Currently we only support an IDPList of idpEntries
	 * and only the required ProviderID in an IDPEntry
	 * $providerIDs is an array of Entity Identifiers
	 *
	 */
	public function setIDPList($IDPList) {
		assert('is_array($IDPList)');
		$this->IDPList = $IDPList;
	}


	/**
	 * This function retrieves the list of providerIDs from this authentication request.
	 * Currently we only support a list of ipd ientity id's.
	 * @return The list of idpidentityids from the request
	 */
	 
	public function getIDPList() {
		return $this->IDPList;
	}

	public function setProxyCount($ProxyCount) {
		assert('is_int($ProxyCount)');
		$this->ProxyCount = $ProxyCount;
	}

	public function getProxyCount() {
		return $this->ProxyCount;
	}
	
	public function setRequesterID(array $RequesterID) {
		$this->RequesterID = $RequesterID;
	}

	public function getRequesterID() {
		return $this->RequesterID;
	}

	/**
	 * Retrieve the value of the AssertionConsumerServiceURL attribute.
	 *
	 * @return string|NULL  The AssertionConsumerServiceURL attribute.
	 */
	public function getAssertionConsumerServiceURL() {
		return $this->assertionConsumerServiceURL;
	}


	/**
	 * Set the value of the AssertionConsumerServiceURL attribute.
	 *
	 * @param string|NULL $assertionConsumerServiceURL  The AssertionConsumerServiceURL attribute.
	 */
	public function setAssertionConsumerServiceURL($assertionConsumerServiceURL) {
		assert('is_string($assertionConsumerServiceURL) || is_null($assertionConsumerServiceURL)');

		$this->assertionConsumerServiceURL = $assertionConsumerServiceURL;
	}


	/**
	 * Retrieve the value of the ProtocolBinding attribute.
	 *
	 * @return string|NULL  The ProtocolBinding attribute.
	 */
	public function getProtocolBinding() {
		return $this->protocolBinding;
	}


	/**
	 * Set the value of the ProtocolBinding attribute.
	 *
	 * @param string $protocolBinding  The ProtocolBinding attribute.
	 */
	public function setProtocolBinding($protocolBinding) {
		assert('is_string($protocolBinding) || is_null($protocolBinding)');

		$this->protocolBinding = $protocolBinding;
	}


	/**
	 * Retrieve the value of the AssertionConsumerServiceIndex attribute.
	 *
	 * @return int|NULL  The AssertionConsumerServiceIndex attribute.
	 */
	public function getAssertionConsumerServiceIndex() {
		return $this->assertionConsumerServiceIndex;
	}


	/**
	 * Set the value of the AssertionConsumerServiceIndex attribute.
	 *
	 * @param string|NULL $assertionConsumerServiceIndex  The AssertionConsumerServiceIndex attribute.
	 */
	public function setAssertionConsumerServiceIndex($assertionConsumerServiceIndex) {
		assert('is_int($assertionConsumerServiceIndex) || is_null($assertionConsumerServiceIndex)');

		$this->assertionConsumerServiceIndex = $assertionConsumerServiceIndex;
	}


	/**
	 * Retrieve the RequestedAuthnContext.
	 *
	 * @return array|NULL  The RequestedAuthnContext.
	 */
	public function getRequestedAuthnContext() {
		return $this->requestedAuthnContext;
	}


	/**
	 * Set the RequestedAuthnContext.
	 *
	 * @param array|NULL $requestedAuthnContext  The RequestedAuthnContext.
	 */
	public function setRequestedAuthnContext($requestedAuthnContext) {
		assert('is_array($requestedAuthnContext) || is_null($requestedAuthnContext)');

		$this->requestedAuthnContext = $requestedAuthnContext;
	}


	/**
	 * Retrieve the Extensions.
	 *
	 * @return SAML2_XML_samlp_Extensions.
	 */
	public function getExtensions() {
		return $this->extensions;
	}


	/**
	 * Set the Extensions.
	 *
	 * @param array|NULL $extensions The Extensions.
	 */
	public function setExtensions($extensions) {
		assert('is_array($extensions) || is_null($extensions)');

		$this->extensions = $extensions;
	}


	/**
	 * Convert this authentication request to an XML element.
	 *
	 * @return DOMElement  This authentication request.
	 */
	public function toUnsignedXML() {

		$root = parent::toUnsignedXML();

		if ($this->forceAuthn) {
			$root->setAttribute('ForceAuthn', 'true');
		}

		if ($this->isPassive) {
			$root->setAttribute('IsPassive', 'true');
		}

		if ($this->assertionConsumerServiceURL !== NULL) {
			$root->setAttribute('AssertionConsumerServiceURL', $this->assertionConsumerServiceURL);
		}

		if ($this->protocolBinding !== NULL) {
			$root->setAttribute('ProtocolBinding', $this->protocolBinding);
		}

		if (!empty($this->nameIdPolicy)) {
			$nameIdPolicy = $this->document->createElementNS(SAML2_Const::NS_SAMLP, 'NameIDPolicy');
			if (array_key_exists('Format', $this->nameIdPolicy)) {
				$nameIdPolicy->setAttribute('Format', $this->nameIdPolicy['Format']);
			}
			if (array_key_exists('SPNameQualifier', $this->nameIdPolicy)) {
				$nameIdPolicy->setAttribute('SPNameQualifier', $this->nameIdPolicy['SPNameQualifier']);
			}
			if (array_key_exists('AllowCreate', $this->nameIdPolicy) && $this->nameIdPolicy['AllowCreate']) {
				$nameIdPolicy->setAttribute('AllowCreate', 'true');
			}
			$root->appendChild($nameIdPolicy);
		}

		$rac = $this->requestedAuthnContext;
		if (!empty($rac) && !empty($rac['AuthnContextClassRef'])) {
			$e = $this->document->createElementNS(SAML2_Const::NS_SAMLP, 'RequestedAuthnContext');
			$root->appendChild($e);
			if (isset($rac['Comparison']) && $rac['Comparison'] !== 'exact') {
				$e->setAttribute('Comparison', $rac['Comparison']);
			}
			foreach ($rac['AuthnContextClassRef'] as $accr) {
				SAML2_Utils::addString($e, SAML2_Const::NS_SAML, 'AuthnContextClassRef', $accr);
			}
		}

		if (!empty($this->extensions)) {
			SAML2_XML_samlp_Extensions::addList($root, $this->extensions);
		}

		if ($this->ProxyCount !== null || count($this->IDPList) > 0 || count($this->RequesterID) > 0) {
			$scoping = $this->document->createElementNS(SAML2_Const::NS_SAMLP, 'Scoping');
			if ($this->ProxyCount !== null) {
				$scoping->setAttribute('ProxyCount', $this->ProxyCount);
			}
			if (count($this->IDPList) > 0) {
				$idplist = $this->document->createElementNS(SAML2_Const::NS_SAMLP, 'IDPList');
				foreach ($this->IDPList as $provider) {
					$idpEntry = $this->document->createElementNS(SAML2_Const::NS_SAMLP, 'IDPEntry');
					$idpEntry->setAttribute('ProviderID', $provider);
					$idplist->appendChild($idpEntry);
				}
				$scoping->appendChild($idplist);
				$root->appendChild($scoping);
			}
			if (count($this->RequesterID) > 0) {
				SAML2_Utils::addStrings($scoping, SAML2_Const::NS_SAMLP, 'RequesterID', FALSE, $this->RequesterID);
			}
		}

		return $root;
	}

}


?>