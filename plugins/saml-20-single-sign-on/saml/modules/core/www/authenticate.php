<?php



$config = SimpleSAML_Configuration::getInstance();

if (!array_key_exists('as', $_REQUEST)) {
	$t = new SimpleSAML_XHTML_Template($config, 'core:authsource_list.tpl.php');

	$t->data['sources'] = SimpleSAML_Auth_Source::getSources();
	$t->show();
	exit();
}


$asId = (string)$_REQUEST['as'];
$as = new SimpleSAML_Auth_Simple($asId);

if(array_key_exists('logout', $_REQUEST)) {
	$as->logout('/' . $config->getBaseURL() . 'logout.php');
}

if (array_key_exists(SimpleSAML_Auth_State::EXCEPTION_PARAM, $_REQUEST)) {
	/* This is just a simple example of an error. */

	$state = SimpleSAML_Auth_State::loadExceptionState();
	assert('array_key_exists(SimpleSAML_Auth_State::EXCEPTION_DATA, $state)');
	$e = $state[SimpleSAML_Auth_State::EXCEPTION_DATA];

	header('Content-Type: text/plain');
	echo "Exception during login:\n";
	foreach ($e->format() as $line) {
		echo $line . "\n";
	}
	exit(0);
}


if (!$as->isAuthenticated()) {
	$url = SimpleSAML_Module::getModuleURL('core/authenticate.php', array('as' => $asId));
	$params = array(
		'ErrorURL' => $url,
		'ReturnTo' => $url,
	);
	$as->login($params);
}

$attributes = $as->getAttributes();

$t = new SimpleSAML_XHTML_Template($config, 'status.php', 'attributes');

$t->data['header'] = '{status:header_saml20_sp}';
$t->data['attributes'] = $attributes;
$t->data['logouturl'] = SimpleSAML_Utilities::selfURLNoQuery() . '?as=' . urlencode($asId) . '&logout';
$t->show();

