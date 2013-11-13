<?php

/**
 * Attribute filter for renaming attributes.
 *
 * @author Olav Morken, UNINETT AS.
 * @package simpleSAMLphp
 * @version $Id$
 */
class sspmod_core_Auth_Process_AttributeMap extends SimpleSAML_Auth_ProcessingFilter {

	/**
	 * Assosiative array with the mappings of attribute names.
	 */
	private $map = array();


	/**
	 * Initialize this filter, parse configuration
	 *
	 * @param array $config  Configuration information about this filter.
	 * @param mixed $reserved  For future use.
	 */
	public function __construct($config, $reserved) {
		parent::__construct($config, $reserved);

		assert('is_array($config)');

		foreach($config as $origName => $newName) {
			if(is_int($origName)) {
				/* No index given - this is a map file. */
				$this->loadMapFile($newName);
				continue;
			}

			if(!is_string($origName)) {
				throw new Exception('Invalid attribute name: ' . var_export($origName, TRUE));
			}

			if(!is_string($newName) && !is_array($newName)) {
				throw new Exception('Invalid attribute name: ' . var_export($newName, TRUE));
			}

			$this->map[$origName] = $newName;
		}
	}


	/**
	 * Loads and merges in a file with a attribute map.
	 *
	 * @param string $fileName  Name of attribute map file. Expected to be in the attributenamemapdir.
	 */
	private function loadMapFile($fileName) {
		$config = SimpleSAML_Configuration::getInstance();
		$filePath = $config->getPathValue('attributenamemapdir', 'attributemap/') . $fileName . '.php';

		if(!file_exists($filePath)) {
			throw new Exception('Could not find attributemap file: ' . $filePath);
		}

		$attributemap = NULL;
		include($filePath);
		if(!is_array($attributemap)) {
			throw new Exception('Attribute map file "' . $filePath . '" didn\'t define an attribute map.');
		}

		$this->map = array_merge($this->map, $attributemap);
	}


	/**
	 * Apply filter to rename attributes.
	 *
	 * @param array &$request  The current request
	 */
	public function process(&$request) {
		assert('is_array($request)');
		assert('array_key_exists("Attributes", $request)');

		$attributes =& $request['Attributes'];

		foreach($attributes as $name => $values) {
			if(array_key_exists($name, $this->map)) {
				unset($attributes[$name]);
				if(!is_array($this->map[$name])) {
					$attributes[$this->map[$name]] = $values;
				} else {
					foreach($this->map[$name] as $to_map) {
						$attributes[$to_map] = $values;
					}
				}
			}
		}

	}

}

?>