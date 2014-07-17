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
<div class="technical-wrapper">
  <div id="regionalimg" class="imagearea" >
    <h2 class="block-title">Regional Planning Efforts</h2>
    <?php global $title;
$title=get_the_title();

?>
    <?php
                            global $cat_name;
                            $category = get_the_category(  );
                            $cat_name=$category[0]->slug;
                            $fieldaliasvalue = $_GET['field_alias_value'];
//WordPress loop for custom post type
                            $my_query = new WP_Query("post_type=regional_planning&posts_per_page=-1&meta_key=field_alias&meta_compare=>=&meta_value='.$fieldaliasvalue.'");
                            while ($my_query->have_posts()) : $my_query->the_post(); ?>
    <?php 

							 if (get_post_meta($post->ID, 'field_alias', TRUE )== $fieldaliasvalue) { ?>
    <div class="panel-col-first">
      <div class="pane-content content">
        <div id="regionimg2" >
          <?php $imagefile=get_field_object('field_5240af4cb1726');
                         ?>
          <img width="200" height="200" class="scale-with-grid" src="<?php echo $imagefile['value']; ?>"> <br />
        </div>
        <div> <?php echo get_post_meta($post->ID, 'members', TRUE ); ?> <br />
        </div>
        <div class="field-content"> <strong>Regional Ocean Partnership</strong> <?php echo get_post_meta($post->ID, 'regional_ocean_partnership', TRUE ); ?> </div>
        <div class="portal"> <strong>Regional Data Portal</strong> <?php echo get_post_meta($post->ID, 'regional_data_portal', TRUE ); ?> </div>
      </div>
    </div>
    <div class="panel-col-second">
      <h2 class="fieldcontentregion"><?php echo get_post_meta($post->ID, 'region', TRUE ); ?></h2>
      <div class="panel-pane pane-custom pane-1"> <em>When you click on these resources, you will be leaving Data.gov.</em>
        <h3 class="fieldcontentregion">State / Regional Coastal Atlases</h3>
        <div class="state"> <?php echo get_post_meta($post->ID, 'state/regioanl_coastal_atlas', TRUE ); ?> </div>
        <h3 class="fieldcontentregion">IOOS Regional Association</h3>
        <div class="state"> <?php echo get_post_meta($post->ID, 'ioos_regional_association', TRUE ); ?> </div>
        <div class="state"> <?php echo get_post_meta($post->ID, 'generic_text', TRUE ); ?> <br />
        </div>
      </div>
    </div>
    <?php } ?>
    <?php endwhile;  wp_reset_query(); ?>
  </div>
  <div id="regionsidebar">
    <div class="panels-flexible-region-inside">
      <div class="panel-pane pane-views pane-ocean-regional-planning">
        <div class="inner">
          <?php
                    $args = array(
                        'orderby'          => '',
                        'meta_key'         => 'field_alias',
                        'orderby'          => 'meta_value',
                        'order'            => 'ASC',
                        'post_type'        => 'regional_planning',
                        'post_status'      => 'publish',
                        'suppress_filters' => true );
                    $query = null;
                    $query = new WP_Query($args);
                    if( $query->have_posts() ) {
                        echo '<h2 class="pane-title ">Planning Regions</h2>';
						echo '<div class="panecontent">';
						echo '<div class="item-list">';
						echo '<ul>';
                        while ($query->have_posts()) : $query->the_post();
                            $link="/ocean/page/regional-planning?field_alias_value=".get_post_meta($post->ID, 'field_alias',TRUE)
                            ?>
          <li><a href="<?php echo $link?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
            <?php the_title(); ?>
            </a></li>
          <?php
                        endwhile;
                    }?>
        </div>
      </div>
    </div>
  </div>
</div>
</div>