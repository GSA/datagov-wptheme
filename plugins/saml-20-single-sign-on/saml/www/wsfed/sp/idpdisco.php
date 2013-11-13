<?php

require_once('../../_include.php');

try {
	$discoHandler = new SimpleSAML_XHTML_IdPDisco(array('wsfed-idp-remote'), 'wsfed');
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