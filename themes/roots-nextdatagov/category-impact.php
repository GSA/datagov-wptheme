<div class="container">


	<?php

	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 0;
	$args  = array(
		'post_type'      => 'post',
		'cat'            => get_query_var( 'cat' ),
		'tax_query'      => array(
			array(
				'taxonomy' => 'featured',
				'field'    => 'slug',
				'terms'    => array( 'highlights' ),
				'operator' => 'NOT IN'
			),
			array(
				'taxonomy' => 'featured',
				'field'    => 'slug',
				'terms'    => array( 'browse' ),
				'operator' => 'NOT IN'
			)
		),
		'paged'          => $paged,
		'posts_per_page' => 5
	);

	$category_query = new WP_Query( $args );

	?>

	<?php if ( ! $category_query->have_posts() ) : ?>
		<div class="alert alert-warning">
			<?php _e( 'Sorry, no results were found.', 'roots' ); ?>
		</div>
		<?php get_search_form(); ?>
	<?php endif; ?>



	<div class="wrap content-page">

		<?php while ( $category_query->have_posts() ) : $category_query->the_post(); ?>
			<?php get_template_part( 'templates/content', 'impact' ); ?>
		<?php endwhile; ?>
		
	</div>




	<?php if ( $category_query->max_num_pages > 1 ) : ?>
		<nav class="post-nav">
			<?php your_pagination( $category_query ); ?>
		</nav>
	<?php endif; ?>
</div>
