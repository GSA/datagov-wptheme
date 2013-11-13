<?php

/**
 * The Shibboleth 1.3 Authentication Request. Not part of SAML 1.1, 
 * but an extension using query paramters no XML.
 *
 * @author Andreas Åkre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @package simpleSAMLphp
 * @version $Id: AuthnRequest.php 2070 2010-01-05 10:19:28Z olavmrk $
 */
class SimpleSAML_XML_Shib13_AuthnRequest {

	private $issuer = null;
	private $relayState = null;

	public function setRelayState($relayState) {
		$this->relayState = $relayState;
	}
	
	public function getRelayState() {
		return $this->relayState;
	}
	
	public function setIssuer($issuer) {
		$this->issuer = $issuer;
	}
	public function getIssuer() {
		return $this->issuer;
	}

	public function createRedirect($destination, $shire = NULL) {
		$metadata = SimpleSAML_Metadata_MetaDataStorageHandler::getMetadataHandler();
		$idpmetadata = $metadata->getMetaDataConfig($destination, 'shib13-idp-remote');

		if ($shire === NULL) {
			$shire = $metadata->getGenerated('AssertionConsumerService', 'shib13-sp-hosted');
		}

		$desturl = $idpmetadata->getDefaultEndpoint('SingleSignOnService', array('urn:mace:shibboleth:1.0:profiles:AuthnRequest'));
		$desturl = $desturl['Location'];

		$target = $this->getRelayState();
		
		$url = $desturl . '?' .
	    	'providerId=' . urlencode($this->getIssuer()) .
		    '&shire=' . urlencode($shire) .
		    (isset($target) ? '&target=' . urlencode($target) : '');
		return $url;
	}

}

?>