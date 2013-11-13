<?php

/**
 * This class implements a metadata source which loads metadata from XML files.
 * The XML files should be in the SAML 2.0 metadata format.
 *
 * @author Olav Morken, UNINETT AS.
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_Metadata_MetaDataStorageHandlerXML extends SimpleSAML_Metadata_MetaDataStorageSource {

	/**
	 * This variable contains an associative array with the parsed metadata.
	 */
	private $metadata;


	/**
	 * This function initializes the XML metadata source. The configuration must contain one of
	 * the following options:
	 * - 'file': Path to a file with the metadata. This path is relative to the simpleSAMLphp
	 *           base directory.
	 * - 'url': URL we should download the metadata from. This is only meant for testing.
	 *
	 * @param $config  The configuration for this instance of the XML metadata source.
	 */
	protected function __construct($config) {

		/* Get the configuration. */
		$globalConfig = SimpleSAML_Configuration::getInstance();

		if(array_key_exists('file', $config)) {
			$src = $globalConfig->resolvePath($config['file']);
		} elseif(array_key_exists('url', $config)) {
			$src = $config['url'];
		} else {
			throw new Exception('Missing either \'file\' or \'url\' in XML metadata source configuration.');
		}


		$SP1x = array();
		$IdP1x = array();
		$SP20 = array();
		$IdP20 = array();
		$AAD = array();

		$entities = SimpleSAML_Metadata_SAMLParser::parseDescriptorsFile($src);
		foreach($entities as $entityId => $entity) {

			$md = $entity->getMetadata1xSP();
			if($md !== NULL) {
				$SP1x[$entityId] = $md;
			}

			$md = $entity->getMetadata1xIdP();
			if($md !== NULL) {
				$IdP1x[$entityId] = $md;
			}

			$md = $entity->getMetadata20SP();
			if($md !== NULL) {
				$SP20[$entityId] = $md;
			}

			$md = $entity->getMetadata20IdP();
			if($md !== NULL) {
				$IdP20[$entityId] = $md;
			}

			$md = $entity->getAttributeAuthorities();
			if (count($md) > 0) {
				$AAD[$entityId] = $md[0];
			}
		}

		$this->metadata = array(
			'shib13-sp-remote' => $SP1x,
			'shib13-idp-remote' => $IdP1x,
			'saml20-sp-remote' => $SP20,
			'saml20-idp-remote' => $IdP20,
			'attributeauthority-remote' => $AAD,
			);

	}


	/**
	 * This function returns an associative array with metadata for all entities in the given set. The
	 * key of the array is the entity id.
	 *
	 * @param $set  The set we want to list metadata for.
	 * @return An associative array with all entities in the given set.
	 */
	public function getMetadataSet($set) {
		if(array_key_exists($set, $this->metadata)) {
			return $this->metadata[$set];
		}

		/* We don't have this metadata set. */
		return array();
	}
}

?>