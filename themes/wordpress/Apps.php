<?php /*
Template Name:Category Applications
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
<script src="<?php echo get_bloginfo('template_directory'); ?>/assets/jquery.bxslider/jquery.bxslider.js"></script>
<link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/assets/jquery.bxslider/jquery.bxslider.css">
<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;

$term_slug = $category[0]->slug;
?>
<?php $category = get_the_category();
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>
<script type="text/javascript">

    $(document).ready(function(){
        $('.bxslider').bxSlider({
            minSlides: 3,
            maxSlides: 4,
            slideWidth: 220,
            slideMargin: 10
        });
    });
</script>
<body class="<?php echo $cat_slug;?>">
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
<div class="sixteen columns">

<?php //query_posts('category_name='.$cat_name ); ?>
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



<!-- Mobile Apps styles --->
<?php
$args = array(
    'post_type' => 'Applications',
    'tax_query'=>	array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'application_types',
            'terms' => 'mobile-30',
            'field' => 'slug',
        ),
        array(
            'taxonomy' => 'category',
            'terms' => $cat_slug,
            'field' => 'slug',
        ),
    )
);

$apps = query_posts($args);
if ( have_posts() ){
    ?>
<div class="Apps-wrapper">
    <div class="Mobile-post" id="post-<?php the_ID(); ?>">
        <div id="Mobiletitle" class="Appstitle" >Mobile Applications</div>

        <div id="Appscontainer" class="appscontainer">

            <ul class="bxslider">
                <?php
                while( have_posts() ) {
                    the_post();



                    ?>
                    <li>
                        <div id="Appsitem" class="appsitem <?php the_ID();?>">
                            <div id="itemtitle"> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
                                <?php the_title() ?>
                            </a>
                            </div>
                            <div id="itemimage">
                                <center>
                                    <?php
                                    $imagefile=get_field_object('field_5240b9c982f41');
                                    ?>
                                    <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>" style="height:85px;">
                                </center>
                            </div>
                            <div id="itemcontent">
                                <?php the_content() ?>
                            </div>
                            <?php //echo "Application URL:".get_post_meta($post->ID, 'field_application_url', TRUE ); ?>
                            <div id="bottomimages">
                                <center>
                                    <?php if (strlen(get_post_meta($post->ID, 'field_ios_app_download_url', TRUE ))>10) :?>
                                    <a href="<?php echo get_post_meta($post->ID, 'field_ios_app_download_url', TRUE );?>">
                                        <img src="<?php echo get_bloginfo('template_directory'); ?>/assets/jquery.bxslider/images/ios.png" class="iconApp" alt="ios">
                                    </a>
                                    <?php endif; ?>
                                    <?php if (strlen(get_post_meta($post->ID, 'field_android_app_download_url', TRUE ))>10) :?>
                                    <a href="<?php echo get_post_meta($post->ID, 'field_android_app_download_url', TRUE );?>">
                                        <img src="<?php echo get_bloginfo('template_directory'); ?>/assets/jquery.bxslider/images/android.png" class="iconApp" alt="android" >
                                    </a>
                                    <?php endif; ?>
                                    <?php if (strlen(get_post_meta($post->ID, 'field_windows_phone_app_download', TRUE ))>10) :?>
                                    <a href="<?php echo get_post_meta($post->ID, 'field_windows_phone_app_download', TRUE );?>">
                                        <img src="<?php echo get_bloginfo('template_directory'); ?>/assets/jquery.bxslider/images/windows.png" class="iconApp" alt="windows">
                                    </a>
                                    <?php endif; ?>
                                </center>
                            </div>
                        </div>
                    </li>
                    <?php
                }

                ?>
            </ul>
        </div><br clear="all" >
    </div>
</div>
<br clear="all" />
    <?php }?>
<!-- Web Apps -->


<?php
$args = array(
    'post_type' => 'Applications',
    'tax_query'=>	array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'application_types',
            'terms' => array('web-30','apps-30'),
            'field' => 'slug',
        ),
        array(
            'taxonomy' => 'category',
            'terms' => $cat_slug,
            'field' => 'slug',
        ),
    )
);

$apps = query_posts($args);
if ( have_posts() ){
    ?>
<div class="Apps-wrapper">
    <div class="Mobile-post" id="post-<?php the_ID(); ?>">
        <div id="Mobiletitle" class="Appstitle" >Web Applications</div>
        <?php
        while( have_posts() ) {
            the_post();
            ?>
            <div id="Webcontainer" class="webcontainer <?php the_ID();?>">

                <div id="webimage"><?php
                    $imagefile=get_field_object('field_5240b9c982f41');
                    ?>
                    <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>">
                </div>
                <div id="webcontent">
                    <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
                        <?php the_title() ?>
                    </a> </h2>
                    <div class='content'>
                        <div id="webtext">
                            <?php the_content() ?>
                        </div>
                    </div>
                </div><br clear="all" />
                <?php //echo "Application URL:".get_post_meta($post->ID, 'field_application_url', TRUE ); ?>
            </div>
            <?php
        }

        ?><br clear="all" />
    </div>
</div>
<br clear="all" />
    <?php } ?>
<!-- Application categories taxonomy-->
<!-- Agriculture Apps -->

<?php
$args = array(
    'post_type' => 'Applications',
    'tax_query'=>	array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'application_categories',
            'terms' => 'agriculture-40',
            'field' => 'slug',
        ),
        array(
            'taxonomy' => 'category',
            'terms' => $cat_slug,
            'field' => 'slug',
        ),
    )
);

$apps = query_posts($args);
if ( have_posts() ){ ?>
<div class="Apps-wrapper">
    <div class="Mobile-post" id="post-<?php the_ID(); ?>">
        <div id="Mobiletitle" class="Appstitle" >Agriculture Applications</div>
        <?php
        while( have_posts() ) {
            the_post();
            ?>
            <div id="Webcontainer" class="webcontainer <?php the_ID();?>">

                <div id="webimage"><?php
                    $imagefile=get_field_object('field_5240b9c982f41');
                    ?>
                    <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>">
                </div>
                <div id="webcontent">
                    <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
                        <?php the_title() ?>
                    </a> </h2>
                    <div class='content'>
                        <div id="webtext">
                            <?php the_content() ?>
                        </div>
                    </div>
                </div><br clear="all" />
                <?php //echo "Application URL:".get_post_meta($post->ID, 'field_application_url', TRUE ); ?>
            </div>
            <?php
        }

        ?><br clear="all" />
    </div>
</div>
<br clear="all" />
    <?php } ?>
<?php
$args = array(
    'post_type' => 'Applications',
    'tax_query'=>	array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'application_categories',
            'terms' => 'education-40',
            'field' => 'slug',
        ),
        array(
            'taxonomy' => 'category',
            'terms' => $cat_slug,
            'field' => 'slug',
        ),
    )
);

$apps = query_posts($args);
if ( have_posts() ){?>
<div class="Apps-wrapper">
    <div class="Mobile-post" id="post-<?php the_ID(); ?>">
        <div id="Mobiletitle" class="Appstitle" >Education Applications</div>
        <?php
        while( have_posts() ) {
            the_post();
            ?>
            <div id="Webcontainer" class="webcontainer <?php the_ID();?>">

                <div id="webimage"><?php
                    $imagefile=get_field_object('field_5240b9c982f41');
                    ?>
                    <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>">
                </div>
                <div id="webcontent">
                    <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
                        <?php the_title() ?>
                    </a> </h2>
                    <div class='content'>
                        <div id="webtext">
                            <?php the_content() ?>
                        </div>
                    </div>
                </div><br clear="all" />
                <?php //echo "Application URL:".get_post_meta($post->ID, 'field_application_url', TRUE ); ?>
            </div>
            <?php
        }

        ?><br clear="all" />
    </div>
</div><br clear="all" />
    <?php } ?>

<!-- Energy & Enivornment-->

<?php
$args = array(
    'post_type' => 'Applications',
    'tax_query'=>	array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'application_categories',
            'terms' => 'energy_&_environment-40',
            'field' => 'slug',
        ),
        array(
            'taxonomy' => 'category',
            'terms' => $cat_slug,
            'field' => 'slug',
        ),
    )
);

$apps = query_posts($args);
if ( have_posts() ){
    ?>
<div class="Apps-wrapper">
    <div class="Mobile-post" id="post-<?php the_ID(); ?>">
        <div id="Mobiletitle" class="Appstitle" >Energy & Environment Applications</div>
        <?php
        while( have_posts() ) {
            the_post();
            ?>
            <div id="Webcontainer" class="webcontainer <?php the_ID();?>">

                <div id="webimage"><?php
                    $imagefile=get_field_object('field_5240b9c982f41');
                    ?>
                    <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>">
                </div>
                <div id="webcontent">
                    <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
                        <?php the_title() ?>
                    </a> </h2>
                    <div class='content'>
                        <div id="webtext">
                            <?php the_content() ?>
                        </div>
                    </div>
                </div><br clear="all" />
                <?php //echo "Application URL:".get_post_meta($post->ID, 'field_application_url', TRUE ); ?>
            </div>
            <?php
        }

        ?><br clear="all" />
    </div>
</div>
    <?php }?>

<!-- Finance -->


<?php
$args = array(
    'post_type' => 'Applications',
    'tax_query'=>	array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'application_categories',
            'terms' => 'finance-40',
            'field' => 'slug',
        ),
        array(
            'taxonomy' => 'category',
            'terms' => $cat_slug,
            'field' => 'slug',
        ),
    )
);

$apps = query_posts($args);
if ( have_posts() ){?>
<div class="Apps-wrapper">
    <div class="Mobile-post" id="post-<?php the_ID(); ?>">
        <div id="Mobiletitle" class="Appstitle" >Finance Applications</div>
        <?php
        while( have_posts() ) {
            the_post();
            ?>
            <div id="Webcontainer" class="webcontainer <?php the_ID();?>">

                <div id="webimage"><?php
                    $imagefile=get_field_object('field_5240b9c982f41');
                    ?>
                    <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>">
                </div>
                <div id="webcontent">
                    <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
                        <?php the_title() ?>
                    </a> </h2>
                    <div class='content'>
                        <div id="webtext">
                            <?php the_content() ?>
                        </div>
                    </div>
                </div><br clear="all" />
                <?php //echo "Application URL:".get_post_meta($post->ID, 'field_application_url', TRUE ); ?>
            </div>
            <?php
        }

        ?><br clear="all" />
    </div>
</div>
    <?php }?>
<!-- Food & Nutrition -->

<?php
$args = array(
    'post_type' => 'Applications',
    'tax_query'=>	array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'application_categories',
            'terms' => 'food_&_nutrition-40',
            'field' => 'slug',
        ),
        array(
            'taxonomy' => 'category',
            'terms' => $cat_slug,
            'field' => 'slug',
        ),
    )
);

$apps = query_posts($args);
if ( have_posts() ){ ?>
<div class="Apps-wrapper">
    <div class="Mobile-post" id="post-<?php the_ID(); ?>">
        <div id="Mobiletitle" class="Appstitle" >Food & Nutrition Applications</div>
        <?php
        while( have_posts() ) {
            the_post();
            ?>
            <div id="Webcontainer" class="webcontainer <?php the_ID();?>">

                <div id="webimage"><?php
                    $imagefile=get_field_object('field_5240b9c982f41');
                    ?>
                    <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>">
                </div>
                <div id="webcontent">
                    <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
                        <?php the_title() ?>
                    </a> </h2>
                    <div class='content'>
                        <div id="webtext">
                            <?php the_content() ?>
                        </div>
                    </div>
                </div><br clear="all" />
                <?php //echo "Application URL:".get_post_meta($post->ID, 'field_application_url', TRUE ); ?>
            </div>
            <?php
        }

        ?><br clear="all" />
    </div>
</div>
    <?php }?>
<!-- Global Food Security -->


<?php
$args = array(
    'post_type' => 'Applications',
    'tax_query'=>	array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'application_categories',
            'terms' => 'global_food_security-40',
            'field' => 'slug',
        ),
        array(
            'taxonomy' => 'category',
            'terms' => $cat_slug,
            'field' => 'slug',
        ),
    )
);

$apps = query_posts($args);
if ( have_posts() ){ ?>
<div class="Apps-wrapper">
    <div class="Mobile-post" id="post-<?php the_ID(); ?>">
        <div id="Mobiletitle" class="Appstitle" >Global Food Security Applications</div>
        <?php
        while( have_posts() ) {
            the_post();
            ?>
            <div id="Webcontainer" class="webcontainer <?php the_ID();?>">

                <div id="webimage"><?php
                    $imagefile=get_field_object('field_5240b9c982f41');
                    ?>
                    <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>">
                </div>
                <div id="webcontent">
                    <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
                        <?php the_title() ?>
                    </a> </h2>
                    <div class='content'>
                        <div id="webtext">
                            <?php the_content() ?>
                        </div>
                    </div>
                </div><br clear="all" />
                <?php //echo "Application URL:".get_post_meta($post->ID, 'field_application_url', TRUE ); ?>
            </div>
            <?php
        }

        ?><br clear="all" />
    </div>
</div>
    <?php }?>
<!-- Health Apps -->

<?php
$args = array(
    'post_type' => 'Applications',
    'tax_query'=>	array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'application_categories',
            'terms' => 'health-40',
            'field' => 'slug',
        ),
        array(
            'taxonomy' => 'category',
            'terms' => $cat_slug,
            'field' => 'slug',
        ),
    )
);

$apps = query_posts($args);
if ( have_posts() ){?>
<div class="Apps-wrapper">
    <div class="Mobile-post" id="post-<?php the_ID(); ?>">
        <div id="Mobiletitle" class="Appstitle" >Health Applications</div>
        <?php
        while( have_posts() ) {
            the_post();
            ?>
            <div id="Webcontainer" class="webcontainer <?php the_ID();?>">

                <div id="webimage"><?php
                    $imagefile=get_field_object('field_5240b9c982f41');
                    ?>
                    <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>">
                </div>
                <div id="webcontent">
                    <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
                        <?php the_title() ?>
                    </a> </h2>
                    <div class='content'>
                        <div id="webtext">
                            <?php the_content() ?>
                        </div>
                    </div>
                </div><br clear="all" />
                <?php //echo "Application URL:".get_post_meta($post->ID, 'field_application_url', TRUE ); ?>
            </div>
            <?php
        }

        ?><br clear="all" />
    </div>
</div>
    <?php }?>
<!-- Rural Apps -->

<?php
$args = array(
    'post_type' => 'Applications',
    'tax_query'=>	array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'application_categories',
            'terms' => 'rural-40',
            'field' => 'slug',
        ),
        array(
            'taxonomy' => 'category',
            'terms' => $cat_slug,
            'field' => 'slug',
        ),
    )
);

$apps = query_posts($args);
if ( have_posts() ){?>
<div class="Apps-wrapper">
    <div class="Mobile-post" id="post-<?php the_ID(); ?>">
        <div id="Mobiletitle" class="Appstitle" >Rural Applications</div>
        <?php
        while( have_posts() ) {
            the_post();
            ?>
            <div id="Webcontainer" class="webcontainer <?php the_ID();?>">

                <div id="webimage"><?php
                    $imagefile=get_field_object('field_5240b9c982f41');
                    ?>
                    <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>">
                </div>
                <div id="webcontent">
                    <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
                        <?php the_title() ?>
                    </a> </h2>
                    <div class='content'>
                        <div id="webtext">
                            <?php the_content() ?>
                        </div>
                    </div>
                </div><br clear="all" />
                <?php //echo "Application URL:".get_post_meta($post->ID, 'field_application_url', TRUE ); ?>
            </div>
            <?php
        }

        ?><br clear="all" />
    </div>
</div>
    <?php }?>
<!-- Safety Apps -->

<?php
$args = array(
    'post_type' => 'Applications',
    'tax_query'=>	array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'application_categories',
            'terms' => 'safety-40',
            'field' => 'slug',
        ),
        array(
            'taxonomy' => 'category',
            'terms' => $cat_slug,
            'field' => 'slug',
        ),
    )
);

$apps = query_posts($args);
if ( have_posts() ){?>
<div class="Apps-wrapper">
    <div class="Mobile-post" id="post-<?php the_ID(); ?>">
        <div id="Mobiletitle" class="Appstitle" >Safety Applications</div>
        <?php
        while( have_posts() ) {
            the_post();
            ?>
            <div id="Webcontainer" class="webcontainer <?php the_ID();?>">

                <div id="webimage"><?php
                    $imagefile=get_field_object('field_5240b9c982f41');
                    ?>
                    <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>">
                </div>
                <div id="webcontent">
                    <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
                        <?php the_title() ?>
                    </a> </h2>
                    <div class='content'>
                        <div id="webtext">
                            <?php the_content() ?>
                        </div>
                    </div>
                </div><br clear="all" />
                <?php //echo "Application URL:".get_post_meta($post->ID, 'field_application_url', TRUE ); ?>
            </div>
            <?php
        }

        ?><br clear="all" />
    </div>
</div>
    <?php }?>

<!-- Telecommunications -->

<?php
$args = array(
    'post_type' => 'Applications',
    'tax_query'=>	array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'application_categories',
            'terms' => 'telecommunications-40',
            'field' => 'slug',
        ),
        array(
            'taxonomy' => 'category',
            'terms' => $cat_slug,
            'field' => 'slug',
        ),
    )
);

$apps = query_posts($args);
if ( have_posts() ){?>
<div class="Apps-wrapper">
    <div class="Mobile-post" id="post-<?php the_ID(); ?>">
        <div id="Mobiletitle" class="Appstitle" >Telecommunications Applications</div>
        <?php
        while( have_posts() ) {
            the_post();
            ?>
            <div id="Webcontainer" class="webcontainer <?php the_ID();?>">

                <div id="webimage"><?php
                    $imagefile=get_field_object('field_5240b9c982f41');
                    ?>
                    <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>">
                </div>
                <div id="webcontent">
                    <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
                        <?php the_title() ?>
                    </a> </h2>
                    <div class='content'>
                        <div id="webtext">
                            <?php the_content() ?>
                        </div>
                    </div>
                </div><br clear="all" />
                <?php //echo "Application URL:".get_post_meta($post->ID, 'field_application_url', TRUE ); ?>
            </div>
            <?php
        }

        ?><br clear="all" />
    </div>
</div>
    <?php }?>
<!-- Transportation -->


<?php
$args = array(
    'post_type' => 'Applications',
    'tax_query'=>	array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'application_categories',
            'terms' => 'transportation-40',
            'field' => 'slug',
        ),
        array(
            'taxonomy' => 'category',
            'terms' => $cat_slug,
            'field' => 'slug',
        ),
    )
);

$apps = query_posts($args);
if ( have_posts() ){?>
<div class="Apps-wrapper">
    <div class="Mobile-post" id="post-<?php the_ID(); ?>">
        <div id="Mobiletitle" class="Appstitle" >Transportation Applications</div>
        <?php
        while( have_posts() ) {
            the_post();
            ?>
            <div id="Webcontainer" class="webcontainer <?php the_ID();?>">

                <div id="webimage"><?php
                    $imagefile=get_field_object('field_5240b9c982f41');
                    ?>
                    <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>">
                </div>
                <div id="webcontent">
                    <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
                        <?php the_title() ?>
                    </a> </h2>
                    <div class='content'>
                        <div id="webtext">
                            <?php the_content() ?>
                        </div>
                    </div>
                </div><br clear="all" />
                <?php //echo "Application URL:".get_post_meta($post->ID, 'field_application_url', TRUE ); ?>
            </div>
            <?php
        }

        ?><br clear="all" />
    </div>
</div>
    <?php }?>






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
<script type="text/javascript">
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
<script type="text/javascript">
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