<?php
class SAML_Client
{
  private $saml;
  private $opt;
  private $secretsauce;
  
  function __construct()
  {
    $this->settings = new SAML_Settings();
    
    require_once(constant('SAMLAUTH_ROOT') . '/saml/lib/_autoload.php');
		if( $this->settings->get_enabled() )
		{
			$this->saml = new SimpleSAML_Auth_Simple((string)get_current_blog_id());
			
			add_action('wp_authenticate',array($this,'authenticate'));
	    add_action('wp_logout',array($this,'logout'));
		}
    
    // Hash to generate password for SAML users.
    // This is never actually used by the user, but we need to know what it is, and it needs to be consistent
    
    // WARNING: If the WP AUTH_KEY is changed, all SAML users will be unable to login! In cases where this is
    //   actually desired, such as an intrusion, you must delete SAML users or manually set their passwords.
    //   it's messy, so be careful!

    $this->secretsauce = constant('AUTH_KEY');
  }
  
  /**
   *  Authenticates the user using SAML
   *
   *  @return void
   */
  public function authenticate()
  {
    if( isset($_GET['loggedout']) && $_GET['loggedout'] == 'true' )
    {
      header('Location: ' . get_option('siteurl'));
      exit();
    }
    else
    {
      $this->saml->requireAuth( array('ReturnTo' => get_admin_url() ) );
      $attrs = $this->saml->getAttributes();
      if(array_key_exists($this->settings->get_attribute('username'), $attrs) )
      {
        $username = $attrs[$this->settings->get_attribute('username')][0];
        if(get_user_by('login',$username))
        {
          $this->simulate_signon($username);
        }
        else
        {
          $this->new_user($attrs);
        }
      }
      else
      {
        die('A username was not provided.');
      }  
    }
  }
  
  /**
   * Sends the user to the SAML Logout URL (using SLO if available) and then redirects to the site homepage
   *
   * @return void
   */
  public function logout()
  { 
    $this->saml->logout( get_option('siteurl') );
  }
  
  /**
   * Creates a new user in the WordPress database using attributes from the IdP
   * 
   * @param array $attrs The array of attributes created by SimpleSAMLPHP
   * @return void
   */
  private function new_user($attrs)
  {
    if( array_key_exists($this->settings->get_attribute('username'),$attrs) )
    {
      $login = (array_key_exists($this->settings->get_attribute('username'),$attrs)) ? $attrs[$this->settings->get_attribute('username')][0] : 'NULL';
      $email = (array_key_exists($this->settings->get_attribute('email'),$attrs)) ? $attrs[$this->settings->get_attribute('email')][0] : '';
      $first_name = (array_key_exists($this->settings->get_attribute('firstname'),$attrs)) ? $attrs[$this->settings->get_attribute('firstname')][0] : '';
      $last_name = (array_key_exists($this->settings->get_attribute('lastname'),$attrs)) ? $attrs[$this->settings->get_attribute('lastname')][0] : '';
      $display_name = $first_name . ' ' . $last_name;
    }
    else
    {
      die('A username was not provided.');
    }
    
    $role = $this->update_role();
    
    if( $role !== false )
    {
      $user_opts = array(
        'user_login' => $login ,
        'user_pass'  => $this->user_password($login,$this->secretsauce) ,
        'user_email' => $email ,
        'first_name' => $first_name ,
        'last_name'  => $last_name ,
        'display_name' => $display_name ,
        'role'       => $role
        );
      wp_insert_user($user_opts);
      $this->simulate_signon($login);
    }
    else
    {
      die('The website administrator has not given you permission to log in.');
    }
  }
  
  /**
   * Authenticates the user with WordPress using wp_signon()
   *
   * @param string $username The user to log in as.
   * @return void
   */
  private function simulate_signon($username)
  {
    remove_filter('wp_authenticate',array($this,'authenticate'));
    
    //todo: add a configuration option in admin settings that would toggle this functionality
    //for now, just commenting this line out since next.data.gov must retain the local wordpress roles 
    //currently, the plugin retrieves roles from IDM/IDP
    //$this->update_role();
    
    $login = array(
      'user_login' => $username,
      'user_password' => $this->user_password($username,$this->secretsauce),
      'remember' => false
    );
    
    $use_ssl = ( defined('FORCE_SSL_ADMIN') && constant('FORCE_SSL_ADMIN') === true ) ? true : '';
    $result = wp_signon($login,$use_ssl);
    if(is_wp_error($result))
    {
      echo $result->get_error_message();
      exit();
    }
    else
    {
      wp_redirect(get_admin_url());
      exit();
    }
  }
  
  /**
   * Updates a user's role if their current one doesn't match the attributes provided by the IdP
   *
   * @return string 
   */
  private function update_role()
  {
    $attrs = $this->saml->getAttributes();
    if(array_key_exists($this->settings->get_attribute('groups'), $attrs) )
    {
      if( in_array($this->settings->get_group('admin'),$attrs[$this->settings->get_attribute('groups')]) )
      {
        $role = 'administrator';
      }
      elseif( in_array($this->settings->get_group('editor'),$attrs[$this->settings->get_attribute('groups')]) )
      {
        $role = 'editor';
      }
      elseif( in_array($this->settings->get_group('author'),$attrs[$this->settings->get_attribute('groups')]) )
      {
        $role = 'author';
      }
      elseif( in_array($this->settings->get_group('contributor'),$attrs[$this->settings->get_attribute('groups')]) )
      {
        $role = 'contributor';
      }
      elseif( in_array($this->settings->get_group('subscriber'),$attrs[$this->settings->get_attribute('groups')]) )
      {
        $role = 'subscriber';
      }
      elseif( $this->settings->get_allow_unlisted_users() )
      {
        $role = 'subscriber';
      }
      else
      {
        $role = false;
      }
    }
    else
    {
      $role = false;
    }
    
    $user = get_user_by('login',$attrs[$this->settings->get_attribute('username')][0]);
    if($user)
    {
      $user->set_role($role);
    }
    
    return $role;
  }
  
  /**
   * Generates a SHA-256 HMAC hash using the username and secret key
   * 
   * @param string $value the user's username
   * @param string $key a secret key
   * @return string 
   */
  private function user_password($value,$key)
  {
    $hash = hash_hmac('sha256',$value,$key);
    return $hash;
  }
  
  public function show_password_fields($show_password_fields) {
    return false;
  }
  
  public function disable_function() {
    die('Disabled');
  }
  
} // End of Class SamlAuth
