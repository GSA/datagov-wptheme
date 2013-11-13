<?php

/**
 * This file implements functions to read and write to a group of memcache
 * servers.
 *
 * The goals of this storage class is to provide failover, redudancy and load
 * balancing. This is accomplished by storing the data object to several
 * groups of memcache servers. Each data object is replicated to every group
 * of memcache servers, but it is only stored to one server in each group.
 *
 * For this code to work correctly, all web servers accessing the data must
 * have the same clock (as measured by the time()-function). Different clock
 * values will lead to incorrect behaviour.
 *
 * @author Olav Morken, UNINETT AS.
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_Memcache {

	/**
	 * Cache of the memcache servers we are using.
	 */
	private static $serverGroups = NULL;


	/**
	 * Find data stored with a given key.
	 *
	 * @param $key  The key of the data.
	 * @return The data stored with the given key, or NULL if no data matching the key was found.
	 */
	public static function get($key) {

		$latestTime = 0.0;
		$latestData = NULL;
		$mustUpdate = FALSE;

		/* Search all the servers for the given id. */
		foreach(self::getMemcacheServers() as $server) {
			$serializedInfo = $server->get($key);
			if($serializedInfo === FALSE) {
				/* Either the server is down, or we don't have the value stored on that server. */
				$mustUpdate = TRUE;
				continue;
			}

			/* Deserialize the object. */
			$info = unserialize($serializedInfo);

			/*
			 * Make sure that this is an array with two keys:
			 * - 'timestamp': The time the data was saved.
			 * - 'data': The data.
			 */
			if(!is_array($info)) {
				SimpleSAML_Logger::warning('Retrieved invalid data from a memcache server.' .
					' Data was not an array.');
				continue;
			}
			if(!array_key_exists('timestamp', $info)) {
				SimpleSAML_Logger::warning('Retrieved invalid data from a memcache server.' .
					' Missing timestamp.');
				continue;
			}
			if(!array_key_exists('data', $info)) {
				SimpleSAML_Logger::warning('Retrieved invalid data from a memcache server.' .
					' Missing data.');
				continue;
			}


			if($latestTime === 0.0) {
				/* First data found. */
				$latestTime = $info['timestamp'];
				$latestData = $info['data'];
				continue;
			}

			if($info['timestamp'] === $latestTime && $info['data'] === $latestData) {
				/* This data matches the data from the other server(s). */
				continue;
			}

			/*
			 * Different data from different servers. We need to update at least one
			 * of them to maintain synchronization.
			 */

			$mustUpdate = TRUE;

			/* Update if data in $info is newer than $latestData. */
			if($latestTime < $info['timestamp']) {
				$latestTime = $info['timestamp'];
				$latestData = $info['data'];
			}
		}

		if($latestTime === 0.0) {
			/* We didn't find any data matching the key. */
			return NULL;
		}

		if($mustUpdate) {
			/* We found data matching the key, but some of the servers need updating. */
			self::set($key, $latestData);
		}

		return $latestData;
	}


	/**
	 * Save a key-value pair to the memcache servers.
	 *
	 * @param $key    The key of the data.
	 * @param $value  The value of the data.
	 * @param int|NULL $expire  The expiration timestamp of the data.
	 */
	public static function set($key, $value, $expire = NULL) {
		$savedInfo = array(
			'timestamp' => microtime(TRUE),
			'data' => $value
			);

		if ($expire === NULL) {
			$expire = self::getExpireTime();
		}

		$savedInfoSerialized = serialize($savedInfo);

		/* Store this object to all groups of memcache servers. */
		foreach(self::getMemcacheServers() as $server) {
			$server->set($key, $savedInfoSerialized, 0, $expire);
		}
	}


	/**
	 * Delete a key-value pair from the memcache servers.
	 *
	 * @param string $key  The key we should delete.
	 */
	public static function delete($key) {
		assert('is_string($key)');

		/* Store this object to all groups of memcache servers. */
		foreach(self::getMemcacheServers() as $server) {
			$server->delete($key);
		}
	}


	/**
	 * This function adds a server from the 'memcache_store.servers'
	 * configuration option to a Memcache object.
	 *
	 * The server parameter is an array with the following keys:
	 *  - hostname
	 *    Hostname or ip address to the memcache server.
	 *  - port (optional)
	 *    port number the memcache server is running on. This
	 *    defaults to memcache.default_port if no value is given.
	 *    The default value of memcache.default_port is 11211.
	 *  - weight (optional)
	 *    The weight of this server in the load balancing
	 *    cluster.
	 *  - timeout (optional)
	 *    The timeout for contacting this server, in seconds.
	 *    The default value is 3 seconds.
	 *
	 * @param $memcache  The Memcache object we should add this server to.
	 * @param $server    The server we should add.
	 */
	private static function addMemcacheServer($memcache, $server) {

		/* The hostname option is required. */
		if(!array_key_exists('hostname', $server)) {
			throw new Exception('hostname setting missing from server in the' .
				' \'memcache_store.servers\' configuration option.');
		}

		$hostname = $server['hostname'];

		/* The hostname must be a valid string. */
		if(!is_string($hostname)) {
			throw new Exception('Invalid hostname for server in the' .
				' \'memcache_store.servers\' configuration option. The hostname is' .
				' supposed to be a string.');
		}

		/* Check if the user has specified a port number. */
		if(array_key_exists('port', $server)) {
			/* Get the port number from the array, and validate it. */
			$port = (int)$server['port'];
			if(($port <= 0) || ($port > 65535)) {
				throw new Exception('Invalid port for server in the' .
					' \'memcache_store.servers\' configuration option. The port number' .
					' is supposed to be an integer between 0 and 65535.');
			}
		} else {
			/* Use the default port number from the ini-file. */
			$port = (int)ini_get('memcache.default_port');
			if($port <= 0 || $port > 65535) {
				/* Invalid port number from the ini-file. fall back to the default. */
				$port = 11211;
			}
		}

		/* Check if the user has specified a weight for this server. */
		if(array_key_exists('weight', $server)) {
			/* Get the weight and validate it. */
			$weight = (int)$server['weight'];
			if($weight <= 0) {
				throw new Exception('Invalid weight for server in the' .
					' \'memcache_store.servers\' configuration option. The weight is' .
					' supposed to be a positive integer.');
			}
		} else {
			/* Use a default weight of 1.  */
			$weight = 1;
		}

		/* Check if the user has specified a timeout for this server. */
		if(array_key_exists('timeout', $server)) {
			/* Get the timeout and validate it. */
			$timeout = (int)$server['timeout'];
			if($timeout <= 0) {
				throw new Exception('Invalid timeout for server in the' .
					' \'memcache_store.servers\' configuration option. The timeout is' .
					' supposed to be a positive integer.');
			}
		} else {
			/* Use a default timeout of 3 seconds. */
			$timeout = 3;
		}

		/* Add this server to the Memcache object. */
		$memcache->addServer($hostname, $port, TRUE, $weight, $timeout);
	}


	/**
	 * This function takes in a list of servers belonging to a group and
	 * creates a Memcache object from the servers in the group.
	 *
	 * @param array $group  Array of servers which should be created as a group.
	 * @return A Memcache object of the servers in the group.
	 */
	private static function loadMemcacheServerGroup(array $group) {
		/* Create the Memcache object. */
		$memcache = new Memcache();
		if($memcache == NULL) {
			throw new Exception('Unable to create an instance of a Memcache object.' .
				' Is the memcache extension installed?');
		}

		/* Iterate over all the servers in the group and add them to the Memcache object. */
		foreach($group as $index => $server) {
			/*
			 * Make sure that we don't have an index. An index would be a sign of invalid configuration.
			 */
			if(!is_int($index)) {
				throw new Exception('Invalid index on element in the' .
					' \'memcache_store.servers\' configuration option. Perhaps you' .
					' have forgotten to add an array(...) around one of the server groups?' .
					' The invalid index was: ' . $index);
			}

			/*
			 * Make sure that the server object is an array. Each server is an array with
			 * name-value pairs.
			 */
			if(!is_array($server)) {
				throw new Exception('Invalid value for the server with index ' . $index .
					'. Remeber that the \'memcache_store.servers\' configuration option' .
					' contains an array of arrays of arrays.');
			}

			self::addMemcacheServer($memcache, $server);
		}

		return $memcache;
	}


	/**
	 * This function gets a list of all configured memcache servers. This list is initialized based
	 * on the content of 'memcache_store.servers' in the configuration.
	 *
	 * @return Array with Memcache objects.
	 */
	private static function getMemcacheServers() {

		/* Check if we have loaded the servers already. */
		if(self::$serverGroups != NULL) {
			return self::$serverGroups;
		}

		/* Initialize the servers-array. */
		self::$serverGroups = array();

		/* Load the configuration. */
		$config = SimpleSAML_Configuration::getInstance();


		$groups = $config->getArray('memcache_store.servers');

		/* Iterate over all the groups in the 'memcache_store.servers' configuration option. */
		foreach($groups as $index => $group) {
			/*
			 * Make sure that the group doesn't have an index. An index would be a sign of
			 * invalid configuration.
			 */
			if(!is_int($index)) {
				throw new Exception('Invalid index on element in the \'memcache_store.servers\'' .
					' configuration option. Perhaps you have forgotten to add an array(...)' .
					' around one of the server groups? The invalid index was: ' . $index);
			}

			/*
			 * Make sure that the group is an array. Each group is an array of servers. Each server is
			 * an array of name => value pairs for that server.
			 */
			if(!is_array($group)) {
				throw new Exception('Invalid value for the server with index ' . $index .
					'. Remeber that the \'memcache_store.servers\' configuration option' .
					' contains an array of arrays of arrays.');
			}

			/* Parse and add this group to the server group list. */
			self::$serverGroups[] = self::loadMemcacheServerGroup($group);
		}

		return self::$serverGroups;
	}


	/**
	 * This is a helper-function which returns the expire value of data
	 * we should store to the memcache servers.
	 *
	 * The value is set depending on the configuration. If no value is
	 * set in the configuration, then we will use a default value of 0.
	 * 0 means that the item will never expire.
	 *
	 * @return  The value which should be passed in the set(...) calls to the memcache objects.
	 */
	private static function getExpireTime()
	{
		/* Get the configuration instance. */
		$config = SimpleSAML_Configuration::getInstance();
		assert($config instanceof SimpleSAML_Configuration);

		/* Get the expire-value from the configuration. */
		$expire = $config->getInteger('memcache_store.expires', 0);

		/* It must be a positive integer. */
		if($expire < 0) {
			throw new Exception('The value of \'memcache_store.expires\' in the' .
				' configuration can\'t be a negative integer.');
		}

		/* If the configuration option is 0, then we should
		 * return 0. This allows the user to specify that the data
		 * shouldn't expire.
		 */
		if($expire == 0) {
			return 0;
		}

		/* The expire option is given as the number of seconds into the
		 * future an item should expire. We convert this to an actual
		 * timestamp.
		 */
		$expireTime = time() + $expire;

		return $expireTime;
	}


	/**
	 * This function retrieves statistics about all memcache server groups.
	 *
	 * @return Array with the names of each stat and an array with the value for each
	 *         server group.
	 */
	public static function getStats()
	{
		$ret = array();

		foreach(self::getMemcacheServers() as $sg) {
			$stats = $sg->getExtendedStats();
			if($stats === FALSE) {
				throw new Exception('Failed to get memcache server status.');
			}

			$stats = SimpleSAML_Utilities::transposeArray($stats);

			$ret = array_merge_recursive($ret, $stats);
		}

		return $ret;
	}


	/**
	 * Retrieve statistics directly in the form returned by getExtendedStats, for
	 * all server groups.
	 *
	 * @return Array with the extended stats output for each server group.
	 */
	public static function getRawStats() {
		$ret = array();

		foreach(self::getMemcacheServers() as $sg) {
			$stats = $sg->getExtendedStats();
			$ret[] = $stats;
		}

		return $ret;
	}

}
?>