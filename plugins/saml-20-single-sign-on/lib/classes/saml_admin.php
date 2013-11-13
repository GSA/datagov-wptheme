<?php
Class SAML_Admin
{
  private $settings;
  
  function __construct()
  {
    $this->settings = new SAML_Settings();
    add_action('init',array($this,'admin_menus'));
  }
  
  function admin_menus()
  {
  	if( is_multisite() )
  	{	
  		add_action('network_admin_menu', array($this,'saml_idp_menus'));
  		add_action('admin_menu', array($this,'saml_sp_menus'));
  	}
  	else
  	{
  		add_action('admin_menu', array($this,'saml_idp_menus'));
  		add_action('admin_menu', array($this,'saml_sp_menus'));
  	}
  }
  
  function saml_idp_menus()
  {
  	if( is_multisite() )
  	{
  		add_submenu_page('settings.php', 'Single Sign-On', 'Single Sign-On', 'manage_network', 'sso_idp.php', array($this,'sso_idp'));
  		add_submenu_page('settings.php', 'Single Sign-On', 'Single Sign-On', 'manage_network', 'sso_help.php', array($this,'sso_help'));
  		
  		remove_submenu_page( 'settings.php', 'sso_help.php' );
  	}
  	else
  	{
  		add_submenu_page('options-general.php', 'Single Sign-On', 'Single Sign-On', 'administrator', 'sso_idp.php', array($this,'sso_idp'));
  		add_submenu_page('options-general.php', 'Single Sign-On', 'Single Sign-On', 'administrator', 'sso_help.php', array($this,'sso_help'));
  		
  		remove_submenu_page( 'options-general.php', 'sso_idp.php' );
  		remove_submenu_page( 'options-general.php', 'sso_help.php' );
  	}
  }
  
  function saml_sp_menus()
  {
  	add_submenu_page('options-general.php', 'Single Sign-On', 'Single Sign-On', 'administrator', 'sso_general.php', array($this,'sso_general'));
  	add_submenu_page('options-general.php', 'Single Sign-On', 'Single Sign-On', 'administrator', 'sso_sp.php', array($this,'sso_sp'));
  	add_submenu_page('options-general.php', 'Single Sign-On', 'Single Sign-On', 'administrator', 'sso_help.php', array($this,'sso_help'));
  	
  	remove_submenu_page( 'options-general.php', 'sso_sp.php');
  	remove_submenu_page( 'options-general.php', 'sso_help.php');
  }
  
  /*
  * Function Get SAML Status
  *   Evaluates SAML configuration for basic sanity
  *  
  *
  * @param void
  * 
  * @return Object
  */
  public function get_saml_status()
  {
    $return = new stdClass;
      $return->html = "";
      $return->num_warnings = 0;
      $return->num_errors = 0;
    
    $status = array(
      'idp_entityid' => array(
          'error_default' => 'You have not changed your IdP&rsquo;s Entity ID from the default value. You should update it to a real value.',
          'error_blank'   => 'You have not provided an Entity ID for your IdP.',
          'warning'       => 'The Entity ID you provided may not be a accessible (perhaps a bad URL). You should check that it is correct.',
          'ok'            => 'You have provided an Entity ID for your IdP.',
        ),
        'idp_sso' => array(
          'error'   => 'You have not changed your IdP&rsquo;s Single Sign-On URL from the default value. You should update it to a real value.',
          'warning' => 'You have not provided a Single Sign-On URL for your IdP. Users will have to log in using the <a href="?page=sso_help.php#idp-first-flow">IdP-first flow</a>.',
          'ok'      => 'You have provided a Single Sign-On URL for your IdP.',
        ), 
        'idp_slo' => array(
          'error'   => 'You have not changed your IdP&rsquo;s Single Logout URL from the default value. You should update it to a real value.',
          'warning' => 'You have not provided a Single Logout URL for your IdP. Users will not be logged out of the IdP when logging out of your site.',
          'ok'      => 'You have provided a Single Logout URL for your IdP.',
        ),  
        'idp_fingerprint' => array(
          'error'   => 'You have not provided a Certificate Fingerprint for your IdP',
          'warning' => '',
          'ok'      => 'You have provided a Certificate Fingerprint for your IdP.',
        ), 
        'sp_certificate' => array(
          'error'   => '',
          'warning' => 'You have not provided a Certificate or Private Key for this site. Users may not be able to log in using the SP-first flow.',
          'ok'      => 'You have provided a Certificate and Private Key for this site.',
        ),
        'sp_attributes' => array(
          'error'   => 'You have not provided the neccessary SAML attributes to allow users to log in. You must <strong>at least</strong> specify SAML attributes to be used for the "username" and "Groups" fields.',
          'warning' => 'You have not provided SAML attributes for all fields. Users may be able to log in, but may not have all attributes such as first and last name.',
          'ok'      => 'You have provided SAML attributes for all user fields.',
        ),
        'sp_permissions' => array(
          'error'   => 'You have not specified any permission groups for SSO users. All SSO users will either be subscribers, or fail to log in.',
          'warning' => 'You have specified some permission groups, but no SSO users will be administrators. This could cause you to lose access to your site.',
          'ok'      => 'You have specified permission groups for this site.',
        )
    );
    
    $status_html = array(
      'error'   => array(
        '<tr class="red"><td><i class="icon-remove icon-large"></i></td><td>',
        '</td></tr>'
      ),
      'warning' => array(
        '<tr class="yellow"><td><i class="icon-warning-sign icon-large"></i></td><td>',
        '</td></tr>'
      ),
      'ok'      => array(
        '<tr class="green"><td><i class="icon-ok icon-large"></i></td><td>',
        '</td></tr>'
      )
    );
    
    $idp_ini = parse_ini_file(constant('SAMLAUTH_CONF') . '/config/saml20-idp-remote.ini',true);
    
    $return->html .= '<table class="saml_status">'."\n";
    
    if (is_array($idp_ini))
    {  
      foreach($idp_ini as $key => $val)
      {
        if( trim($key) != '' && $key != 'https://your-idp.net')
        {
          $return->html .= $status_html['ok'][0] . $status['idp_entityid']['ok'] . $status_html['ok'][1]; 
        }
        elseif( trim($key) == 'https://your-idp.net')
        {
          $return->html .= $status_html['error'][0] . $status['idp_entityid']['error_default'] . $status_html['ok'][1];
          $return->num_errors++;
        }
        elseif($key == '')
        {
          $return->html .= $status_html['error'][0] . $status['idp_entityid']['error_blank'] . $status_html['ok'][1];
          $return->num_errors++;
        }
        
        if( $val['SingleSignOnService'] == 'https://your-idp.net/SSOService' )
        {
          $return->html .= $status_html['error'][0] . $status['idp_sso']['error'] . $status_html['error'][1];
        }
        elseif( trim( $val['SingleSignOnService'] ) != '')
        {
          $return->html .= $status_html['ok'][0] . $status['idp_sso']['ok'] . $status_html['ok'][1];
        }
        else
        {
          $return->html .= $status_html['warning'][0] . $status['idp_sso']['warning'] . $status_html['warning'][1];
        }
        
        if( $val['SingleLogoutService'] == 'https://your-idp.net/SingleLogoutService' )
        {
          $return->html .= $status_html['error'][0] . $status['idp_slo']['error'] . $status_html['error'][1];
          $return->num_errors++;
        }
        elseif( trim( $val['SingleLogoutService'] ) != '')
        {
          $return->html .= $status_html['ok'][0] . $status['idp_slo']['ok'] . $status_html['ok'][1];
        }
        else
        {
          $return->html .= $status_html['warning'][0] . $status['idp_slo']['warning'] . $status_html['warning'][1];
        }
        
        if( $val['certFingerprint'] != '0000000000000000000000000000000000000000' && $val['certFingerprint'] != '')
        {
          $return->html .= $status_html['ok'][0] . $status['idp_fingerprint']['ok'] . $status_html['ok'][1];
        }
        else
        {
          $return->html .= $status_html['error'][0] . $status['idp_fingerprint']['error'] . $status_html['ok'][1];
          $return->num_errors++;
        }
      }
    }
    
    if(file_exists(constant('SAMLAUTH_CONF') . '/certs/' . get_current_blog_id() . '/' . get_current_blog_id() . '.cer') && file_exists(constant('SAMLAUTH_CONF') . '/certs/' . get_current_blog_id() . '/' . get_current_blog_id() . '.key'))
    {
      $return->html .= $status_html['ok'][0] . $status['sp_certificate']['ok'] . $status_html['ok'][1];
    }
    else
    {
      $return->html .= $status_html['warning'][0] . $status['sp_certificate']['warning'] . $status_html['warning'][1];
    }
    
    if( trim($this->settings->get_attribute('username')) == '' || trim($this->settings->get_attribute('groups')) == '' )
    {
      $return->html .= $status_html['error'][0] . $status['sp_attributes']['error'] . $status_html['error'][1];
      $return->num_errors++;
    }
    elseif(trim ($this->settings->get_attribute('firstname')) == '' || trim ($this->settings->get_attribute('lastname')) == '' || trim ($this->settings->get_attribute('email')) == '')
    {
      $return->html .= $status_html['warning'][0] . $status['sp_attributes']['warning'] . $status_html['warning'][1];
    }
    else
    {
      $return->html .= $status_html['ok'][0] . $status['sp_attributes']['ok'] . $status_html['ok'][1];
    }
    
    if( trim($this->settings->get_group('admin')) != '' )
    {
      $return->html .= $status_html['ok'][0] . $status['sp_permissions']['ok'] . $status_html['ok'][1];
    }
    elseif(trim($this->settings->get_group('admin')) == '' && (trim($this->settings->get_group('editor')) != '' || trim($this->settings->get_group('author')) != '' || trim($this->settings->get_group('contributor')) != '' || trim($this->settings->get_group('subscriber')) != '') )
    {
      $return->html .= $status_html['warning'][0] . $status['sp_permissions']['warning'] . $status_html['warning'][1];
    }
    elseif( trim($this->settings->get_group('admin')) == '' && trim($this->settings->get_group('editor')) == '' && trim($this->settings->get_group('author')) == '' && trim($this->settings->get_group('contributor')) == '' && trim($this->settings->get_group('subscriber')) == '' )
    {
      $return->html .= $status_html['error'][0] . $status['sp_permissions']['error'] . $status_html['error'][1];
      $return->num_errors++;
    }
    
    $return->html .= '</table>'."\n";
    
    return $return;
  }
  
  public function sso_general(){
    include(constant('SAMLAUTH_ROOT') . '/lib/controllers/' . __FUNCTION__ . '.php');
  }
  
  public function sso_idp(){
    include(constant('SAMLAUTH_ROOT') . '/lib/controllers/' . __FUNCTION__ . '.php');
  }
  
  public function sso_sp(){
    include(constant('SAMLAUTH_ROOT') . '/lib/controllers/' . __FUNCTION__ . '.php');
  }
  
  public function sso_help(){
    include(constant('SAMLAUTH_ROOT') . '/lib/controllers/' . __FUNCTION__ . '.php');
  }
  
}

// End of file saml_admin.php