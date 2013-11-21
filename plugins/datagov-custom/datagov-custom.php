<?php
/*
Plugin Name: Datagov Custom
Description: This plugin holds custom types/taxnomies definitions, actions, filters etc.
Version: 1.0
*/

// Define current version constant
# define( 'DCPT_VERSION', '0.8.1' );

//Custom Post Types

#Challenges
add_action('admin_menu', 'add_export_link');
add_action('init', 'cptui_register_my_cpt_challenge');
function cptui_register_my_cpt_challenge() {
register_post_type('challenge', array(
'label' => 'Challenges',
'description' => '',
'public' => true,
'show_ui' => true,
'show_in_menu' => true,
'capability_type' => 'post',
'map_meta_cap' => true,
'hierarchical' => false,
'rewrite' => array('slug' => 'challenge', 'with_front' => true),
'query_var' => true,
'supports' => array('title','editor','comments','author'),
'taxonomies' => array('category'),
'labels' => array (
  'name' => 'Challenges',
  'singular_name' => 'Challenge',
  'menu_name' => 'Challenges',
  'add_new' => 'Add Challenge',
  'add_new_item' => 'Add New Challenge',
  'edit' => 'Edit',
  'edit_item' => 'Edit Challenge',
  'new_item' => 'New Challenge',
  'view' => 'View Challenge',
  'view_item' => 'View Challenge',
  'search_items' => 'Search Challenges',
  'not_found' => 'No Challenges Found',
  'not_found_in_trash' => 'No Challenges Found in Trash',
  'parent' => 'Parent Challenge',
)
) ); }

#Applications
add_action('init', 'cptui_register_my_cpt_applications');
function cptui_register_my_cpt_applications() {
register_post_type('applications', array(
'label' => 'Applications',
'description' => 'Add a developer application related to Data.gov or specific community.',
'public' => true,
'show_ui' => true,
'show_in_menu' => true,
'capability_type' => 'post',
'map_meta_cap' => true,
'hierarchical' => false,
'rewrite' => array('slug' => 'applications', 'with_front' => true),
'query_var' => true,
'supports' => array('title','editor','comments','revisions','author'),
'taxonomies' => array('category','application categories','application types'),
'labels' => array (
  'name' => 'Applications',
  'singular_name' => 'Application',
  'menu_name' => 'Applications',
  'add_new' => 'Add Application',
  'add_new_item' => 'Add New Application',
  'edit' => 'Edit',
  'edit_item' => 'Edit Application',
  'new_item' => 'New Application',
  'view' => 'View Application',
  'view_item' => 'View Application',
  'search_items' => 'Search Applications',
  'not_found' => 'No Applications Found',
  'not_found_in_trash' => 'No Applications Found in Trash',
  'parent' => 'Parent Application',
)
) ); }

#Events
add_action('init', 'cptui_register_my_cpt_events');
function cptui_register_my_cpt_events() {
register_post_type('events', array(
'label' => 'Events',
'description' => '',
'public' => true,
'show_ui' => true,
'show_in_menu' => true,
'capability_type' => 'post',
'map_meta_cap' => true,
'hierarchical' => false,
'rewrite' => array('slug' => 'events', 'with_front' => true),
'query_var' => true,
'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes','post-formats'),
'taxonomies' => array('category'),
'labels' => array (
  'name' => 'Events',
  'singular_name' => 'Event',
  'menu_name' => 'Events',
  'add_new' => 'Add Event',
  'add_new_item' => 'Add New Event',
  'edit' => 'Edit',
  'edit_item' => 'Edit Event',
  'new_item' => 'New Event',
  'view' => 'View Event',
  'view_item' => 'View Event',
  'search_items' => 'Search Events',
  'not_found' => 'No Events Found',
  'not_found_in_trash' => 'No Events Found in Trash',
  'parent' => 'Parent Event',
)
) ); }

#ArcGis Maps
add_action('init', 'cptui_register_my_cpt_arcgis_maps');
function cptui_register_my_cpt_arcgis_maps() {
register_post_type('arcgis_maps', array(
'label' => 'ArcGiS Maps',
'description' => '',
'public' => true,
'show_ui' => true,
'show_in_menu' => true,
'capability_type' => 'post',
'map_meta_cap' => true,
'hierarchical' => false,
'rewrite' => array('slug' => 'arcgis_maps', 'with_front' => true),
'query_var' => true,
'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes','post-formats'),
'taxonomies' => array('category'),
'labels' => array (
  'name' => 'ArcGiS Maps',
  'singular_name' => 'ArcGiS Map',
  'menu_name' => 'ArcGiS Maps',
  'add_new' => 'Add ArcGiS Map',
  'add_new_item' => 'Add New ArcGiS Map',
  'edit' => 'Edit',
  'edit_item' => 'Edit ArcGiS Map',
  'new_item' => 'New ArcGiS Map',
  'view' => 'View ArcGiS Map',
  'view_item' => 'View ArcGiS Map',
  'search_items' => 'Search ArcGiS Maps',
  'not_found' => 'No ArcGiS Maps Found',
  'not_found_in_trash' => 'No ArcGiS Maps Found in Trash',
  'parent' => 'Parent ArcGiS Map',
)
) ); }

#Regional Planning
add_action('init', 'cptui_register_my_cpt_regional_planning');
function cptui_register_my_cpt_regional_planning() {
register_post_type('regional_planning', array(
'label' => 'Regional Planning',
'description' => '',
'public' => true,
'show_ui' => true,
'show_in_menu' => true,
'capability_type' => 'post',
'map_meta_cap' => true,
'hierarchical' => false,
'rewrite' => array('slug' => 'regional_planning', 'with_front' => true),
'query_var' => true,
'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes','post-formats'),
'taxonomies' => array('category'),
'labels' => array (
  'name' => 'Regional Planning',
  'singular_name' => 'Regional Planning',
  'menu_name' => 'Regional Planning',
  'add_new' => 'Add Regional Planning',
  'add_new_item' => 'Add New Regional Planning',
  'edit' => 'Edit',
  'edit_item' => 'Edit Regional Planning',
  'new_item' => 'New Regional Planning',
  'view' => 'View Regional Planning',
  'view_item' => 'View Regional Planning',
  'search_items' => 'Search Regional Planning',
  'not_found' => 'No Regional Planning Found',
  'not_found_in_trash' => 'No Regional Planning Found in Trash',
  'parent' => 'Parent Regional Planning',
)
) ); }

//Custom Taxonomies

#Application Categories
add_action('init', 'cptui_register_my_taxes_application_categories');
function cptui_register_my_taxes_application_categories() {
register_taxonomy( 'application_categories',array (
  0 => 'applications',
),
array( 'hierarchical' => true,
	'label' => 'Application Categories',
	'show_ui' => true,
	'query_var' => true,
	'show_admin_column' => false,
	'labels' => array (
  'search_items' => 'Application Category',
  'popular_items' => '',
  'all_items' => '',
  'parent_item' => '',
  'parent_item_colon' => '',
  'edit_item' => '',
  'update_item' => '',
  'add_new_item' => '',
  'new_item_name' => '',
  'separate_items_with_commas' => '',
  'add_or_remove_items' => '',
  'choose_from_most_used' => '',
)
) ); 
}

#Announcements and News
add_action('init', 'cptui_register_my_taxes_announcements_and_news');
function cptui_register_my_taxes_announcements_and_news() {
register_taxonomy( 'announcements_and_news',array (
  0 => 'post',
),
array( 'hierarchical' => true,
	'label' => 'Announcements and News',
	'show_ui' => true,
	'query_var' => true,
	'show_admin_column' => false,
	'labels' => array (
  'search_items' => 'Announcements and News',
  'popular_items' => '',
  'all_items' => '',
  'parent_item' => '',
  'parent_item_colon' => '',
  'edit_item' => '',
  'update_item' => '',
  'add_new_item' => '',
  'new_item_name' => '',
  'separate_items_with_commas' => '',
  'add_or_remove_items' => '',
  'choose_from_most_used' => '',
)
) ); 
}

#Application Types
add_action('init', 'cptui_register_my_taxes_application_types');
function cptui_register_my_taxes_application_types() {
register_taxonomy( 'application_types',array (
  0 => 'applications',
),
array( 'hierarchical' => true,
	'label' => 'Application Types',
	'show_ui' => true,
	'query_var' => true,
	'show_admin_column' => false,
	'labels' => array (
  'search_items' => 'Application Type',
  'popular_items' => '',
  'all_items' => '',
  'parent_item' => '',
  'parent_item_colon' => '',
  'edit_item' => '',
  'update_item' => '',
  'add_new_item' => '',
  'new_item_name' => '',
  'separate_items_with_commas' => '',
  'add_or_remove_items' => '',
  'choose_from_most_used' => '',
)
) ); 
}

function add_export_link(){
    add_links_page('Download Links', 'Download Links', 8,'../wp-content/plugins/datagov-custom/wp_download_links.php', '');
}

/* Adds Subscribe2 support for custom post types */
function my_post_types($types) {
    $types = array('applications',
      'arcgis_maps',
      'attachment',
      'challenge',
      'events',
      'metric_organization',
      'page',
      'qa_faqs',
      'regional_planning'
    );
    return $types;
}
add_filter('s2_post_types', 'my_post_types');
