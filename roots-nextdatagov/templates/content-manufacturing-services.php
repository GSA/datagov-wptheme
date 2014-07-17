<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>
<?php include('category-subnav.php'); ?>
<div class="container">
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
</div>
