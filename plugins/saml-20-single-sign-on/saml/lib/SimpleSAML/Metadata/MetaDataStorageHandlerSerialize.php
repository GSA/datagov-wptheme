<?php

/**
 * Class for handling metadata files in serialized format.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_Metadata_MetaDataStorageHandlerSerialize extends SimpleSAML_Metadata_MetaDataStorageSource {

	/**
	 * The file extension we use for our metadata files.
	 */
	const EXTENSION = '.serialized';


	/**
	 * The base directory where metadata is stored.
	 */
	private $directory;


	/**
	 * Constructor for this metadata handler.
	 *
	 * Parses configuration.
	 *
	 * @param array $config  The configuration for this metadata handler.
	 */
	public function __construct($config) {
		assert('is_array($config)');

		$globalConfig = SimpleSAML_Configuration::getInstance();

		$cfgHelp = SimpleSAML_Configuration::loadFromArray($config, 'serialize metadata source');

		$this->directory = $cfgHelp->getString('directory');

		/* Resolve this directory relative to the simpleSAMLphp directory (unless it is
		 * an absolute path).
		 */
		$this->directory = $globalConfig->resolvePath($this->directory);
	}


	/**
	 * Helper function for retrieving the path of a metadata file.
	 *
	 * @param string $entityId  The entity ID.
	 * @param string $set  The metadata set.
	 * @return string  The path to the metadata file.
	 */
	private function getMetadataPath($entityId, $set) {
		assert('is_string($entityId)');
		assert('is_string($set)');

		return $this->directory . '/' . rawurlencode($set) . '/' . rawurlencode($entityId) . self::EXTENSION;
	}


	/**
	 * Retrieve a list of all available metadata sets.
	 *
	 * @return array  An array with the available sets.
	 */
	public function getMetadataSets() {

		$ret = array();

		$dh = @opendir($this->directory);
		if ($dh === FALSE) {
			SimpleSAML_Logger::warning('Serialize metadata handler: Unable to open directory: ' . var_export($this->directory, TRUE));
			return $ret;
		}

		while ( ($entry = readdir($dh)) !== FALSE) {

			if ($entry[0] === '.') {
				/* Skip '..', '.' and hidden files. */
				continue;
			}

			$path = $this->directory . '/' . $entry;

			if (!is_dir($path)) {
				SimpleSAML_Logger::warning('Serialize metadata handler: Metadata directory contained a file where only directories should exist: ' . var_export($path, TRUE));
				continue;
			}

			$ret[] = rawurldecode($entry);
		}

		closedir($dh);

		return $ret;
	}


	/**
	 * Retrieve a list of all available metadata for a given set.
	 *
	 * @param string $set  The set we are looking for metadata in.
	 * @return array  An associative array with all the metadata for the given set.
	 */
	public function getMetadataSet($set) {
		assert('is_string($set)');

		$ret = array();

		$dir = $this->directory . '/' . rawurlencode($set);
		if (!is_dir($dir)) {
			/* Probably some code asked for a metadata set which wasn't available. */
			return $ret;
		}

		$dh = @opendir($dir);
		if ($dh === FALSE) {
			SimpleSAML_Logger::warning('Serialize metadata handler: Unable to open directory: ' . var_export($dir, TRUE));
			return $ret;
		}

		$extLen = strlen(self::EXTENSION);

		while ( ($file = readdir($dh)) !== FALSE) {
			if (strlen($file) <= $extLen) {
				continue;
			}

			if (substr($file, -$extLen) !== self::EXTENSION) {
				continue;
			}

			$entityId = substr($file, 0, -$extLen);
			$entityId = rawurldecode($entityId);

			$md = $this->getMetaData($entityId, $set);
			if ($md !== NULL) {
				$ret[$entityId] = $md;
			}
		}

		closedir($dh);

		return $ret;
	}


	/**
	 * Retrieve a metadata entry.
	 *
	 * @param string $entityId  The entityId we are looking up.
	 * @param string $set  The set we are looking for metadata in.
	 * @return array  An associative array with metadata for the given entity, or NULL if we are unable to
	 *         locate the entity.
	 */
	public function getMetaData($entityId, $set) {
		assert('is_string($entityId)');
		assert('is_string($set)');

		$filePath = $this->getMetadataPath($entityId, $set);

		if (!file_exists($filePath)) {
			return NULL;
		}

		$data = @file_get_contents($filePath);
		if ($data === FALSE) {
			SimpleSAML_Logger::warning('Error reading file ' . $filePath .
				': ' . SimpleSAML_Utilities::getLastError());
			return NULL;
		}

		$data = @unserialize($data);
		if ($data === FALSE) {
			SimpleSAML_Logger::warning('Error deserializing file: ' . $filePath);
			return NULL;
		}

		return $data;
	}


	/**
	 * Save a metadata entry.
	 *
	 * @param string $entityId  The entityId of the metadata entry.
	 * @param string $set  The metadata set this metadata entry belongs to.
	 * @param array $metadata  The metadata.
	 */
	public function saveMetadata($entityId, $set, $metadata) {
		assert('is_string($entityId)');
		assert('is_string($set)');
		assert('is_array($metadata)');

		$filePath = $this->getMetadataPath($entityId, $set);
		$newPath = $filePath . '.new';

		$dir = dirname($filePath);
		if (!is_dir($dir)) {
			SimpleSAML_Logger::info('Creating directory: ' . $dir);
			$res = @mkdir($dir, 0777, TRUE);
			if ($res === FALSE) {
				SimpleSAML_Logger::error('Failed to create directory ' . $dir .
					': ' . SimpleSAML_Utilities::getLastError());
				return FALSE;
			}
		}

		$data = serialize($metadata);

		SimpleSAML_Logger::debug('Writing: ' . $newPath);

		$res = file_put_contents($newPath, $data);
		if ($res === FALSE) {
			SimpleSAML_Logger::error('Error saving file ' . $newPath .
				': ' . SimpleSAML_Utilities::getLastError());
			return FALSE;
		}

		$res = rename($newPath, $filePath);
		if ($res === FALSE) {
			SimpleSAML_Logger::error('Error renaming ' . $newPath . ' to ' . $filePath .
				': ' . SimpleSAML_Utilities::getLastError());
			return FALSE;
		}


		return TRUE;
	}


	/**
	 * Delete a metadata entry.
	 *
	 * @param string $entityId  The entityId of the metadata entry.
	 * @param string $set  The metadata set this metadata entry belongs to.
	 */
	public function deleteMetadata($entityId, $set) {
		assert('is_string($entityId)');
		assert('is_string($set)');

		$filePath = $this->getMetadataPath($entityId, $set);

		if (!file_exists($filePath)) {
			SimpleSAML_Logger::warning('Attempted to erase non-existant metadata entry ' .
				var_export($entityId, TRUE) . ' in set ' . var_export($set, TRUE) . '.');
			return;
		}

		$res = unlink($filePath);
		if ($res === FALSE) {
			SimpleSAML_Logger::error('Failed to delete file ' . $filePath .
				': ' . SimpleSAML_Utilities::getLastError());
		}
	}

}

?>