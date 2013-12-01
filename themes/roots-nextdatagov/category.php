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

<h1>Highlights</h1>
<p>Perhaps reuse highlights template_part from frontpage</p>

<h1>News</h1>
<?php endif; ?>



<?php if (!have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'roots'); ?>
  </div>
  <?php get_search_form(); ?>
<?php endif; ?>

<?php while (have_posts()) : the_post(); ?>
  <?php get_template_part('templates/content', get_post_format()); ?>
<?php endwhile; ?>

<?php if ($wp_query->max_num_pages > 1) : ?>
<nav class="post-nav">
<?php your_pagination($wp_query) ;?>  
</nav>
<?php endif; ?>
