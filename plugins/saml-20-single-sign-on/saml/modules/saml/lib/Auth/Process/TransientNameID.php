<?php

/**
 * Authproc filter to generate a transient NameID.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class sspmod_saml_Auth_Process_TransientNameID extends sspmod_saml_BaseNameIDGenerator {

	/**
	 * Initialize this filter, parse configuration
	 *
	 * @param array $config  Configuration information about this filter.
	 * @param mixed $reserved  For future use.
	 */
	public function __construct($config, $reserved) {
		parent::__construct($config, $reserved);
		assert('is_array($config)');

		$this->format = SAML2_Const::NAMEID_TRANSIENT;
	}


	/**
	 * Get the NameID value.
	 *
	 * @return string|NULL  The NameID value.
	 */
	protected function getValue(array &$state) {

		return SimpleSAML_Utilities::generateID();
	}

}
