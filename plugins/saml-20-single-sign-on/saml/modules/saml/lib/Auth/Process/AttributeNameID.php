<?php

/**
 * Authproc filter to create a NameID from an attribute.
 *
 * @package simpleSAMLphp
 * @version $Id$
 */
class sspmod_saml_Auth_Process_AttributeNameID extends sspmod_saml_BaseNameIDGenerator {

	/**
	 * The attribute we should use as the NameID.
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

		if (!isset($config['Format'])) {
			throw new SimpleSAML_Error_Exception('AttributeNameID: Missing required option \'Format\'.');
		}
		$this->format = (string)$config['Format'];

		if (!isset($config['attribute'])) {
			throw new SimpleSAML_Error_Exception('AttributeNameID: Missing required option \'attribute\'.');
		}
		$this->attribute = (string)$config['attribute'];
	}


	/**
	 * Get the NameID value.
	 *
	 * @return string|NULL  The NameID value.
	 */
	protected function getValue(array &$state) {

		if (!isset($state['Attributes'][$this->attribute]) || count($state['Attributes'][$this->attribute]) === 0) {
			SimpleSAML_Logger::warning('Missing attribute ' . var_export($this->attribute, TRUE) . ' on user - not generating attribute NameID.');
			return NULL;
		}
		if (count($state['Attributes'][$this->attribute]) > 1) {
			SimpleSAML_Logger::warning('More than one value in attribute ' . var_export($this->attribute, TRUE) . ' on user - not generating attribute NameID.');
		}
		$value = array_values($state['Attributes'][$this->attribute]); /* Just in case the first index is no longer 0. */
		$value = $value[0];
		return $value;
	}

}
