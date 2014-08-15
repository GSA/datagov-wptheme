<!DOCTYPE html>
<!--[if IE 8 ]>    <html class="no-js ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9 ]>    <html class="no-js ie ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 10 ]>    <html class="no-js ie ie10" <?php language_attributes(); ?>> <![endif]-->
<html class="no-js" <?php language_attributes(); ?>>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php wp_title('|', true, 'right'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <?php wp_head(); ?>

  <link rel="alternate" type="application/rss+xml" title="<?php echo get_bloginfo('name'); ?> Feed" href="<?php echo home_url(); ?>/feed/">
  <link href="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/favicon.ico" rel="shortcut icon" />
  <link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/favicon-120.png" sizes="120x120" />
</head>
