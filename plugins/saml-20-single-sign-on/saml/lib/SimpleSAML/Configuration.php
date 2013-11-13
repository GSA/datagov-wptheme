<?php
 
/**
 * Configuration of SimpleSAMLphp
 *
 * @author Andreas Aakre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @package simpleSAMLphp
 * @version $Id: Configuration.php 3176 2012-09-25 10:15:16Z jaimepc@gmail.com $
 */
class SimpleSAML_Configuration {

	/**
	 * A default value which means that the given option is required.
	 */
	const REQUIRED_OPTION = '___REQUIRED_OPTION___';


	/**
	 * Associative array with mappings from instance-names to configuration objects.
	 */
	private static $instance = array();


	/**
	 * Configration directories.
	 *
	 * This associative array contains the mappings from configuration sets to
	 * configuration directories.
	 */
	private static $configDirs = array();


	/**
	 * Cache of loaded configuration files.
	 *
	 * The index in the array is the full path to the file.
	 */
	private static $loadedConfigs = array();


	/**
	 * The configuration array.
	 */
	private $configuration;


	/**
	 * The location which will be given when an error occurs.
	 */
	private $location;


	/**
	 * The file this configuration was loaded from.
	 */
	private $filename = NULL;



	/**
	 * Initializes a configuration from the given array.
	 *
	 * @param array $config  The configuration array.
	 * @param string $location  The location which will be given when an error occurs.
	 */
	public function __construct($config, $location) {
		assert('is_array($config)');
		assert('is_string($location)');

		$this->configuration = $config;
		$this->location = $location;
	}


	/**
	 * Load the given configuration file.
	 *
	 * @param string $filename  The full path of the configuration file.
	 * @param bool @required  Whether the file is required.
	 * @return SimpleSAML_Configuration  The configuration file. An exception will be thrown if the
	 *                                   configuration file is missing.
	 */
	private static function loadFromFile($filename, $required) {
		assert('is_string($filename)');
		assert('is_bool($required)');

		if (array_key_exists($filename, self::$loadedConfigs)) {
			return self::$loadedConfigs[$filename];
		}

		if (file_exists($filename)) {
			$config = 'UNINITIALIZED';

			/* The file initializes a variable named '$config'. */
			require($filename);

			/* Check that $config is initialized to an array. */
			if (!is_array($config)) {
				throw new Exception('Invalid configuration file: ' . $filename);
			}

		} elseif ($required) {
			/* File does not exist, but is required. */
			throw new Exception('Missing configuration file: ' . $filename);

		} else {
			/* File does not exist, but is optional. */
			$config = array();
		}

		if (array_key_exists('override.host', $config)) {
			$host = $_SERVER['HTTP_HOST'];
			if (array_key_exists($host, $config['override.host'])) {
				$ofs = $config['override.host'][$host];
				foreach (SimpleSAML_Utilities::arrayize($ofs) AS $of) {
					$overrideFile = dirname($filename) . '/' . $of;
					if (!file_exists($overrideFile)) {
						throw new Exception('Config file [' . $filename . '] requests override for host ' . $host . ' but file does not exists [' . $of . ']');
					}
					require($overrideFile);
				}
			}
		}

		$cfg = new SimpleSAML_Configuration($config, $filename);
		$cfg->filename = $filename;

		self::$loadedConfigs[$filename] = $cfg;

		return $cfg;
	}


	/**
	 * Set the directory for configuration files for the given configuration set.
	 *
	 * @param string $path  The directory which contains the configuration files.
	 * @param string $configSet  The configuration set. Defaults to 'simplesaml'.
	 */
	public static function setConfigDir($path, $configSet = 'simplesaml') {
		assert('is_string($path)');
		assert('is_string($configSet)');

		self::$configDirs[$configSet] = $path;
	}


	/**
	 * Load a configuration file from a configuration set.
	 *
	 * @param string $filename  The name of the configuration file.
	 * @param string $configSet  The configuration set. Optional, defaults to 'simplesaml'.
	 */
	public static function getConfig($filename = 'config.php', $configSet = 'simplesaml') {
		assert('is_string($filename)');
		assert('is_string($configSet)');

		if (!array_key_exists($configSet, self::$configDirs)) {
			if ($configSet !== 'simplesaml') {
				throw new Exception('Configuration set \'' . $configSet . '\' not initialized.');
			} else {
				self::$configDirs['simplesaml'] = dirname(dirname(dirname(__FILE__))) . '/config';
			}
		}

		$dir = self::$configDirs[$configSet];
		$filePath = $dir . '/' . $filename;
		return self::loadFromFile($filePath, TRUE);
	}


	/**
	 * Load a configuration file from a configuration set.
	 *
	 * This function will return a configuration object even if the file does not exist.
	 *
	 * @param string $filename  The name of the configuration file.
	 * @param string $configSet  The configuration set. Optional, defaults to 'simplesaml'.
	 * @return SimpleSAML_Configuration  A configuration object.
	 */
	public static function getOptionalConfig($filename = 'config.php', $configSet = 'simplesaml') {
		assert('is_string($filename)');
		assert('is_string($configSet)');

		if (!array_key_exists($configSet, self::$configDirs)) {
			if ($configSet !== 'simplesaml') {
				throw new Exception('Configuration set \'' . $configSet . '\' not initialized.');
			} else {
				self::$configDirs['simplesaml'] = dirname(dirname(dirname(__FILE__))) . '/config';
			}
		}

		$dir = self::$configDirs[$configSet];
		$filePath = $dir . '/' . $filename;
		return self::loadFromFile($filePath, FALSE);
	}


	/**
	 * Loads a configuration from the given array.
	 *
	 * @param array $config  The configuration array.
	 * @param string $location  The location which will be given when an error occurs. Optional.
	 * @return SimpleSAML_Configuration  The configuration object.
	 */
	public static function loadFromArray($config, $location = '[ARRAY]') {
		assert('is_array($config)');
		assert('is_string($location)');

		return new SimpleSAML_Configuration($config, $location);
	}


	/**
	 * Get a configuration file by its instance name.
	 *
	 * This function retrieves a configuration file by its instance name. The instance
	 * name is initialized by the init function, or by copyFromBase function.
	 *
	 * If no configuration file with the given instance name is found, an exception will
	 * be thrown.
	 *
	 * @param string $instancename  The instance name of the configuration file. Depreceated.
	 * @return SimpleSAML_Configuration  The configuration object.
	 */
	public static function getInstance($instancename = 'simplesaml') {
		assert('is_string($instancename)');

		if ($instancename === 'simplesaml') {
			return self::getConfig();
		}

		if (!array_key_exists($instancename, self::$instance)) 
			throw new Exception('Configuration with name ' . $instancename . ' is not initialized.');
		return self::$instance[$instancename];
	}


	/**
	 * Initialize a instance name with the given configuration file.
	 *
	 * @see setConfigDir()
	 * @depreceated  This function is superseeded by the setConfigDir function.
	 */
	public static function init($path, $instancename = 'simplesaml', $configfilename = 'config.php') {
		assert('is_string($path)');
		assert('is_string($instancename)');
		assert('is_string($configfilename)');

		if ($instancename === 'simplesaml') {
			/* For backwards compatibility. */
			self::setConfigDir($path, 'simplesaml');
		}

		/* Check if we already have loaded the given config - return the existing instance if we have. */
		if(array_key_exists($instancename, self::$instance)) {
			return self::$instance[$instancename];
		}

		self::$instance[$instancename] = self::loadFromFile($path . '/' . $configfilename, TRUE);
	}


	/**
	 * Load a configuration file which is located in the same directory as this configuration file.
	 *
	 * @see getConfig()
	 * @depreceated  This function is superseeded by the getConfig() function.
	 */
	public function copyFromBase($instancename, $filename) {
		assert('is_string($instancename)');
		assert('is_string($filename)');
		assert('$this->filename !== NULL');

		/* Check if we already have loaded the given config - return the existing instance if we have. */
		if(array_key_exists($instancename, self::$instance)) {
			return self::$instance[$instancename];
		}

		$dir = dirname($this->filename);

		if ($instancename === 'simplesaml') {
			/* For backwards compatibility. */
			self::setConfigDir($path, 'simplesaml');
		}

		self::$instance[$instancename] = self::loadFromFile($dir . '/' . $filename, TRUE);
		return self::$instance[$instancename];
	}


	/**
	 * Retrieve the current version of simpleSAMLphp.
	 *
	 * @return string
	 */
	public function getVersion() {
		return '1.10.0';
	}


	/** 
	 * Retrieve a configuration option set in config.php.
	 *
	 * @param $name  Name of the configuration option.
	 * @param $default  Default value of the configuration option. This parameter will default to NULL if not
	 *                  specified. This can be set to SimpleSAML_Configuration::REQUIRED_OPTION, which will
	 *                  cause an exception to be thrown if the option isn't found.
	 * @return  The configuration option with name $name, or $default if the option was not found.
	 */
	public function getValue($name, $default = NULL) {

		/* Return the default value if the option is unset. */
		if (!array_key_exists($name, $this->configuration)) {
			if($default === self::REQUIRED_OPTION) {
				throw new Exception($this->location . ': Could not retrieve the required option ' .
					var_export($name, TRUE));
			}
			return $default;
		}

		return $this->configuration[$name];
	}
	
	
	/**
	 * Check whether an key in the configuration exists...
	 */
	public function hasValue($name) {
		return array_key_exists($name, $this->configuration);
	}
	/**
	 * Check whether an key in the configuration exists...
	 */
	public function hasValueOneOf($names) {
		foreach($names AS $name) if ($this->hasValue($name)) return TRUE;
		return FALSE;
	}

	/**
	 * Retrieve the absolute path of the simpleSAMLphp installation,
	 * relative to the root of the website.
	 *
	 * For example: simplesaml/
	 *
	 * The path will always end with a '/' and never have a leading slash.
	 *
	 * @return string  The absolute path relative to the root of the website.
	 */
	public function getBaseURL() {
		$baseURL = $this->getString('baseurlpath', 'simplesaml/');
		
		if (preg_match('/^\*(.*)$/D', $baseURL, $matches)) {
			/* deprecated behaviour, will be removed in the future */
			return SimpleSAML_Utilities::getFirstPathElement(false) . $matches[1];
		}

		if (preg_match('#^https?://[^/]*/(.*)$#', $baseURL, $matches)) {
			/* we have a full url, we need to strip the path */
			return $matches[1];
		} elseif ($baseURL === '' || $baseURL === '/') {
			/* Root directory of site. */
			return '';
		} elseif (preg_match('#^/?([^/]?.*/)#D', $baseURL, $matches)) {
			/* local path only */
			return $matches[1];
		} else {
			/* invalid format */
			throw new SimpleSAML_Error_Exception('Incorrect format for option \'baseurlpath\'. Value is: "'.
				$this->getString('baseurlpath', 'simplesaml/') . '". Valid format is in the form'.
				' [(http|https)://(hostname|fqdn)[:port]]/[path/to/simplesaml/].');
		}
	}


	/**
	 * This function resolves a path which may be relative to the
	 * simpleSAMLphp base directory.
	 *
	 * The path will never end with a '/'.
	 *
	 * @param $path  The path we should resolve. This option may be NULL.
	 * @return $path if $path is an absolute path, or $path prepended with
	 *         the base directory of this simpleSAMLphp installation. We
	 *         will return NULL if $path is NULL.
	 */
	public function resolvePath($path) {
		if($path === NULL) {
			return NULL;
		}

		assert('is_string($path)');

		/* Prepend path with basedir if it doesn't start with a slash or a Windows
		 * drive letter (e.g. "C:\"). We assume getBaseDir ends with a slash.
		 */
		if ($path[0] !== '/' &&
		    !(preg_match('@^[a-z]:[\\\\/]@i', $path, $matches) && is_dir($matches[0])))
			$path = $this->getBaseDir() . $path;

		/* Remove trailing slashes. */
		while (substr($path, -1) === '/') {
			$path = substr($path, 0, -1);
		}

		return $path;
	}


	/**
	 * Retrieve a path configuration option set in config.php.
	 * The function will always return an absolute path unless the
	 * option is not set. It will then return the default value.
	 *
	 * It checks if the value starts with a slash, and prefixes it
	 * with the value from getBaseDir if it doesn't.
	 *
	 * @param $name Name of the configuration option.
	 * @param $default Default value of the configuration option. 
	 * 		This parameter will default to NULL if not specified.
	 * @return The path configuration option with name $name, or $default if
	 *  the option was not found.
	 */
	public function getPathValue($name, $default = NULL) {

		/* Return the default value if the option is unset. */
		if (!array_key_exists($name, $this->configuration)) {
			$path = $default;
		} else {
			$path = $this->configuration[$name];
		}

		if ($path === NULL) {
			return NULL;
		}

		return $this->resolvePath($path) . '/';
	}


	/** 
	 * Retrieve the base directory for this simpleSAMLphp installation.
	 * This function first checks the 'basedir' configuration option. If
	 * this option is undefined or NULL, then we fall back to looking at
	 * the current filename.
	 *
	 * @return The absolute path to the base directory for this simpleSAMLphp
	 *  installation. This path will always end with a slash.
	 */
	public function getBaseDir() {
		/* Check if a directory is configured in the configuration
		 * file.
		 */
		$dir = $this->getString('basedir', NULL);
		if($dir !== NULL) {
			/* Add trailing slash if it is missing. */
			if(substr($dir, -1) !== '/') {
				$dir .= '/';
			}

			return $dir;
		}

		/* The directory wasn't set in the configuration file. Our
		 * path is <base directory>/lib/SimpleSAML/Configuration.php
		 */

		$dir = __FILE__;
		assert('basename($dir) === "Configuration.php"');

		$dir = dirname($dir);
		assert('basename($dir) === "SimpleSAML"');

		$dir = dirname($dir);
		assert('basename($dir) === "lib"');

		$dir = dirname($dir);

		/* Add trailing slash. */
		$dir .= '/';

		return $dir;
	}


	/**
	 * This function retrieves a boolean configuration option.
	 *
	 * An exception will be thrown if this option isn't a boolean, or if this option isn't found, and no
	 * default value is given.
	 *
	 * @param $name  The name of the option.
	 * @param $default  A default value which will be returned if the option isn't found. The option will be
	 *                  required if this parameter isn't given. The default value can be any value, including
	 *                  NULL.
	 * @return  The option with the given name, or $default if the option isn't found and $default is specified.
	 */
	public function getBoolean($name, $default = self::REQUIRED_OPTION) {
		assert('is_string($name)');

		$ret = $this->getValue($name, $default);

		if($ret === $default) {
			/* The option wasn't found, or it matches the default value. In any case, return
			 * this value.
			 */
			return $ret;
		}

		if(!is_bool($ret)) {
			throw new Exception($this->location . ': The option ' . var_export($name, TRUE) .
				' is not a valid boolean value.');
		}

		return $ret;
	}


	/**
	 * This function retrieves a string configuration option.
	 *
	 * An exception will be thrown if this option isn't a string, or if this option isn't found, and no
	 * default value is given.
	 *
	 * @param $name  The name of the option.
	 * @param $default  A default value which will be returned if the option isn't found. The option will be
	 *                  required if this parameter isn't given. The default value can be any value, including
	 *                  NULL.
	 * @return  The option with the given name, or $default if the option isn't found and $default is specified.
	 */
	public function getString($name, $default = self::REQUIRED_OPTION) {
		assert('is_string($name)');

		$ret = $this->getValue($name, $default);

		if($ret === $default) {
			/* The option wasn't found, or it matches the default value. In any case, return
			 * this value.
			 */
			return $ret;
		}

		if(!is_string($ret)) {
			throw new Exception($this->location . ': The option ' . var_export($name, TRUE) .
				' is not a valid string value.');
		}

		return $ret;
	}


	/**
	 * This function retrieves an integer configuration option.
	 *
	 * An exception will be thrown if this option isn't an integer, or if this option isn't found, and no
	 * default value is given.
	 *
	 * @param $name  The name of the option.
	 * @param $default  A default value which will be returned if the option isn't found. The option will be
	 *                  required if this parameter isn't given. The default value can be any value, including
	 *                  NULL.
	 * @return  The option with the given name, or $default if the option isn't found and $default is specified.
	 */
	public function getInteger($name, $default = self::REQUIRED_OPTION) {
		assert('is_string($name)');

		$ret = $this->getValue($name, $default);

		if($ret === $default) {
			/* The option wasn't found, or it matches the default value. In any case, return
			 * this value.
			 */
			return $ret;
		}

		if(!is_int($ret)) {
			throw new Exception($this->location . ': The option ' . var_export($name, TRUE) .
				' is not a valid integer value.');
		}

		return $ret;
	}


	/**
	 * This function retrieves an integer configuration option where the value must be in the specified range.
	 *
	 * An exception will be thrown if:
	 * - the option isn't an integer
	 * - the option isn't found, and no default value is given
	 * - the value is outside of the allowed range
	 *
	 * @param $name  The name of the option.
	 * @param $minimum  The smallest value which is allowed.
	 * @param $maximum  The largest value which is allowed.
	 * @param $default  A default value which will be returned if the option isn't found. The option will be
	 *                  required if this parameter isn't given. The default value can be any value, including
	 *                  NULL.
	 * @return  The option with the given name, or $default if the option isn't found and $default is specified.
	 */
	public function getIntegerRange($name, $minimum, $maximum, $default = self::REQUIRED_OPTION) {
		assert('is_string($name)');
		assert('is_int($minimum)');
		assert('is_int($maximum)');

		$ret = $this->getInteger($name, $default);

		if($ret === $default) {
			/* The option wasn't found, or it matches the default value. In any case, return
			 * this value.
			 */
			return $ret;
		}

		if ($ret < $minimum || $ret > $maximum) {
			throw new Exception($this->location . ': Value of option ' . var_export($name, TRUE) .
				' is out of range. Value is ' . $ret . ', allowed range is ['
				. $minimum . ' - ' . $maximum . ']');
		}

		return $ret;
	}


	/**
	 * Retrieve a configuration option with one of the given values.
	 *
	 * This will check that the configuration option matches one of the given values. The match will use
	 * strict comparison. An exception will be thrown if it does not match.
	 *
	 * The option can be mandatory or optional. If no default value is given, it will be considered to be
	 * mandatory, and an exception will be thrown if it isn't provided. If a default value is given, it
	 * is considered to be optional, and the default value is returned. The default value is automatically
	 * included in the list of allowed values.
	 *
	 * @param $name  The name of the option.
	 * @param $allowedValues  The values the option is allowed to take, as an array.
	 * @param $default  The default value which will be returned if the option isn't found. If this parameter
	 *                  isn't given, the option will be considered to be mandatory. The default value can be
	 *                  any value, including NULL.
	 * @return  The option with the given name, or $default if the option isn't found adn $default is given.
	 */
	public function getValueValidate($name, $allowedValues, $default = self::REQUIRED_OPTION) {
		assert('is_string($name)');
		assert('is_array($allowedValues)');

		$ret = $this->getValue($name, $default);
		if($ret === $default) {
			/* The option wasn't found, or it matches the default value. In any case, return
			 * this value.
			 */
			return $ret;
		}

		if(!in_array($ret, $allowedValues, TRUE)) {
			$strValues = array();
			foreach($allowedValues as $av) {
				$strValues[] = var_export($av, TRUE);
			}
			$strValues = implode(', ', $strValues);

			throw new Exception($this->location . ': Invalid value given for the option ' .
				var_export($name, TRUE) . '. It should have one of the following values: ' .
				$strValues . '; but it had the following value: ' . var_export($ret, TRUE));
		}

		return $ret;
	}


	/**
	 * This function retrieves an array configuration option.
	 *
	 * An exception will be thrown if this option isn't an array, or if this option isn't found, and no
	 * default value is given.
	 *
	 * @param string $name  The name of the option.
	 * @param mixed$default  A default value which will be returned if the option isn't found. The option will be
	 *                       required if this parameter isn't given. The default value can be any value, including
	 *                       NULL.
	 * @return mixed  The option with the given name, or $default if the option isn't found and $default is specified.
	 */
	public function getArray($name, $default = self::REQUIRED_OPTION) {
		assert('is_string($name)');

		$ret = $this->getValue($name, $default);

		if ($ret === $default) {
			/* The option wasn't found, or it matches the default value. In any case, return
			 * this value.
			 */
			return $ret;
		}

		if (!is_array($ret)) {
			throw new Exception($this->location . ': The option ' . var_export($name, TRUE) .
				' is not an array.');
		}

		return $ret;
	}


	/**
	 * This function retrieves an array configuration option.
	 *
	 * If the configuration option isn't an array, it will be converted to an array.
	 *
	 * @param string $name  The name of the option.
	 * @param mixed $default  A default value which will be returned if the option isn't found. The option will be
	 *                       required if this parameter isn't given. The default value can be any value, including
	 *                       NULL.
	 * @return array  The option with the given name, or $default if the option isn't found and $default is specified.
	 */
	public function getArrayize($name, $default = self::REQUIRED_OPTION) {
		assert('is_string($name)');

		$ret = $this->getValue($name, $default);

		if ($ret === $default) {
			/* The option wasn't found, or it matches the default value. In any case, return
			 * this value.
			 */
			return $ret;
		}

		if (!is_array($ret)) {
			$ret = array($ret);
		}

		return $ret;
	}


	/**
	 * This function retrieves a configuration option with a string or an array of strings.
	 *
	 * If the configuration option is a string, it will be converted to an array with a single string
	 *
	 * @param string $name  The name of the option.
	 * @param mixed $default  A default value which will be returned if the option isn't found. The option will be
	 *                       required if this parameter isn't given. The default value can be any value, including
	 *                       NULL.
	 * @return array  The option with the given name, or $default if the option isn't found and $default is specified.
	 */
	public function getArrayizeString($name, $default = self::REQUIRED_OPTION) {
		assert('is_string($name)');

		$ret = $this->getArrayize($name, $default);

		if ($ret === $default) {
			/* The option wasn't found, or it matches the default value. In any case, return
			 * this value.
			 */
			return $ret;
		}

		foreach ($ret as $value) {
			if (!is_string($value)) {
				throw new Exception($this->location . ': The option ' . var_export($name, TRUE) .
					' must be a string or an array of strings.');
			}
		}

		return $ret;
	}


	/**
	 * Retrieve an array as a SimpleSAML_Configuration object.
	 *
	 * This function will load the value of an option into a SimpleSAML_Configuration
	 * object. The option must contain an array.
	 *
	 * An exception will be thrown if this option isn't an array, or if this option
	 * isn't found, and no default value is given.
	 *
	 * @param string $name  The name of the option.
	 * @param mixed $default  A default value which will be returned if the option isn't found. The option will be
	 *                        required if this parameter isn't given. The default value can be any value, including
	 *                        NULL.
	 * @return mixed  The option with the given name, or $default if the option isn't found and $default is specified.
	 */
	public function getConfigItem($name, $default = self::REQUIRED_OPTION) {
		assert('is_string($name)');

		$ret = $this->getValue($name, $default);

		if ($ret === $default) {
			/* The option wasn't found, or it matches the default value. In any case, return
			 * this value.
			 */
			return $ret;
		}

		if (!is_array($ret)) {
			throw new Exception($this->location . ': The option ' . var_export($name, TRUE) .
				' is not an array.');
		}

		return self::loadFromArray($ret, $this->location . '[' . var_export($name, TRUE) . ']');
	}


	/**
	 * Retrieve an array of arrays as an array of SimpleSAML_Configuration objects.
	 *
	 * This function will retrieve an option containing an array of arrays, and create an
	 * array of SimpleSAML_Configuration objects from that array. The indexes in the new
	 * array will be the same as the original indexes, but the values will be
	 * SimpleSAML_Configuration objects.
	 *
	 * An exception will be thrown if this option isn't an array of arrays, or if this option
	 * isn't found, and no default value is given.
	 *
	 * @param string $name  The name of the option.
	 * @param string $location  Name of the items in the array.
	 * @param mixed $default  A default value which will be returned if the option isn't found. The option will be
	 *                        required if this parameter isn't given. The default value can be any value, including
	 *                        NULL.
	 * @return mixed  The option with the given name, or $default if the option isn't found and $default is specified.
	 */
	public function getConfigList($name, $default = self::REQUIRED_OPTION) {
		assert('is_string($name)');

		$ret = $this->getValue($name, $default);

		if ($ret === $default) {
			/* The option wasn't found, or it matches the default value. In any case, return
			 * this value.
			 */
			return $ret;
		}

		if (!is_array($ret)) {
			throw new Exception($this->location . ': The option ' . var_export($name, TRUE) .
				' is not an array.');
		}

		$out = array();
		foreach ($ret as $index => $config) {
			$newLoc = $this->location . '[' . var_export($name, TRUE) . '][' .
				var_export($index, TRUE) . ']';
			if (!is_array($config)) {
				throw new Exception($newLoc . ': The value of this element was expected to be an array.');
			}
			$out[$index] = self::loadFromArray($config, $newLoc);
		}

		return $out;
	}


	/**
	 * Retrieve list of options.
	 *
	 * This function returns the name of all options which are defined in this
	 * configuration file, as an array of strings.
	 *
	 * @return array  Name of all options defined in this configuration file.
	 */
	public function getOptions() {

		return array_keys($this->configuration);
	}


	/**
	 * Convert this configuration object back to an array.
	 *
	 * @return array  An associative array with all configuration options and values.
	 */
	public function toArray() {

		return $this->configuration;
	}


	/**
	 * Retrieve the default binding for the given endpoint type.
	 *
	 * This function combines the current metadata type (saml 2 / saml 1.1)
	 * with the endpoint type to determine which binding is the default.
	 *
	 * @param string $endpointType  The endpoint type.
	 * @return string  The default binding.
	 */
	private function getDefaultBinding($endpointType) {
		assert('is_string($endpointType)');

		$set = $this->getString('metadata-set');
		switch ($set.':'.$endpointType) {
		case 'saml20-idp-remote:SingleSignOnService':
		case 'saml20-idp-remote:SingleLogoutService':
		case 'saml20-sp-remote:SingleLogoutService':
			return SAML2_Const::BINDING_HTTP_REDIRECT;
		case 'saml20-sp-remote:AssertionConsumerService':
			return SAML2_Const::BINDING_HTTP_POST;
		case 'saml20-idp-remote:ArtifactResolutionService':
			return SAML2_Const::BINDING_SOAP;
		case 'shib13-idp-remote:SingleSignOnService':
			return 'urn:mace:shibboleth:1.0:profiles:AuthnRequest';
		case 'shib13-sp-remote:AssertionConsumerService':
			return 'urn:oasis:names:tc:SAML:1.0:profiles:browser-post';
		default:
			throw new Exception('Missing default binding for ' . $endpointType . ' in ' . $set);
		}
	}


	/**
	 * Helper function for dealing with metadata endpoints.
	 *
	 * @param string $endpointType  The endpoint type.
	 * @return array  Array of endpoints of the given type.
	 */
	public function getEndpoints($endpointType) {
		assert('is_string($endpointType)');

		$loc = $this->location . '[' . var_export($endpointType, TRUE) . ']:';

		if (!array_key_exists($endpointType, $this->configuration)) {
			/* No endpoints of the given type. */
			return array();
		}


		$eps = $this->configuration[$endpointType];
		if (is_string($eps)) {
			/* For backwards-compatibility. */
			$eps = array($eps);
		} elseif (!is_array($eps)) {
			throw new Exception($loc . ': Expected array or string.');
		}


		foreach ($eps as $i => &$ep) {
			$iloc = $loc . '[' . var_export($i, TRUE) . ']';

			if (is_string($ep)) {
				/* For backwards-compatibility. */
				$ep = array(
					'Location' => $ep,
					'Binding' => $this->getDefaultBinding($endpointType),
				);
				$responseLocation = $this->getString($endpointType . 'Response', NULL);
				if ($responseLocation !== NULL) {
					$ep['ResponseLocation'] = $responseLocation;
				}
			} elseif (!is_array($ep)) {
				throw new Exception($iloc . ': Expected a string or an array.');
			}

			if (!array_key_exists('Location', $ep)) {
				throw new Exception($iloc . ': Missing Location.');
			}
			if (!is_string($ep['Location'])) {
				throw new Exception($iloc . ': Location must be a string.');
			}

			if (!array_key_exists('Binding', $ep)) {
				throw new Exception($iloc . ': Missing Binding.');
			}
			if (!is_string($ep['Binding'])) {
				throw new Exception($iloc . ': Binding must be a string.');
			}

			if (array_key_exists('ResponseLocation', $ep)) {
				if (!is_string($ep['ResponseLocation'])) {
					throw new Exception($iloc . ': ResponseLocation must be a string.');
				}
			}

			if (array_key_exists('index', $ep)) {
				if (!is_int($ep['index'])) {
					throw new Exception($iloc . ': index must be an integer.');
				}
			}

		}

		return $eps;
	}


	/**
	 * Find the default endpoint of the given type.
	 *
	 * @param string $endpointType  The endpoint type.
	 * @param array $bindings  Array with acceptable bindings. Can be NULL if any binding is allowed.
	 * @param mixed $default  The default value to return if no matching endpoint is found. If no default is provided, an exception will be thrown.
	 * @return  array|NULL  The default endpoint, or NULL if no acceptable endpoints are used.
	 */
	public function getDefaultEndpoint($endpointType, array $bindings = NULL, $default = self::REQUIRED_OPTION) {
		assert('is_string($endpointType)');

		$endpoints = $this->getEndpoints($endpointType);
		$defaultEndpoint = SimpleSAML_Utilities::getDefaultEndpoint($endpoints, $bindings);
		if ($defaultEndpoint !== NULL) {
			return $defaultEndpoint;
		}

		if ($default === self::REQUIRED_OPTION) {
			$loc = $this->location . '[' . var_export($endpointType, TRUE) . ']:';
			throw new Exception($loc . 'Could not find a supported ' . $endpointType . ' endpoint.');
		}

		return $default;
	}


	/**
	 * Retrieve a string which may be localized into many languages.
	 *
	 * The default language returned is always 'en'.
	 * @param string $name  The name of the option.
	 * @param mixed $default  The default value. If no default is given, and the option isn't found, an exception will be thrown.
	 * @return array  Associative array with language=>string pairs.
	 */
	public function getLocalizedString($name, $default = self::REQUIRED_OPTION) {
		assert('is_string($name)');

		$ret = $this->getValue($name, $default);
		if($ret === $default) {
			/* The option wasn't found, or it matches the default value. In any case, return
			 * this value.
			 */
			return $ret;
		}

		$loc = $this->location . '[' . var_export($name, TRUE) . ']';

		if (is_string($ret)) {
			$ret = array('en' => $ret,);
		}

		if (!is_array($ret)) {
			throw new Exception($loc . ': Must be an array or a string.');
		}

		foreach ($ret as $k => $v) {
			if (!is_string($k)) {
				throw new Exception($loc . ': Invalid language code: ' . var_export($k, TRUE));
			}
			if (!is_string($v)) {
				throw new Exception($loc . '[' . var_export($v, TRUE) . ']: Must be a string.');
			}
		}

		return $ret;
	}

	/**
	 * Get public key from metadata.
	 *
	 * @param string|NULL $use  The purpose this key can be used for. (encryption or signing).
	 * @param bool $required  Whether the public key is required. If this is TRUE, a
	 *                        missing key will cause an exception. Default is FALSE.
	 * @param string $prefix  The prefix which should be used when reading from the metadata
	 *                        array. Defaults to ''.
	 * @return array|NULL  Public key data, or NULL if no public key or was found.
	 */
	public function getPublicKeys($use = NULL, $required = FALSE, $prefix = '') {
		assert('is_bool($required)');
		assert('is_string($prefix)');

		if ($this->hasValue($prefix . 'keys')) {
			$ret = array();
			foreach ($this->getArray($prefix . 'keys') as $key) {
				if ($use !== NULL && isset($key[$use]) && !$key[$use]) {
					continue;
				}
				if (isset($key['X509Certificate'])) {
					/* Strip whitespace from key. */
					$key['X509Certificate'] = preg_replace('/\s+/', '', $key['X509Certificate']);
				}
				$ret[] = $key;

			}
			if (!empty($ret)) {
				return $ret;
			}
		} elseif ($this->hasValue($prefix . 'certData')) {
			$certData = $this->getString($prefix . 'certData');
			$certData = preg_replace('/\s+/', '', $certData);
			return array(
				array(
					'encryption' => TRUE,
					'signing' => TRUE,
					'type' => 'X509Certificate',
					'X509Certificate' => $certData,
				),
			);
		} elseif ($this->hasValue($prefix . 'certificate')) {
			$file = $this->getString($prefix . 'certificate');
			$file = SimpleSAML_Utilities::resolveCert($file);
			$data = @file_get_contents($file);

			if ($data === FALSE) {
				throw new Exception($this->location . ': Unable to load certificate/public key from file "' . $file . '".');
			}

			/* Extract certificate data (if this is a certificate). */
			$pattern = '/^-----BEGIN CERTIFICATE-----([^-]*)^-----END CERTIFICATE-----/m';
			if (!preg_match($pattern, $data, $matches)) {
				throw new SimpleSAML_Error_Exception($this->location . ': Could not find PEM encoded certificate in "' . $file . '".');
			}
			$certData = preg_replace('/\s+/', '', $matches[1]);

			return array(
				array(
					'encryption' => TRUE,
					'signing' => TRUE,
					'type' => 'X509Certificate',
					'X509Certificate' => $certData,
				),
			);
		}

		if ($required) {
			throw new SimpleSAML_Error_Exception($this->location . ': Missing certificate in metadata.');
		} else {
			return NULL;
		}
	}

}
