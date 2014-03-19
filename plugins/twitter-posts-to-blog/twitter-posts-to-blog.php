<?php
/*
Plugin Name: Twitter posts to Blog
Description: Post twetts to your blog
Version: 1.11.20
Author: Damian Gomez
*/

require_once 'libs/twitteroauth/twitteroauth.php';
require_once 'functions.php';

//Variables
$dg_tw_queryes = array();
$dg_tw_time = array();
$dg_tw_ft = array();
$dg_tw_publish = '';
$tokens_error = false;

//Actions
add_action('wp_loaded',						'dg_tw_options');
add_action('dg_tw_event_start',				'dg_tw_load_next_items');
add_action('wp_ajax_dg_tw_event_start',		'dg_tw_load_next_items');
add_action('admin_menu',					'dg_add_menu_item');
add_action('loop_start',					'dg_tw_loop_start');
add_action('admin_notices',					'dg_tw_feedback' );
add_action('wp_ajax_dg_tw_manual_publish',	'dg_tw_manual_publish');

//Filters
add_filter('cron_schedules','dg_tw_schedule');


function myplugin_init() {
	load_plugin_textdomain('twitter-posts-to-blog', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action('plugins_loaded', 'myplugin_init');

//Hooks
register_activation_hook( __FILE__, 'dg_tw_activation' );
register_deactivation_hook( __FILE__, 'dg_tw_deactivation' );
?>