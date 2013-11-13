<?php
/**
 * This hook lets the module describe itself.
 *
 * @param array &$moduleinfo  The links on the frontpage, split into sections.
 */
function modinfo_hook_moduleinfo(&$moduleinfo) {
	assert('is_array($moduleinfo)');
	assert('array_key_exists("info", $moduleinfo)');
	

	$moduleinfo['info']['modinfo'] = array(
		'name' => array('en' => 'Module information'),
		'description' => array('en' => 'This module lists all available modules, and tells whether they are enabled or not.'),
		
		'dependencies' => array('core'),
		'uses' => array('sanitycheck'),
	);

}
?>