<?php

/**
 * Serializable class used to hold an XML element.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SAML2_XML_Chunk {

	/**
	 * The localName of the element.
	 *
	 * @var string
	 */
	public $localName;


	/**
	 * The namespaceURI of this element.
	 *
	 * @var string
	 */
	public $namespaceURI;


	/**
	 * The DOMElement we contain.
	 *
	 * @var DOMElement
	 */
	private $xml;


	/**
	 * The DOMElement as a text string. Used during serialization.
	 *
	 * @var string|NULL
	 */
	private $xmlString;


	/**
	 * Create a XMLChunk from a copy of the given DOMElement.
	 *
	 * @param DOMElement $xml  The element we should copy.
	 */
	public function __construct(DOMElement $xml) {

		$this->localName = $xml->localName;
		$this->namespaceURI = $xml->namespaceURI;

		$this->xml = SAML2_Utils::copyElement($xml);
	}


	/**
	 * Get this DOMElement.
	 *
	 * @return DOMElement  This element.
	 */
	public function getXML() {
		assert('$this->xml instanceof DOMElement || is_string($this->xmlString)');

		if ($this->xml === NULL) {
			$doc = new DOMDocument();
			$doc->loadXML($this->xmlString);
			$this->xml = $doc->firstChild;
		}

		return $this->xml;
	}


	/**
	 * Append this XML element to a different XML element.
	 *
	 * @param DOMElement $parent  The element we should append this element to.
	 * @return DOMElement  The new element.
	 */
	public function toXML(DOMElement $parent) {

		return SAML2_Utils::copyElement($this->getXML(), $parent);
	}


	/**
	 * Serialization handler.
	 *
	 * Converts the XML data to a string that can be serialized
	 *
	 * @return array  List of properties that should be serialized.
	 */
	public function __sleep() {
		assert('$this->xml instanceof DOMElement || is_string($this->xmlString)');

		if ($this->xmlString === NULL) {
			$this->xmlString = $this->xml->ownerDocument->saveXML($this->xml);
		}

		return array('xmlString', 'localName', 'namespaceURI');
	}

}
