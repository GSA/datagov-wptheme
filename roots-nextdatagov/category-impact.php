<?php get_template_part( 'templates/category', 'intro' ); ?>


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
		'orderby' => 'meta_value_num', 
		'meta_key' => 'industry'
	);

	$category_query = new WP_Query( $args );

	?>

	<?php if ( ! $category_query->have_posts() ) : ?>
		<div class="alert alert-warning">
			<?php _e( 'Sorry, no results were found.', 'roots' ); ?>
		</div>
		<?php get_search_form(); ?>
	<?php endif; ?>


	<?php

	$industries = array();
	$headings = array();

	while ( $category_query->have_posts() ) {
		
		$category_query->the_post();

		$term = get_post_custom_values('industry');
		$term = $term[0];

		if(empty($industries[$term])) {
			$industries[$term] = get_category($term);	
		}
		
	}

	rewind_posts();

	?>
	<ul id="impact-topics" class="topics">
	<?php  foreach ($industries as $industry): ?>
		<li class="topic-<?php echo $industry->slug ?>"><a href="#<?php echo $industry->slug ?>"><i></i><span><?php echo $industry->name ?></span></a></li>
	<?php endforeach; ?>
	</ul>
<?php /*
	<div class="wrap content-page">

		<?php while ( $category_query->have_posts() ) : $category_query->the_post(); ?>
			<?php include(locate_template('templates/content-impact.php')); ?>			
		<?php endwhile; ?>
		
	</div>
*/ ?>

	<div class="row">
		<div class="col-sm-12 col-md-6">
			<div class="thumbnail">
				<img src="https://collegescorecard.ed.gov/img/hero-large.jpg" alt="...">
				<div class="caption">
					<h3>College Scorecard</h3>
					<p>Department of Education</p>
					<p><a href="#" class="btn btn-primary" role="button">Contact</a> <a href="/impacts/college-scorecard/" class="btn btn-default" role="button">Read more...</a></p>
				</div>
			</div>
		</div>
		<div class="col-sm-12 col-md-6">
			<div class="thumbnail">
				<img src="https://nebula.wsimg.com/76bc7274deb35b88aae0db199c44479a?AccessKeyId=1EC2C512A0E17DC8D90B&disposition=0&alloworigin=1" alt="...">
				<div class="caption">
					<h3>Open Data Summer Camp</h3>
					<p>Department of Agriculture (USDA)</p>
					<p><a href="#" class="btn btn-primary" role="button">Contact</a> <a href="/impacts/open-data-summer-camp/" class="btn btn-default" role="button">Read more...</a></p>
				</div>
			</div>
		</div>
	</div>


	<?php if ( $category_query->max_num_pages > 1 ) : ?>
		<nav class="post-nav">
			<?php your_pagination( $category_query ); ?>
		</nav>
	<?php endif; ?>
</div>
