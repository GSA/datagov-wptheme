<div class="wrap container">
<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <header>
      <h1 class="entry-title"><?php the_title(); ?></h1>
      <?php get_template_part('templates/entry-meta'); ?>
    </header>
    <div class="entry-content">
     <?php the_content('Read the rest of this entry »'); ?>
                        <?php echo "<strong>Map Title:</strong>&nbsp;".get_post_meta($post->ID, 'map_title', TRUE ); ?>

                        <br />
                        <?php echo "<strong>ArcGIS Server Address:</strong>&nbsp;".get_post_meta($post->ID, 'arcgis_server_address', TRUE ); ?>
                        <br />
                        <?php echo "<strong>Map Category:</strong>&nbsp;".get_post_meta($post->ID, 'map_category', TRUE ); ?>
                        <br />
                        <?php echo "<strong>Map ID:</strong>&nbsp;".get_post_meta($post->ID, 'map_id', TRUE ); ?>
                        <br />
                        <br />
                        <?php echo "<strong>Group ID:</strong>&nbsp;".get_post_meta($post->ID, 'group_id', TRUE ); ?>
    </div>
    <footer>
      <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
    </footer>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?>
</div>
