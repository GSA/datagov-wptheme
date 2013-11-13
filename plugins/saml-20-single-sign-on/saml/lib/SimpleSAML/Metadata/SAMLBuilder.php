<?php

/**
 * Class for generating SAML 2.0 metadata from simpleSAMLphp metadata arrays.
 *
 * This class builds SAML 2.0 metadata for an entity by examining the metadata for the entity.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_Metadata_SAMLBuilder {



	/**
	 * The EntityDescriptor we are building.
	 */
	private $entityDescriptor;


	private $maxCache = NULL;
	private $maxDuration = NULL;

	/**
	 * Initialize the builder.
	 *
	 * @param string $entityId  The entity id of the entity.
	 */
	public function __construct($entityId, $maxCache = NULL, $maxDuration = NULL) {
		assert('is_string($entityId)');

		$this->maxCache = $maxCache;
		$this->maxDuration = $maxDuration;

		$this->entityDescriptor = new SAML2_XML_md_EntityDescriptor();
		$this->entityDescriptor->entityID = $entityId;
	}

	private function setExpiration($metadata) {

		if (array_key_exists('expire', $metadata)) {
			if ($metadata['expire'] - time() < $this->maxDuration)
				$this->maxDuration = $metadata['expire'] - time();
		}

		if ($this->maxCache !== NULL)
			$this->entityDescriptor->cacheDuration = 'PT' . $this->maxCache . 'S';
		if ($this->maxDuration !== NULL)
			$this->entityDescriptor->validUntil = time() + $this->maxDuration;
	}


	/**
	 * Retrieve the EntityDescriptor.
	 *
	 * Retrieve the EntityDescriptor element which is generated for this entity.
	 * @return DOMElement  The EntityDescriptor element for this entity.
	 */
	public function getEntityDescriptor() {

		$xml = $this->entityDescriptor->toXML();
		$xml->ownerDocument->appendChild($xml);

		return $xml;
	}


	/**
	 * Retrieve the EntityDescriptor as text.
	 *
	 * This function serializes this EntityDescriptor, and returns it as text.
	 *
	 * @param bool $formatted  Whether the returned EntityDescriptor should be
	 *                         formatted first.
	 * @return string  The serialized EntityDescriptor.
	 */
	public function getEntityDescriptorText($formatted = TRUE) {
		assert('is_bool($formatted)');

		$xml = $this->getEntityDescriptor();
		if ($formatted) {
			SimpleSAML_Utilities::formatDOMElement($xml);
		}

		return $xml->ownerDocument->saveXML();
	}


	/**
	 * @param SimpleSAML_Configuration $metadata  Metadata.
	 * @param $e Reference to the element where the Extensions element should be included.
	 */
	private function addExtensions(SimpleSAML_Configuration $metadata, SAML2_XML_md_RoleDescriptor $e) {

		if ($metadata->hasValue('tags')) {
			$a = new SAML2_XML_saml_Attribute();
			$a->Name = 'tags';
			foreach ($metadata->getArray('tags') as $tag) {
				$a->AttributeValue[] = new SAML2_XML_saml_AttributeValue($tag);
			}
			$e->Extensions[] = $a;
		}

		if ($metadata->hasValue('hint.cidr')) {
			$a = new SAML2_XML_saml_Attribute();
			$a->Name = 'hint.cidr';
			foreach ($metadata->getArray('hint.cidr') as $hint) {
				$a->AttributeValue[] = new SAML2_XML_saml_AttributeValue($hint);
			}
			$e->Extensions[] = $a;
		}

		if ($metadata->hasValue('scope')) {
			foreach ($metadata->getArray('scope') as $scopetext) {
				$s = new SAML2_XML_shibmd_Scope();
				$s->scope = $scopetext;
				$s->regexp = FALSE;
				$e->Extensions[] = $s;
			}
		}

		if ($metadata->hasValue('EntityAttributes')) {
			$ea = new SAML2_XML_mdattr_EntityAttributes();
			foreach ($metadata->getArray('EntityAttributes') as $attributeName => $attributeValues) {
				$a = new SAML2_XML_saml_Attribute();
				$a->Name = $attributeName;
				$a->NameFormat = 'urn:oasis:names:tc:SAML:2.0:attrname-format:uri';

				// Attribute names that is not URI is prefixed as this: '{nameformat}name'
				if (preg_match('/^\{(.*?)\}(.*)$/', $attributeName, $matches)) {
					$a->Name = $matches[2];
					$nameFormat = $matches[1];
					if ($nameFormat !== SAML2_Const::NAMEFORMAT_UNSPECIFIED) {
						$a->NameFormat = $nameFormat;
					}
				}
				foreach ($attributeValues as $attributeValue) {
					$a->AttributeValue[] = new SAML2_XML_saml_AttributeValue($attributeValue);
				}
				$ea->children[] = $a;
			}
			$this->entityDescriptor->Extensions[] = $ea;
		}

		if ($metadata->hasValue('UIInfo')) {
			$ui = new SAML2_XML_mdui_UIInfo();
			foreach ($metadata->getArray('UIInfo') as $uiName => $uiValues) {
				switch ($uiName) {
					case 'DisplayName':
						$ui->DisplayName = $uiValues;
					break;
					case 'Description':
						$ui->Description = $uiValues;
					break;
					case 'InformationURL':
						$ui->InformationURL = $uiValues;
					break;
					case 'PrivacyStatementURL':
						$ui->PrivacyStatementURL = $uiValues;
					break;
					case 'Keywords':
						foreach ($uiValues as $lang => $keywords) {
							$uiItem = new SAML2_XML_mdui_Keywords();
							$uiItem->lang = $lang;
							$uiItem->Keywords = $keywords;
							$ui->Keywords[] = $uiItem;
						}
					break;
					case 'Logo':
						foreach ($uiValues as $logo) {
							$uiItem = new SAML2_XML_mdui_Logo();
							$uiItem->url    = $logo['url'];
							$uiItem->width  = $logo['width'];
							$uiItem->height = $logo['height'];
							if (isset($logo['lang'])) {
								$uiItem->lang = $logo['lang'];
							}
							$ui->Logo[] = $uiItem;
						}
					break;
				}
			}
			$e->Extensions[] = $ui;
		}

		if ($metadata->hasValue('DiscoHints')) {
			$dh = new SAML2_XML_mdui_DiscoHints();
			foreach ($metadata->getArray('DiscoHints') as $dhName => $dhValues) {
				switch ($dhName) {
					case 'IPHint':
						$dh->IPHint = $dhValues;
					break;
					case 'DomainHint':
						$dh->DomainHint = $dhValues;
					break;
					case 'GeolocationHint':
						$dh->GeolocationHint = $dhValues;
					break;
				}
			}
			$e->Extensions[] = $dh;
		}
	}


	/**
	 * Add Organization element.
	 *
	 * This function adds an organization element to the metadata.
	 *
	 * @param array $orgName  An array with the localized OrganizatioName.
	 * @param array $orgDisplayName  An array with the localized OrganizatioDisplayName.
	 * @param array $orgURL  An array with the localized OrganizatioURL.
	 */
	public function addOrganization(array $orgName, array $orgDisplayName, array $orgURL) {

		$org = new SAML2_XML_md_Organization();

		$org->OrganizationName = $orgName;
		$org->OrganizationDisplayName = $orgDisplayName;
		$org->OrganizationURL = $orgURL;

		$this->entityDescriptor->Organization = $org;
	}


	/**
	 * Add organization element based on metadata array.
	 *
	 * @param array $metadata  The metadata we should extract the organization information from.
	 */
	public function addOrganizationInfo(array $metadata) {

		if (
			empty($metadata['OrganizationName']) ||
			empty($metadata['OrganizationDisplayName']) ||
			empty($metadata['OrganizationURL'])
		    ) {
			/* Empty or incomplete organization information. */
			return;
		}

		$orgName = SimpleSAML_Utilities::arrayize($metadata['OrganizationName'], 'en');
		$orgDisplayName = SimpleSAML_Utilities::arrayize($metadata['OrganizationDisplayName'], 'en');
		$orgURL = SimpleSAML_Utilities::arrayize($metadata['OrganizationURL'], 'en');

		$this->addOrganization($orgName, $orgDisplayName, $orgURL);
	}


	/**
	 * Add endpoint list to metadata.
	 *
	 * @param array $endpoints  The endpoints.
	 * @param bool $indexed  Whether the endpoints should be indexed.
	 * @return array  Array of endpoint objects.
	 */
	private static function createEndpoints(array $endpoints, $indexed) {
		assert('is_bool($indexed)');

		$ret = array();

		foreach ($endpoints as &$ep) {
			if ($indexed) {
				$t = new SAML2_XML_md_IndexedEndpointType();
			} else {
				$t = new SAML2_XML_md_EndpointType();
			}

			$t->Binding = $ep['Binding'];
			$t->Location = $ep['Location'];
			if (isset($ep['ResponseLocation'])) {
				$t->ResponseLocation = $ep['ResponseLocation'];
			}
			if (isset($ep['hoksso:ProtocolBinding'])) {
			    $t->setAttributeNS(SAML2_Const::NS_HOK, 'hoksso:ProtocolBinding', SAML2_Const::BINDING_HTTP_REDIRECT);
			}

			if ($indexed) {
				if (!isset($ep['index'])) {
					/* Find the maximum index. */
					$maxIndex = -1;
					foreach ($endpoints as $ep) {
						if (!isset($ep['index'])) {
							continue;
						}

						if ($ep['index'] > $maxIndex) {
							$maxIndex = $ep['index'];
						}
					}

					$ep['index'] = $maxIndex + 1;
				}

				$t->index = $ep['index'];
			}

			$ret[] = $t;
		}

		return $ret;
	}


	/**
	 * Add an AttributeConsumingService element to the metadata.
	 *
	 * @param DOMElement $spDesc  The SPSSODescriptor element.
	 * @param SimpleSAML_Configuration $metadata  The metadata.
	 */
	private function addAttributeConsumingService(SAML2_XML_md_SPSSODescriptor $spDesc, SimpleSAML_Configuration $metadata) {
		$attributes = $metadata->getArray('attributes', array());
		$name = $metadata->getLocalizedString('name', NULL);

		if ($name === NULL || count($attributes) == 0) {
			/* We cannot add an AttributeConsumingService without name and attributes. */
			return;
		}

		/*
		 * Add an AttributeConsumingService element with information as name and description and list
		 * of requested attributes
		 */
		$attributeconsumer = new SAML2_XML_md_AttributeConsumingService();

		$attributeconsumer->index = 0;

		$attributeconsumer->ServiceName = $name;
		$attributeconsumer->ServiceDescription = $metadata->getLocalizedString('description', array());

		$nameFormat = $metadata->getString('attributes.NameFormat', SAML2_Const::NAMEFORMAT_UNSPECIFIED);
		foreach ($attributes as $attribute) {
			$t = new SAML2_XML_md_RequestedAttribute();
			$t->Name = $attribute;
			if ($nameFormat !== SAML2_Const::NAMEFORMAT_UNSPECIFIED) {
				$t->NameFormat = $nameFormat;
			}
			$attributeconsumer->RequestedAttribute[] = $t;
		}

		$spDesc->AttributeConsumingService[] = $attributeconsumer;
	}


	/**
	 * Add metadata set for entity.
	 *
	 * This function is used to add a metadata array to the entity.
	 *
	 * @param string $set  The metadata set this metadata comes from.
	 * @param array $metadata  The metadata.
	 */
	public function addMetadata($set, $metadata) {
		assert('is_string($set)');
		assert('is_array($metadata)');
		
		$this->setExpiration($metadata);

		switch ($set) {
		case 'saml20-sp-remote':
			$this->addMetadataSP20($metadata);
			break;
		case 'saml20-idp-remote':
			$this->addMetadataIdP20($metadata);
			break;
		case 'shib13-sp-remote':
			$this->addMetadataSP11($metadata);
			break;
		case 'shib13-idp-remote':
			$this->addMetadataIdP11($metadata);
			break;
		case 'attributeauthority-remote':
			$this->addAttributeAuthority($metadata);
			break;
		default:
			SimpleSAML_Logger::warning('Unable to generate metadata for unknown type \'' . $set . '\'.');
		}
		
	}

	/**
	 * Add SAML 2.0 SP metadata.
	 *
	 * @param array $metadata  The metadata.
	 */
	public function addMetadataSP20($metadata) {
		assert('is_array($metadata)');
		assert('isset($metadata["entityid"])');
		assert('isset($metadata["metadata-set"])');

		$metadata = SimpleSAML_Configuration::loadFromArray($metadata, $metadata['entityid']);

		$e = new SAML2_XML_md_SPSSODescriptor();
		$e->protocolSupportEnumeration[] = 'urn:oasis:names:tc:SAML:2.0:protocol';


		$this->addExtensions($metadata, $e);

		$this->addCertificate($e, $metadata);

		$e->SingleLogoutService = self::createEndpoints($metadata->getEndpoints('SingleLogoutService'), FALSE);

		$e->NameIDFormat = $metadata->getArrayizeString('NameIDFormat', array());

		$endpoints = $metadata->getEndpoints('AssertionConsumerService');
		foreach ($metadata->getArrayizeString('AssertionConsumerService.artifact', array()) as $acs) {
			$endpoints[] = array(
				'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact',
				'Location' => $acs,
			);
		}
		$e->AssertionConsumerService = self::createEndpoints($endpoints, TRUE);

		$this->addAttributeConsumingService($e, $metadata);

		$this->entityDescriptor->RoleDescriptor[] = $e;

		foreach ($metadata->getArray('contacts', array()) as $contact) {
			if (array_key_exists('contactType', $contact) && array_key_exists('emailAddress', $contact)) {
				$this->addContact($contact['contactType'], $contact);
			}
		}

	}


	/**
	 * Add SAML 2.0 IdP metadata.
	 *
	 * @param array $metadata  The metadata.
	 */
	public function addMetadataIdP20($metadata) {
		assert('is_array($metadata)');
		assert('isset($metadata["entityid"])');
		assert('isset($metadata["metadata-set"])');

		$metadata = SimpleSAML_Configuration::loadFromArray($metadata, $metadata['entityid']);

		$e = new SAML2_XML_md_IDPSSODescriptor();
		$e->protocolSupportEnumeration[] = 'urn:oasis:names:tc:SAML:2.0:protocol';

		if ($metadata->getBoolean('redirect.sign', FALSE)) {
			$e->WantAuthnRequestSigned = TRUE;
		}

		$this->addExtensions($metadata, $e);

		$this->addCertificate($e, $metadata);

		if ($metadata->hasValue('ArtifactResolutionService')){
			$e->ArtifactResolutionService = self::createEndpoints($metadata->getEndpoints('ArtifactResolutionService'), TRUE);
		}

		$e->SingleLogoutService = self::createEndpoints($metadata->getEndpoints('SingleLogoutService'), FALSE);

		$e->NameIDFormat = $metadata->getArrayizeString('NameIDFormat', array());

		$e->SingleSignOnService = self::createEndpoints($metadata->getEndpoints('SingleSignOnService'), FALSE);

		$this->entityDescriptor->RoleDescriptor[] = $e;

		foreach ($metadata->getArray('contacts', array()) as $contact) {
			if (array_key_exists('contactType', $contact) && array_key_exists('emailAddress', $contact)) {
				$this->addContact($contact['contactType'], $contact);
			}
		}

	}


	/**
	 * Add SAML 1.1 SP metadata.
	 *
	 * @param array $metadata  The metadata.
	 */
	public function addMetadataSP11($metadata) {
		assert('is_array($metadata)');
		assert('isset($metadata["entityid"])');
		assert('isset($metadata["metadata-set"])');

		$metadata = SimpleSAML_Configuration::loadFromArray($metadata, $metadata['entityid']);

		$e = new SAML2_XML_md_SPSSODescriptor();
		$e->protocolSupportEnumeration[] = 'urn:oasis:names:tc:SAML:1.1:protocol';

		$this->addCertificate($e, $metadata);

		$e->NameIDFormat = $metadata->getArrayizeString('NameIDFormat', array());

		$endpoints = $metadata->getEndpoints('AssertionConsumerService');
		foreach ($metadata->getArrayizeString('AssertionConsumerService.artifact', array()) as $acs) {
			$endpoints[] = array(
				'Binding' => 'urn:oasis:names:tc:SAML:1.0:profiles:artifact-01',
				'Location' => $acs,
			);
		}
		$e->AssertionConsumerService = self::createEndpoints($endpoints, TRUE);

		$this->addAttributeConsumingService($e, $metadata);

		$this->entityDescriptor->RoleDescriptor[] = $e;
	}


	/**
	 * Add SAML 1.1 IdP metadata.
	 *
	 * @param array $metadata  The metadata.
	 */
	public function addMetadataIdP11($metadata) {
		assert('is_array($metadata)');
		assert('isset($metadata["entityid"])');
		assert('isset($metadata["metadata-set"])');

		$metadata = SimpleSAML_Configuration::loadFromArray($metadata, $metadata['entityid']);

		$e = new SAML2_XML_md_IDPSSODescriptor();
		$e->protocolSupportEnumeration[] = 'urn:oasis:names:tc:SAML:1.1:protocol';
		$e->protocolSupportEnumeration[] = 'urn:mace:shibboleth:1.0';

		$this->addCertificate($e, $metadata);

		$e->NameIDFormat = $metadata->getArrayizeString('NameIDFormat', array());

		$e->SingleSignOnService = self::createEndpoints($metadata->getEndpoints('SingleSignOnService'), FALSE);

		$this->entityDescriptor->RoleDescriptor[] = $e;
	}


	/**
	 * Add a AttributeAuthorityDescriptor.
	 *
	 * @param array $metadata  The AttributeAuthorityDescriptor, in the format returned by SAMLParser.
	 */
	public function addAttributeAuthority(array $metadata) {
		assert('is_array($metadata)');
		assert('isset($metadata["entityid"])');
		assert('isset($metadata["metadata-set"])');

		$metadata = SimpleSAML_Configuration::loadFromArray($metadata, $metadata['entityid']);

		$e = new SAML2_XML_md_AttributeAuthorityDescriptor();
		$e->protocolSupportEnumeration = $metadata->getArray('protocols', array());

		$this->addExtensions($metadata, $e);
		$this->addCertificate($e, $metadata);

		$e->AttributeService = self::createEndpoints($metadata->getEndpoints('AttributeService'), FALSE);
		$e->AssertionIDRequestService = self::createEndpoints($metadata->getEndpoints('AssertionIDRequestService'), FALSE);

		$e->NameIDFormat = $metadata->getArrayizeString('NameIDFormat', array());

		$this->entityDescriptor->RoleDescriptor[] = $e;
	}


	/**
	 * Add contact information.
	 *
	 * Accepts a contact type, and an array of the following elements (all are optional):
	 * - emailAddress     Email address (as string), or array of email addresses.
	 * - telephoneNumber  Telephone number of contact (as string), or array of telephone numbers.
	 * - name             Full name of contact, either as <GivenName> <SurName>, or as <SurName>, <GivenName>.
	 * - surName          Surname of contact.
	 * - givenName        Givenname of contact.
	 * - company          Company name of contact.
	 *
	 * 'name' will only be used if neither givenName nor surName is present.
	 *
	 * The following contact types are allowed:
	 * "technical", "support", "administrative", "billing", "other"
	 *
	 * @param string $type  The type of contact.
	 * @param array $details  The details about the contact.
	 */
	public function addContact($type, $details) {
		assert('is_string($type)');
		assert('is_array($details)');
		assert('in_array($type, array("technical", "support", "administrative", "billing", "other"), TRUE)');

		/* Parse name into givenName and surName. */
		if (isset($details['name']) && empty($details['surName']) && empty($details['givenName'])) {
			$names = explode(',', $details['name'], 2);
			if (count($names) === 2) {
				$details['surName'] = trim($names[0]);
				$details['givenName'] = trim($names[1]);
			} else {
				$names = explode(' ', $details['name'], 2);
				if (count($names) === 2) {
					$details['givenName'] = trim($names[0]);
					$details['surName'] = trim($names[1]);
				} else {
					$details['surName'] = trim($names[0]);
				}
			}
		}

		$e = new SAML2_XML_md_ContactPerson();
		$e->contactType = $type;

		if (isset($details['company'])) {
			$e->Company = $details['company'];
		}
		if (isset($details['givenName'])) {
			$e->GivenName = $details['givenName'];
		}
		if (isset($details['surName'])) {
			$e->SurName = $details['surName'];
		}

		if (isset($details['emailAddress'])) {
			$eas = $details['emailAddress'];
			if (!is_array($eas)) {
				$eas = array($eas);
			}
			foreach ($eas as $ea) {
				$e->EmailAddress[] = $ea;
			}
		}

		if (isset($details['telephoneNumber'])) {
			$tlfNrs = $details['telephoneNumber'];
			if (!is_array($tlfNrs)) {
				$tlfNrs = array($tlfNrs);
			}
			foreach ($tlfNrs as $tlfNr) {
				$e->TelephoneNumber[] = $tlfNr;
			}
		}

		$this->entityDescriptor->ContactPerson[] = $e;
	}


	/**
	 * Add a KeyDescriptor with an X509 certificate.
	 *
	 * @param SAML2_XML_md_RoleDescriptor $rd  The RoleDescriptor the certificate should be added to.
	 * @param string $use  The value of the use-attribute.
	 * @param string $x509data  The certificate data.
	 */
	private function addX509KeyDescriptor(SAML2_XML_md_RoleDescriptor $rd, $use, $x509data) {
		assert('in_array($use, array("encryption", "signing"), TRUE)');
		assert('is_string($x509data)');

		$keyDescriptor = SAML2_Utils::createKeyDescriptor($x509data);
		$keyDescriptor->use = $use;
		$rd->KeyDescriptor[] = $keyDescriptor;
	}


	/**
	 * Add certificate.
	 *
	 * Helper function for adding a certificate to the metadata.
	 *
	 * @param SAML2_XML_md_RoleDescriptor $rd  The RoleDescriptor the certificate should be added to.
	 * @param SimpleSAML_Configuration $metadata  The metadata for the entity.
	 */
	private function addCertificate(SAML2_XML_md_RoleDescriptor $rd, SimpleSAML_Configuration $metadata) {

		$keys = $metadata->getPublicKeys();
		if ($keys !== NULL) {
			foreach ($keys as $key) {
				if ($key['type'] !== 'X509Certificate') {
					continue;
				}
				if (!isset($key['signing']) || $key['signing'] === TRUE) {
					$this->addX509KeyDescriptor($rd, 'signing', $key['X509Certificate']);
				}
				if (!isset($key['encryption']) || $key['encryption'] === TRUE) {
					$this->addX509KeyDescriptor($rd, 'encryption', $key['X509Certificate']);
				}
			}
		}

		if ($metadata->hasValue('https.certData')) {
			$this->addX509KeyDescriptor($rd, 'signing', $metadata->getString('https.certData'));
		}
	}

}
