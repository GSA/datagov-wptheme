<?php

/**
 * Base class for logout handlers.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
abstract class SimpleSAML_IdP_LogoutHandler {

	/**
	 * The IdP we are logging out from.
	 *
	 * @var SimpleSAML_IdP
	 */
	protected $idp;


	/**
	 * Initialize this logout handler.
	 *
	 * @param SimpleSAML_IdP $idp  The IdP we are logging out from.
	 */
	public function __construct(SimpleSAML_IdP $idp) {
		$this->idp = $idp;
	}


	/**
	 * Start a logout operation.
	 *
	 * This function must never return.
	 *
	 * @param array &$state  The logout state.
	 * @param string|NULL $assocId  The association that started the logout.
	 */
	abstract public function startLogout(array &$state, $assocId);


	/**
	 * Handles responses to our logout requests.
	 *
	 * This function will never return.
	 *
	 * @param string $assocId  The association that is terminated.
	 * @param string|NULL $relayState  The RelayState from the start of the logout.
	 * @param SimpleSAML_Error_Exception|NULL $error  The error that occurred during session termination (if any).
	 */
	public function onResponse($assocId, $relayState, SimpleSAML_Error_Exception $error = NULL) {
		assert('is_string($assocId)');
		assert('is_string($relayState) || is_null($relayState)');

		/* Don't do anything by default. */
	}

}
