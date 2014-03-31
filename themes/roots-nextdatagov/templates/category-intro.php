
<?php

// See if there is a frontpage for the category.

	$args = array(
		'post_type'           => 'page',
		'ignore_sticky_posts' => 1,
		'cat'                 => get_query_var( 'cat' ),
		'tax_query'           => array(
			array(
				'taxonomy' => 'featured',
				'field'    => 'slug',
				'terms'    => array( 'browse' ),
				'operator' => 'IN'
			)
		),
		'posts_per_page'      => 1
	);

	$category_intro = new WP_Query( $args );

?>



<?php while ( $category_intro->have_posts() ) : $category_intro->the_post(); ?>
	<div class="intro">
		<div class="container">
			<?php the_content(); ?>
		</div>
	</div>
<?php endwhile; ?>

