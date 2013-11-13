<?php
/**
 * Show a 403 Forbidden page about not authorized to access an application.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */

if (!array_key_exists('StateId', $_REQUEST)) {
	throw new SimpleSAML_Error_BadRequest('Missing required StateId query parameter.');
}

$id = $_REQUEST['StateId'];
$state = SimpleSAML_Auth_State::loadState($id, 'authorize:Authorize');

$globalConfig = SimpleSAML_Configuration::getInstance();
$t = new SimpleSAML_XHTML_Template($globalConfig, 'authorize:authorize_403.php');
header('HTTP/1.0 403 Forbidden');
$t->show();


?>
