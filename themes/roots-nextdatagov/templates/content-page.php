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
       if(!empty($term_slug)){
           $args = array(
               'category_name'=> $term_slug, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
           wp_list_bookmarks($args);
       }
      if (strcasecmp($term_name,$term_slug)!=0) {
          $args = array(
              'category_name'=> $term_name, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
          wp_list_bookmarks($args);
      }
?>
</ul></nav></div></div>
<div class="wrap container content-page">

<div class="container">
<?php while (have_posts()) : the_post(); ?>
  <?php the_content(); ?>
  <?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
<?php endwhile; ?>
</div>
</div>
