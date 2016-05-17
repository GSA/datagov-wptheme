<?php
if (is_category()) {
    $cat_ID = get_query_var( 'cat' );

    $category =  get_category( $cat_ID );
    $term_name = $category->cat_name;
    $term_slug = $category->slug;

} else {
    $category = get_the_category();
    $term_name = $term_slug = '';
    if (isset($category[0])){
      $term_name = $category[0]->cat_name;
      $term_slug = $category[0]->slug;
    }
}

$args = array(
	'post_type'           => 'post',
	'ignore_sticky_posts' => 1,
	'tax_query'           => array(
		'relation' => 'AND',
		array(
			'taxonomy' => 'post_format',
			'field'    => 'slug',
			'terms'    => array( 'post-format-link', 'post-format-status', 'post-format-gallery' ),
			'operator' => 'NOT IN'
		),
		array(
			'taxonomy' => 'featured',
			'field'    => 'slug',
			'terms'    => array( 'highlights' ),
			'operator' => 'IN'
		)
	),
	'posts_per_page'      => 1
);

if ( is_category() ) {
	$args['cat'] = get_query_var( 'cat' );
}

$highlight_posts = new WP_Query( $args );

if ( ( $highlight_posts->have_posts() ) ):
	?>

	<section id="highlights" class="wrap wrap-lightblue">
		<div class="container">

			<div class="page-header">
				<h1>Highlights</h1>
			</div>

			<div id="highlightsCarousel" class="carousel highlights slide">
				<?php
				/**
				 * reset counter
				 */
				$checkFirst = 0;
				?>
				<!-- Carousel items -->
				<div id="highlightsCarouselInner" class="carousel-inner">
					<?php while ( $highlight_posts->have_posts() ) : $highlight_posts->the_post(); ?>
						<div
							class="highlight item <?php echo( ! $checkFirst ++ ? 'active' : '' ); ?> <?php get_category_by_slug( isset( $slug ) ? $slug : '' ) ?>">
							<header>
								<?php
								// TODO: Style this! Adding "FALSE" to hide until properly styled
								if ( false && ! is_category() && ! is_archive() ): ?>

									<h5 class="category">
										<?php
										$category = get_the_category();
										echo $category[0]->cat_name;
										?>
									</h5>

								<?php endif; ?>

								<h2 class="entry-title" style="float:left;"><?php the_title(); ?></h2>
								<?php if ( get_post_format() == 'image' ): ?>
									<div class="dataset-link btn-right" style="clear:none;margin:0px; width:180px;">
										<a class="btn btn-default"
										   href="<?php the_field( 'link_to_dataset' ); ?>">
											<span class="glyphicon glyphicon-download"></span> View this Dataset
										</a>
									</div>
								<?php endif; ?>
							</header>
							<br clear="all"/>
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="featured-image col-md-4">
									<?php the_post_thumbnail( 'medium' ); ?>
								</div>
							<?php endif; ?>

							<article
								class="<?php if ( has_post_thumbnail() ) : ?>col-md-8<?php else: ?>no-image<?php endif; ?>">
								<?php the_content(); ?>
							</article>
						</div><!--/.highlight-->
					<?php endwhile; ?>
				</div>
				<?php
				/**
				 * reset counter
				 */
				$checkFirst = 0;
				?>
				<div class="pull-right">
                    <a href="/<?php echo( (isset( $term_slug ) && $term_slug) ? $term_slug.'/highlights' : 'highlights' ) ?>" class="more-link" style="color:#fff;">More Highlights</a>
                </div>
			</div>
		</div>
		<!--/.container-->
	</section><!--/.wrap-lightblue-->

<?php

endif;
wp_reset_postdata();
