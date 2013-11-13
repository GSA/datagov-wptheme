<?php
  // Setup Default Options Array
  
  if (isset($_POST['submit']) && wp_verify_nonce($_POST['_wpnonce'],'sso_sp') ) 
  {    
      if( isset($_POST['auto_cert']) )
      {
        $pk = openssl_pkey_new(array('private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA));
    
        global $current_user;
        get_currentuserinfo();
        
        $dn = array(
        "countryName" => "US",
        "organizationName" => get_bloginfo('name'),
        "commonName" => get_bloginfo('name') . " SAML Signing Certificate",
        "emailAddress" => $current_user->user_email
        );
        
        $csr = openssl_csr_new($dn,$pk); 
        $crt = openssl_csr_sign($csr,null,$pk,1825);
        
        $keyfile = null; 
        $certfile = null;
        
        openssl_pkey_export($pk,$keyfile);
        openssl_x509_export($crt,$certfile);
        
        
        $upload_dir = constant('SAMLAUTH_CONF') . '/certs/' . get_current_blog_id();
        
        if(! is_dir($upload_dir))
        {
            mkdir($upload_dir, 0775);
        }
                
        $cert_uploaded = ( file_put_contents($upload_dir . '/' . get_current_blog_id() . '.cer', $certfile) ) ? true : false ;
        $key_uploaded = ( file_put_contents($upload_dir . '/' . get_current_blog_id() . '.key', $keyfile) ) ? true : false ;
        
      }
      elseif( ( isset($_FILES['certificate']) && isset($_FILES['privatekey']) ) && ( $_FILES['certificate']['error'] == 0 && $_FILES['privatekey']['error'] == 0 ) )
      {
          $cert = file_get_contents($_FILES['certificate']['tmp_name']);
          $key = file_get_contents($_FILES['privatekey']['tmp_name']);
          if(openssl_x509_check_private_key($cert,$key))
          {
              $upload_dir = constant('SAMLAUTH_CONF') . '/certs/' . get_current_blog_id();
              if(! is_dir($upload_dir))
              {
                  mkdir($upload_dir, 0775);
              }
              $cert_uploaded = ( file_put_contents($upload_dir . '/' . get_current_blog_id() . '.cer', $cert) ) ? true : false ;
              $key_uploaded = ( file_put_contents($upload_dir . '/' . get_current_blog_id() . '.key', $key) ) ? true : false ;
          }
          else
          {
              echo '<div class="error below-h2"><p>The certificate and private key you provided do not correspond to one another. They were not uploaded.</p></div>'."\n";
          }
      }

      // Update settings
      
      $this->settings->enable_cache();
            
      $this->settings->set_attribute('username',$_POST['username_attribute']);
      $this->settings->set_attribute('firstname',$_POST['firstname_attribute']);
      $this->settings->set_attribute('lastname',$_POST['lastname_attribute']);
      $this->settings->set_attribute('email',$_POST['email_attribute']);
      $this->settings->set_attribute('groups',$_POST['groups_attribute']);
      
      $this->settings->set_group('admin',$_POST['admin_group']);
      $this->settings->set_group('editor',$_POST['editor_group']);
      $this->settings->set_group('author',$_POST['author_group']);
      $this->settings->set_group('contributor',$_POST['contributor_group']);
      $this->settings->set_group('subscriber',$_POST['subscriber_group']);
      
      $this->settings->set_idp($_POST['idp']);
      $this->settings->set_nameidpolicy($_POST['nameidpolicy']);
      $this->settings->set_allow_unlisted_users( ($_POST['allow_unlisted_users'] == 'allow') ? true : false );
      
      $this->settings->disable_cache();
      
  }

  $status = $this->get_saml_status();

  include(constant('SAMLAUTH_ROOT') . '/lib/views/nav_tabs.php');
	include(constant('SAMLAUTH_ROOT') . '/lib/views/sso_sp.php');

?>