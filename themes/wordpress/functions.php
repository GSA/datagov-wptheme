<?php

add_theme_support( 'post-formats', array( 'link', 'status', 'image', 'gallery' ) );

// add support for page categories
add_action( 'init', 'enable_category_taxonomy_for_pages', 500 );

function enable_category_taxonomy_for_pages() {
    register_taxonomy_for_object_type('category','page');
}

//template for category blog
function arphabet_widgets_init() {

    register_sidebar( array(
        'name' => 'Category-Blog',
        'id' => 'Category-Blog',
        'before_widget' => '<div>',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="rounded">',
        'after_title' => '</h2>',
    ) );
}
add_action( 'widgets_init', 'arphabet_widgets_init' );

//template for header
function arphabet2_widgets_init() {

    register_sidebar( array(
        'name' => 'header',
        'id' => 'header',
        'before_widget' => '<div>',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="rounded">',
        'after_title' => '</h2>',
    ) );
}
add_action( 'widgets_init', 'arphabet2_widgets_init' );
//Container for Categories
function arphabet3_widgets_init() {

    register_sidebar( array(
        'name' => 'Category Container',
        'id' => 'category_container',
        'before_widget' => '<div>',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="rounded">',
        'after_title' => '</h2>',
    ) );
}
add_action( 'widgets_init', 'arphabet3_widgets_init' );
// bottom boxes for categories
function arphabet4_widgets_init() {

    register_sidebar( array(
        'name' => 'Category bottom box 1',
        'id' => 'category_box_1',
        'before_widget' => '<div>',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="rounded">',
        'after_title' => '</h2>',
    ) );
}
add_action( 'widgets_init', 'arphabet4_widgets_init' );
function arphabet5_widgets_init() {

    register_sidebar( array(
        'name' => 'Category bottom box 2',
        'id' => 'category_box_2',
        'before_widget' => '<div>',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="rounded">',
        'after_title' => '</h2>',
    ) );
}
add_action( 'widgets_init', 'arphabet5_widgets_init' );
function arphabet6_widgets_init() {

    register_sidebar( array(
        'name' => 'Category bottom box 3',
        'id' => 'category_box_3',
        'before_widget' => '<div>',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="rounded">',
        'after_title' => '</h2>',
    ) );
}
add_action( 'widgets_init', 'arphabet6_widgets_init' );
function arphabet7_widgets_init() {

    register_sidebar( array(
        'name' => 'Category bottom box 4',
        'id' => 'category_box_4',
        'before_widget' => '<div>',
        'after_widget' => '</div>',
        'before_title' => '<h2 class="rounded">',
        'after_title' => '</h2>',
    ) );
}
add_action( 'widgets_init', 'arphabet7_widgets_init' );

add_action( 'wp_enqueue_scripts', 'datagov_enqueue_scripts' );
function datagov_enqueue_scripts() {
	// The filemtime calls are to prevent an old version being cached by the browser if the source file has changed.
	wp_enqueue_style('datagov-base',
          get_template_directory_uri() . '/stylesheets/base.css',
          array(), 
          filemtime(dirname( __FILE__ ) . '/stylesheets/base.css')
        );
	wp_enqueue_style('datagov-skeleton',
          get_template_directory_uri() . '/stylesheets/skeleton.css',
          array(),
          filemtime( dirname( __FILE__ ) . '/stylesheets/skeleton.css')
        );
	wp_enqueue_style('datagov-layout',
          get_template_directory_uri() . '/stylesheets/layout.css',
          array(),
          filemtime( dirname( __FILE__ ) . '/stylesheets/layout.css')
        );
	wp_enqueue_style('datagov-datagov',
          get_template_directory_uri() . '/stylesheets/datagov.css',
          array(),
          filemtime( dirname( __FILE__ ) . '/stylesheets/datagov.css')
        );
	wp_enqueue_style('datagov-joyride-2-1',
          get_template_directory_uri() . '/stylesheets/joyride-2.1.css',
          array(),
          filemtime( dirname( __FILE__ ) . '/stylesheets/joyride-2.1.css')
        );
	wp_enqueue_style('datagov-style',
          get_template_directory_uri() . '/style.css',
          array(),
          filemtime( dirname( __FILE__ ) . '/style.css' )
        );
	wp_enqueue_style('datagov-googlefonts',
          '//fonts.googleapis.com/css?family=Abel|Lato:100,300,400,700'
        );
	wp_enqueue_style('datagov-fancybox-css',
          get_template_directory_uri() . '/assets/fancyBox/source/jquery.fancybox.css',
          array(), 
          filemtime( dirname( __FILE__ ) . '/assets/fancyBox/source/jquery.fancybox.css') 
        );
	wp_enqueue_style('datagov-fancybox-buttons-css',
          get_template_directory_uri() . '/assets/fancyBox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5',
          array(), 
          '/assets/fancyBox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5' 
        );
	wp_enqueue_style('datagov-fancybox-thumbs-css',
          get_template_directory_uri() . '/assets/fancyBox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7',
          array(), 
          '/assets/fancyBox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7' 
        );


	wp_enqueue_script('jquery-masonry' );
	// This next call should really be removed, and replaced with the jQuery UI components actually needed, as registered in core.
        
	wp_enqueue_script('google-jquery',
          '//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js',
          array(),
          '1.8.2'
        );
	wp_enqueue_script('google-jquery-ui',
          '//code.jquery.com/ui/1.10.3/jquery-ui.js',
          array( 'jquery' ),
          '1.10.3'
        );
	wp_enqueue_script('datagov-d3',
          '//d3js.org/d3.v3.min.js',
          array(),
          'v3'
        );
	wp_enqueue_script('datagov-lazyload',
          get_template_directory_uri() . '/js/jquery.lazyload.min.js',
          array( 'jquery' ),
          filemtime( dirname( __FILE__ ) . '/js/jquery.lazyload.min.js' )
        );
	wp_enqueue_script('datagov-text-fadeto',
          get_template_directory_uri() . '/js/jquery.text.fadeto.js',
          array( 'jquery' ), filemtime( dirname( __FILE__ ) . '/js/jquery.text.fadeto.js'  )
        );
	wp_enqueue_script('datagov-autocomplete',
          get_template_directory_uri() . '/js/autocomplete.js',
          array(),
          filemtime( dirname( __FILE__ ) . '/js/autocomplete.js')
        );
	wp_enqueue_script('datagov-joyride',
          get_template_directory_uri() . '/js/jquery.joyride-2.1.js',
          array( 'jquery' ), filemtime( dirname( __FILE__ ) . '/js/jquery.joyride-2.1.js'  ),
          true
        );
	wp_enqueue_script('datagov-cookie',get_template_directory_uri() . '/js/jquery.cookie.js',
          array( 'jquery' ),
          filemtime( dirname( __FILE__ ) . '/js/jquery.cookie.js'),
          true
        );
	wp_enqueue_script('datagov-modernizr',
          get_template_directory_uri() . '/js/modernizr.mq.js',
          array(),
          filemtime( dirname( __FILE__ ) . '/js/modernizr.mq.js'),
          true
        );
	wp_enqueue_script('datagov-js',
          get_template_directory_uri() . '/js/datagov.js',
          array( 'jquery' ),
          filemtime( dirname( __FILE__ ) . '/js/datagov.js'),
          true
        );
	wp_enqueue_script('datagov-v1',
          get_template_directory_uri() . '/js/v1.js',
          array(),
          filemtime( dirname( __FILE__ ) . '/js/v1.js'),
          true
        );
	wp_enqueue_script('datagov-autosize',
          get_template_directory_uri() . '/js/autosize.js',
          array(),
          filemtime( dirname( __FILE__ ) . '/js/autosize.js'),
          true
        );
	wp_enqueue_script('datagov-fancybox-js',
          get_template_directory_uri() . '/assets/fancyBox/source/jquery.fancybox.js?v=2.1.5',
          array(),
          '/assets/fancyBox/source/jquery.fancybox.js?v=2.1.5', 
          true
        );
	wp_enqueue_script( 'datagov-fancybox-buttons-js',
          get_template_directory_uri() . '/assets/fancyBox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5',
          array(),
          '/assets/fancyBox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5', 
          true
        );
	wp_enqueue_script( 'datagov-fancybox-thumbs-js',
          get_template_directory_uri() . '/assets/fancyBox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7',
          array(),
          '/assets/fancyBox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7', 
          true
        );
	wp_enqueue_script( 'datagov-fancybox-media-js',
          get_template_directory_uri() . '/assets/fancyBox/source/helpers/jquery.fancybox-media.js?v=1.0.6',
          array(),
          '/assets/fancyBox/source/helpers/jquery.fancybox-media.js?v=1.0.6', 
          true
        );
}
