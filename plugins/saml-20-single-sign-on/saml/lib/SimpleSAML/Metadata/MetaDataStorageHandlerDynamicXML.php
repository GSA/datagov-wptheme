<?php

/**
 * This class implements the dynamic SAML profile, where the entityID equals an URL where metadata is located.
 * The XML files should be in the SAML 2.0 metadata format.
 *
 * @author Andreas Åkre Solberg, UNINETT AS.
 * @author Olav Morken, UNINETT AS.
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_Metadata_MetaDataStorageHandlerDynamicXML extends SimpleSAML_Metadata_MetaDataStorageSource {

	/**
	 * The cache directory, or NULL if no cache directory is configured.
	 */
	private $cacheDir;


	/**
	 * The maximum cache length, in seconds.
	 */
	private $cacheLength;


	/**
	 * This function initializes the dynamic XML metadata source.
	 *
	 * Options:
	 * - 'cachedir':  Directory where metadata can be cached. Optional.
	 * - 'cachelength': Maximum time metadata cah be cached, in seconds. Default to 24
	 *                  hours (86400 seconds).
	 *
	 * @param array $config  The configuration for this instance of the XML metadata source.
	 */
	protected function __construct($config) {
		assert('is_array($config)');

		if (array_key_exists('cachedir', $config)) {
			$globalConfig = SimpleSAML_Configuration::getInstance();
			$this->cacheDir = $globalConfig->resolvePath($config['cachedir']);
		} else {
			$this->cacheDir = NULL;
		}

		if (array_key_exists('cachelength', $config)) {
			$this->cacheLength = $config['cachelength'];
		} else {
			$this->cacheLength = 86400;
		}

	}


	/**
	 * This function returns an associative array with metadata for all entities in the given set. The
	 * key of the array is the entity id.
	 *
	 * @param $set  The set we want to list metadata for.
	 * @return An associative array with all entities in the given set.
	 */
	public function getMetadataSet($set) {

		/* We don't have this metadata set. */
		return array();
	}


	/**
	 * Find the cache file name for an entity,
	 *
	 * @param string $set  The metadata set this entity belongs to.
	 * @param string $entityId  The entity id of this entity.
	 * @return string  The full path to the cache file.
	 */
	private function getCacheFilename($set, $entityId) {
		assert('is_string($set)');
		assert('is_string($entityId)');

		$cachekey = sha1($entityId);
		$globalConfig = SimpleSAML_Configuration::getInstance();
		return $this->cacheDir . '/' . $set . '-' . $cachekey . '.cached.xml';
	}


	/**
	 * Load a entity from the cache.
	 *
	 * @param string $set  The metadata set this entity belongs to.
	 * @param string $entityId  The entity id of this entity.
	 * @return array|NULL  The associative array with the metadata for this entity, or NULL
	 *                     if the entity could not be found.
	 */
	private function getFromCache($set, $entityId) {
		assert('is_string($set)');
		assert('is_string($entityId)');

		if (empty($this->cacheDir)) {
			return NULL;
		}

		$cachefilename = $this->getCacheFilename($set, $entityId);
		if (!file_exists($cachefilename)) return NULL;
		if (!is_readable($cachefilename)) throw new Exception('Could not read cache file for entity [' . $cachefilename. ']');
		SimpleSAML_Logger::debug('MetaData - Handler.DynamicXML: Reading cache [' . $entityId . '] => [' . $cachefilename . ']' );

		/* Ensure that this metadata isn't older that the cachelength option allows. This
		 * must be verified based on the file, since this option may be changed after the
		 * file is written.
		 */
		$stat = stat($cachefilename);
		if ($stat['mtime'] + $this->cacheLength <= time()) {
			SimpleSAML_Logger::debug('MetaData - Handler.DynamicXML: Cache file older that the cachelength option allows.');
			return NULL;
		}

		$rawData = file_get_contents($cachefilename);
		if (empty($rawData)) {
			throw new Exception('Error reading metadata from cache file "' . $cachefilename . '": ' .
				SimpleSAML_Utilities::getLastError());
		}

		$data = unserialize($rawData);
		if ($data === FALSE) {
			throw new Exception('Error deserializing cached data from file "' . $cachefilename .'".');
		}

		if (!is_array($data)) {
			throw new Exception('Cached metadata from "' . $cachefilename . '" wasn\'t an array.');
		}

		return $data;
	}


	/**
	 * Save a entity to the cache.
	 *
	 * @param string $set  The metadata set this entity belongs to.
	 * @param string $entityId  The entity id of this entity.
	 * @param array $data  The associative array with the metadata for this entity.
	 */
	private function writeToCache($set, $entityId, $data) {
		assert('is_string($set)');
		assert('is_string($entityId)');
		assert('is_array($data)');

		if (empty($this->cacheDir)) {
			return;
		}

		$cachefilename = $this->getCacheFilename($set, $entityId);
		if (!is_writable(dirname($cachefilename))) throw new Exception('Could not write cache file for entity [' . $cachefilename. ']');
		SimpleSAML_Logger::debug('MetaData - Handler.DynamicXML: Writing cache [' . $entityId . '] => [' . $cachefilename . ']' );
		file_put_contents($cachefilename, serialize($data));
	}


	/**
	 * Retrieve metadata for the correct set from a SAML2Parser.
	 *
	 * @param SimpleSAML_Metadata_SAMLParser $entity  A SAML2Parser representing an entity.
	 * @param string $set  The metadata set we are looking for.
	 * @return array|NULL  The associative array with the metadata, or NULL if no metadata for
	 *                     the given set was found.
	 */
	private static function getParsedSet(SimpleSAML_Metadata_SAMLParser $entity, $set) {
		assert('is_string($set)');

		switch($set) {
		case 'saml20-idp-remote':
			return $entity->getMetadata20IdP();
		case 'saml20-sp-remote':
			return $entity->getMetadata20SP();
		case 'shib13-idp-remote':
			return $entity->getMetadata1xIdP();
		case 'shib13-sp-remote':
			return $entity->getMetadata1xSP();
		default:
			SimpleSAML_Logger::warning('MetaData - Handler.DynamicXML: Unknown metadata set: ' . $set);
		}

		return NULL;
	}


	/**
	 * Overriding this function from the superclass SimpleSAML_Metadata_MetaDataStorageSource.
	 *
	 * This function retrieves metadata for the given entity id in the given set of metadata.
	 * It will return NULL if it is unable to locate the metadata.
	 *
	 * This class implements this function using the getMetadataSet-function. A subclass should
	 * override this function if it doesn't implement the getMetadataSet function, or if the
	 * implementation of getMetadataSet is slow.
	 *
	 * @param $index  The entityId or metaindex we are looking up.
	 * @param $set  The set we are looking for metadata in.
	 * @return An associative array with metadata for the given entity, or NULL if we are unable to
	 *         locate the entity.
	 */
	public function getMetaData($index, $set) {
		assert('is_string($index)');
		assert('is_string($set)');

		if (!preg_match('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', $index)) {
			SimpleSAML_Logger::info('MetaData - Handler.DynamicXML: EntityID/index [' . $index . '] does not look like an URL. Skipping.' );
			return NULL;
		}

		SimpleSAML_Logger::info('MetaData - Handler.DynamicXML: Loading metadata entity [' . $index . '] from [' . $set . ']' );

		/* Read from cache if possible. */
		$data = $this->getFromCache($set, $index);

		if ($data !== NULL && array_key_exists('expires', $data) && $data['expires'] < time()) {
			/* Metadata has expired. */
			$data = NULL;
		}

		if (isset($data)) {
			/* Metadata found in cache and not expired. */
			SimpleSAML_Logger::debug('MetaData - Handler.DynamicXML: Using cached metadata.');
			return $data;
		}

		SimpleSAML_Logger::debug('MetaData - Handler.DynamicXML: Downloading [' . $index . ']' );
		$xmldata = file_get_contents($index);
		if (empty($xmldata)) {
			throw new Exception('Error downloading metadata from "' . $index . '": ' .
				SimpleSAML_Utilities::getLastError());
		}

		$entities = SimpleSAML_Metadata_SAMLParser::parseDescriptorsString($xmldata);
		SimpleSAML_Logger::debug('MetaData - Handler.DynamicXML: Completed parsing of [' .
			$index . '] Found [' . count($entities). '] entries.' );

		if (count($entities) === 0) {
			throw new Exception('No entities found in "' . $index . '".');
		}

		if (!array_key_exists($index, $entities)) {
			throw new Exception ('No entity with correct entity id found in "' . $index . '".');
		}

		$entity = $entities[$index];
		$data = self::getParsedSet($entity, $set);
		if ($data === NULL) {
			throw new Exception('No metadata for set "' . $set .
				'" available from "' . $index . '".');
		}

		$this->writeToCache($set, $index, $data);

		return $data;
	}

}

?>