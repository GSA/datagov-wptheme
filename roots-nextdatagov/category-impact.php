<?php get_template_part('templates/category', 'intro'); ?>

<div class="container">

    <div class="row impact-buttons">
        <div class="topic-developers col-md-6"><a href="#data-stories"
                                                  class="btn btn-lg"><span><i></i>Data stories</span></a></div>
        <div class="topic-business col-md-6"><a href="#business-impact"
                                                class="btn btn-lg"><i></i><span>Business Impact</span></a></div>
    </div>


    <hr/>

    <h2 class="category-header topic-developers"><a name="data-stories"
                                                    href="#data-stories"><i></i><span>Data stories</span></a></h2>

    <?php include(locate_template('templates/content-data-stories.php')); ?>

    <?php

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

    <?php if (!$category_query->have_posts()) : ?>
        <div class="alert alert-warning">
            <?php _e('Sorry, no results were found.', 'roots'); ?>
        </div>
        <?php get_search_form(); ?>
    <?php endif; ?>

    <h2 class="category-header topic-business"><a name="business-impact" href="#business-impact"><i></i><span>Business impact</span></a>
    </h2>

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
</div>
