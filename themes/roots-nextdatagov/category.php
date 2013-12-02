This is category.php 

<?php if (get_query_var('paged') < 1): ?>

<?php 

// See if there is a frontpage for the category.
$cat = get_query_var('cat');
$this_category = get_category ($cat);
$intro_page = $this_category->slug;
$args = array('name' => $intro_page, 'post_type' => 'page');
    
$category_intro = new WP_Query($args);
?>

<?php while ($category_intro->have_posts()) : $category_intro->the_post(); ?>
    <h1>Intro Text</h1>
    <?php the_content(); ?>      
<?php endwhile; ?>


<?php
$args = array( 
                'post_type' => 'post',
                'ignore_sticky_posts' => 1,                
                'tax_query' => array(
                	                array(
                	                'taxonomy' => 'post_format',
                	                'field' => 'slug',
                	                'terms' => array( 'post-format-link', 'post-format-status', 'post-format-gallery'),
                	                'operator' => 'NOT IN'
                	                )
                                ),               
                'meta_query' => array(
                                    array(
                                    'key' => 'highlight',
                                    'value' => 'Yes',
                                    'compare' => '=='
                                    )
                                ),                 
                'posts_per_page' => 1 );

$highlight_posts = new WP_Query($args);

?>

<?php while ($highlight_posts->have_posts()) : $highlight_posts->the_post(); ?>
  <h1>Highlights</h1>
  
  <article <?php post_class(); ?>>
    <header>
      <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    </header>
    <div class="entry-summary">
        <?php the_content(); ?>      
    </div>
  </article>  
    
<?php endwhile; ?>


<h1>News</h1>
<?php endif; ?>



<?php

$paged = (get_query_var('paged')) ? get_query_var('paged') : 0;
$args = array(
                'post_type' => 'post',
                'cat' => get_query_var('cat'),
                'meta_query' => array(
                    		        'relation' => 'OR',
                                    array(
                                    'key' => 'highlight',
                                    'value' => 'Yes',
                                    'compare' => '!='
                                    ),
                                    array(
                                    'key' => 'highlight',
                                    'value' => 'Yes',
                                    'compare' => 'NOT EXISTS'
                                    )                                    
                                ),
                'paged' => $paged,                 
                'posts_per_page' => 15 );

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
