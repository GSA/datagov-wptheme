<?php

/**
 * Class which handles iframe logout.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class SimpleSAML_IdP_LogoutIFrame extends SimpleSAML_IdP_LogoutHandler {

	/**
	 * Start the logout operation.
	 *
	 * @param array &$state  The logout state.
	 * @param string|NULL $assocId  The SP we are logging out from.
	 */
	public function startLogout(array &$state, $assocId) {
		assert('is_string($assocId) || is_null($assocId)');

		$associations = $this->idp->getAssociations();

		if (count($associations) === 0) {
			$this->idp->finishLogout($state);
		}

		foreach ($associations as $id => &$association) {
			$idp = SimpleSAML_IdP::getByState($association);
			$association['core:Logout-IFrame:Name'] = $idp->getSPName($id);
			$association['core:Logout-IFrame:State'] = 'onhold';
		}
		$state['core:Logout-IFrame:Associations'] = $associations;

		if (!is_null($assocId)) {
			$spName = $this->idp->getSPName($assocId);
			if ($spName === NULL) {
				$spName = array('en' => $assocId);
			}

			$state['core:Logout-IFrame:From'] = $spName;
		} else {
			$state['core:Logout-IFrame:From'] = NULL;
		}

		$params = array(
			'id' => SimpleSAML_Auth_State::saveState($state, 'core:Logout-IFrame'),
		);
		if (isset($state['core:Logout-IFrame:InitType'])) {
			$params['type'] = $state['core:Logout-IFrame:InitType'];
		}

		$url = SimpleSAML_Module::getModuleURL('core/idp/logout-iframe.php', $params);
		SimpleSAML_Utilities::redirect($url);
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

		$spId = sha1($assocId);
		$this->idp->terminateAssociation($assocId);

		echo('<!DOCTYPE html>
<html>
<head>
<title>Logout response from ' . htmlspecialchars(var_export($assocId, TRUE)) . '</title>
<script>
');
		if ($error) {
			$errorMsg = $error->getMessage();
			echo('window.parent.logoutFailed("' . $spId . '", "' . addslashes($errorMsg) . '");');
		} else {
			echo('window.parent.logoutCompleted("' . $spId . '");');
		}
		echo('
</script>
</head>
<body>
</body>
</html>
');

		exit(0);
	}

}
