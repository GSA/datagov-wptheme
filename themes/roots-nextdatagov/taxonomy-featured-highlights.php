<div class="wrap container">
  <div class="highlights-listing">

<?php 

$args = array(
  'type'                     => 'post',
  'child_of'                 => 0,
  'parent'                   => '',
  'orderby'                  => 'name',
  'order'                    => 'ASC',
  'hide_empty'               => 1,
  'hierarchical'             => 1,
  'exclude'                  => '112,71,73,79,64,82,65,62,63,70,74,59,67,26880,102,93,69,61,57,60,72,94,56,26881,26879,81,68,75,26882,26883,26877',
  'include'                  => '',
  'number'                   => '',
  'taxonomy'                 => 'category',
  'pad_counts'               => false 

);

  $categories = get_categories($args); 
  foreach ($categories as $category) {

    // heading - $category->cat_name

    $args = array( 
                  'post_type' => 'post',
                  'cat' => $category->cat_ID,
                  'tax_query' => array(
                                  'relation' => 'AND',                    
                                    array(
                                    'taxonomy' => 'post_format',
                                    'field' => 'slug',
                                    'terms' => array( 'post-format-link', 'post-format-status', 'post-format-gallery'),
                                    'operator' => 'NOT IN'
                                    ), 
                                    array(
                                    'taxonomy' => 'featured',
                                    'field' => 'slug',
                                    'terms' => array( 'highlights'),
                                    'operator' => 'IN'
                                    )                                 
                                  ),                 
                  'posts_per_page' => 10 );


      $category_query = new WP_Query($args);
      ?>


      <?php if ($category_query->have_posts()): ?>
            <h1 class="category category-header topic-<?php echo $category->slug;?>">
              <a href="/<?php echo $category->slug;?>"><i></i>
              <span><?php echo $category->cat_name;?></span></a>
            </h1>
      <?php endif; ?>

      <?php 
      while ($category_query->have_posts()) { 
          $category_query->the_post();
      ?>

	      <div class="highlight <?php isset( $slug ) ? get_category_by_slug( $slug ) : false; ?>">
		      <header>
		      <?php if(!is_category() && !is_archive()): ?>
                        <h5 class="category"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></h5>
                    <?php endif; ?>
                    
                    <h2 class="entry-title"><?php the_title(); ?></h2>
                </header>
                           
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="featured-image col-md-4">
                        <?php the_post_thumbnail('medium'); ?>
                    </div>
                <?php endif; ?>

                <article class="<?php if ( has_post_thumbnail() ) : ?>col-md-8<?php else: ?>no-image<?php endif;?>">
                <?php the_content(); ?>
                </article>

                <?php if(get_post_format() == 'image'): ?>    
                    <div class="dataset-link">
                        <a class="btn btn-default pull-right" href="<?php the_field('link_to_dataset'); ?>">
                          <span class="glyphicon glyphicon-download"></span> View this Dataset
                        </a>
                    </div>            
                <?php endif;?>   

            </div><!--/.highlight-->

        <?php
        wp_reset_postdata();    
        ?>

      <?php
      }

  }
?>

</div>
</div>
