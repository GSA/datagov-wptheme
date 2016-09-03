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
<?php include('category-subnav.php'); ?>
</div>
<div class="container">
<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <header>
      <?php get_template_part('templates/entry-meta'); ?>
    </header>
    <div class="entry-content">
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
                $values = false;
                if(function_exists('get_field')) {
                  $values = get_field('group');
                }
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
    <footer>
      <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
    </footer>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?>
</div>
