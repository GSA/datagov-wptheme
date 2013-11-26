<?php get_header(); ?>
<body>

<div class="banner disclaimer">
    <p>This is a demonstration site exploring the future of Data.gov. <span id="stop-disclaimer"> Give us your feedback on <a href="https://twitter.com/usdatagov">Twitter</a>, <a href="http://quora.com">Quora</a></span>, <a href="https://github.com/GSA/datagov-design/">Github</a>, or <a href="http://www.data.gov/contact-us">contact us</a></p>
</div>


<!-- Header Background Color, Image, or Visualization
================================================== -->

<div class="menu-container">
    <div class="header-next-top" >


        <?php get_template_part('navigation'); ?>



    </div>
</div>

<div class="next-header">

    <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('header') ) : ?><?php endif; ?>
    <div id="data-viz"></div>


</div>

<!-- Navigation & Search
================================================== -->

<div class="container">
    <div class="next-top">


        <?php get_template_part('primary-search'); ?>

    </div> <!-- top -->


    <!-- WordPress Content
    ================================================== -->

    <div id="content" class="site-content" role="main">

        <header class="page-header">
            <h1 class="page-title"><?php _e( 'Not Found', 'wordpress' ); ?></h1>
        </header>

        <div class="page-wrapper">
            <div class="page-content">
                <h2><?php _e( 'This is somewhat embarrassing, isn’t it?', 'wordpress' ); ?></h2>
                <p><?php _e( 'It looks like nothing was found at this location.', 'wordpress' ); ?></p>


            </div><!-- .page-content -->
        </div><!-- .page-wrapper -->

    </div><!-- #content -->