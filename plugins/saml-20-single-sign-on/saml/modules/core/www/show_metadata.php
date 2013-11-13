<?php



/* Load simpleSAMLphp, configuration */
$config = SimpleSAML_Configuration::getInstance();
$session = SimpleSAML_Session::getInstance();

SimpleSAML_Utilities::requireAdmin();


if (!array_key_exists('entityid', $_REQUEST))
	throw new Exception('required parameter [entityid] missing');
if (!array_key_exists('set', $_REQUEST))
	throw new Exception('required parameter [set] missing');
if (!in_array($_REQUEST['set'], array('saml20-idp-remote', 'saml20-sp-remote', 'shib13-idp-remote', 'shib13-sp-remote')))
	throw new Exception('Invalid set');


$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();

$m = $metadata->getMetadata($_REQUEST['entityid'], $_REQUEST['set']);

// echo('<pre>'); print_r($m);
// exit;


$t = new SimpleSAML_XHTML_Template($config, 'core:show_metadata.tpl.php');
$t->data['pageid'] = 'show_metadata';
$t->data['header'] = 'simpleSAMLphp Show Metadata';
$t->data['backlink'] = SimpleSAML_Module::getModuleURL('core/frontpage_federation.php');
$t->data['m'] = $m;


$t->show();

