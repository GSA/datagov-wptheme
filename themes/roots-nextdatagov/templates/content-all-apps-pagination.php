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

    $args_featured = array(
        'post_type' => 'Applications',
        'posts_per_page' => -1,
        'post_status'=>'publish',
        'orderby'=> 'modified',
        'tax_query'=>	array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'featured',
                'field' => 'slug',
                'terms' => array( 'highlights'),
            ),
        ),
    );

    $args_nonfeatured = array(
        'post_type' => 'Applications',
        'posts_per_page' => -1,
        'post_status'=>'publish',
        'orderby'=> 'modified',
        'tax_query'=>	array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'featured',
                'field' => 'slug',
                'terms' => array( 'highlights'),
                'operator' => 'NOT IN'
            )
        ),
    );


    $result_featured = new WP_Query($args_featured);
    wp_reset_query();
    $featured = array();
    $i=0;
    while( $result_featured->have_posts() ) {
        $result_featured->the_post();
        $featured[$i]['title']= get_the_title($post->ID);
        $featured[$i]['conent']= get_the_content($post->ID);
        $featured[$i]['field_application_url'] = get_post_meta($post->ID, 'field_application_url', TRUE );
        $featured[$i]['image_url']=get_field_object('field_5240b9c982f41')['value']['url'];
        $featured[$i]['image_alt']=get_field_object('field_5240b9c982f41')['value']['alt'];
        $featured[$i]['featured']=true;
        $i++;
    }

    $result_nonfeatured = new WP_Query($args_nonfeatured);
    $not_featured = array();
    $i=0;
    while( $result_nonfeatured->have_posts() ) {
        $result_nonfeatured->the_post();
        $not_featured[$i]['title']= get_the_title($post->ID);
        $not_featured[$i]['conent']= get_the_content($post->ID);
        $not_featured[$i]['field_application_url'] = get_post_meta($post->ID, 'field_application_url', TRUE );
        $not_featured[$i]['image_url']=get_field_object('field_5240b9c982f41')['value']['url'];
        $not_featured[$i]['image_alt']=get_field_object('field_5240b9c982f41')['value']['alt'];
        $not_featured[$i]['featured']=false;
        $i++;
    }
    wp_reset_query();
    $apparray=array_merge($featured,$not_featured);
    $total_apps = count($apparray);
    $apps_per_page = 10;
    if(isset($apparray)) {
        $total_pages = ceil($total_apps / $apps_per_page);
    } else {
        $total_pages = 1;
        $total_apps = 0;
    }
    if (isset($_GET['currentpage']) && is_numeric($_GET['currentpage'])) {
        $currentpage = (int) $_GET['currentpage'];
    } else {
        $currentpage = 1;
    }
    if ($currentpage > $total_pages) {
        $currentpage  = $total_pages;
    }
    if ($currentpage < 1) {
        $currentpage = 1;
    }
    $start = ($currentpage - 1) * $apps_per_page + 1;
    ?>
    <div class="Apps-wrapper">
        <div class="Mobile-post" id="post-<?php $term->slug; ?>">
            <div class="Appstitle" ></div>
            <?php
            for($i=$start-1; $i<$start-1+$apps_per_page; $i++) {
                if(isset($apparray[$i])) { ?>
                    <div class="webcontainer <?php the_ID();?>">
                        <div id="webimage">
                            <img class="scale-with-grid" src="<?php echo $apparray[$i]['image_url']; ?>" alt="<?php echo $apparray[$i]['image_alt']; ?>">
                        </div>
                        <div id="webcontent">
                            <h2> <a href="<?php
                                echo $apparray[$i]['field_application_url']; ?>">
                                <?php echo $apparray[$i]['title'];?>
                            </a> </h2>

                            <div class='content'>
                                <div id="webtext">
                                    <?php echo $apparray[$i]['conent']; ?>
                                </div>
                            </div>
                        </div>
                        <br clear="all" />
                    </div>
                    <?php
                    $terms=$apparray[$i]['featured'];
                    if($terms) { ?>
                        <div><p style="margin-top:1px;"><img width="30px" height="30px" src="/wp-content/themes/roots-nextdatagov/assets/img/featured.png">  Featured!</p></div>
                        <?php
                    }
                }
            }
            ?>
            <br clear="all" />
        </div>

    </div>
    <div class='pagination'>
        <p class="counter">
            <?php printf( __( 'Page %1$s of %2$s' ), $currentpage, $total_pages ); ?>
        </p>
        <ul class='pagination'>
            <?php
            $output = "";
            if($total_apps > $apps_per_page) {
                $range = 10;
                if ($currentpage > 1) {
                    //$output .= "<br clear='both'/><li class='pager-first first'><a class='prev page-numbers pagenav local-link' href='?currentpage=1'> FIRST </a></li> ";
                    $prevpage = $currentpage - 1;
                    $output .= "<li class='pagination-prev'><a class='prev page-numbers pagenav local-link' href='?currentpage=$prevpage'>Previous</a> </li>";
                }
                for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
                    if (($x > 0) && ($x <= $total_pages)) {
                        if ($x == $currentpage) {
                            if ($total_pages > 1) {
                                $output .= "<li><span class='page-numbers pagenav current'> $x </span></li>";
                            }
                        }
                        else {
                            $output .= "<li><a class='page-numbers pagenav' href='?currentpage=$x'> $x </a></li>";
                        }
                    }
                }
                if ($currentpage != $total_pages) {
                    $nextpage = $currentpage + 1;
                    $output .= " <li class='pagination-next'> <a href='?currentpage=$nextpage'> Next</a></li> ";
                    //$output .= " <li class='pagination-next'><a href='?currentpage=$total_pages'>  LAST </a> </li>";
                }
            }
            print $output;
            ?>
        </ul>
    </div>
