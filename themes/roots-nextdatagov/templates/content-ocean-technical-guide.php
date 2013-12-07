<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>
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
<div class="container">
<?php
                    while( have_posts() ) {
                        the_post();
                        ?>
    <?php the_content();   ?>
    <?php }?>
                <?php $post = get_post('35665')?>
                <?php $post1 = get_post('40636')?>
                <?php $post2 = get_post('115892')?>
                <?php $post3 = get_post('115902')?>
                <div class="technical-wrapper">
                    <div class="inner">
                        <h2 class="pane-title block-title"><?php echo $post->post_title;?></h2>


                        <p><?php echo $post->post_content;?></p>


                    </div>



                    <p><?php echo $post1->post_content;?></p>
                    <p><?php echo $post2->post_content;?></p>
                    <p><?php echo $post3->post_content;?></p>


                </div>


</div>