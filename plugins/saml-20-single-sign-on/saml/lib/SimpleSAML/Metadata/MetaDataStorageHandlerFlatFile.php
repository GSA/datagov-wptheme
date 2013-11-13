<?php

/**
 * This file defines a flat file metadata source.
 * Instantiation of session handler objects should be done through
 * the class method getMetadataHandler().
 *
 * @author Andreas �kre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @package simpleSAMLphp
 * @version $Id: MetaDataStorageHandlerFlatFile.php 2389 2010-07-09 06:54:25Z olavmrk $
 */
class SimpleSAML_Metadata_MetaDataStorageHandlerFlatFile extends SimpleSAML_Metadata_MetaDataStorageSource {

	/**
	 * This is the directory we will load metadata files from. The path will always end
	 * with a '/'.
	 */
	private $directory;


	/**
	 * This is an associative array which stores the different metadata sets we have loaded.
	 */
	private $cachedMetadata = array();


	/**
	 * This constructor initializes the flatfile metadata storage handler with the
	 * specified configuration. The configuration is an associative array with the following
	 * possible elements:
	 * - 'directory': The directory we should load metadata from. The default directory is
	 *                set in the 'metadatadir' configuration option in 'config.php'.
	 *
	 * @param $config  An associtive array with the configuration for this handler.
	 */
	protected function __construct($config) {
		assert('is_array($config)');

		/* Get the configuration. */
		$globalConfig = SimpleSAML_Configuration::getInstance();


		/* Find the path to the directory we should search for metadata in. */
		if(array_key_exists('directory', $config)) {
			$this->directory = $config['directory'];
		} else {
			$this->directory = $globalConfig->getString('metadatadir', 'metadata/');
		}

		/* Resolve this directory relative to the simpleSAMLphp directory (unless it is
		 * an absolute path).
		 */
		$this->directory = $globalConfig->resolvePath($this->directory) . '/';
	}


	/**
	 * This function loads the given set of metadata from a file our metadata directory.
	 * This function returns NULL if it is unable to locate the given set in the metadata directory.
	 *
	 * @param $set  The set of metadata we are loading.
	 * @return Associative array with the metadata, or NULL if we are unable to load metadata from the given file.
	 */
	private function load($set) {

		$metadatasetfile = $this->directory . $set . '.php';

		if (!file_exists($metadatasetfile)) {
			return NULL;
		}

		$metadata = array();

		include($metadatasetfile);

		if (!is_array($metadata)) {
			throw new Exception('Could not load metadata set [' . $set . '] from file: ' . $metadatasetfile);
		}

		return $metadata;
	}


	/**
	 * This function retrieves the given set of metadata. It will return an empty array if it is
	 * unable to locate it.
	 *
	 * @param $set  The set of metadata we are retrieving.
	 * @return Asssociative array with the metadata. Each element in the array is an entity, and the
	 *         key is the entity id.
	 */
	public function getMetadataSet($set) {

		if(array_key_exists($set, $this->cachedMetadata)) {
			return $this->cachedMetadata[$set];
		}

		$metadataSet = $this->load($set);
		if($metadataSet === NULL) {
			$metadataSet = array();
		}

		/* Add the entity id of an entry to each entry in the metadata. */
		foreach ($metadataSet AS $entityId => &$entry) {
			if (preg_match('/__DYNAMIC(:[0-9]+)?__/', $entityId)) {
				$entry['entityid'] = $this->generateDynamicHostedEntityID($set);
			} else {
				$entry['entityid'] = $entityId;
			}
		}

		$this->cachedMetadata[$set] = $metadataSet;

		return $metadataSet;
	}
	
	private function generateDynamicHostedEntityID($set) {

		/* Get the configuration. */
		$baseurl = SimpleSAML_Utilities::getBaseURL();

		if ($set === 'saml20-idp-hosted') {
			return $baseurl . 'saml2/idp/metadata.php';
		} elseif($set === 'saml20-sp-hosted') {
			return $baseurl . 'saml2/sp/metadata.php';			
		} elseif($set === 'shib13-idp-hosted') {
			return $baseurl . 'shib13/idp/metadata.php';
		} elseif($set === 'shib13-sp-hosted') {
			return $baseurl . 'shib13/sp/metadata.php';
		} elseif($set === 'wsfed-sp-hosted') {
			return 'urn:federation:' . SimpleSAML_Utilities::getSelfHost();
		} elseif($set === 'adfs-idp-hosted') {
			return 'urn:federation:' . SimpleSAML_Utilities::getSelfHost() . ':idp';
		} else {
			throw new Exception('Can not generate dynamic EntityID for metadata of this type: [' . $set . ']');
		}
	}


}

?>