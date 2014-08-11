<script src="<?php echo get_bloginfo('template_directory'); ?>/assets/js/jquery.bxslider.js"></script>
<link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/assets/css/jquery.bxslider.css">

<?php
$category = get_the_category();
$cat_slug = $category[0]->slug;
?>


<?php include('category-subnav.php'); ?>


<script type="text/javascript">

    setTimeout(function() {
    jQuery(document).ready(function(){

        if (jQuery(window).width() < 480) {
            jQuery('.bxslider').bxSlider({
                minSlides: 1,
                maxSlides: 1,
                slideWidth: 220,
                slideMargin: 10
            });
        }
        else{
            jQuery('.bxslider').bxSlider({
                minSlides: 1,
                maxSlides: 4,
                slideWidth: 220,
                slideMargin: 10
            });
        }
    });},2000);
</script>

<div class="container">
<?php
while( have_posts() ) {
    the_post();
    ?>
<div class="Apps-wrapper">
    <div class="Apps-post" id="post-<?php the_ID(); ?>">
       
      <div  class="Appstitle" ><?php the_title();?></div>
    <?php the_content();   ?>
    <?php }?>
</div>
</div>



<!-- Mobile Apps styles -->
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
        <div  class="Appstitle" >Mobile Applications</div>

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
                                    <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>">
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
                                        <img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/ios.png" class="iconApp" alt="ios">
                                    </a>
                                    <?php endif; ?>
                                    <?php if (strlen(get_post_meta($post->ID, 'field_android_app_download_url', TRUE ))>10) :?>
                                    <a href="<?php echo get_post_meta($post->ID, 'field_android_app_download_url', TRUE );?>">
                                        <img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/android.png" class="iconApp" alt="android">
                                    </a>
                                    <?php endif; ?>
                                    <?php if (strlen(get_post_meta($post->ID, 'field_windows_phone_app_download', TRUE ))>10) :?>
                                    <a href="<?php echo get_post_meta($post->ID, 'field_windows_phone_app_download', TRUE );?>">
                                        <img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/windows.png" class="iconApp" alt="windows">
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
        <div  class="Appstitle" >Web Applications</div>
        <?php
        while( have_posts() ) {
            the_post();
            ?>
            <div  class="webcontainer <?php the_ID();?>">

                <div id="webimage"><?php
                    $imagefile=get_field_object('field_5240b9c982f41');
                    ?>
                    <img <?php if ($imagefile['value']['url'] ==''){ ?> class="scale-with-grid noImage"<?php }else{  ?> class="scale-with-grid" <?php }?> src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>" >
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

<!-- Application categories taxonomy-->

<?php
$terms = get_terms("application_categories");
$count = count($terms);
if ( $count > 0 ){
    foreach ( $terms as $term ) {
        if($term->count >0){
            $args = array(
                'post_type' => 'Applications',
                'tax_query'=>	array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'application_categories',
                        'terms' => $term->slug,
                        'field' => 'slug',
                    ),
                    array(
                        'taxonomy' => 'category',
                        'terms' => $cat_slug,
                        'field' => 'slug',
                    ),
                )
            );
            $result = new WP_Query($args);
            if($result->have_posts()){
            ?>
            <div class="Apps-wrapper">
                <div class="Mobile-post" id="post-<?php $term->slug; ?>">
                    <div class="Appstitle" ><?php echo $term->name; ?> Applications</div>
                    <?php
                    while( $result->have_posts() ) {
                        $result->the_post();
                        ?>
                        <div class="webcontainer <?php the_ID();?>">
                            <div id="webimage">
                                <?php
                                $imagefile=get_field_object('field_5240b9c982f41');
                                ?>
                                <img <?php if ($imagefile['value']['url'] ==''){ ?> class="scale-with-grid noImage"<?php }else{  ?> class="scale-with-grid" <?php }?> src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>"> </div>
                            <div id="webcontent">
                                <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
                                    <?php the_title() ?>
                                </a> </h2>
                                <div class='content'>
                                    <div id="webtext">
                                        <?php the_content() ?>
                                    </div>
                                </div>
                            </div>
                            <br clear="all" />
                            <?php //echo "Application URL:".get_post_meta($post->ID, 'field_application_url', TRUE ); ?>
                        </div>
                        <?php
                    }
                    ?>
                    <br clear="all" />
                </div>
            </div>
            <br clear="all" />
                <?php }
        }
    }
}
?>
</div>