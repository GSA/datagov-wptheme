<?php

/*
 * This endpoint is provided for backwards compatibility,
 * and should not be used.
 *
 * Use SingleLogoutService.php instead.
 */
require_once('../../_include.php');

SimpleSAML_Logger::info('SAML2.0 - IdP.SingleLogoutServiceiFrame: Accessing SAML 2.0 IdP endpoint SingleLogoutService (iFrame version)');

$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();
$idpEntityId = $metadata->getMetaDataCurrentEntityID('saml20-idp-hosted');
$idp = SimpleSAML_IdP::getById('saml2:' . $idpEntityId);
sspmod_saml_IdP_SAML2::receiveLogoutMessage($idp);
assert('FALSE');
