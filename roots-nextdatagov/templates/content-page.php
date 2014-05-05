<?php
$category = get_the_category();
if ( $category ) {
	$cat_name = $term_name = $category[0]->cat_name;
	$cat_slug = $term_slug = $category[0]->slug;

	$allowed_slug_arrays = array(
		"climate-ecosystems",
		"coastalflooding",
		"energysupply",
		"foodsupply",
		"humanhealth",
		"transportation",
		"water",
		"climate"
	);

	?>
	<div class="subnav banner">
		<div class="container">
			<nav role="navigation" class="topic-subnav">
				<ul class="nav navbar-nav">
					<?php
					// show Links associated to a community
					// we need to build $args based either term_name or term_slug
					if ( in_array( $term_slug, $allowed_slug_arrays ) ) {
						wp_nav_menu( array(
							'theme_location' => 'climate_navigation',
							'menu_class'     => 'nav',
							'items_wrap'     => '%3$s'
						) );
					}
					if ( ! empty( $term_slug ) ) {
						$args = array(
							'category_name' => $term_slug,
							'categorize'    => 0,
							'title_li'      => 0,
							'orderby'       => 'rating'
						);
						wp_list_bookmarks( $args );
					}
					if ( strcasecmp( $term_name, $term_slug ) != 0 ) {
						$args = array(
							'category_name' => $term_name,
							'categorize'    => 0,
							'title_li'      => 0,
							'orderby'       => 'rating'
						);
						wp_list_bookmarks( $args );
					}


					?>
				</ul>
			</nav>
		</div>
	</div>
<?php
}
?>
<div class="wrap container content-page">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php if ( has_category() && $cat_slug !== 'uncategorized' ): ?>

			<h1 class="page-title">
				<?php the_title(); ?>
			</h1>

		<?php endif; ?>

		<?php the_content(); ?>
		<?php wp_link_pages( array( 'before' => '<nav class="pagination">', 'after' => '</nav>' ) ); ?>
	<?php endwhile; ?>
</div>
