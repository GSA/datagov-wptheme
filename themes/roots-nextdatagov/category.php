This is category.php 

<?php if (get_query_var('paged') < 1): ?>

    <?php
    echo '<br> Show landing page stuff only on the first page';
    ?>

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
