<?php /*
Template Name: Community-Landing-Page
*/
?>

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
<body >

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
    <div class="category-content">

        <div class="content">
            <div id="blog" class="blog" style="font-size:30px;margin-left:5px;">Events</div>
            <div class="sixteen columns">
                <div id="communities-left">

                    <?php
                    $args = array(
                        'orderby'          => '',
                        'order'            => 'DESC',
                        'post_type'        => 'events',
                        'posts_per_page' => 500,
                        'post_status'      => 'publish',
                        'suppress_filters' => true );

                    $query = null;
                    $query = new WP_Query($args);

                    if( $query->have_posts() ) {

                        while ($query->have_posts()) : $query->the_post(); ?>
                            <div id="view-event" class="All-cat-post">




                                <div class="category-wrapper">
                                    <div class="view-event" id="post-<?php the_ID(); ?>">
                                        <div class="core">
                                      <span class="views-field views-field-title">
                                      <a href="<?php the_permalink(); ?>"> <?php the_title(); ?></a></span>
                                            <?php
                                            echo '<strong>Start Date:</strong>&nbsp;';
                                            echo date("l jS \of F Y h:i A",get_post_meta($post->ID, 'start_date_and_time',TRUE));
                                            echo '<br>';
                                            echo '<strong>End Date:</strong>&nbsp;';
                                            if(get_post_meta($post->ID, 'end_date_and_time',TRUE))
                                                echo date("l jS \of F Y h:i A",get_post_meta($post->ID, 'end_date_and_time',TRUE));
                                            else
                                                echo "-";
                                            echo '<br>';
                                            echo '<strong>Time Zone:</strong>&nbsp;';
                                            echo strtoupper(get_post_meta($post->ID, 'time_zone',TRUE));

                                            ?>





                                            <div class="views-field-group-audience">
                                                <strong>Category:</strong>&nbsp;
                                                <?php
                                                $values = get_field('group');
                                                $taxanomy='category';
                                                if($values)
                                                {
                                                    foreach($values as $value)
                                                    {

                                                        $term = get_term( $value, $taxanomy );
                                                        $name = $term->name;
                                                        echo ' <a href= "/'.strtolower($name).'">' . $name . '  </a>';


                                                    }

                                                }

                                                // always good to see exactly what you are working with
                                                //var_dump($values);

                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <?php
                        endwhile;
                    }?>
                </div>

                <div id="communities-right">
                    <?php $post = get_post('127666')?>
                    <?php echo $post->post_content;?>

                </div>
                <?php get_template_part('footer'); ?>
            </div>
        </div>
    </div><!-- container -->
    <script src="<?php echo get_bloginfo('template_directory'); ?>/js/jquery.joyride-2.1.js"></script>
    <script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/js/jquery.cookie.js"></script>
    <script type="text/javascript" src="<?php echo get_bloginfo('template_directory'); ?>/js/modernizr.mq.js"></script>

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

            $("#joyRideTipContent").joyride({
                autoStart: true,
                modal: true,
                cookieMonster: true,
                cookieName: 'datagov',
                cookieDomain: 'next.data.gov'
            });
        });
    </script>


    <script>
        $(function () {
            var
                $demo = $('#rotate-stats'),
                strings = JSON.parse($demo.attr('data-strings')).targets,
                randomString;

            randomString = function () {
                return strings[Math.floor(Math.random() * strings.length)];
            };

            $demo.fadeTo(randomString());
            setInterval(function () {
                $demo.fadeTo(randomString());
            }, 15000);
        });
    </script>

    <script src="<?php echo get_bloginfo('template_directory'); ?>/js/v1.js"></script>
    <script src="<?php echo get_bloginfo('template_directory'); ?>/js/autosize.js"></script>

    <!-- End Document
    ================================================== -->
</body>


</html>