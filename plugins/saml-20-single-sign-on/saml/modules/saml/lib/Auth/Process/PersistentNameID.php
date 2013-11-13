<?php

/**
 * Authproc filter to generate a persistent NameID.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class sspmod_saml_Auth_Process_PersistentNameID extends sspmod_saml_BaseNameIDGenerator {

	/**
	 * Which attribute contains the unique identifier of the user.
	 *
	 * @var string
	 */
	private $attribute;


	/**
	 * Initialize this filter, parse configuration.
	 *
	 * @param array $config  Configuration information about this filter.
	 * @param mixed $reserved  For future use.
	 */
	public function __construct($config, $reserved) {
		parent::__construct($config, $reserved);
		assert('is_array($config)');

		$this->format = SAML2_Const::NAMEID_PERSISTENT;

		if (!isset($config['attribute'])) {
			throw new SimpleSAML_Error_Exception('PersistentNameID: Missing required option \'attribute\'.');
		}
		$this->attribute = $config['attribute'];
	}


	/**
	 * Get the NameID value.
	 *
	 * @return string|NULL  The NameID value.
	 */
	protected function getValue(array &$state) {

		if (!isset($state['Destination']['entityid'])) {
			SimpleSAML_Logger::warning('No SP entity ID - not generating persistent NameID.');
			return NULL;
		}
		$spEntityId = $state['Destination']['entityid'];

		if (!isset($state['Source']['entityid'])) {
			SimpleSAML_Logger::warning('No IdP entity ID - not generating persistent NameID.');
			return NULL;
		}
		$idpEntityId = $state['Source']['entityid'];

		if (!isset($state['Attributes'][$this->attribute]) || count($state['Attributes'][$this->attribute]) === 0) {
			SimpleSAML_Logger::warning('Missing attribute ' . var_export($this->attribute, TRUE) . ' on user - not generating persistent NameID.');
			return NULL;
		}
		if (count($state['Attributes'][$this->attribute]) > 1) {
			SimpleSAML_Logger::warning('More than one value in attribute ' . var_export($this->attribute, TRUE) . ' on user - not generating persistent NameID.');
			return NULL;
		}
		$uid = array_values($state['Attributes'][$this->attribute]); /* Just in case the first index is no longer 0. */
		$uid = $uid[0];

		$secretSalt = SimpleSAML_Utilities::getSecretSalt();

		$uidData = 'uidhashbase' . $secretSalt;
		$uidData .= strlen($idpEntityId) . ':' . $idpEntityId;
		$uidData .= strlen($spEntityId) . ':' . $spEntityId;
		$uidData .= strlen($uid) . ':' . $uid;
		$uidData .= $secretSalt;

		return sha1($uidData);
	}

}
