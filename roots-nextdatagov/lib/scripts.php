<?php
/**
 * Enqueue scripts and stylesheets
 *
 * Enqueue stylesheets in the following order:
 * 1. /theme/assets/css/main.min.css
 *
 * Enqueue scripts in the following order:
 * 1. jquery-1.11.3.min.js via Google CDN
 * 2. /theme/assets/js/vendor/modernizr-2.7.1.min.js
 * 3. /theme/assets/js/main.min.js (in footer)
 */
function roots_scripts() {
	wp_enqueue_style('datagov-googlefonts', '//fonts.googleapis.com/css?family=Abel|Lato:100,300,400,700' );
	//wp_enqueue_style('fontawesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css');

	wp_enqueue_style('roots_main', get_template_directory_uri() . '/assets/css/main.min.css', false, 'd4d209028267e6bfd84bc8a7aeb90f98');
	wp_enqueue_style('rei_css', get_template_directory_uri() . '/assets/css/rei.css', false, '' );


	// jQuery is loaded using the same method from HTML5 Boilerplate:
	// Grab Google CDN's latest jQuery with a protocol relative URL; fallback to local if offline
	// It's kept in the header instead of footer to avoid conflicts with plugins.
	if ( ! is_admin() && current_theme_supports( 'jquery-cdn' ) ) {
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js', false, null, false );
		add_filter( 'script_loader_src', 'roots_jquery_local_fallback', 10, 1 );
	}

	if ( is_single() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script('comment-reply' );
	}

	wp_register_script('respond', get_template_directory_uri() . '/assets/js/vendor/respond.min.js', false, null, false );
	wp_register_script('modernizr', get_template_directory_uri() . '/assets/js/vendor/modernizr-2.7.1.min.js', false, null, false );
	wp_register_script('sticky', get_template_directory_uri() . '/assets/js/vendor/jquery.sticky.js', false, null, false );
	wp_register_script('roots_scripts', get_template_directory_uri() . '/assets/js/scripts.min.js', false, '9ab03eaa29bcfa40a4e8e34e7afda278', true );
    wp_register_script( 'datatables', 'https://cdn.datatables.net/1.10.8/js/jquery.dataTables.min.js', false, null, false );
    wp_register_script( 'fixedcolumns', 'https://cdn.datatables.net/fixedcolumns/3.1.0/js/dataTables.fixedColumns.min.js', false, null, false );
    wp_register_script( 'scroller', 'https://cdn.datatables.net/scroller/1.3.0/js/dataTables.scroller.min.js', false, null, false );



    wp_enqueue_script('respond' );
	wp_enqueue_script('modernizr' );
	wp_enqueue_script('jquery' );
    wp_enqueue_script('datatables' );
    wp_enqueue_script('fixedcolumns' );
    wp_enqueue_script('scroller' );
	// wp_enqueue_script( 'sticky' );
	wp_enqueue_script('roots_scripts' );
	wp_enqueue_script('ext_link_handler', get_template_directory_uri() . '/assets/js/ext-link-handler.js', array(
			'jquery',
			'wpp-frontend'
		), '' );
//    wp_enqueue_script('zebra_tooltips', get_template_directory_uri() . '/assets/js/zebra_tooltips.js', array(
//        'jquery',
//        'wpp-frontend'
//    ), '' );
	wp_enqueue_script('Federated-Analytics', 'https://dap.digitalgov.gov/Universal-Federated-Analytics-Min.js?agency=GSA', false, null );
//	wp_enqueue_script('cycle_all', get_template_directory_uri() . '/assets/js/jquery.cycle.all.js', array( 'jquery' ), '' );
}

add_action( 'wp_enqueue_scripts', 'roots_scripts', 100 );

// http://wordpress.stackexchange.com/a/12450
function roots_jquery_local_fallback( $src, $handle = null ) {
	static $add_jquery_fallback = false;

	if ( $add_jquery_fallback ) {
		echo '<script>window.jQuery || document.write(\'<script src="' . get_template_directory_uri() . '/assets/js/vendor/jquery-1.12.0.min.js"><\/script>\')</script>' . "\n";
		$add_jquery_fallback = false;
	}

	if ( $handle === 'jquery' ) {
		$add_jquery_fallback = true;
	}

	return $src;
}

add_action( 'wp_head', 'roots_jquery_local_fallback' );

function roots_google_analytics() {
	?>
	<script>
		(function (b, o, i, l, e, r) {
			b.GoogleAnalyticsObject = l;
			b[l] || (b[l] =
				function () {
					(b[l].q = b[l].q || []).push(arguments)
				});
			b[l].l = +new Date;
			e = o.createElement(i);
			r = o.getElementsByTagName(i)[0];
			e.src = '//www.google-analytics.com/analytics.js';
			r.parentNode.insertBefore(e, r)
		}(window, document, 'script', 'ga'));
		ga('create', '<?php echo GOOGLE_ANALYTICS_ID; ?>');
		ga('send', 'pageview');
	</script>

<?php
}

if ( GOOGLE_ANALYTICS_ID && ! current_user_can( 'manage_options' ) ) {
	add_action( 'wp_footer', 'roots_google_analytics', 20 );
}
