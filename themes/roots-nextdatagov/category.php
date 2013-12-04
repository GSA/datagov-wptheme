<div class="wrap container">

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


<?php get_template_part('templates/content','highlights'); ?>

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
