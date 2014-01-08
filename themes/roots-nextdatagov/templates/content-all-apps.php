<script src="<?php echo get_bloginfo('template_directory'); ?>/assets/js/jquery.bxslider.js"></script>
<link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/assets/css/jquery.bxslider.css">
<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>
<script type="text/javascript">


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
                minSlides: 3,
                maxSlides: 4,
                slideWidth: 220,
                slideMargin: 10
            });
        }
    });
</script>
<div class="subnav banner">
    <div class="container">
        <nav role="navigation" class="topic-subnav">
            <ul class="nav navbar-nav">
                <?php
                // show Links associated to a community
                // we need to build $args based either term_name or term_slug
                if(!empty($term_slug)){
                    $args = array(
                        'category_name'=> $term_slug, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
                    wp_list_bookmarks($args);
                }
                if (strcasecmp($term_name,$term_slug)!=0) {
                    $args = array(
                        'category_name'=> $term_name, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
                    wp_list_bookmarks($args);
                }
                ?>
            </ul></nav></div>
</div>
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

<!-- Application featured taxonomy-->
<?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'orderby'=> 'modified',
    'post_type' => 'Applications',
    'post_status' => 'publish',
    'posts_per_page' => 5,
    'paged' => $paged,
);
$result = new WP_Query($args);
if($result->found_posts> 0) {  ?>
    <div class="Apps-wrapper">
        <div class="Mobile-post" id="post-<?php $term->slug; ?>">
            <div class="Appstitle" ></div>
            <?php
            while( $result->have_posts() ) {
                $result->the_post();
                ?>
                <div class="webcontainer <?php the_ID();?>">
                    <div id="webimage">
                        <?php
                        $imagefile=get_field_object('field_5240b9c982f41');
                        ?>
                        <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>"> </div>
                    <div id="webcontent">
                        <h2> <a href="<?php
                            $terms=wp_get_post_terms($post->ID, 'featured');
                            echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
                            <?php the_title()?>
                        </a> </h2>

                        <div class='content'>
                            <div id="webtext">
                                <?php the_content() ?>
                            </div>
                            <?php
                            if(!empty($terms)) { ?>
                                <div><p style="margin-top:5px;"><img width="30px" height="30px" src="/wp-content/themes/roots-nextdatagov/assets/img/featured.png">Featured!</p></div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <br clear="all" />
                    <?php //echo "Application URL:".get_post_meta($post->ID, 'field_application_url', TRUE ); ?>
                </div>
                <?php
            } ?>
        </div>
    </div>
    <nav class="post-nav">
        <?php your_pagination($result) ;?>
    </nav>
    <br clear="all" />
</div>
<?php }
?>
