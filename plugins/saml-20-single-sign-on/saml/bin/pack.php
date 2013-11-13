#!/usr/bin/env php
<?php

/* This is the base directory of the simpleSAMLphp installation. */
$baseDir = dirname(dirname(__FILE__));

/* Add library autoloader. */
require_once($baseDir . '/lib/_autoload.php');

if (count($argv) < 1) {
	echo "Wrong number of parameters. Run:   " . $argv[0] . " [install,show] url [branch]\n"; exit;
}

// Needed in order to make session_start to be called before output is printed.
$session = SimpleSAML_Session::getInstance();
$config = SimpleSAML_Configuration::getConfig('config.php');


$action = $argv[1];


function getModinfo() {
	global $argv;
	if (count($argv) < 2)
		throw new Exception('Missing second parameter: URL/ID');
	return sspmod_core_ModuleDefinition::load($argv[2]);
}

function getBranch() {
	global $argv;
	if (isset($argv[3])) return $argv[3];
	return NULL;
}

switch($action) {
	case 'install':
	 	$mod = getModinfo();
		$installer = new sspmod_core_ModuleInstaller($mod);
		$installer->install(getBranch());
		break;
	
	case 'remove': 
	 	$mod = getModinfo();
		$installer = new sspmod_core_ModuleInstaller($mod);
		$installer->remove(getBranch());
		break;
		
	case 'upgrade': 
	 	$mod = getModinfo();
		$installer = new sspmod_core_ModuleInstaller($mod);
		$installer->upgrade(getBranch());
		break;
	
	case 'upgrade-all' :
		$mdir = scandir($config->getBaseDir() . 'modules/');
		foreach($mdir AS $md) {
			if (!sspmod_core_ModuleDefinition::validId($md)) continue;
			if (!sspmod_core_ModuleDefinition::isDefined($md)) continue;
			$moduledef = sspmod_core_ModuleDefinition::load($md, 'remote');
			$installer = new sspmod_core_ModuleInstaller($moduledef);
			
			if ($moduledef->updateExists() || $moduledef->alwaysUpdate()) {
				echo "Upgrading [" . $md . "]\n";
				$installer->upgrade();				
			} else {
				echo "No updates available for [" . $md . "]\n";
			}
		}
		break;
			
	default: 
		throw new Exception('Unknown action [' . $action . ']');
}




