<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>
<div class="subnav banner">
    <div class="container">
        <nav role="navigation" class="topic-subnav">
            <ul class="nav navbar-nav">
                <?php
                // show Links associated to a community
                // we need to build $args based either term_name or term_slug
                $args = array(
                    'category_name'=> $term_slug, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
                wp_list_bookmarks($args);
                if (strcasecmp($term_name,$term_slug)!=0) {
                    $args = array(
                        'category_name'=> $term_name, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
                    wp_list_bookmarks($args);
                }
                ?>
            </ul></nav></div>
</div>
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
