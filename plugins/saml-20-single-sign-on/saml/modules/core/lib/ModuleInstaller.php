<?php


/**
 * Perform installation and updates on simpleSAMLphp modules
 * based on information found in a module definition.
 *
 * @Author	Andreas Åkre Solberg, <andreas.solberg@uninett.no>
 */
class sspmod_core_ModuleInstaller {
	
	public $module;
	
	public function __construct(sspmod_core_ModuleDefinition $module) {
		$this->module = $module;
		
	}
	
	public function remove($branch = NULL) { 
		$access = $this->module->getAccess($branch);
		
		switch($access['type']) {
			// case 'svn' :
			// 	$this->requireInstalled();
			// 	$this->remove($access);
			// 	break;
			
			default:
				$this->requireInstalled();
				$this->removeModuleDir($access);
				break;
				
		}
	}
	
	public function install($branch = NULL) {
		
		$access = $this->module->getAccess($branch);
		
		switch($access['type']) {
			case 'svn' :
				$this->requireNotInstalled();
				$this->svnCheckout($access);
				$this->enable();
				$this->prepareConfig();
				break;
			
			case 'zip' :
				$this->requireNotInstalled();
				$this->zipLoad($access);
				$this->enable();
				$this->prepareConfig();
				break;

			
			default:
				throw new Exception('Unknown access method type. Not one of [zip,tgz,svn]');
				
		}
		
	}
	
	public static function exec($cmd) {
		echo ' $ ' . $cmd . "\n";
		$output = shell_exec(escapeshellcmd($cmd));	
		
		if (empty($output)) return;
		
		$oa = explode("\n", $output);
		
		foreach($oa AS $ol) {
			echo ' > ' . $ol . "\n";			
		}

	}
	
	public function upgrade($branch = NULL) {
		
		$access = $this->module->getAccess($branch);
		
		switch($access['type']) {
			case 'svn' :
				$this->requireInstalled();
				$this->svnUpdate($access);
				$this->enable();
				$this->prepareConfig();
				break;
				
			case 'zip' :
				$this->requireInstalled();
				$this->zipLoad($access);
				$this->enable();
				$this->prepareConfig();
				break;
			
			default:
				throw new Exception('Unknown access method type. Not one of [zip,tgz,svn]');
				
		}
		
	}
	
	public function dirExists() {
		$config = SimpleSAML_Configuration::getConfig('config.php');
		$basedir = $config->getBaseDir();
		
		$dir = $basedir . 'modules/' . $this->module->def['id'];
		
		return (file_exists($dir) && is_dir($dir));
	}
	
	public function requireValidURL($url) {
		if (!preg_match('|http(s)?://[a-zA-Z0-9_-/.]|', $url)) 
			throw new Exception('Invalid URL [' . $url . ']');
	}
	
	public function requireNotInstalled() {
		if ($this->dirExists())
			throw new Exception('The module [' . $this->module->def['id'] . '] is already installed.');
	}
	
	public function requireInstalled() {
		if (!$this->dirExists())
			throw new Exception('The module [' . $this->module->def['id'] . '] is not installed.');
	}
	
	public function svnCheckout($access) {
		$config = SimpleSAML_Configuration::getConfig('config.php');
		$basedir = $config->getBaseDir();
		$cmd = "svn co " . escapeshellarg($access['url']) . " " . $basedir . "modules/" . $this->module->def['id'];
		self::exec($cmd);
	}
	
	public function svnUpdate($access) {
		$config = SimpleSAML_Configuration::getConfig('config.php');
		$basedir = $config->getBaseDir();
		$cmd = "svn up " . $basedir . "modules/" . $this->module->def['id'];
		self::exec($cmd);
	}
	
	public function removeModuleDir($access) {
		$config = SimpleSAML_Configuration::getConfig('config.php');
		$basedir = $config->getBaseDir();
		$cmd = "rm -rf " . $basedir . "modules/" . $this->module->def['id'];
		self::exec($cmd);
	}
	
	public function enable() {
		$config = SimpleSAML_Configuration::getConfig('config.php');
		$basedir = $config->getBaseDir();
		
		$this->requireInstalled();
		
		$cmd = "touch " . $basedir . "modules/" . $this->module->def['id'] . '/enable';
		self::exec($cmd);
	}
	
	public function prepareConfig() {
		$config = SimpleSAML_Configuration::getConfig('config.php');
		$basedir = $config->getBaseDir();
		
		$this->requireInstalled();
		
		$dir = $basedir . "modules/" . $this->module->def['id'] . '/config-templates';
		if (!file_exists($dir)) return;
		
		$files = scandir($dir);
		foreach($files AS $file) {
			if(!preg_match('|^.*\.php|', $file)) continue;
			
			if (file_exists($basedir . 'config/' . $file)) {
				echo "Configuration file [" . $file . "] already exists. Will not overwrite existing file.\n";
				continue;
			}
			
			$cmd = 'cp ' . $dir . '/' . $file . ' ' . $basedir . 'config/';
			self::exec($cmd);	
		}
	}
	
	public function zipLoad($access) {
		$config = SimpleSAML_Configuration::getConfig('config.php');
		$basedir = $config->getBaseDir();
		
		$zipfile = $access['url'];
		$localfile = tempnam(sys_get_temp_dir(), 'ssp-module-');
		$filecontents = file_get_contents($zipfile);
		file_put_contents($localfile, $filecontents);
		
		$cmd = "unzip -qo " . escapeshellarg($localfile) . " -d " . $basedir . "modules/";
		self::exec($cmd);

	}
	
}