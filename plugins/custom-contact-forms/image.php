<?php
/*
	Custom Contact Forms Plugin
	By Taylor Lovett - http://www.taylorlovett.com
	Plugin URL: http://www.taylorlovett.com/wordpress-plugins
*/
error_reporting(0);
//header("Content-type: image/png");
require_once('custom-contact-forms-utils.php');
ccf_utils::load_module('images/custom-contact-forms-images.php');
$image = new CustomContactFormsImages();
$str = rand(10000, 99999);
if (!session_id())
	session_start();
$captcha_name = 'ccf_captcha_' . $_GET['fid'];
if (!$_SESSION[$captcha_name])
	$_SESSION[$captcha_name] = $str;
else
	$str = $_SESSION[$captcha_name];
$image->createImageWithText($str);
?>