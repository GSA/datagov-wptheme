<?php

Class SAML_Settings
{
  private $wp_option;
  private $current_version;
  private $cache;
  private $settings;
  
  function __construct()
  {
    $this->wp_option = 'saml_authentication_options';
    $this->current_version = '0.9.0';
    $this->cache = false;
    $this->_check_environment();
    $this->_get_settings();
  }
  
  /**
   * Get the "enabled" setting
   *
   * @return bool
   */
  public function get_enabled()
  {
    return (bool) $this->settings['enabled'];
  }
  
  public function get_idp()
  {
    return (string) $this->settings['idp'];
  }
  
  public function get_nameidpolicy()
  {
    return (string) $this->settings['nameidpolicy'];
  }
  
  /**
   * Get one of the "attribute" settings
   *
   * @param string $attribute the attribute to retrieve
   * @return string|bool The value of the attribute, or false if the attribute does not exist
   */
  public function get_attribute($attribute)
  {
    if(is_string($attribute) && array_key_exists($attribute, $this->settings['attributes']) )
    {
      return (string) $this->settings['attributes'][$attribute];
    }
    else
    {
      return false;
    }
  }
  
  /**
   * Get one of the "group" settings
   *
   * @param string $group
   * @return string|bool The name of the group, or false if the attribute does not exist
   */
  public function get_group($group)
  {
    if(is_string($group) && array_key_exists($group, $this->settings['groups']) )
    {
      return (string) $this->settings['groups'][$group];
    }
    else
    {
      return false;
    }
  }
  
  /**
   * Get the "allow_unlisted_users" setting
   *
   * @return bool
   */
  public function get_allow_unlisted_users()
  {
    return (bool) $this->settings['allow_unlisted_users'];  
  }
  
  /**
   * Sets whether to enable SAML authentication
   * 
   * @param bool $value The new value
   * @return void
   */
  public function set_enabled($value)
  {
    if( is_bool($value) ) 
    {
      $this->settings['enabled'] = $value;
      $this->_set_settings();
    }
  }
  
  /**
   * Sets the IdP Entity ID
   * 
   * @param string $value The new Entity ID
   * @return void
   */
  public function set_idp($value)
  {
    if( is_string($value) )
    {
      $this->settings['idp'] = $value;
      $this->_set_settings();
    }
  }
  
  /**
   * Sets the NameID Policy
   * 
   * @param string $value The new NameID Policy
   * @return void
   */
  public function set_nameidpolicy($value)
  {
    $policies = array(
      'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
      'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
      'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent'
    );
    
    if( is_string($value) && in_array($value,$policies) )
    {
      $this->settings['nameidpolicy'] = $value;
      $this->_set_settings();
    }
  }
  
  /**
   * Sets whether to allow unlisted_users (users with no group)
   * 
   * @param bool $value The new value
   * @return void
   */
  public function set_allow_unlisted_users($value)
  {
    if( is_bool($value) ) 
    {
      $this->settings['allow_unlisted_users'] = $value;
      $this->_set_settings();
    }
  }
  
  /**
   * Sets an attribute to a new value
   *
   * @param string $attributename The array key (in $this->settings->attributes) of the attribute to change
   * @param string $value The new value for the attribute
   * @return void
   */
  public function set_attribute($attributename, $value)
  {
    if( is_string($attributename) && is_string($value) && array_key_exists($attributename,$this->settings['attributes']) )
    {
      $this->settings['attributes'][$attributename] = $value;
      $this->_set_settings();      
    }
  }
  
  /**
   * Sets a group to a new value
   *
   * @param string $groupname The array key (in $this->settings->groups) of the group to change
   * @param string $value The new value for the group
   * @return void
   */
  public function set_group($groupname, $value)
  {
    if( is_string($groupname) && is_string($value) && array_key_exists($groupname,$this->settings['groups']) )
    {
      $this->settings['groups'][$groupname] = $value;
      $this->_set_settings();
    }
  }
  
  /**
   * Prevents use of ::_set_settings()
   *
   * @return void
   */
  public function enable_cache()
  {
    $this->cache = true;
  }
  
  /**
   * Saves settings and sets cache to false
   *
   * @return void
   */
  public function disable_cache()
  {
    $this->cache = false;
    $this->_set_settings();
  }

  /**
   * Retrieves settings from the database; performs upgrades or sets defaults as necessary
   *
   * @return void
   */
  private function _get_settings()
  {
    $wp_option = get_option($this->wp_option);
    
    if( is_array($wp_option) )
    {
      $this->settings = $wp_option;
      if( $this->_upgrade_settings() )
      {
        $this->_set_settings();
      }
    }
    else
    {
      $this->settings = $this->_use_defaults();
      $this->_set_settings();
    }  
  }
  
  /**
   * Writes settings to the database\
   *
   * @return bool true if settings are written to DB, false otherwise
   */
  private function _set_settings()
  {
    if($this->cache === false)
    {
      update_option($this->wp_option, $this->settings);
      return true;
    }
    else
    {
      return false;
    }
  }
  
  /**
   * Returns an array of default settings for the database. Typically used on first run.
   *
   * @return array the array of settings
   */
  private function _use_defaults()
  {
    $defaults = array(
      'option_version' => $this->current_version,
      'enabled' => false,
      'idp' => 'https://your-idp.net',
      'nameidpolicy' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
      'attributes' => array(
        'username' => '',
        'firstname' => '',
        'lastname' => '',
        'email' => '',
        'groups' => '',
      ),
      'groups' => array(
        'super_admin' => '',
        'admin' => '',
        'editor' => '',
        'author' => '',
        'contributor' => '',
        'subscriber' => '',
      ),      
      'allow_unlisted_users' => true
    );
    
    return($defaults);
  }
  
  /**
   * Checks for the presence of various files and directories that the plugin needs to operate
   *
   * @return void
   */
  private function _check_environment()
  {
  	if(! file_exists( constant('SAMLAUTH_CONF') ) )
  	{
  		mkdir( constant('SAMLAUTH_CONF'), 0775, true );
  	}
  	
  	if(! file_exists( constant('SAMLAUTH_CONF') . '/certs') )
  	{
  		mkdir( constant('SAMLAUTH_CONF') . '/certs', 0775, true );
  	}
  	
  	if(! file_exists( constant('SAMLAUTH_CONF') . '/config' ) )
  	{
  		mkdir( constant('SAMLAUTH_CONF') . '/config' , 0775, true );
  	}
  	
  	if(! file_exists(constant('SAMLAUTH_CONF') . '/config/saml20-idp-remote.ini') )
  	{
  		file_put_contents(constant('SAMLAUTH_CONF') . '/config/saml20-idp-remote.ini',"[https://your-idp.net]\nname = Your IdP\nSingleSignOnService = https://your-idp.net/SSOService\nSingleLogoutService = https://your-idp.net/SingleLogoutService\ncertFingerprint = 0000000000000000000000000000000000000000");
  	}
  	
  	if(! file_exists( constant('SAMLAUTH_CONF') . '/certs/.htaccess' ) || md5_file( constant('SAMLAUTH_CONF') . '/certs/.htaccess' ) != '9f6dc1ce87ca80bc859b47780447f1a6')
  	{
  		file_put_contents( constant('SAMLAUTH_CONF') . '/certs/.htaccess' , "<Files ~ \"\\.(key)$\">\nDeny from all\n</Files>" );
  	}
  }
  
  /**
   * Upgrades the settings array to the latest version
   *
   * @return bool true if changes were made, false otherwise
   */
  private function _upgrade_settings()
  {
    $changed = false;
    
    // Versioning the settings is a new feature: old plugin versions won't have a version number at all.
    if( array_key_exists('option_version',$this->settings) )
    {
      /**
       * array $previous version info [major, minor, tertiary]
       */
      $previous = explode('.',$this->settings['option_version']);
    }
    else
    {
      $previous = array('0','0','0');
    }
    
    if( (int)$previous[0] == 0 && (int)$previous[1] < 9 )
    {
      // Version 0.9.0 is the first to include versioning. Older versions need the version key added, as well as moving attributes and groups into corresponding nested keys.
      
      $changed = true;
       
      $this->settings['option_version'] = $this->current_version;
      $this->settings['attributes'] = array(
        'username' => $this->settings['username_attribute'],
        'firstname' => $this->settings['firstname_attribute'],
        'lastname' => $this->settings['lastname_attribute'],
        'email' => $this->settings['email_attribute'],
        'groups' => $this->settings['groups_attribute'],
      );
      $this->settings['groups'] = array(
        'super_admin' => $this->settings['super_admin_group'],
        'admin' => $this->settings['admin_group'],
        'editor' => $this->settings['editor_group'],
        'author' => $this->settings['author_group'],
        'contributor' => $this->settings['contributor_group'],
        'subscriber' => $this->settings['subscriber_group'],
      );
      
      unset($this->settings['username_attribute']);
      unset($this->settings['firstname_attribute']);
      unset($this->settings['lastname_attribute']);
      unset($this->settings['email_attribute']);
      unset($this->settings['groups_attribute']);
      
      unset($this->settings['super_admin_group']);
      unset($this->settings['admin_group']);
      unset($this->settings['editor_group']);
      unset($this->settings['author_group']);
      unset($this->settings['contributor_group']);
      unset($this->settings['subscriber_group']);
    }
    
    return($changed);
  }
}
// End of file saml_settings.php