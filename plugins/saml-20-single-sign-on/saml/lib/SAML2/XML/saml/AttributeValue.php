<?php

/**
 * Class representing an AttributeValue.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SAML2_XML_saml_AttributeValue {

	/**
	 * The raw DOMElement representing this value.
	 *
	 * @var DOMElement
	 */
	public $element;


	/**
	 * Create an AttributeValue.
	 *
	 * @param mixed $value
	 * The value of this element.
	 * Can be one of:
	 *  - string                      Create an attribute value with a simple string.
	 *  - DOMElement(AttributeValue)  Create an attribute value of the given DOMElement.
	 *  - DOMElement                  Create an attribute value with the given DOMElement as a child.
	 */
	public function __construct($value) {
		assert('is_string($value) || $value instanceof DOMElement');

		if (is_string($value)) {
			$doc = new DOMDocument();
			$this->element = $doc->createElementNS(SAML2_Const::NS_SAML, 'saml:AttributeValue');
			$this->element->setAttributeNS(SAML2_Const::NS_XSI, 'xsi:type', 'xs:string');
			$this->element->appendChild($doc->createTextNode($value));

			/* Make sure that the xs-namespace is available in the AttributeValue (for xs:string). */
			$this->element->setAttributeNS(SAML2_Const::NS_XS, 'xs:tmp', 'tmp');
			$this->element->removeAttributeNS(SAML2_Const::NS_XS, 'tmp');

			return;
		}

		if ($value->namespaceURI === SAML2_Const::NS_SAML && $value->localName === 'AttributeValue') {
			$this->element = SAML2_Utils::copyElement($value);
			return;
		}

		$doc = new DOMDocument();
		$this->element = $doc->createElementNS(SAML2_Const::NS_SAML, 'saml:AttributeValue');
		SAML2_Utils::copyElement($value, $this->element);
	}


	/**
	 * Append this attribute value to an element.
	 *
	 * @param DOMElement $parent  The element we should append this attribute value to.
	 * @return DOMElement  The generated AttributeValue element.
	 */
	public function toXML(DOMElement $parent) {
		assert('$this->element instanceof DOMElement');
		assert('$this->element->namespaceURI === SAML2_Const::NS_SAML && $this->element->localName === "AttributeValue"');

		$v = SAML2_Utils::copyElement($this->element, $parent);

		return $v;
	}

	/*
	 * Returns a plain text content of the attribute value.
	 */
	public function getString() {
		return $this->element->textContent;
	}


	/**
	 * Convert this attribute value to a string.
	 *
	 * If this element contains XML data, that data vil be encoded as a string and returned.
	 *
	 * @return string  This attribute value.
	 */
	public function __toString() {
		assert('$this->element instanceof DOMElement');

		$doc = $this->element->ownerDocument;

		$ret = '';
		foreach ($this->element->childNodes as $c) {
			$ret .= $doc->saveXML($c);
		}

		return $ret;
	}

}
