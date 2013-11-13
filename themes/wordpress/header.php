<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php wp_title( '|', true, 'right' ); ?></title>
<?php /* Put these back in when  the ?>
	<meta name="description" content="">
	<meta name="author" content="">
<?php /**/ ?>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', 'UA-42145528-1', 'data.gov');
	  ga('send', 'pageview');
	</script>

	<!-- Favicons
	================================================== -->
	<link rel="shortcut icon" href="<?php echo get_bloginfo('template_directory'); ?>/images/favicon.ico">

	<?php wp_head(); ?>

        <script type="text/javascript">
            $(document).ready(function() {
                /*
                 *  Simple image gallery. Uses default settings
                 */

                $('.colorbox-inline').fancybox({autoWidth:'True'});
            });
        </script>
</head>
<!--<body <?php body_class(); ?>> -->

