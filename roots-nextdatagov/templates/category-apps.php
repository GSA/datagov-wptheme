<?php
$categories = get_the_category();
?>

<div class="container">

	<?php

    $cat_id = get_query_var( 'cat' );
    if (!$cat_id) {
        $cat_id = $categories[0]->cat_ID;
    }

	$args  = array(
    	'post_type' => 'Applications',
		'cat'            => $cat_id,
		'tax_query'      => array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'featured',
				'field'    => 'slug',
				'terms'    => array( 'featured' ),
				'operator' => 'IN'
			)
		),
		'paged'          => 0,
		'posts_per_page' => 3
	);

	$category_query = new WP_Query( $args );

	?>


	<?php if($category_query->have_posts()): ?>
		<div class="page-header">
			<h1>Featured Apps</h1>
		</div>
	<?php endif; ?>

	<?php while ( $category_query->have_posts() ) : $category_query->the_post(); ?>
		<?php get_template_part( 'templates/content', 'featured-apps' ); ?>
	<?php endwhile; ?>

</div>
