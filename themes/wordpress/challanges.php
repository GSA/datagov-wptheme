<?php /*
Template Name:Categories Challanges
*/
?>

<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">
<!--<![endif]-->
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
<body class="challenges <?php echo $cat_slug; ?>">
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
<div class="next-header category <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>"> </div>

<!-- Navigation & Search
================================================== -->

<div class="container">
    <div class="next-top category <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">
        <?php get_template_part('category-search'); ?>
    </div>
    <!-- top -->

</div>
<div class="page-nav"> </div>
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
<div id="blog" class="blog" style="font-size:30px;"></div>
<div class="sixteen columns">
<div class="content">
    <?php
    while( have_posts() ) {
        the_post();
        ?>
          <div class="Apps-wrapper">
          <div class="Apps-post" id="post-<?php the_ID(); ?>">
           <div id="appstitle" class="Appstitle" ><?php the_title();?></div>
        <?php the_content();   ?>
        <?php }?>
</div>
</div>
</div>
<div class="upcomingC">
    <h1>Upcoming Challenges</h1>
    <?php

    $args = array(
        'post_type' => 'challenge',
        'tax_query'=>	array(
            'relation' => 'AND',

            array(
                'taxonomy' => 'category',
                'terms' => $cat_slug,
                'field' => 'slug',
            ),
        )
    );

    $apps = query_posts($args);

    ?>
    <?php
    $count = 0;
    while( have_posts() ) {
        the_post();
        ?>
        <?php $curr_date=strtotime(date('Ymd', time()));
        $start_date=strtotime(get_post_meta($post->ID, 'field_challenge_start_date', TRUE ));
        $end_date=strtotime(get_post_meta($post->ID, 'field_challenge_end_date', TRUE ));
        //echo $end_date;
        ?>
        <?php  if ( $start_date > $curr_date) { ?>
            <div id="cat-posts" class="All-cat-post horizontal_dotted_line cat-post">
                <div class="core">
                    <div class="title"> <a href="<?php echo get_post_meta($post->ID, 'field_challenge_url', TRUE ); ?>">
                        <?php the_title() ?>
                    </a> <br/>
                    </div>
                    <div class="body">
                        <?php
                        $imagefile=get_field_object('field_5241b4eb20cea');
                        //var_dump($imagefile);
                        ?>
                        <?php
                        $image=  strlen($imagefile['value']['url']);
                        if ($image>0){ ?>
                            <img class="scale-with-grid" src="<?php echo  $imagefile['value']['url']; ?>" style="float:right; margin-left:10px; height:80px;">
                            <?php }else{?>
                            <img class="scale-with-grid" src="test">
                            <?php }?>
                        <?php the_content() ?>
                    </div>
                    <br clear="all" />
                </div>
            </div>
            <?php
            $count ++;} ?>
        <?php

    }
    if ($count < 1){?>
        <div id="cat-posts" class="no-cat-post">
            <div class="core">
                <div class="body">
            There are no Upcoming challenges in this category.
                </div>
            </div>
        </div>
        <?php }
    ?>
</div>

<div class="openC">
    <h1>Open Challenges</h1>
    <?php

    $args = array(
        'post_type' => 'challenge',
        'tax_query'=>	array(
            'relation' => 'AND',

            array(
                'taxonomy' => 'category',
                'terms' => $cat_slug,
                'field' => 'slug',
            ),
        )
    );

    $apps = query_posts($args);

    ?>
    <?php
    $count = 0;
    while( have_posts() ) {
        the_post();
        ?>
        <?php $curr_date=strtotime(date('Ymd', time()));
        $start_date=strtotime(get_post_meta($post->ID, 'field_challenge_start_date', TRUE ));
        $end_date=strtotime(get_post_meta($post->ID, 'field_challenge_end_date', TRUE ));
        //echo $end_date;
        ?>
        <?php  if ( $curr_date<= $end_date) { ?>
            <div id="cat-posts" class="All-cat-post horizontal_dotted_line cat-post">
                <div class="core">
                    <div class="title"> <a href="<?php echo get_post_meta($post->ID, 'field_challenge_url', TRUE ); ?>">
                        <?php the_title() ?>
                    </a> <br/>
                    </div>
                    <div class="body">
                        <?php
                        $imagefile=get_field_object('field_5241b4eb20cea');
                        //var_dump($imagefile);
                        ?>
                        <?php
                        $image=  strlen($imagefile['value']['url']);
                        if ($image>0){ ?>
                            <img class="scale-with-grid" src="<?php echo  $imagefile['value']['url']; ?>" style="float:right; margin-left:10px; height:80px;">
                            <?php }else{?>
                            <img class="scale-with-grid" src="test">
                            <?php }?>
                        <?php the_content() ?>
                    </div>
                    <br clear="all" />
                </div>
            </div>
            <?php
            $count ++;} ?>
        <?php

    }
    if ($count < 1){?>
         <div id="cat-posts" class="no-cat-post">
            <div class="core">
                <div class="body">
            There are no Current challenges in this category.
                    </div></div>
        </div>
        <?php }
    ?>
</div>
<div class="closedC">
    <h1>Just Closed – Stay Tuned for Winners</h1>
    <?php $category = get_the_category();
    $cat_name = $category[0]->cat_name;
    $cat_slug = $category[0]->slug;
    ?>
    <?php
    $args = array(
        'post_type' => 'challenge',
        'tax_query'=>	array(
            'relation' => 'AND',

            array(
                'taxonomy' => 'category',
                'terms' => $cat_slug,
                'field' => 'slug',
            ),
        )
    );

    $apps = query_posts($args);
    $count = 0;
    while( have_posts() ) {
        the_post();
        ?>
        <?php
        $curr_date=strtotime(date('Ymd', time()));
        $start_date=strtotime(get_post_meta($post->ID, 'field_challenge_start_date', TRUE ));
        $end_date=strtotime(get_post_meta($post->ID, 'field_challenge_end_date', TRUE ));
        //echo $end_date;
        $winner = get_field_object('field_5241b50e67153');
        ?>
        <?php  if ( ($curr_date> $end_date) &&  empty ($winner['value'])  ) { ?>
            <div id="cat-posts" class="All-cat-post horizontal_dotted_line cat-post">
                <div class="core">
                    <div class="title"> <a href="<?php echo get_post_meta($post->ID, 'field_challenge_url', TRUE ); ?>">
                        <?php the_title() ?>
                    </a><br/>
                    </div>
                    <div class="body">
                        <?php
                        $imagefile=get_field_object('field_5241b4eb20cea');
                        //var_dump($imagefile);
                        ?>
                        <?php
                        $image=  strlen($imagefile['value']['url']);
                        if ($image>0){ ?>
                            <img class="scale-with-grid" src="<?php echo  $imagefile['value']['url']; ?>" style="float:right; margin-left:10px; height:80px;">
                            <?php }else{?>
                            <img class="scale-with-grid" src="test">
                            <?php }  ?>
                        <?php the_content() ?>
                    </div>
                    <br clear="all" />
                </div>
            </div>
            <?php $count ++;} ?>
        <?php
    }
    if ($count < 1){?>
        <div id="cat-posts" class="no-cat-post">
             <div id="cat-posts" class="no-cat-post">
            <div class="core">
                <div class="body">
            There are no Closed challenges in this category.
                    </div></div>
        </div>
        <?php }
    ?>
</div>
<div class="winner">
    <h1>Winner Announced</h1>
    <?php $category = get_the_category();
    $cat_name = $category[0]->cat_name;
    $cat_slug = $category[0]->slug;
    ?>
    <?php
    $args = array(
        'post_type' => 'challenge',
        'tax_query'=>	array(
            'relation' => 'AND',

            array(
                'taxonomy' => 'category',
                'terms' => $cat_slug,
                'field' => 'slug',
            ),
        )
    );

    $apps = query_posts($args);
    $count = 0;
    while( have_posts() ) {
        the_post();
        ?>
        <?php
        $curr_date=strtotime(date('Ymd', time()));
        $start_date=strtotime(get_post_meta($post->ID, 'field_challenge_start_date', TRUE ));
        $end_date=strtotime(get_post_meta($post->ID, 'field_challenge_end_date', TRUE ));
        //echo $end_date;
        $winner= get_field_object('field_5241b50e67153');
        ?>
        <?php  if ( !empty ($winner['value'])  ) { ?>
            <div id="cat-posts" class="All-cat-post horizontal_dotted_line cat-post">
                <div class="core">
                    <div class="title"> <a href="<?php echo get_post_meta($post->ID, 'field_challenge_url', TRUE ); ?>">
                        <?php the_title() ?>
                    </a> <br/>
                    </div>
                    <div class="body">
                        <?php
                        $imagefile=get_field_object('field_5241b4eb20cea');
                        //var_dump($imagefile);
                        ?>
                        <?php
                        $image=  strlen($imagefile['value']['url']);
                        if ($image>0){ ?>
                            <img class="scale-with-grid" src="<?php echo  $imagefile['value']['url']; ?>" style="float:right; margin-left:10px; height:80px;">
                            <?php }else{?>
                            <img class="scale-with-grid" src="test">
                            <?php }  ?>
                        <?php the_content() ?>
                    </div>
                    <br clear="all" />
                </div>
            </div>
            <?php $count ++;} ?>
        <?php
    }
    if ($count < 1){?>
        <div id="cat-posts" class="no-cat-post">
             <div id="cat-posts" class="no-cat-post">
            <div class="core">
                <div class="body">
            There are no Winners announced in this category.
                    </div></div>
        </div>
        <?php }
    ?>
</div>
</div>
<!-- sixteen columns -->

<?php get_template_part('footer'); ?>
</div>
<!-- content -->
</div>
</div>
<!-- container -->

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