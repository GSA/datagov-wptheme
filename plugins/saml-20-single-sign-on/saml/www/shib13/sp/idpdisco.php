<?php

require_once('../../_include.php');

try {
	$discoHandler = new SimpleSAML_XHTML_IdPDisco(array('shib13-idp-remote'), 'shib13');
} catch (Exception $exception) {
	/* An error here should be caused by invalid query parameters. */
	throw new SimpleSAML_Error_Error('DISCOPARAMS', $exception);
}

try {
	$discoHandler->handleRequest();
} catch(Exception $exception) {
	/* An error here should be caused by metadata. */
	throw new SimpleSAML_Error_Error('METADATA', $exception);
}

?>