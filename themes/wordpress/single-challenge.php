<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->


<?php get_template_part('header'); ?>

<body class="single">


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
<div class="next-header category <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">
</div>


<!-- Navigation & Search
================================================== -->

<div class="container">
    <div class="next-top category <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">


        <?php get_template_part('category-search'); ?>

    </div> <!-- top -->

</div>

<div class="page-nav">
</div>

<div class="container">


    <div class="sixteen columns page-nav-items">

        <?php
        $category = get_the_category(  );
        $cat_name=$category[0]->cat_name;


        $args = array(
            'category_name'=>$cat_name, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');

        wp_list_bookmarks($args); ?>

    </div>

    <!-- WordPress Content
    ================================================== -->

    <div class="content">

        <div class="sixteen columns">

            <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>

                <div class="single-post">

                    <div class="title"><?php the_title(); ?></div>
                    <div class="body">
                        <?php the_content(); ?>
                        <?php echo "<strong>Challenge URL:</strong>&nbsp;".get_post_meta($post->ID, 'field_challenge_url', TRUE ); ?>
                        <br />

                        <?php echo "<strong>Challenge Start Date:</strong>&nbsp;".get_post_meta($post->ID, 'field_challenge_start_date', TRUE ); ?>
                        <br />
                        <?php echo "<strong><Challange End Date:</strong>&nbsp;".get_post_meta($post->ID, 'field_challenge_end_date', TRUE ); ?>
                        <br />
                        <?php echo "<strong>Challenge Award:</strong>&nbsp;".get_post_meta($post->ID, 'field_challenge_award', TRUE ); ?>
                        <br />
                        <?php echo "<strong><Challenge Thumbnail:</strong>&nbsp; " ?>
                        <?php
                        $imagefile=get_field_object('field_5241b4eb20cea');
                        ?>

                        <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>">


                        <br />
                        <?php echo "<strong>Winner Announced:</strong>&nbsp;".get_post_meta($post->ID, 'field_winner_announced', TRUE ); ?>
                        <br />

                    </div>
                </div>

                <?php endwhile; ?>

            <?php else : ?>

            <h2 class="center">Not Found</h2>
            <p class="center">Sorry, but you are looking for something that isn't here.</p>
            <?php include (TEMPLATEPATH . "/searchform.php"); ?>

            <?php endif; ?>



        </div>

    </div> <!-- sixteen columns -->

</div> <!-- content -->

</div><!-- container -->

<script>
    $(window).load(function(){
        $('#posts').masonry({
            // options
            columnWidth: 287,
            itemSelector : '.post',
            isResizable: true,
            isAnimated: true,
            gutterWidth: 25
        });
    });
</script>

<script src="<?php echo get_bloginfo('template_directory'); ?>/js/autosize.js"></script>


<!-- End Document
================================================== -->
</body>


</html>