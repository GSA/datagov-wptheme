<?php
$term_name = get_term_by('id', get_query_var('cat'), 'category')->name;
$term_slug = get_query_var('category_name');

// show Links associated to a community
// we need to build $args based either term_name or term_slug
$args = array(
                'category_name'=> $term_slug, 
                'categorize'=>0, 
                'title_li'=>0,
                'echo' =>0,
                'orderby'=>'rating'
            );
            
$subnav = wp_list_bookmarks($args);

if (strcasecmp($term_name,$term_slug)!=0) {
  $args = array(
                  'category_name'=> $term_name, 
                  'categorize'=>0, 
                  'title_li'=>0,
                  'echo' =>0,
                  'orderby'=>'rating',
                  );
                  
  $subnav_extra = wp_list_bookmarks($args);                  
}
$allowed_slug_arrays = array("climate-ecosystems","coastalflooding","energysupply","foodsupply","humanhealth","transportation","water","climate");
if($subnav OR $subnav_extra):
?>

    <div class="subnav banner">
        <div class="container">

        <?php if($subnav): ?>

           <nav class="topic-subnav" role="navigation">
               <ul class="nav navbar-nav">         
                <?php echo $subnav ?>
                   <?php
                   if(in_array($term_slug,$allowed_slug_arrays))
                       wp_nav_menu(array('theme_location' => 'climate_navigation', 'menu_class' => 'nav','items_wrap' => '%3$s'));
                   ?>
                </ul>
            </nav>
        
        <?php endif; ?>

        <?php if($subnav_extra): ?>
            <nav class="topic-subnav" role="navigation">
                <ul class="nav navbar-nav">
                    <?php echo $subnav_extra ?>
                    <?php
                    if(in_array($term_slug,$allowed_slug_arrays))
                        wp_nav_menu(array('theme_location' => 'climate_navigation', 'menu_class' => 'nav','items_wrap' => '%3$s'));
                    ?>
                </ul>
            </nav>
        <?php endif; ?>

        </div>
    </div>

<?php endif; ?>



<?php if (get_query_var('paged') < 1): ?>

<?php 

// See if there is a frontpage for the category.

$args = array( 
                'post_type' => 'page',
                'ignore_sticky_posts' => 1,  
                'cat' => get_query_var('cat'),
                'tax_query' => array(
                	                array(
                	                'taxonomy' => 'featured',
                	                'field' => 'slug',
                	                'terms' => array( 'browse'),
                	                'operator' => 'IN'
                	                )  
                                ),                 
                'posts_per_page' => 1 );
                         
$category_intro = new WP_Query($args);

?>



<?php while ($category_intro->have_posts()) : $category_intro->the_post(); ?>
<div class="intro">
    <div class="container">
        <?php the_content(); ?>      
    </div>
</div>    
<?php endwhile; ?>



<?php get_template_part('templates/content','highlights'); ?>


<?php endif; ?>

<div class="container">
  <div class="page-header">
    <h1>Updates</h1>
  </div>

<?php

$paged = (get_query_var('paged')) ? get_query_var('paged') : 0;
$args = array(
                'post_type' => 'post',
                'cat' => get_query_var('cat'),
                'tax_query' => array(
                	                array(
                	                'taxonomy' => 'featured',
                	                'field' => 'slug',
                	                'terms' => array( 'highlights'),
                	                'operator' => 'NOT IN'
                	                ),   
                                  array(
                                  'taxonomy' => 'featured',
                                  'field' => 'slug',
                                  'terms' => array( 'browse'),
                                  'operator' => 'NOT IN'
                                  )                 	                
                                ),               
                'paged' => $paged,                 
                'posts_per_page' => 5 );

$category_query = new WP_Query($args);

?>

<?php if (!$category_query->have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'roots'); ?>
  </div>
  <?php get_search_form(); ?>
<?php endif; ?>

<?php while ($category_query->have_posts()) : $category_query->the_post(); ?>
  <?php get_template_part('templates/content', get_post_format()); ?>
<?php endwhile; ?>

<?php if ($category_query->max_num_pages > 1) : ?>
<nav class="post-nav">
<?php your_pagination($category_query) ;?>  
</nav>
<?php endif; ?>
</div>
