<?php

$modules = SimpleSAML_Module::getModules();
sort($modules);

$modinfo = array();

foreach($modules as $m) {
	$modinfo[$m] = array(
		'enabled' => SimpleSAML_Module::isModuleEnabled($m),
	);
	if (sspmod_core_ModuleDefinition::isDefined($m)) {
		$modinfo[$m]['def'] = sspmod_core_ModuleDefinition::load($m);
	}

}

function cmpa($a, $b) {
	
    if (isset($a['def']) && !isset($b['def'])) return -1;
    if (isset($b['def']) && !isset($a['def'])) return 1;
	return 0;
}
uasort($modinfo, 'cmpa');

$config = SimpleSAML_Configuration::getInstance();
$t = new SimpleSAML_XHTML_Template($config, 'modinfo:modlist.php');
$t->data['modules'] = $modinfo;
$t->show();

?>