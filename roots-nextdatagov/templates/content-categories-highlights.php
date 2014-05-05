<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
$allowed_slug_arrays = array("climate-ecosystems","coastalflooding","energysupply","foodsupply","humanhealth","transportation","water","climate");
?>
<div class="subnav banner">
    <div class="container">
        <nav role="navigation" class="topic-subnav">
            <ul class="nav navbar-nav">
                <?php
                // show Links associated to a community
                // we need to build $args based either term_name or term_slug
                if(in_array($term_slug,$allowed_slug_arrays))
                    wp_nav_menu(array('theme_location' => 'climate_navigation', 'menu_class' => 'nav','items_wrap' => '%3$s'));
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
<div class="wrap container content-page">
  <div class="highlights-listing">
<?php
      $args = array(
          'post_type' => 'post',
          'tax_query' => array(
              'relation' => 'AND',
              array(
                  'taxonomy' => 'post_format',
                  'field' => 'slug',
                  'terms' => array( 'post-format-link', 'post-format-status', 'post-format-gallery'),
                  'operator' => 'NOT IN'
              ),
              array(
                  'taxonomy' => 'featured',
                  'field' => 'slug',
                  'terms' => array( 'highlights'),
                  'operator' => 'IN'
              )
          ),
          'posts_per_page' => 5,
          'paged' => $paged,
          'category_name'=> $cat_slug );


      $category_query = new WP_Query($args);

   if ($category_query->have_posts()):
      while ($category_query->have_posts()):
          $category_query->the_post();
            ?>
            <div class="highlight <?php $cat_name ?> clearfix">
            <header>
                <h2 class="entry-title"><?php the_title(); ?></h2>
            </header>

            <?php if ( has_post_thumbnail() ) : ?>
            <div class="featured-image col-md-4">
                <?php the_post_thumbnail('medium'); ?>
            </div>
            <?php endif; ?>

            <article class="<?php if ( has_post_thumbnail() ) : ?>col-md-8<?php else: ?>no-image<?php endif;?>">
                <?php the_content(); ?>
            </article>

            <?php if(get_post_format() == 'image'): ?>
            <div class="dataset-link">
                <a class="btn btn-default pull-right" href="<?php the_field('link_to_dataset'); ?>">
                    <span class="glyphicon glyphicon-download"></span> View this Dataset
                </a>
            </div>
            <?php endif;?>
     </div><!--/.highlight-->
          <?php
          wp_reset_postdata();
          ?>
<?php
        endwhile;
   ?>
<nav class="post-nav">
   <?php your_pagination($category_query) ;?>
</nav>
<?php
    endif;
?>
    </div>
</div>