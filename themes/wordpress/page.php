<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->


<?php get_template_part('header'); ?>
<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php $category = get_the_category();
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>
<body class="single page <?php if (!empty($category)){echo $cat_slug;}else{echo "uncategorized";} ?> post-<?php the_id(); ?>" >
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


        // show Links associated to a community
        // we need to build $args based either term_name or term_slug
        $args = array(
            'category_name'=> $term_slug, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
        wp_list_bookmarks($args);
        if (strcasecmp($term_name,$term_slug)!=0) {
            $args = array(
                'category_name'=> $term_name, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
            wp_list_bookmarks($args);
        }
        ?>

    </div>

    <!-- WordPress Content
    ================================================== -->

    <div class="content">
        <?php while ( have_posts() ) : the_post(); ?>
        <div class="single-post">
            <?php //get_template_part( 'content', 'page' ); ?>
            <div class="Apps-wrapper">
                <div id="appstitle" class="Appstitle" style="padding-left: 0px;"><div class="title"><?php the_title(); ?></div>
                </div></div>
            <?php the_content(); ?></div>

        </div>
        <?php //comments_template( '', true ); ?>

        <?php endwhile; // end of the loop. ?>
        <?php get_template_part('footer'); ?>

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
