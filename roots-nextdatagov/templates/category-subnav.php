<?php
$term_name = get_term_by( 'id', get_query_var( 'cat' ), 'category' )->name;
$term_slug = get_query_var( 'category_name' );

// show Links associated to a community
// we need to build $args based either term_name or term_slug
$args = array(
	'category_name' => $term_slug,
	'categorize'    => 0,
	'title_li'      => 0,
	'echo'          => 0,
	'orderby'       => 'rating'
);

$subnav = wp_list_bookmarks( $args );

if ( strcasecmp( $term_name, $term_slug ) != 0 ) {
	$args = array(
		'category_name' => $term_name,
		'categorize'    => 0,
		'title_li'      => 0,
		'echo'          => 0,
		'orderby'       => 'rating',
	);

	$subnav_extra = wp_list_bookmarks( $args );
}
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
if ( $subnav OR ( isset( $subnav_extra ) && $subnav_extra ) ):
	?>

	<div class="subnav banner">
		<div class="container">

			<?php if ( $subnav ): ?>

				<nav class="topic-subnav" role="navigation">
					<ul class="nav navbar-nav">
						<?php
						if ( in_array( $term_slug, $allowed_slug_arrays ) ) {
							wp_nav_menu( array(
								'theme_location' => 'climate_navigation',
								'menu_class'     => 'nav',
								'items_wrap'     => '%3$s'
							) );
						}
						?>
						<?php echo $subnav ?>
					</ul>
				</nav>

			<?php endif; ?>

			<?php if ( isset( $subnav_extra ) && $subnav_extra ): ?>
				<nav class="topic-subnav" role="navigation">
					<ul class="nav navbar-nav">
						<?php
						if ( in_array( $term_slug, $allowed_slug_arrays ) ) {
							wp_nav_menu( array(
								'theme_location' => 'climate_navigation',
								'menu_class'     => 'nav',
								'items_wrap'     => '%3$s'
							) );
						}
						?>
						<?php echo $subnav_extra ?>
					</ul>
				</nav>
			<?php endif; ?>

		</div>
	</div>

<?php else: ?>


	<?php 
		$sub_menu = wp_nav_menu( array('menu' => $term_slug, 'echo' => false, 'fallback_cb' => '', 'menu_class' => 'nav navbar-nav') ); 

		$valid_sub_menu = false;		

		if (!empty($sub_menu)) {

			$expected_html = 'ul id="menu-' . $term_slug . '"';

			if(strpos($sub_menu, $expected_html) == 1) {
				$valid_sub_menu = true;		
			}
			
		}

		
	?> 


	<?php if($valid_sub_menu): ?> 


		<div class="subnav banner">
			<div class="container">
				<nav class="topic-subnav" role="navigation">
					<?php echo $sub_menu; ?>
				</nav>
			</div>			
		</div>

	<?php endif; ?>


<?php endif; ?>

