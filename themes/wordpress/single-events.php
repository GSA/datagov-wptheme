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
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>

<body class="single <?php if (!empty($category)){echo $cat_slug;}else{echo "uncategorized";} ?>">


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

        <div class="sixteen columns">

            <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>

				    <div class="single-post">
		
						<div class="title"><?php the_title(); ?></div>
                <div class="body">
                    <?php echo "<strong>Group:</strong>&nbsp;";
                    ?>
                    <?php
                    $values = get_field('group');
                    $taxanomy='category';
                    if($values)
                    {
                        echo '<ul>';
                        foreach($values as $value)
                        {

                            $term = get_term( $value, $taxanomy );
                            $name = $term->name;
                            echo '<li>' . $name . '</li>';
                        }

                        echo '</ul>';
                    }

                    // always good to see exactly what you are working with
                    //var_dump($values);

                    ?>
                    <?php echo "<strong>Start Date and Time:</strong>&nbsp; ".date("l jS \of F Y h:i A",get_post_meta($post->ID, 'start_date_and_time',TRUE)); ?> <br />
                    <?php echo "<strong>End Date and Time:</strong>&nbsp; ";
                    if(get_post_meta($post->ID, 'end_date_and_time',TRUE)) {
                        echo date("l jS \of F Y h:i A",get_post_meta($post->ID, 'end_date_and_time',TRUE));
                    }
                    else{
                        echo "-" ;
                    }
                    ?><br />
                    <?php echo "<strong>Location:</strong>&nbsp; ".get_post_meta($post->ID, 'location', TRUE ); ?> <br />
                    <?php echo "<strong>Description: ".get_post_meta($post->ID, 'body', TRUE ); ?> <br />
                    <br />
                    <?php echo "<strong>Time Zone:</strong>&nbsp; " .strtoupper(get_post_meta($post->ID, 'time_zone',TRUE));?> <br />





                </div>

                <?php endwhile; ?>

            <?php else : ?>

            <h2 class="center">Not Found</h2>
            <p class="center">Sorry, but you are looking for something that isn't here.</p>
            <?php include (TEMPLATEPATH . "/searchform.php"); ?>

            <?php endif; ?>



        </div>

        </div> <!-- sixteen columns -->
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

