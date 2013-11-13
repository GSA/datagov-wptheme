<?php

/**
 * Class representing SAML 2 IndexedEndpointType.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SAML2_XML_md_IndexedEndpointType extends SAML2_XML_md_EndpointType {

	/**
	 * The index for this endpoint.
	 *
	 * @var int
	 */
	public $index;


	/**
	 * Whether this endpoint is the default.
	 *
	 * @var bool|NULL
	 */
	public $isDefault = NULL;


	/**
	 * Initialize an IndexedEndpointType.
	 *
	 * @param DOMElement|NULL $xml  The XML element we should load.
	 */
	public function __construct(DOMElement $xml = NULL) {
		parent::__construct($xml);

		if ($xml === NULL) {
			return;
		}

		if (!$xml->hasAttribute('index')) {
			throw new Exception('Missing index on ' . $xml->tagName);
		}
		$this->index = (int)$xml->getAttribute('index');

		$this->isDefault = SAML2_Utils::parseBoolean($xml, 'isDefault', NULL);
	}


	/**
	 * Add this endpoint to an XML element.
	 *
	 * @param DOMElement $parent  The element we should append this endpoint to.
	 * @param string $name  The name of the element we should create.
	 */
	public function toXML(DOMElement $parent, $name) {
		assert('is_string($name)');
		assert('is_int($this->index)');
		assert('is_null($this->isDefault) || is_bool($this->isDefault)');

		$e = parent::toXML($parent, $name);
		$e->setAttribute('index', (string)$this->index);

		if ($this->isDefault === TRUE) {
			$e->setAttribute('isDefault', 'true');
		} elseif ($this->isDefault === FALSE) {
			$e->setAttribute('isDefault', 'false');
		}

		return $e;
	}

}
