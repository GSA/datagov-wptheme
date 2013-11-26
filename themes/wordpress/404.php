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

    <div class="content">
        <?php while ( have_posts() ) : the_post(); ?>
        <div class="single-post">
            <?php //get_template_part( 'content', 'page' ); ?>
            <h2> Page Not Found</h2>
            <h3><?php _e( 'This is somewhat embarrassing, isn’t it?', 'wordpress' ); ?></h3>
            <p><?php _e( 'It looks like nothing was found at this location. Maybe try a search?', 'wordpress' ); ?></p>

        </div>
        <?php //comments_template( '', true ); ?>

        <?php endwhile; // end of the loop. ?>
        <?php get_template_part('footer'); ?>

    </div> <!-- content -->
