<?php

/**
 * Class which handles traditional logout.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_IdP_LogoutTraditional extends SimpleSAML_IdP_LogoutHandler {

	/**
	 * Picks the next SP and issues a logout request.
	 *
	 * This function never returns.
	 *
	 * @param array &$state  The logout state.
	 */
	private function logoutNextSP(array &$state) {

		$association = array_pop($state['core:LogoutTraditional:Remaining']);
		if ($association === NULL) {
			$this->idp->finishLogout($state);
		}

		$relayState = SimpleSAML_Auth_State::saveState($state, 'core:LogoutTraditional', TRUE);

		$id = $association['id'];
		SimpleSAML_Logger::info('Logging out of ' . var_export($id, TRUE) . '.');

		try {
			$idp = SimpleSAML_IdP::getByState($association);
			$url = call_user_func(array($association['Handler'], 'getLogoutURL'), $idp, $association, $relayState);
			SimpleSAML_Utilities::redirect($url);
		} catch (Exception $e) {
			SimpleSAML_Logger::warning('Unable to initialize logout to ' . var_export($id, TRUE) . '.');
			$this->idp->terminateAssociation($id);
			$state['core:Failed'] = TRUE;

			/* Try the next SP. */
			$this->logoutNextSP($state);
			assert('FALSE');
		}
	}


	/**
	 * Start the logout operation.
	 *
	 * This function never returns.
	 *
	 * @param array &$state  The logout state.
	 * @param string $assocId  The association that started the logout.
	 */
	public function startLogout(array &$state, $assocId) {

		$state['core:LogoutTraditional:Remaining'] = $this->idp->getAssociations();

		self::logoutNextSP($state);
	}


	/**
	 * Continue the logout operation.
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

		if ($relayState === NULL) {
			throw new SimpleSAML_Error_Exception('RelayState lost during logout.');
		}

		$state = SimpleSAML_Auth_State::loadState($relayState, 'core:LogoutTraditional');

		if ($error === NULL) {
			SimpleSAML_Logger::info('Logged out of ' . var_export($assocId, TRUE) . '.');
			$this->idp->terminateAssociation($assocId);
		} else {
			SimpleSAML_Logger::warning('Error received from ' . var_export($assocId, TRUE) . ' during logout:');
			$error->logWarning();
			$state['core:Failed'] = TRUE;
		}

		self::logoutNextSP($state);
	}

}
