<?php
/**
 * Roots initial setup and constants
 */
function roots_setup() {
    // Make theme available for translation
    load_theme_textdomain('roots', get_template_directory() . '/lang');

    // Register wp_nav_menu() menus (http://codex.wordpress.org/Function_Reference/register_nav_menus)
    register_nav_menus(array(
        'primary_navigation' => __('Primary Navigation', 'roots'),
        'footer_navigation' => __('Footer Navigation', 'roots'),
        'footer2_navigation' => __('Footer2 Navigation', 'roots'),
        'social_navigation' => __('Social Links', 'roots'),
        'topics_navigation' => __('Topics Navigation', 'roots'),
    ));

    // Add post thumbnails (http://codex.wordpress.org/Post_Thumbnails)
    add_theme_support('post-thumbnails');
    // set_post_thumbnail_size(150, 150, false);
    // add_image_size('category-thumb', 300, 9999); // 300px wide (and unlimited height)

    // Add post formats (http://codex.wordpress.org/Post_Formats)
    // add_theme_support('post-formats', array('aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat'));

    add_theme_support( 'post-formats', array( 'link', 'status', 'image', 'gallery' ) );

    // Tell the TinyMCE editor to use a custom stylesheet
    add_editor_style('/assets/css/editor-style.css');
}
add_action('after_setup_theme', 'roots_setup');

// Set pagination
function my_post_queries( $query ) {
    // do not alter the query on wp-admin pages and only alter it if it's the main query
    if (!is_admin() && $query->is_main_query()){

        // alter the query for the home and category pages
        if(is_home()){
            $query->set('posts_per_page', 3);
        }

        if(is_category()){
            $query->set('posts_per_page', 5);
        }

    }
}
add_action( 'pre_get_posts', 'my_post_queries' );

// Backwards compatibility for older than PHP 5.3.0
if (!defined('__DIR__')) { define('__DIR__', dirname(__FILE__)); }
