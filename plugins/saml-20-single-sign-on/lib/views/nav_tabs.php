<?php
$tab = $_GET['page'];
$is_multisite = is_multisite();

?>
<link rel="stylesheet" href="<?php echo constant('SAMLAUTH_URL') . '/lib/css/sso.css';?>" />
<link href="//netdna.bootstrapcdn.com/font-awesome/3.1.1/css/font-awesome.css" rel="stylesheet">
<div class="wrap">
  <div id="icon-options-general" class="icon32"></div>
  <h2 class="nav-tab-wrapper">
    Single Sign-On&nbsp;
    <a href="?page=sso_general.php" class="nav-tab<?php if($tab == 'sso_general.php'){echo ' nav-tab-active';}?>">General <span class="badge badge-important" id="sso_errors"><?php if($status->num_errors != 0) echo $status->num_errors; ?></span></a>
    <a href="?page=sso_idp.php" class="nav-tab<?php if($tab == 'sso_idp.php'){echo ' nav-tab-active';}?>">Identity Provider</a>
    <a href="?page=sso_sp.php" class="nav-tab<?php if($tab == 'sso_sp.php'){echo ' nav-tab-active';}?>">Service Provider</a>
    <a href="?page=sso_help.php" class="nav-tab<?php if($tab == 'sso_help.php'){echo ' nav-tab-active';}?>">Help</a>
  </h2>
</div>