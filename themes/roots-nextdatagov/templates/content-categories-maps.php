<?php
              while( have_posts() ) {
                  the_post();
                  ?>

<div class="Apps-wrapper">
  <div class="Apps-post" id="post-<?php the_ID(); ?>">
    <div id="appstitle" class="Appstitle" >
      <?php the_title();?>
    </div>
    <?php the_content();   ?>
    <?php }?>
  </div>
</div>

<!-- Maps --->
<?php
					$args = array(
						 'post_type' => 'Applications',
					   'tax_query'=>	array(
						'relation' => 'AND',
					array(
					'taxonomy' => 'application_types',
					'terms' => 'maps-30',
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
    <div id="Mobiletitle" class="Appstitle" >Maps</div>
    <?php
			while( have_posts() ) {
				the_post();
	  ?>
    <div id="Webcontainer" class="webcontainer <?php the_ID();?>">
      <div id="webimage">
        <?php 
				$imagefile=get_field_object('field_5240b9c982f41');
		  ?>
        <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>"> </div>
      <div id="webcontent">
        <h2> <a href="<?php echo get_post_meta($post->ID, 'field_application_url', TRUE ); ?>">
          <?php the_title() ?>
          </a> </h2>
        <div class='content'>
          <div id="webtext">
            <?php the_content() ?>
          </div>
        </div>
      </div>
      <br clear="all" />
    </div>
    <?php
				}
        
		?>
    <br clear="all" />
  </div>
</div>
<br clear="all" />
<?php }?>
