<?php


/**
 * Represents a definitino of a module.
 * Is usually read and parsed from a JSON definition file.
 *
 * @Author	Andreas Åkre Solberg, <andreas.solberg@uninett.no>
 */
class sspmod_core_ModuleDefinition {
	
	public $def;
	private static $cache;
	
	private function __construct($def) {
		$this->def = $def;
		$this->requireValidIdentifier();
	}
	
	public static function validId($id) {
		return preg_match('|^[a-zA-Z_]+$|', $id);
	}
	
	public static function isDefined($id) {
		$config = SimpleSAML_Configuration::getConfig('config.php');
		$basedir = $config->getBaseDir();
		$filename = $basedir . 'modules/' . $id . '/definition.json';
		return (file_exists($filename));
	}
	
	public static function load($id, $force = NULL) {
		
		if (isset($cache[$id])) return $cache[$id];
		
		if (self::validId($id)) {
			$config = SimpleSAML_Configuration::getConfig('config.php');
			$basedir = $config->getBaseDir();
			$filename = $basedir . 'modules/' . $id . '/definition.json';
			if (!file_exists($filename))
				throw new Exception('Could not read definition file for module [' . $id . '] : ' . $filename);
			$defraw = file_get_contents($filename);
			$def = json_decode($defraw, TRUE);
			
		} elseif(preg_match('|^http(s)?://.*$|', $id)) {
			$defraw = file_get_contents($id);
			$def = json_decode($defraw, TRUE);
		} else {
			throw new Exception('Could not resolve [' . $id . '] as URL nor module identifier.');
		}
		$cache[$id] = new sspmod_core_ModuleDefinition($def);
		
		
		
		if (isset($force)) {
			if ($force === 'local') {
				if(preg_match('|^http(s)?://.*$|', $id)) {
					return self::load($def['id']);
				}
			} elseif($force === 'remote') {
				if (self::validId($id)) {
					if (!isset($def['definition']))
						throw new Exception('Could not load remote definition file for module [' . $id . ']');
					return self::load($def['definition']);
				}
			}
		}
		
		return $cache[$id];
	}
	
	private function requireValidIdentifier() {
		if (!isset($this->def['id']))
			throw new Exception('Missing [id] value in module definition');
		if (!preg_match('|^[a-zA-Z_]+$|', $this->def['id'])) 
			throw new Exception('Illegal characters in [id] in module definition');
	}
	
	public function getVersion($branch = NULL) {
		if (!isset($this->def['access']))	throw new Exception('Missing [access] statement in module definition');
		if (!isset($this->def['branch']))	throw new Exception('Missing [branch] statement in module definition');
		
		if (is_null($branch)) $branch = $this->def['branch'];
		
		if (!isset($this->def['access'][$branch])) throw new Exception('Missing [access] information for branch [' . var_export($branch, TRUE) . ']');
		if (!isset($this->def['access'][$branch]['version'])) throw new Exception('Missing version information in [access] in branch [' . var_export($branch, TRUE) . ']');
		
		return $this->def['access'][$branch]['version'];
	}
	
	public function alwaysUpdate($branch = NULL) {
		$access = $this->getAccess($branch);
		if ($access['type'] === 'svn') return TRUE;
		return FALSE;
	}
	
	public function getBranch($branch = NULL) {
		if (!isset($this->def['branch']))	throw new Exception('Missing [branch] statement in module definition');
		if (is_null($branch)) $branch = $this->def['branch'];
		return $branch;
	}
	
	public function updateExists($branch = NULL) {
		$branch = $this->getBranch($branch);
		
		$localDef = self::load($this->def['id'], 'local');
		$thisVersion = $localDef->getVersion($branch);

		$remoteDef = self::load($this->def['definition'], 'remote');
		$remoteVersion = $remoteDef->getVersion($branch);
		
		#echo ' Comparing versions local [' . $thisVersion . '] and remote [' . $remoteVersion . ']' . "\n";
		
		return version_compare($remoteVersion, $thisVersion, '>');
	}
	
	
	public function getAccess($branch = NULL) {
		if (!isset($this->def['access']))	throw new Exception('Missing [access] statement in module definition');
		if (!isset($this->def['branch']))	throw new Exception('Missing [branch] statement in module definition');
		
		if (is_null($branch)) $branch = $this->def['branch'];
		
		if (!isset($this->def['access'][$branch])) throw new Exception('Missing [access] information for branch [' . var_export($branch, TRUE) . ']');

		return $this->def['access'][$branch];
	}
	
	
}