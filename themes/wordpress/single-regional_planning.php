<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">
<!--<![endif]-->

<?php get_template_part('header'); ?>

<body class="single">
<div class="banner disclaimer">
    <p>This is a demonstration site exploring the future of Data.gov. <span id="stop-disclaimer"> Give us your feedback on <a href="https://twitter.com/usdatagov">Twitter</a>, <a href="http://quora.com">Quora</a></span>, <a href="https://github.com/GSA/datagov-design/">Github</a>, or <a href="http://www.data.gov/contact-us">contact us</a></p>
</div>

<!-- Header Background Color, Image, or Visualization
	================================================== -->
<div class="menu-container">
    <div class="header-next-top" >


        <?php get_template_part('navigation'); ?>



    </div>
</div>
<div class="next-header category <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>"> </div>

<!-- Navigation & Search
	================================================== -->

<div class="container">
  <div class="next-top category <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">

    <?php get_template_part('category-search'); ?>
  </div>
  <!-- top --> 
  
</div>
<div class="page-nav"> </div>
<div class="container">
    <div class="sixteen columns page-nav-items">

        <?php
        $category = get_the_category(  );
        $cat_name=$category[0]->cat_name;


        $args = array(
            'category_name'=>$cat_name, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');

        wp_list_bookmarks($args); ?>

    </div>
  
  <!-- WordPress Content
	================================================== -->
  
  <div class="content">
    <div class="sixteen columns">
      <?php if (have_posts()) : ?>
      <?php while (have_posts()) : the_post(); ?>
      <div class="single-post">
        <div class="title">
          <?php the_title(); ?>
        </div>
        <div class="body">
          <?php the_content('Read the rest of this entry »'); ?>
          <?php echo "<strong>Region:</strong>&nbsp;".get_post_meta($post->ID, 'region', TRUE ); ?> <br />
          <?php echo "<strong>Alias:</strong>&nbsp;".get_post_meta($post->ID, 'field_alias', TRUE ); ?> <br />
          <?php echo "<strong>Members:</strong>&nbsp;".get_post_meta($post->ID, 'field_members', TRUE ); ?> <br />
          <?php echo "<strong>Regional Ocean Partnership:</strong>&nbsp;".get_post_meta($post->ID, 'regional_ocean_partnership', TRUE ); ?> <br />
          <br />
          <?php echo "<strong>Regional Data Portal:</strong>&nbsp;".get_post_meta($post->ID, 'regional_data_portal', TRUE ); ?> <br />
          <?php echo "<strong>State/Regioanl Coastal Atlas:</strong>&nbsp;".get_post_meta($post->ID, 'state/regioanl_coastal_atlas', TRUE ); ?> <br />
          <br />
          <?php echo "<strong>IOOS Regional Association:</strong>&nbsp;".get_post_meta($post->ID, 'ioos_regional_association', TRUE ); ?> <br />
          <?php echo "<strong>State/Regioanl Coastal Atlas:</strong>&nbsp;".get_post_meta($post->ID, 'state/regioanl_coastal_atlas', TRUE ); ?> <br />
          <?php echo "<strong>Generic Text:</strong>&nbsp;".get_post_meta($post->ID, 'generic_text', TRUE ); ?> <br />
          <?php echo "<strong>Region Map:</strong>&nbsp; ".get_post_meta($post->ID, 'region_map', TRUE ); ?> <br />
            <?php
            $imagefile=get_field_object('field_5240af4cb1726');
            ?>

            <img class="scale-with-grid" src="<?php echo $imagefile['value']; ?>">


            <?php echo "<strong>Group:</strong>&nbsp;";
											?>
          <?php
                $values = get_field('group');
                $taxanomy='category';
                if($values)
                    {
	                 echo '<ul>';
	                    foreach($values as $value)
	                    {
		
	                        $term = get_term( $value, $taxanomy );
	                        $name = $term->name;
	                        echo '<li>' . $name . '</li>';
	                    }

                    echo '</ul>';
                    }
 
// always good to see exactly what you are working with
//var_dump($values);
 
?>
        </div>
      </div>
      <?php endwhile; ?>
      <?php else : ?>
      <h2 class="center">Not Found</h2>
      <p class="center">Sorry, but you are looking for something that isn't here.</p>
      <?php include (TEMPLATEPATH . "/searchform.php"); ?>
      <?php endif; ?>
    </div>
  </div>
  <!-- sixteen columns -->
</div>
<!-- content -->

</div>
<!-- container --> 

<script>
$(window).load(function(){
  $('#posts').masonry({
    // options
    columnWidth: 287,
    itemSelector : '.post',
    isResizable: true,
    isAnimated: true,
    gutterWidth: 25
  });
});
</script> 
<script src="<?php echo get_bloginfo('template_directory'); ?>/js/autosize.js"></script> 

<!-- End Document
================================================== -->
</body>
</html>