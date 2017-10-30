<?php get_template_part('templates/category', 'intro'); ?>

<div class="container">
  <?php
  $stories_per_page = 3;
  $args = array(
      'post_type' => 'impact',
      'public' => true,
      'posts_per_page' => $stories_per_page
  );
  $the_query = new WP_Query($args);

  function remove_more_link_scroll($link)
  {
      $link = preg_replace('|#more-[0-9]+|', '', $link);
      return $link;
  }
  add_filter('the_content_more_link', 'remove_more_link_scroll');
  $i = 0;

  $paged = (get_query_var('paged')) ? get_query_var('paged') : 0;
  $args = array(
      'post_type' => 'post',
      'cat' => get_query_var('cat'),
      'tax_query' => array(
          array(
              'taxonomy' => 'featured',
              'field' => 'slug',
              'terms' => array('highlights'),
              'operator' => 'NOT IN'
          ),
          array(
              'taxonomy' => 'featured',
              'field' => 'slug',
              'terms' => array('browse'),
              'operator' => 'NOT IN'
          )
      ),
      'orderby' => 'meta_value_num',
      'meta_key' => 'industry'
  );

  $category_query = new WP_Query($args);

  ?>

  <?php if($the_query->have_posts() && $category_query->have_posts()) : ?>
    <div class="row impact-buttons">
        <div class="col-md-6"><a href="#data-stories" class="btn btn-lg">
                <i class="fa fa-book fa-2" aria-hidden="true"></i><span>Data Stories</span></a>
        </div>
        <div class="col-md-6"><a href="#business-impact" class="btn btn-lg">
                <i class="fa fa-bar-chart-o fa-2" aria-hidden="true"></i><span>Business Impact</span></a>
        </div>
    </div>
  <?php endif; ?>
  <?php if($the_query->have_posts()) : ?>
    <hr/>
    <h4 class="category-header"><a name="data-stories" href="#data-stories">
            <i class="fa fa-book" aria-hidden="true"></i><span>Data Stories</span></a></h4>

    <?php include(locate_template('templates/content-data-stories.php')); ?>
  <?php endif; ?>
  <?php if($category_query->have_posts()) : ?>
    <?php if (!$category_query->have_posts()) : ?>
        <div class="alert alert-warning">
            <?php _e('Sorry, no results were found.', 'roots'); ?>
        </div>
        <?php get_search_form(); ?>
    <?php endif; ?>
    <hr/>
    <h4 class="category-header"><a name="business-impact" href="#business-impact">
            <i class="fa fa-bar-chart-o" aria-hidden="true"></i><span>Business Impact</span></a>
    </h4>

    <?php

    $industries = array();
    $headings = array();

    while ($category_query->have_posts()) {

        $category_query->the_post();

        $term = get_post_custom_values('industry');
        $term = $term[0];

        if (empty($industries[$term])) {
            $industries[$term] = get_category($term);
        }

    }

    rewind_posts();

    ?>

    <ul id="impact-topics" class="topics">
        <?php foreach ($industries as $industry): ?>
            <li class="topic-<?php echo $industry->slug ?>"><a
                    href="#<?php echo $industry->slug ?>"><i></i><span><?php echo $industry->name ?></span></a></li>
        <?php endforeach; ?>
    </ul>

    <div class="wrap content-page">

        <?php while ($category_query->have_posts()) : $category_query->the_post(); ?>
            <?php include(locate_template('templates/content-impact.php')); ?>
        <?php endwhile; ?>

    </div>


    <?php if ($category_query->max_num_pages > 1) : ?>
        <nav class="post-nav">
            <?php your_pagination($category_query); ?>
        </nav>
    <?php endif; ?>
  <?php endif; ?>
</div>
