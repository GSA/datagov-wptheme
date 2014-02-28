<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
$allowed_slug_arrays = array("climate-ecosystems","coastalflooding","energysupply","foodsupply","humanhealth","transportation","water","climate");

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
                if(in_array($term_slug,$allowed_slug_arrays))
                    wp_nav_menu(array('theme_location' => 'climate_navigation', 'menu_class' => 'nav','items_wrap' => '%3$s'));

                ?>
            </ul></nav></div>
</div>
<div class="container">

          <div class="content">
              <?php
              while( have_posts() ) {
                  the_post();
                  ?>
          <div class="Apps-wrapper">
          <div class="Apps-post" id="post-<?php the_ID(); ?>">
           <div id="appstitle" class="Appstitle" ><?php the_title();?></div>
                  <?php the_content();   ?>
                  <?php }?>
          </div>
          </div>
          </div>
       
 
        <!-- Federal Data --->
        <?php
					$args = array(
						 'post_type' => 'Applications',
					   'tax_query'=>	array(
						'relation' => 'AND',
					array(
					'taxonomy' => 'application_types',
					'terms' => 'federal_data-30',
					'field' => 'slug',
					),
					array(
					'taxonomy' => 'category',
					'terms' => $cat_slug,
					'field' => 'slug',
					),
				)
			);
					
		$apps = query_posts($args);
    if ( have_posts() ){
	?>
     <div class="Apps-wrapper">
  		<div class="Mobile-post" id="post-<?php the_ID(); ?>">
         <div id="Mobiletitle" class="Appstitle" >Federal Data</div>
			<?php
			while( have_posts() ) {
				the_post();
	  ?>
      <div id="Webcontainer" class="webcontainer <?php the_ID();?>">
      
      
        <div id="webcontent">
        <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
          <?php the_title() ?>
          </a> </h2>
        <div class='content'>
         <div id="webtext">
          <?php the_content() ?>
        </div>
        </div>
        </div><br clear="all" />
        
        </div>
        <?php
				}
        
		?><br clear="all" />
        </div>
        </div>
      <br clear="all" />
       <?php }?>
        
        
        
        <!-- Non Federal Data -->
       
       
        <?php
					$args = array(
						 'post_type' => 'Applications',
					   'tax_query'=>	array(
						'relation' => 'AND',
					array(
					'taxonomy' => 'application_types',
					'terms' => 'non-federal_data-30',
					'field' => 'slug',
					),
					array(
					'taxonomy' => 'category',
					'terms' => $cat_slug,
					'field' => 'slug',
					),
				)
			);
					
		$apps = query_posts($args);
        if ( have_posts() ){
        ?>
         <div class="Apps-wrapper">
  		<div class="Mobile-post" id="post-<?php the_ID(); ?>">
         <div id="Mobiletitle" class="Appstitle" >Non Federal Data</div>
			<?php
			while( have_posts() ) {
				the_post();
	  ?>
      <div id="Webcontainer" class="webcontainer <?php the_ID();?>">
      
      
        <div id="webcontent">
        <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
          <?php the_title() ?>
          </a> </h2>
        <div class='content'>
         <div id="webtext">
          <?php the_content() ?>
        </div>
        </div>
        </div><br clear="all" />
        
        </div>
        <?php
				}
        
		?><br clear="all" />
        </div>
        </div>
      <br clear="all" />
      <?php } ?>
          
      <!-- General Reference -->
             
        <?php
					$args = array(
						 'post_type' => 'Applications',
					   'tax_query'=>	array(
						'relation' => 'AND',
					array(
					'taxonomy' => 'application_types',
					'terms' => 'general_reference-30',
					'field' => 'slug',
					),
					array(
					'taxonomy' => 'category',
					'terms' => $cat_slug,
					'field' => 'slug',
					),
				)
			);
					
		$apps = query_posts($args);
        if ( have_posts() ){ ?>
         <div class="Apps-wrapper">
  		<div class="Mobile-post" id="post-<?php the_ID(); ?>">
         <div id="Mobiletitle" class="Appstitle" >General Reference</div>
			<?php
			while( have_posts() ) {
				the_post();
	  ?>
      <div id="Webcontainer" class="webcontainer <?php the_ID();?>">
      
      
        <div id="webcontent">
        <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
          <?php the_title() ?>
          </a> </h2>
        <div class='content'>
         <div id="webtext">
          <?php the_content() ?>
        </div>
        </div>
        </div><br clear="all" />
        
        </div>
        <?php
				}
        
		?><br clear="all" />
        </div>
        </div>
        <br clear="all" />
      <?php } ?>

      
      
      
      
      
      
    </div>