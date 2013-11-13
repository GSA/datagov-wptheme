<div class="wrap">
<h3>Status</h3>
<?php
  echo $status->html;
?>
<br/>
<div class="option-separator"></div>

<h3>Your SAML Info</h3>
<p>You will need to supply your identity provider with this information. If you want your users to be able to log in directly from WordPress (as opposed to logging in from a separate SSO portal), then you will also need to supply your IdP with the <strong>signing certificate</strong> used on the <a href="?page=sso_sp.php">Service Provider tab</a>.</p>

  <p>
    <strong>Your Entity ID:</strong><br/>
    <pre class="metadata-box">
    <?php echo $metadata['entityID'];?>
    </pre>
  </p>
  <p>
    <strong>Your Single Logout URL:</strong><br/>
    <pre class="metadata-box">
    <?php echo $metadata['Logout'];?>
    </pre>
  </p>
  <p>
    <strong>Your SAML Assertion Consumer URL:</strong><br/>
    <pre class="metadata-box">
    <?php echo $metadata['Consumer'];?>
    </pre>
  </p>
  <br/>
  <div class="option-separator"></div>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . basename(__FILE__); ?>&updated=true">
<?php wp_nonce_field('sso_general'); ?>
<table class="form-table">
	<tr valign="top">
    <th scope="row"><label for="enabled"><strong>Enable SAML authentication</strong></label></th> 
    <?php
			$checked = ($saml_opts['enabled']) ? ' checked="checked"' : '';
		?><td><input type="checkbox" name="enabled" id="enabled" value="enabled" <?php echo $checked;?> />
    </td>
    </tr>
    <tr>
    <td colspan="2">
      <p style="width: 480px;"><i class="blue icon-lightbulb icon-3x" style="float:left;margin-right: 0.25em;"></i> <strong>Tip:</strong> You can use a different browser (or a Google Chrome Incognito window) to test SAML authentication, while leaving this window open. If SAML logins don't work right away, you can use this window to troubleshoot.</p>
    </td>
  </tr>
  <tr>
    <td><input type="submit" name="submit" class="button button-primary" value="Update Options" /></td>
  </tr>
</table>
</form>
</div>