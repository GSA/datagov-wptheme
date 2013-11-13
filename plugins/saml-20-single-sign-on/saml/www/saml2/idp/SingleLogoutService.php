<?php

/**
 * This SAML 2.0 endpoint can receive incoming LogoutRequests. It will also send LogoutResponses, 
 * and LogoutRequests and also receive LogoutResponses. It is implemeting SLO at the SAML 2.0 IdP.
 *
 * @author Andreas Åkre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @package simpleSAMLphp
 * @version $Id: SingleLogoutService.php 2140 2010-01-27 09:26:39Z olavmrk $
 */

require_once('../../_include.php');

SimpleSAML_Logger::info('SAML2.0 - IdP.SingleLogoutService: Accessing SAML 2.0 IdP endpoint SingleLogoutService');

$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();
$idpEntityId = $metadata->getMetaDataCurrentEntityID('saml20-idp-hosted');
$idp = SimpleSAML_IdP::getById('saml2:' . $idpEntityId);

if (isset($_REQUEST['ReturnTo'])) {
	$idp->doLogoutRedirect((string)$_REQUEST['ReturnTo']);
} else {
	sspmod_saml_IdP_SAML2::receiveLogoutMessage($idp);
}
assert('FALSE');
