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
function cptui_register_my_cpt_challenge()
{
    register_post_type('challenge', array(
        'label'           => 'Challenges',
        'description'     => '',
        'public'          => true,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'hierarchical'    => false,
        'rewrite'         => array('slug' => 'challenge', 'with_front' => true),
        'query_var'       => true,
        'supports'        => array('title', 'editor', 'comments', 'author'),
        'taxonomies'      => array('category'),
        'labels'          => array(
            'name'               => 'Challenges',
            'singular_name'      => 'Challenge',
            'menu_name'          => 'Challenges',
            'add_new'            => 'Add Challenge',
            'add_new_item'       => 'Add New Challenge',
            'edit'               => 'Edit',
            'edit_item'          => 'Edit Challenge',
            'new_item'           => 'New Challenge',
            'view'               => 'View Challenge',
            'view_item'          => 'View Challenge',
            'search_items'       => 'Search Challenges',
            'not_found'          => 'No Challenges Found',
            'not_found_in_trash' => 'No Challenges Found in Trash',
            'parent'             => 'Parent Challenge',
        )
    ));
}

#Applications
add_action('init', 'cptui_register_my_cpt_applications');
function cptui_register_my_cpt_applications()
{
    register_post_type('applications', array(
        'label'           => 'Applications',
        'description'     => 'Add a developer application related to Data.gov or specific community.',
        'public'          => true,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'hierarchical'    => false,
        'rewrite'         => array('slug' => 'applications', 'with_front' => true),
        'query_var'       => true,
        'supports'        => array('title', 'editor', 'comments', 'revisions', 'author'),
        'taxonomies'      => array('category', 'application categories', 'application types'),
        'labels'          => array(
            'name'               => 'Applications',
            'singular_name'      => 'Application',
            'menu_name'          => 'Applications',
            'add_new'            => 'Add Application',
            'add_new_item'       => 'Add New Application',
            'edit'               => 'Edit',
            'edit_item'          => 'Edit Application',
            'new_item'           => 'New Application',
            'view'               => 'View Application',
            'view_item'          => 'View Application',
            'search_items'       => 'Search Applications',
            'not_found'          => 'No Applications Found',
            'not_found_in_trash' => 'No Applications Found in Trash',
            'parent'             => 'Parent Application',
        )
    ));
}

#Events
add_action('init', 'cptui_register_my_cpt_events');
function cptui_register_my_cpt_events()
{
    register_post_type('events', array(
        'label'           => 'Events',
        'description'     => '',
        'public'          => true,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'hierarchical'    => false,
        'rewrite'         => array('slug' => 'events', 'with_front' => true),
        'query_var'       => true,
        'supports'        => array('title', 'editor', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'thumbnail', 'author', 'page-attributes', 'post-formats'),
        'taxonomies'      => array('category'),
        'labels'          => array(
            'name'               => 'Events',
            'singular_name'      => 'Event',
            'menu_name'          => 'Events',
            'add_new'            => 'Add Event',
            'add_new_item'       => 'Add New Event',
            'edit'               => 'Edit',
            'edit_item'          => 'Edit Event',
            'new_item'           => 'New Event',
            'view'               => 'View Event',
            'view_item'          => 'View Event',
            'search_items'       => 'Search Events',
            'not_found'          => 'No Events Found',
            'not_found_in_trash' => 'No Events Found in Trash',
            'parent'             => 'Parent Event',
        )
    ));
}

#ArcGis Maps
add_action('init', 'cptui_register_my_cpt_arcgis_maps');
function cptui_register_my_cpt_arcgis_maps()
{
    register_post_type('arcgis_maps', array(
        'label'           => 'ArcGiS Maps',
        'description'     => '',
        'public'          => true,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'hierarchical'    => false,
        'rewrite'         => array('slug' => 'arcgis_maps', 'with_front' => true),
        'query_var'       => true,
        'supports'        => array('title', 'editor', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'thumbnail', 'author', 'page-attributes', 'post-formats'),
        'taxonomies'      => array('category'),
        'labels'          => array(
            'name'               => 'ArcGiS Maps',
            'singular_name'      => 'ArcGiS Map',
            'menu_name'          => 'ArcGiS Maps',
            'add_new'            => 'Add ArcGiS Map',
            'add_new_item'       => 'Add New ArcGiS Map',
            'edit'               => 'Edit',
            'edit_item'          => 'Edit ArcGiS Map',
            'new_item'           => 'New ArcGiS Map',
            'view'               => 'View ArcGiS Map',
            'view_item'          => 'View ArcGiS Map',
            'search_items'       => 'Search ArcGiS Maps',
            'not_found'          => 'No ArcGiS Maps Found',
            'not_found_in_trash' => 'No ArcGiS Maps Found in Trash',
            'parent'             => 'Parent ArcGiS Map',
        )
    ));
}

#Regional Planning
add_action('init', 'cptui_register_my_cpt_regional_planning');
function cptui_register_my_cpt_regional_planning()
{
    register_post_type('regional_planning', array(
        'label'           => 'Regional Planning',
        'description'     => '',
        'public'          => true,
        'show_ui'         => true,
        'show_in_menu'    => true,
        'capability_type' => 'post',
        'map_meta_cap'    => true,
        'hierarchical'    => false,
        'rewrite'         => array('slug' => 'regional_planning', 'with_front' => true),
        'query_var'       => true,
        'supports'        => array('title', 'editor', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'thumbnail', 'author', 'page-attributes', 'post-formats'),
        'taxonomies'      => array('category'),
        'labels'          => array(
            'name'               => 'Regional Planning',
            'singular_name'      => 'Regional Planning',
            'menu_name'          => 'Regional Planning',
            'add_new'            => 'Add Regional Planning',
            'add_new_item'       => 'Add New Regional Planning',
            'edit'               => 'Edit',
            'edit_item'          => 'Edit Regional Planning',
            'new_item'           => 'New Regional Planning',
            'view'               => 'View Regional Planning',
            'view_item'          => 'View Regional Planning',
            'search_items'       => 'Search Regional Planning',
            'not_found'          => 'No Regional Planning Found',
            'not_found_in_trash' => 'No Regional Planning Found in Trash',
            'parent'             => 'Parent Regional Planning',
        )
    ));
}

//Custom Taxonomies

#Application Categories
add_action('init', 'cptui_register_my_taxes_application_categories');
function cptui_register_my_taxes_application_categories()
{
    register_taxonomy('application_categories', array(
            0 => 'applications',
        ),
        array('hierarchical'      => true,
              'label'             => 'Application Categories',
              'show_ui'           => true,
              'query_var'         => true,
              'show_admin_column' => false,
              'labels'            => array(
                  'search_items'               => 'Application Category',
                  'popular_items'              => '',
                  'all_items'                  => 'All Application Category',
                  'parent_item'                => 'Parent Application Category',
                  'parent_item_colon'          => 'Parent Application Category:',
                  'edit_item'                  => 'Edit Application Category',
                  'update_item'                => 'Update Application Category',
                  'add_new_item'               => 'Add New Application Category',
                  'new_item_name'              => 'New Application Category',
                  'separate_items_with_commas' => '',
                  'add_or_remove_items'        => '',
                  'choose_from_most_used'      => '',
              )
        ));
}

#Announcements and News
add_action('init', 'cptui_register_my_taxes_announcements_and_news');
function cptui_register_my_taxes_announcements_and_news()
{
    register_taxonomy('announcements_and_news', array(
            0 => 'post',
        ),
        array('hierarchical'      => true,
              'label'             => 'Announcements and News',
              'show_ui'           => true,
              'query_var'         => true,
              'show_admin_column' => false,
              'labels'            => array(
                  'search_items'               => 'Announcements and News',
                  'popular_items'              => '',
                  'all_items'                  => 'All Announcements and News',
                  'parent_item'                => 'Parent Announcements and News',
                  'parent_item_colon'          => 'Parent Announcements and News:',
                  'edit_item'                  => 'Edit Announcements and News',
                  'update_item'                => 'Update Announcements and News',
                  'add_new_item'               => 'Add New Announcements and News',
                  'new_item_name'              => 'New Announcements and News',
                  'separate_items_with_commas' => '',
                  'add_or_remove_items'        => '',
                  'choose_from_most_used'      => '',
              )
        ));
}

#Application Types
add_action('init', 'cptui_register_my_taxes_application_types');
function cptui_register_my_taxes_application_types()
{
    if (is_admin()) {
        $labelarray = array(
            'search_items'  => 'Application Type',
            'popular_items' => '',
            'all_items'     => 'All Application Type',
            'parent_item'   => 'Parent Application Type',
            'parent_item_colon' => 'Parent Application Type:',
            'edit_item'     => 'Edit Application Type',
            'update_item'   => 'Update Application Type',
            'add_new_item'  => 'Add New Application Type',
            'new_item_name' => 'New Application Type',
            'separate_items_with_commas' => '',
            'add_or_remove_items' => '',
            'choose_from_most_used' => '',
        );
    } else {
        $labelarray = array(
            'search_items' => 'Application Type',
            'popular_items' => '',
            'all_items'   => 'All Application Type',
            'parent_item' => 'Parent Application Type',
            'parent_item_colon' => 'Parent Application Type:'
        );
    }
    register_taxonomy('application_types', array(
            0 => 'applications',
        ),
        array('hierarchical'      => true,
              'label'             => 'Application Types',
              'show_ui'           => true,
              'query_var'         => true,
              'show_admin_column' => false,
              'labels'            => $labelarray
        ));
}

#Migrate legacy Tags
add_action('init', 'cptui_register_my_taxes_legacy_datacomm_tags');
function cptui_register_my_taxes_legacy_datacomm_tags()
{
    register_taxonomy('legacy_datacomm_tags', array(
            0 => 'post',
        ),
        array('hierarchical'      => true,
              'label'             => 'Migrate legacy Tags',
              'show_ui'           => true,
              'query_var'         => true,
              'show_admin_column' => false,
              'labels'            => array(
                  'search_items'               => 'Migrate legacy Tag',
                  'popular_items'              => '',
                  'all_items'                  => 'All Migrate legacy Tag',
                  'parent_item'                => 'Parent Migrate legacy Tag',
                  'parent_item_colon'          => 'Parent Migrate legacy Tag:',
                  'edit_item'                  => 'Edit Migrate legacy Tag',
                  'update_item'                => 'Update Migrate legacy Tag',
                  'add_new_item'               => 'Add New Migrate legacy Tag',
                  'new_item_name'              => 'New Migrate legacy Tag',
                  'separate_items_with_commas' => '',
                  'add_or_remove_items'        => '',
                  'choose_from_most_used'      => '',
              )
        ));
}

function add_export_link()
{
    add_links_page('Download Links', 'Download Links', 'read', '../wp-content/plugins/datagov-custom/wp_download_links.php', 'pccf_render_form');
}

/* Adds Subscribe2 support for custom post types */
function my_post_types($types)
{
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

/**
 * Set "Edge-Control: no-store" header for post pages so that AKAMAI doesn't cache them
 */
function add_edge_control_header()
{
    // get post id this way since none of the conditional tags are avaliable at this stage in the boostrap process
    $url         = explode('?', 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    $post_id     = url_to_postid($url[0]);
    $post_type   = get_post_type($post_id);
    $post_format = get_post_format($post_id);

    if ($post_type == 'post' && !$post_format) { // post type is 'post' and post format is not set ==> blog post
        header('Edge-Control: no-store');
    }
}

add_action('send_headers', 'add_edge_control_header');

function add_login_logout_link($items, $args)
{
    $loginoutlink = wp_loginout('index.php', false);
    $items .= '<li id="login">' . $loginoutlink . '</li>';

    return $items;
}

/**
 * Include misc js
 */
function datagov_custom_js()
{
    // load js only on admin pages
    if (is_admin()) {
        wp_register_script('datagov_custom_misc_js', plugins_url('/datagov-custom-misc.js', __FILE__), array('jquery'));
        wp_enqueue_script('datagov_custom_misc_js');
    }
}

add_action('admin_init', 'datagov_custom_js');

function datagov_custom_keep_my_links($text)
{
    $raw_excerpt = $text;
    if ('' == $text) {
        $text  = get_the_content('');
        $text  = strip_shortcodes($text);
        $text  = apply_filters('the_content', $text);
        $text  = str_replace(']]>', ']]>', $text);
        $text  = strip_tags($text, '<a>');
        $excerpt_length = apply_filters('excerpt_length', 55);
        $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
        $words = preg_split('/(<a.*?a>)|\n|\r|\t|\s/', $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        if (count($words) > $excerpt_length) {
            array_pop($words);
            $text = implode(' ', $words);
            $text = $text . $excerpt_more;
        } else {
            $text = implode(' ', $words);
        }
    }

    return apply_filters('new_wp_trim_excerpt', $text, $raw_excerpt);
}

include_once(dirname(dirname(__FILE__)) . '/suggested-datasets/suggested-datasets.php');