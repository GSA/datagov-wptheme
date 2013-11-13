<?php

/**
 * Filter to add attributes to the identity by executing a query against an LDAP directory
 *
 * Original Author: Steve Moitozo II <steve_moitozo@jaars.org>
 * Created: 20100513
 * Updated: 20100920 Steve Moitozo II
 *          - incorporated feedback from Olav Morken to prep code for inclusion in SimpleSAMLphp distro
 *          - moved call to ldap_set_options() inside test for $ds
 *          - added the output of ldap_error() to the exceptions
 *          - reduced some of the nested ifs
 *          - added support for multiple values
 *          - added support for anonymous binds
 *          - added escaping of search filter and attribute
 * Updated: 20111118 Ryan Panning
 *          - Updated the class to use BaseFilter which reuses LDAP connection features
 *          - Added conversion of original filter option names for backwards-compatibility
 *          - Updated the constructor to use the new config method
 *          - Updated the process method to use the new config variable names
 *
 * @author Steve Moitozo, JAARS, Inc., Ryan Panning
 * @package simpleSAMLphp
 * @version $Id$
 */
class sspmod_ldap_Auth_Process_AttributeAddFromLDAP extends sspmod_ldap_Auth_Process_BaseFilter {


	/**
	 * Name of the attribute to add LDAP values to
	 *
	 * @var string
	 */
	protected $new_attribute;


	/**
	 * LDAP attribute to add to the request attributes
	 *
	 * @var string
	 */
	protected $search_attribute;


	/**
	 * LDAP search filter to use in the LDAP query
	 *
	 * @var string
	 */
	protected $search_filter;


	/**
	 * Initialize this filter.
	 *
	 * @param array $config  Configuration information about this filter.
	 * @param mixed $reserved  For future use.
	 */
	public function __construct($config, $reserved) {

		// For backwards compatibility, check for old config names
		if (isset($config['ldap_host']))             $config['ldap.hostname']      = $config['ldap_host'];
		if (isset($config['ldap_port']))             $config['ldap.port']          = $config['ldap_port'];
		if (isset($config['ldap_bind_user']))        $config['ldap.username']      = $config['ldap_bind_user'];
		if (isset($config['ldap_bind_pwd']))         $config['ldap.password']      = $config['ldap_bind_pwd'];
		if (isset($config['userid_attribute']))      $config['attribute.username'] = $config['userid_attribute'];
		if (isset($config['ldap_search_base_dn']))   $config['ldap.basedn']        = $config['ldap_search_base_dn'];
		if (isset($config['ldap_search_filter']))    $config['search.filter']      = $config['ldap_search_filter'];
		if (isset($config['ldap_search_attribute'])) $config['search.attribute']   = $config['ldap_search_attribute'];
		if (isset($config['new_attribute_name']))    $config['attribute.new']      = $config['new_attribute_name'];

		// Remove the old config names
		unset(
			$config['ldap_host'],
			$config['ldap_port'],
			$config['ldap_bind_user'],
			$config['ldap_bind_pwd'],
			$config['userid_attribute'],
			$config['ldap_search_base_dn'],
			$config['ldap_search_filter'],
			$config['ldap_search_attribute'],
			$config['new_attribute_name']
		);

		// Now that we checked for BC, run the parent constructor
		parent::__construct($config, $reserved);

		// Get filter specific config options
		$this->new_attribute    = $this->config->getString('attribute.new');
		$this->search_attribute = $this->config->getString('search.attribute');
		$this->search_filter    = $this->config->getString('search.filter');
	}


	/**
	 * Add attributes from an LDAP server.
	 *
	 * @param array &$request  The current request
	 */
	public function process(&$request) {
		assert('is_array($request)');
		assert('array_key_exists("Attributes", $request)');

		$attributes =& $request['Attributes'];
		$map =& $this->attribute_map;

		if(!isset($attributes[$map['username']])){
			throw new Exception('The user\'s identity does not have an attribute called "'.$map['username'].'"');
		}


		// perform a merge on the ldap_search_filter

		// loop over the attributes and build the search and replace arrays
		foreach($attributes as $attr => $val){
			$arrSearch[] = '%'.$attr.'%';

			if(strlen($val[0]) > 0){
				$arrReplace[] = SimpleSAML_Auth_LDAP::escape_filter_value($val[0]);
			}else{
				$arrReplace[] = '';
			}
		}

		// merge the attributes into the ldap_search_filter
		$filter = str_replace($arrSearch, $arrReplace, $this->search_filter);

		// search for matching entries
		$entries = $this->getLdap()->searchformultiple($this->base_dn, $filter, (array) $this->search_attribute, TRUE, FALSE);

		// handle [multiple] values
		if(is_array($entries) && is_array($entries[0])){
			$results = array();
			foreach($entries as $entry){
				$entry = $entry[strtolower($this->search_attribute)];
				for($i = 0; $i < $entry['count']; $i++){
					$results[] = $entry[$i];
				}
			}
			$attributes[$this->new_attribute] = array_values($results);
		}

	}

}
