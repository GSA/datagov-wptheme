<?php
/**
 * Filter to modify attributes using regular expressions
 *
 * This filter can modify or replace attributes given a regular expression.
 *
 * @author Jacob Christiansen, WAYF
 * @package simpleSAMLphp
 * @version $Id: AttributeAlter.php 2244 2010-03-29 13:05:20Z jach@wayf.dk $
 */
class sspmod_core_Auth_Process_AttributeAlter extends SimpleSAML_Auth_ProcessingFilter {

	/**
	 * Should found pattern be replace
	 */
	private $replace = FALSE;
	
	/**
	 * Pattern to be search for.
	 */
	private $pattern = '';
	
	/**
	 * String to replace found pattern.
	 */
	private $replacement = '';
	
	/**
	 * Attribute to search in
	 */
	private $subject = '';

	/**
	 * Attribute to place result in.
	 */
	private $target = '';
	
	/**
	 * Initialize this filter.
	 *
	 * @param array $config  Configuration information about this filter.
	 * @param mixed $reserved  For future use.
	 */
	public function __construct($config, $reserved) {
		assert('is_array($config)');

		parent::__construct($config, $reserved);

		// Parse filter configuration		
		foreach($config as $name => $value) {
			// Is %replace set?
			if(is_int($name)) {
				if($value == '%replace') {
					$this->replace = TRUE;
				} else {
					throw new Exception('Unknown flag : ' . var_export($value, TRUE));
				}
				continue;
			}
			
			// Unknown flag
			if(!is_string($name)) {
				throw new Exception('Unknown flag : ' . var_export($name, TRUE));
			}
			
			// Set pattern
			if($name == 'pattern') {
				$this->pattern = $value;
			}
			
			// Set replacement
			if($name == 'replacement') {
				$this->replacement = $value;
			}
			
			// Set subject
			if($name == 'subject') {
				$this->subject = $value;
			}
			
			// Set target
			if($name == 'target') {
				$this->target = $value;
			}
		}
	}

	/**
	 * Apply filter to modify attributes.
	 *
	 * Modify existing attributes with the configured values.
	 *
	 * @param array &$request  The current request
	 */
	public function process(&$request) {
		assert('is_array($request)');
		assert('array_key_exists("Attributes", $request)');

		// Get attributes from request
		$attributes =& $request['Attributes'];

		// Check that all required params are set in config
		if(empty($this->pattern) || empty($this->subject)) {
			throw new Exception("Not all params set in config.");
		}

		if(!$this->replace && empty($this->replacement)) {
			throw new Exception("'replacement' must be set if '%replace' is not set");
		}

		// Use subject as taget if target is not set
		if(empty($this->target)) {
			$this->target = $this->subject;		
		}
	
		// Check if attributes contains subject and target attribute
		if (array_key_exists($this->subject, $attributes) && array_key_exists($this->target, $attributes)) {
			if($this->replace == TRUE) {
				$matches = array();
				// Try to match pattern
				if(preg_match($this->pattern, $attributes[$this->subject][0], $matches) > 0) {
					if(empty($this->replacement)) {
						$attributes[$this->target][0] = $matches[0];	
					} else {
						$attributes[$this->target][0] = $this->replacement;
					}
				}
			} else {	
				$attributes[$this->target] = preg_replace($this->pattern, $this->replacement, $attributes[$this->subject]);
			}		
		}
	}
}