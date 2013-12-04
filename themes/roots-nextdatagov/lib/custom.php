<?php
/**
 * Custom functions
 */


 /**
  * Add custom taxonomies
  *
  * Additional custom taxonomies can be defined here
  * http://codex.wordpress.org/Function_Reference/register_taxonomy
  */
 function add_custom_taxonomies() {
 	// Add new "Locations" taxonomy to Posts
 	register_taxonomy('featured', 'post', array(
 		// Hierarchical taxonomy (like categories)
 		'hierarchical' => true,
 		// This array of options controls the labels displayed in the WordPress Admin UI
 		'labels' => array(
 			'name' => _x( 'Featured Content', 'taxonomy general name' ),
 			'singular_name' => _x( 'Feature Content Type', 'taxonomy singular name' ),
 			'search_items' =>  __( 'Search Featured Content' ),
 			'all_items' => __( 'All Featured Content' ),
 			'parent_item' => __( 'Parent Feature Content' ),
 			'parent_item_colon' => __( 'Parent Feature Content:' ),
 			'edit_item' => __( 'Edit Featured Content' ),
 			'update_item' => __( 'Update Featured Content' ),
 			'add_new_item' => __( 'Add New Featured Content Type' ),
 			'new_item_name' => __( 'New Featured Content Type' ),
 			'menu_name' => __( 'Featured Content' ),
 		),
 		// Control the slugs used for this taxonomy
 		'rewrite' => array(
 			'slug' => '', // This controls the base slug that will display before each term
 			'with_front' => false, // Don't display the category base before "/featured/"
 			'hierarchical' => true // This will allow URL's like "/featured/highlights/something"
 		),
 	));
 }
 add_action( 'init', 'add_custom_taxonomies', 0 );
