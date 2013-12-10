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
                  'posts_per_page' => 5 );


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
          get_template_part('templates/content', 'highlights'); 
      }

  }
?>

</div>
</div>