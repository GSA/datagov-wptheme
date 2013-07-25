<?php

add_theme_support( 'post-formats', array( 'link', 'status', 'image' ) );

add_action( 'wp_enqueue_scripts', 'datagov_enqueue_scripts' );
function datagov_enqueue_scripts() {
	// The filemtime calls are to prevent an old version being cached by the browser if the source file has changed.
	wp_enqueue_style( 'datagov-base',        get_template_directory_uri() . '/stylesheets/base.css',        array(), filemtime( dirname( __FILE__ ) . '/stylesheets/base.css'        ) );
	wp_enqueue_style( 'datagov-skeleton',    get_template_directory_uri() . '/stylesheets/skeleton.css',    array(), filemtime( dirname( __FILE__ ) . '/stylesheets/skeleton.css'    ) );
	wp_enqueue_style( 'datagov-layout',      get_template_directory_uri() . '/stylesheets/layout.css',      array(), filemtime( dirname( __FILE__ ) . '/stylesheets/layout.css'      ) );
	wp_enqueue_style( 'datagov-datagov',     get_template_directory_uri() . '/stylesheets/datagov.css',     array(), filemtime( dirname( __FILE__ ) . '/stylesheets/datagov.css'     ) );
	wp_enqueue_style( 'datagov-joyride-2-1', get_template_directory_uri() . '/stylesheets/joyride-2.1.css', array(), filemtime( dirname( __FILE__ ) . '/stylesheets/joyride-2.1.css' ) );
	wp_enqueue_style( 'datagov-googlefonts', '//fonts.googleapis.com/css?family=Abel|Lato:100,300,400,700' );

	wp_enqueue_script( 'jquery-masonry' );
	// This next call should really be removed, and replaced with the jQuery UI components actually needed, as registered in core.
	wp_enqueue_script( 'google-jquery-ui',     '//code.jquery.com/ui/1.10.3/jquery-ui.min.js',              array( 'jquery' ), '1.10.3' );
	wp_enqueue_script( 'datagov-d3',           'http://d3js.org/d3.v3.min.js',                              array(),           'v3' );
	wp_enqueue_script( 'datagov-lazyload',     get_template_directory_uri() . '/js/jquery.lazyload.min.js', array( 'jquery' ), filemtime( dirname( __FILE__ ) . '/js/jquery.lazyload.min.js' ) );
	wp_enqueue_script( 'datagov-text-fadeto',  get_template_directory_uri() . '/js/jquery.text.fadeto.js',  array( 'jquery' ), filemtime( dirname( __FILE__ ) . '/js/jquery.text.fadeto.js'  ) );
	wp_enqueue_script( 'datagov-autocomplete', get_template_directory_uri() . '/js/autocomplete.js',        array(),           filemtime( dirname( __FILE__ ) . '/js/autocomplete.js'        ) );
	wp_enqueue_script( 'datagov-joyride',      get_template_directory_uri() . '/js/jquery.joyride-2.1.js',  array( 'jquery' ), filemtime( dirname( __FILE__ ) . '/js/jquery.joyride-2.1.js'  ), true );
	wp_enqueue_script( 'datagov-cookie',       get_template_directory_uri() . '/js/jquery.cookie.js',       array( 'jquery' ), filemtime( dirname( __FILE__ ) . '/js/jquery.cookie.js'       ), true );
	wp_enqueue_script( 'datagov-modernizr',    get_template_directory_uri() . '/js/modernizr.mq.js',        array(),           filemtime( dirname( __FILE__ ) . '/js/modernizr.mq.js'        ), true );
	wp_enqueue_script( 'datagov-js',           get_template_directory_uri() . '/js/datagov.js',             array( 'jquery' ), filemtime( dirname( __FILE__ ) . '/js/datagov.js'             ), true );
	wp_enqueue_script( 'datagov-v1',           get_template_directory_uri() . '/js/v1.js',                  array(),           filemtime( dirname( __FILE__ ) . '/js/v1.js'                  ), true );
	wp_enqueue_script( 'datagov-autosize',     get_template_directory_uri() . '/js/autosize.js',            array(),           filemtime( dirname( __FILE__ ) . '/js/autosize.js'            ), true );
}
