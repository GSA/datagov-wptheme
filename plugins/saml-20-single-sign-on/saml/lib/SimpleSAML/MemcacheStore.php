<?php 

/**
 * This class provides a class with behaviour similar to the $_SESSION variable.
 * Data is automatically saved on exit.
 *
 * Care should be taken when using this class to store objects. It will not detect changes to objects
 * automatically. Instead, a call to set(...) should be done to notify this class of changes.
 *
 * @author Olav Morken, UNINETT AS.
 * @package simpleSAMLphp
 * @version $Id: MemcacheStore.php 2418 2010-07-13 11:56:17Z olavmrk $
 * @deprecated This class will be removed in version 1.8 of simpleSAMLphp.
 */
class SimpleSAML_MemcacheStore {


	/**
	 * This variable contains an array with all key-value pairs stored
	 * in this object.
	 */
	private $data = NULL;


	/**
	 * This function is used to find an existing storage object. It will return NULL if no storage object
	 * with the given id is found.
	 *
	 * @param $id  The id of the storage object we are looking for. A id consists of lowercase
	 *             alphanumeric characters.
	 * @return The corresponding MemcacheStorage object if the data is found or NULL if it isn't found.
	 */
	public static function find($id) {
		assert(self::isValidID($id));

		$serializedData = SimpleSAML_Memcache::get($id);
		if($serializedData === NULL) {
			return NULL;
		}

		$data = unserialize($serializedData);

		if(!($data instanceof self)) {
			SimpleSAML_Logger::warning('Retrieved key from memcache did not contain a MemcacheStore object.');
			return NULL;
		}

		return $data;
	}


	/**
	 * This function retrieves the specified key from this storage object.
	 *
	 * @param $key  The key we should retrieve the value of.
	 * @return The value of the specified key, or NULL of the key wasn't found.
	 */
	public function get($key) {
		if(!array_key_exists($key, $this->data)) {
			return NULL;
		}

		return $this->data[$key];
	}


	/**
	 * This function determines whether the argument is a valid id.
	 * A valid id is a string containing lowercase alphanumeric
	 * characters.
	 *
	 * @param $id  The id we should validate.
	 * @return  TRUE if the id is valid, FALSE otherwise.
	 */
	private static function isValidID($id) {
		if(!is_string($id)) {
			return FALSE;
		}

		if(strlen($id) < 1) {
			return FALSE;
		}

		if(preg_match('/[^0-9a-z]/', $id)) {
			return FALSE;
		}

		return TRUE;
	}

}
