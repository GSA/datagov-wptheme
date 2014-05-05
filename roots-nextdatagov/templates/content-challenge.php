<div class="wrap container"><?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <header>
      <h1 class="entry-title"><?php the_title(); ?></h1>
      <?php get_template_part('templates/entry-meta'); ?>
    </header>
    <div class="entry-content">
      <?php the_content(); ?>
      <?php echo "<strong>Challenge URL:</strong>&nbsp;".get_post_meta($post->ID, 'field_challenge_url', TRUE ); ?>
                        <br />

                        <?php echo "<strong>Challenge Start Date:</strong>&nbsp;".get_post_meta($post->ID, 'field_challenge_start_date', TRUE ); ?>
                        <br />
                        <?php echo "<strong><Challange End Date:</strong>&nbsp;".get_post_meta($post->ID, 'field_challenge_end_date', TRUE ); ?>
                        <br />
                        <?php echo "<strong>Challenge Award:</strong>&nbsp;".get_post_meta($post->ID, 'field_challenge_award', TRUE ); ?>
                        <br />
                        <?php echo "<strong><Challenge Thumbnail:</strong>&nbsp; " ?>
                        <?php
                        $imagefile=get_field_object('field_5241b4eb20cea');
                        ?>

                        <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>">


                        <br />
                        <?php echo "<strong>Winner Announced:</strong>&nbsp;".get_post_meta($post->ID, 'field_winner_announced', TRUE ); ?>
                        <br />
    </div>
    <footer>
      <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
    </footer>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?>
</div>