<?php

/**
 * Base class for datastores.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
abstract class SimpleSAML_Store {

	/**
	 * Our singleton instance.
	 *
	 * This is FALSE if the datastore isn't enabled, and NULL
	 * if we haven't attempted to initialize it.
	 *
	 * @var SimpleSAML_Store|FALSE|NULL
	 */
	private static $instance;


	/**
	 * Retrieve our singleton instance.
	 *
	 * @return SimpleSAML_Store|FALSE  The datastore, or FALSE if it isn't enabled.
	 */
	public static function getInstance() {

		if (self::$instance !== NULL) {
			return self::$instance;
		}

		$config = SimpleSAML_Configuration::getInstance();
		$storeType = $config->getString('store.type', NULL);
		if ($storeType === NULL) {
			$storeType = $config->getString('session.handler', 'phpsession');
		}

		switch ($storeType) {
		case 'phpsession':
			/* We cannot support advanced features with the PHP session store. */
			self::$instance = FALSE;
			break;
		case 'memcache':
			self::$instance = new SimpleSAML_Store_Memcache();
			break;
		case 'sql':
			self::$instance = new SimpleSAML_Store_SQL();
			break;
		default:
			if (strpos($storeType, ':') === FALSE) {
				throw new SimpleSAML_Error_Exception('Unknown datastore type: ' . var_export($storeType, TRUE));
			}
			/* Datastore from module. */
			$className = SimpleSAML_Module::resolveClass($storeType, 'Store', 'SimpleSAML_Store');
			self::$instance = new $className();
		}

		return self::$instance;
	}


	/**
	 * Retrieve a value from the datastore.
	 *
	 * @param string $type  The datatype.
	 * @param string $key  The key.
	 * @return mixed|NULL  The value.
	 */
	abstract public function get($type, $key);


	/**
	 * Save a value to the datastore.
	 *
	 * @param string $type  The datatype.
	 * @param string $key  The key.
	 * @param mixed $value  The value.
	 * @param int|NULL $expire  The expiration time (unix timestamp), or NULL if it never expires.
	 */
	abstract public function set($type, $key, $value, $expire = NULL);


	/**
	 * Delete a value from the datastore.
	 *
	 * @param string $type  The datatype.
	 * @param string $key  The key.
	 */
	abstract public function delete($type, $key);

}
