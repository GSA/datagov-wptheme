<?php
/*
Plugin Name: SAML 2.0 Single Sign-On
Version: 0.9.1
Plugin URI: http://keithbartholomew.com
Description: Authenticate users using <a href="http://rnd.feide.no/simplesamlphp">simpleSAMLphp</a>.
Author: Keith Bartholomew
Author URI: http://keithbartholomew.com
*/

$upload_dir = wp_upload_dir();
define('SAMLAUTH_CONF', $upload_dir['basedir'] . '/saml-20-single-sign-on/etc');
define('SAMLAUTH_CONF_URL', $upload_dir['baseurl'] . '/saml-20-single-sign-on/etc');
define('SAMLAUTH_ROOT',dirname(__FILE__));
define('SAMLAUTH_URL',plugins_url() . '/' . basename( dirname(__FILE__) ) );

// Things needed everywhere
require_once( constant('SAMLAUTH_ROOT') . '/lib/classes/saml_settings.php' );
require_once( constant('SAMLAUTH_ROOT') . '/lib/classes/saml_client.php' );

$SAML_Client = new SAML_Client();

// WordPress action hooks
add_action('lost_password', array($SAML_Client,'disable_function'));
add_action('retrieve_password', array($SAML_Client,'disable_function'));
add_action('password_reset', array($SAML_Client,'disable_function'));
add_filter('show_password_fields', array($SAML_Client,'show_password_fields'));


// Things needed only by the admin portal
if( is_admin() )
{
  require_once( constant('SAMLAUTH_ROOT') . '/lib/classes/saml_admin.php' );
  $SAML_Admin = new SAML_Admin();
}

// end of file 
