<?php $shared_facilities = get_post('40685')?>

<div class="technical-wrapper">
  <div class="inner">
    <div id="services">
      <?php while ( have_posts() ) : the_post(); ?>
      <h2 class="pane-title block-title">
        <?php the_title();?>
      </h2>
      <?php the_content(); ?>
      <?php endwhile; // end of the loop. ?>
      <?php echo $shared_facilities->post_content;?> </div>
  </div>
</div>
