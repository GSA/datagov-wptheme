<?php

/**
 * Various SAML 2 constants.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SAML2_Const {

	/**
	 * Password authentication context.
	 */
	const AC_PASSWORD = 'urn:oasis:names:tc:SAML:2.0:ac:classes:Password';

	/**
	 * Unspecified authentication context.
	 */
	const AC_UNSPECIFIED = 'urn:oasis:names:tc:SAML:2.0:ac:classes:unspecified';


	/**
	 * The URN for the HTTP-POST binding.
	 */
	const BINDING_HTTP_POST = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST';

	/**
	 * The URN for the HTTP-Redirect binding.
	 */
	const BINDING_HTTP_REDIRECT = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect';

	/**
	 * The URN for the HTTP-ARTIFACT binding.
	 */
	const BINDING_HTTP_ARTIFACT = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact';

	/**
	 * The URN for the SOAP binding.
	 */
	const BINDING_SOAP = 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP';

	/**
	 * The URN for the Holder-of-Key Web Browser SSO Profile binding
	 */
	const BINDING_HOK_SSO = 'urn:oasis:names:tc:SAML:2.0:profiles:holder-of-key:SSO:browser';


	/**
	 * Bearer subject confirmation method.
	 */
	const CM_BEARER = 'urn:oasis:names:tc:SAML:2.0:cm:bearer';

	/**
	* Holder-of-Key subject confirmation method.
	*/
	const CM_HOK = 'urn:oasis:names:tc:SAML:2.0:cm:holder-of-key';


	/**
	 * The URN for the unspecified attribute NameFormat.
	 */
	const NAMEFORMAT_UNSPECIFIED = 'urn:oasis:names:tc:SAML:2.0:attrname-format:unspecified';


	/**
	 * Unspecified NameID format.
	 */
	const NAMEID_UNSPECIFIED = 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified';

	/**
	 * Persistent NameID format.
	 */
	const NAMEID_PERSISTENT = 'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent';

	/**
	 * Transient NameID format.
	 */
	const NAMEID_TRANSIENT = 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient';

	/**
	 * Encrypted NameID format.
	 */
	const NAMEID_ENCRYPTED = 'urn:oasis:names:tc:SAML:2.0:nameid-format:encrypted';


	/**
	 * The namespace for the SOAP protocol.
	 */
	const NS_SOAP = 'http://schemas.xmlsoap.org/soap/envelope/';

	/**
	 * The namespace for the SAML 2 protocol.
	 */
	const NS_SAMLP = 'urn:oasis:names:tc:SAML:2.0:protocol';

	/**
	 * The namespace for the SAML 2 assertions.
	 */
	const NS_SAML = 'urn:oasis:names:tc:SAML:2.0:assertion';

	/**
	 * The namespace for the SAML 2 metadata.
	 */
	const NS_MD = 'urn:oasis:names:tc:SAML:2.0:metadata';

	/**
	 * The namespace fox XML schema.
	 */
	const NS_XS = 'http://www.w3.org/2001/XMLSchema';

	/**
	 * The namespace for XML schema instance.
	 */
	const NS_XSI = 'http://www.w3.org/2001/XMLSchema-instance';

	/**
	 * The namespace for the SAML 2 HoK Web Browser SSO Profile.
	 */
	const NS_HOK = 'urn:oasis:names:tc:SAML:2.0:profiles:holder-of-key:SSO:browser';

	/**
	 * Top-level status code indicating successful processing of the request.
	 */
	const STATUS_SUCCESS = 'urn:oasis:names:tc:SAML:2.0:status:Success';

	/**
	 * Top-level status code indicating that there was a problem with the request.
	 */
	const STATUS_REQUESTER = 'urn:oasis:names:tc:SAML:2.0:status:Requester';

	/**
	 * Top-level status code indicating that there was a problem generating the response.
	 */
	const STATUS_RESPONDER = 'urn:oasis:names:tc:SAML:2.0:status:Responder';

	/**
	 * Top-level status code indicating that the request was from an unsupported version of the SAML protocol.
	 */
	const STATUS_VERSION_MISMATCH = 'urn:oasis:names:tc:SAML:2.0:status:VersionMismatch';


	/**
	 * Second-level status code for NoPassive errors.
	 */
	const STATUS_NO_PASSIVE = 'urn:oasis:names:tc:SAML:2.0:status:NoPassive';

	/**
	 * Second-level status code for PartialLogout.
	 */
	const STATUS_PARTIAL_LOGOUT = 'urn:oasis:names:tc:SAML:2.0:status:PartialLogout';

	/**
	 * Second-level status code for ProxyCountExceeded.
	 */
	const STATUS_PROXY_COUNT_EXCEEDED = 'urn:oasis:names:tc:SAML:2.0:status:ProxyCountExceeded';
	

}

?>