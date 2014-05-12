
<?php

// See if there is a frontpage for the category.

	$current_category_id = get_query_var( 'cat' );

	$args = array(
		'post_type'           => 'page',
		'ignore_sticky_posts' => 1,
		'cat'                 => $current_category_id,
		'tax_query'           => array(
			array(
				'taxonomy' => 'featured',
				'field'    => 'slug',
				'terms'    => array( 'browse' ),
				'operator' => 'IN'
			)
		),
		'posts_per_page'      => 10
	);

	$category_intro = new WP_Query( $args );

	

?>

<?php 
while ( $category_intro->have_posts() ) : 

	$category_intro->the_post(); 
	$this_category = get_the_category(); 

	if($this_category[0]->term_id == $current_category_id):		
?>

		<div class="intro">
			<div class="container">

				<?php the_content(); ?>

			</div>
		</div>

	<?php endif; ?>
<?php endwhile; ?>

