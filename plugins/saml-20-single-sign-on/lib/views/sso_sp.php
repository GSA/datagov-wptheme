<div class="wrap">

<?php
    $idp = parse_ini_file( constant('SAMLAUTH_CONF') . '/config/saml20-idp-remote.ini',true);
    if($idp === FALSE)
    {
        echo '<div class="error below-h2"><p>No Identity Providers have been configured. You will not be able to configure WordPress for Single Sign-On until this is set up.</p></div>'."\n";
    }
?>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . basename(__FILE__); ?>&updated=true" enctype="multipart/form-data">
<?php wp_nonce_field('sso_sp'); ?>
<input type="hidden" name="MAX_FILE_SIZE" value="4194304" /> 
<fieldset class="options">


<h3>Authentication</h3>
<table class="form-table">
<?php 
/************************************************
* With only one IdP, this field is not needed.
************************************************
  <tr valign="top">
    <th scope="row"><label for="idp">Identity Provider</label></th> 
    <td>
    <select name="idp" id="idp">
      <?php foreach($idp as $key => $array) {
            $selected = ($key == $this->settings->get_idp()) ? ' selected="selected"' : '';
        echo '<option value="' . $key . '"' . $selected . '>' . $array['name'] . '</option>'."\n";
      } ?>
    </select>
    </td>
  </tr>
  <?php */ ?>
  <input type="hidden" name="idp" id="idp" value="<?php echo $this->settings->get_idp(); ?>" />
  <tr valign="top">
    <th scope="row"><label for="nameidpolicy">NameID Policy: </label></th> 
    <td>
        <select name="nameidpolicy">
      <?php
          $policies = array(
            'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
            'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
            'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent'
          );
          foreach($policies as $policy)
          {
            $selected = ( $this->settings->get_nameidpolicy() == $policy ) ? ' selected="selected"' : '';
            echo '<option value="' . $policy . '"' . $selected . '>' . $policy . '</option>'."\n";
          }
      ?>
      </select><br/>
      <span class="setting-description">Your site will require a NameID in this format, and fail otherwise. Default: emailAddress</span>
    </td>
  </tr>
  <tr>
    <th scope="row">&nbsp;</th>
    <td>
      <input type="checkbox" name="auto_cert" value="auto_cert" onclick="jQuery('.manual_cert').toggle('300');"/>&nbsp;&nbsp;Generate a new certificate and private key for me<br/>
    </td>
  </tr>
  <tr valign="top" class="manual_cert">
    <th scope="row"><label for="certificate">Signing Certificate</label></th> 
    <?php
            if(file_exists(constant('SAMLAUTH_CONF') . '/certs/' . get_current_blog_id() . '/' . get_current_blog_id() . '.cer') && file_exists(constant('SAMLAUTH_CONF') . '/certs/' . get_current_blog_id() . '/' . get_current_blog_id() . '.key'))
            {
	            $certificate = file_get_contents( constant('SAMLAUTH_CONF') . '/certs/' . get_current_blog_id() . '/' . get_current_blog_id() . '.cer' );
	            $certificate_cn = openssl_x509_parse($certificate);
	            $certificate_cn = $certificate_cn['subject']['CN'];
	            $privatekey = file_get_contents( constant('SAMLAUTH_CONF') . '/certs/' . get_current_blog_id() . '/' . get_current_blog_id() . '.key' );
	            $privatekey_match = openssl_x509_check_private_key($certificate,$privatekey);
            }
            else
            {
            	$certificate = false;
            	$privatekey = false;
            	$privatekey_match = false;
            }
        ?>
    <td><input type="file" name="certificate" id="certificate" /><?php if($certificate !== false ) {echo '&nbsp;<span class="green">Using certificate: <strong>' . $certificate_cn . '</strong>.</span> <a href="' . constant('SAMLAUTH_CONF_URL') . '/certs/' . get_current_blog_id() . '/' . get_current_blog_id() . '.cer' . '" target="_blank">[download]</a>';}?>
    <br/>
    <span class="setting-description">This doesn't have to be the certificate used to secure your website, it can just be self-signed.</span> 
    </td>
  </tr>
   <tr valign="top" class="manual_cert">
    <th scope="row"><label for="privatekey">Signing Private Key</label></th> 
    <td><input type="file" name="privatekey" id="privatekey" /><?php if($privatekey_match){echo '&nbsp;<span class="green">Your private key matches the certificate.</span>';}?>
    <br/>
    <span class="setting-description">The key is used to sign login requests. This is created when you create your certificate.</span> 
    </td>
  </tr> 
</table> 
<h3>Attributes</h3>
<table class="form-table">
  <tr valign="top">
    <th scope="row">
      <strong>Autofill with defaults for:</strong>
    </th>
    <td>
      <a href="#" onclick="idpDefaults('adfs'); return false;" style="margin-right:2em;">ADFS 2.0</a>
      <a href="#" onclick="idpDefaults('onelogin'); return false;" style="margin-right:2em;">OneLogin</a>
      <a href="#" onclick="idpDefaults('simplesamlphp'); return false;" >SimpleSAMLPHP w/ Active Directory</a>
    </td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="username_attribute">Attribute to be used as username</label></th> 
    <td><input type="text" name="username_attribute" id="username_attribute_inp" value="<?php echo $this->settings->get_attribute('username'); ?>" size="40" data-if-empty="error" />
    </td>
  </tr>

    <tr valign="top">
    <th scope="row"><label for="firstname_attribute">Attribute to be used as First Name</label></th> 
    <td><input type="text" name="firstname_attribute" id="firstname_attribute_inp" value="<?php echo $this->settings->get_attribute('firstname'); ?>" size="40" data-if-empty="warning" />
    </td>
  </tr>

    <tr valign="top">
    <th scope="row"><label for="lastname_attribute">Attribute to be used as Last Name</label></th> 
    <td><input type="text" name="lastname_attribute" id="lastname_attribute_inp" value="<?php echo $this->settings->get_attribute('lastname'); ?>" size="40" data-if-empty="warning" />
    </td>
  </tr>

    <tr valign="top">
    <th scope="row"><label for="email_attribute">Attribute to be used as E-mail</label></th> 
    <td><input type="text" name="email_attribute" id="email_attribute_inp" value="<?php echo $this->settings->get_attribute('email'); ?>" size="40" data-if-empty="warning" />
    </td>
  </tr>
  <tr valign="top">
    <th scope="row"><label for="groups_attribute">Attribute to be used as Groups</label></th> 
    <td><input type="text" name="groups_attribute" id="groups_attribute_inp" value="<?php echo $this->settings->get_attribute('groups'); ?>" size="40" data-if-empty="error" />
    </td>
  </tr>
  </table>
  <h3>Groups</h3>
  <p>You don't have to fill in all of these, but you should have at least one. Users will get their WordPress permissions based on the highest-ranking group they are members of.</p>
  <table class="form-table">
  <tr>
    <th><label for="admin_entitlement">Administrators Group Name</label></th>
    <td><input type="text" name="admin_group" id="admin_group" value="<?php echo $this->settings->get_group('admin'); ?>" size="40" data-if-empty="warning" /><br/>
    <span class="setting-description">Users in this group will be assigned the role of &ldquo;Administrator&rdquo;</span>
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="editor_group">Editors Group Name</label></th>
    <td><input type="text" name="editor_group" id="editor_group" value="<?php echo $this->settings->get_group('editor'); ?>" size="40" /><br/>
    <span class="setting-description">Users in this group will be assigned the role of &ldquo;Editor&rdquo;</span>
    </td>
  </tr>
  <tr>
    <th scope="row"><label for="editor_group">Authors Group Name</label></th>
    <td><input type="text" name="author_group" id="author_group" value="<?php echo $this->settings->get_group('author'); ?>" size="40" /><br/>
    <span class="setting-description">Users in this group will be assigned the role of &ldquo;Author&rdquo;</span>
    </td>
  </tr>
  <tr>
    <th><label for="editor_group">Contributors Group Name</label></th>
    <td><input type="text" name="contributor_group" id="contributor_group" value="<?php echo $this->settings->get_group('contributor'); ?>" size="40" /><br/>
    <span class="setting-description">Users in this group will be assigned the role of &ldquo;Contributor&rdquo;</span>
    </td>
  </tr>
  <tr>
    <th><label for="editor_group">Subscribers Group Name</label></th>
    <td><input type="text" name="subscriber_group" id="subscriber_group" value="<?php echo $this->settings->get_group('subscriber'); ?>" size="40" /><br/>
    <span class="setting-description">Users in this group will be assigned the role of &ldquo;Subscriber&rdquo;</span>
    </td>
  </tr>
  <tr>
    <th><label for="allow_unlisted_users">Allow Unlisted Users</label></th>
    <td><input type="checkbox" name="allow_unlisted_users" id="allow_unlisted_users" value="allow" <?php echo ($this->settings->get_allow_unlisted_users()) ? 'checked="checked"' : ''; ?> /><br/>
    <span class="setting-description">Users in this group will be assigned the role of &ldquo;Subscriber&rdquo;</span>
    </td>
  </tr>
</table>
</fieldset>
<div class="submit">
  <input type="submit" name="submit" class="button button-primary" value="Update Options" />
</div>
</form>
</div>
<script type="text/javascript">
  function idpDefaults(idp)
  {
    var $ = jQuery;
    
    if( typeof idp === 'undefined' )
    {
      return false;
    }
    else if(idp == 'adfs')
    {
      $('#username_attribute_inp').val('http://schemas.microsoft.com/ws/2008/06/identity/claims/windowsaccountname');
      $('#firstname_attribute_inp').val('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname');
      $('#lastname_attribute_inp').val('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname');
      $('#email_attribute_inp').val('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress');
      $('#groups_attribute_inp').val('http://schemas.xmlsoap.org/claims/Group');
      return true;
    }
    else if(idp == 'onelogin')
    {
      $('#username_attribute_inp').val('User.Username');
      $('#firstname_attribute_inp').val('User.FirstName');
      $('#lastname_attribute_inp').val('User.LastName');
      $('#email_attribute_inp').val('User.email');
      $('#groups_attribute_inp').val('memberOf');
      return true;
    }
    else if(idp == 'simplesamlphp')
    {
      $('#username_attribute_inp').val('sAMAccountName');
      $('#firstname_attribute_inp').val('givenName');
      $('#lastname_attribute_inp').val('sn');
      $('#email_attribute_inp').val('email');
      $('#groups_attribute_inp').val('memberOf');
      return true;
    }
    else
    {
      return false;
    }
  }
</script>

