<?php
require_once ('../../../wp-load.php');
require_once ('../../../wp-blog-header.php');
if (current_user_can( 'manage_options' )) {
  get_ckan_metric_info();
}
?>done