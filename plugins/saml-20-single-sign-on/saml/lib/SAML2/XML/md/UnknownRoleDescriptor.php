<?php

/**
 * Class representing unknown RoleDescriptors.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SAML2_XML_md_UnknownRoleDescriptor extends SAML2_XML_md_RoleDescriptor {

	/**
	 * This RoleDescriptor as XML
	 *
	 * @var SAML2_XML_Chunk
	 */
	private $xml;


	/**
	 * Initialize an unknown RoleDescriptor.
	 *
	 * @param DOMElement $xml  The XML element we should load.
	 */
	public function __construct(DOMElement $xml) {
		parent::__construct('md:RoleDescriptor', $xml);

		$this->xml = new SAML2_XML_Chunk($xml);
	}


	/**
	 * Add this RoleDescriptor to an EntityDescriptor.
	 *
	 * @param DOMElement $parent  The EntityDescriptor we should append this RoleDescriptor to.
	 */
	public function toXML(DOMElement $parent) {

		$this->xml->toXML($parent);
	}

}
