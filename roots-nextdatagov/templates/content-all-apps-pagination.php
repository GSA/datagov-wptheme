<?php
$category = get_the_category();
if ( $category ) {
	$cat_name = $term_name = $category[0]->cat_name;
	$cat_slug = $term_slug = $category[0]->slug;

// show Links associated to a community
// we need to build $args based either term_name or term_slug
	if ( ! empty( $term_slug ) ) {
		$args      = array(
			'category_name' => $term_slug,
			'categorize'    => 0,
			'title_li'      => 0,
			'orderby'       => 'rating',
			'echo'          => 0
		);
		$bookmarks = wp_list_bookmarks( $args );
	}
	if ( strcasecmp( $term_name, $term_slug ) != 0 ) {
		$args      = array(
			'category_name' => $term_name,
			'categorize'    => 0,
			'title_li'      => 0,
			'orderby'       => 'rating',
			'echo'          => 0
		);
		$bookmarks = wp_list_bookmarks( $args );
	}


	if ( $bookmarks ): ?>

		<div class="subnav banner">
			<div class="container">
				<nav role="navigation" class="topic-subnav">
					<ul class="nav navbar-nav">
						<?php

						echo $bookmarks;

						?>
					</ul>
				</nav>
			</div>
		</div>

	<?php endif;

}
?>






<div class="intro">

    <div class="container">
    
	    <?php while( have_posts() ) : the_post(); ?>

	      <div class="Apps-post" id="post-<?php the_ID(); ?>">
	        <?php the_content();   ?>
	      </div>
	        
	    <?php endwhile; ?>

    </div>

</div>






	<!-- Application featured taxonomy-->
<?php
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$args_featured = array(
	'post_type'      => 'Applications',
	'posts_per_page' => - 1,
	'post_status'    => 'publish',
	'orderby'        => 'modified',
	'tax_query'      => array(
		'relation' => 'AND',
		array(
			'taxonomy' => 'featured',
			'field'    => 'slug',
			'terms'    => array( 'highlights' ),
		),
	),
);

$args_nonfeatured = array(
	'post_type'      => 'Applications',
	'posts_per_page' => - 1,
	'post_status'    => 'publish',
	'orderby'        => 'modified',
	'tax_query'      => array(
		'relation' => 'AND',
		array(
			'taxonomy' => 'featured',
			'field'    => 'slug',
			'terms'    => array( 'highlights' ),
			'operator' => 'NOT IN'
		)
	),
);


$result_featured = new WP_Query( $args_featured );
wp_reset_query();
$featured = array();
$i        = 0;
while ( $result_featured->have_posts() ) {
	$result_featured->the_post();
	$featured[ $i ]['title']                 = get_the_title( $post->ID );
	$featured[ $i ]['conent']                = get_the_content( $post->ID );
	$featured[ $i ]['field_application_url'] = get_post_meta( $post->ID, 'field_application_url', true );
	$imagefile                               = get_field_object( 'field_5240b9c982f41' );
	$featured[ $i ]['image_url']             = $imagefile['value']['url'];
	$featured[ $i ]['image_alt']             = $imagefile['value']['alt'];
	$featured[ $i ]['featured']              = true;
	$i ++;
}

$result_nonfeatured = new WP_Query( $args_nonfeatured );
$not_featured       = array();
$i                  = 0;
while ( $result_nonfeatured->have_posts() ) {
	$result_nonfeatured->the_post();
	$not_featured[ $i ]['title']                 = get_the_title( $post->ID );
	$not_featured[ $i ]['conent']                = get_the_content( $post->ID );
	$not_featured[ $i ]['field_application_url'] = get_post_meta( $post->ID, 'field_application_url', true );
	$imagefile                                   = get_field_object( 'field_5240b9c982f41' );
	$not_featured[ $i ]['image_url']             = $imagefile['value']['url'];
	$not_featured[ $i ]['image_alt']             = $imagefile['value']['alt'];
	$not_featured[ $i ]['featured']              = false;
	$i ++;
}
wp_reset_query();
$apparray      = array_merge( $featured, $not_featured );
$total_apps    = count( $apparray );
$apps_per_page = 10;
if ( isset( $apparray ) ) {
	$total_pages = ceil( $total_apps / $apps_per_page );
} else {
	$total_pages = 1;
	$total_apps  = 0;
}
if ( isset( $_GET['currentpage'] ) && is_numeric( $_GET['currentpage'] ) ) {
	$currentpage = (int) $_GET['currentpage'];
} else {
	$currentpage = 1;
}
if ( $currentpage > $total_pages ) {
	$currentpage = $total_pages;
}
if ( $currentpage < 1 ) {
	$currentpage = 1;
}
$start = ( $currentpage - 1 ) * $apps_per_page + 1;
?>

<div class="container">
	<div class="Apps-wrapper">
		<div class="Mobile-post" id="post-<?php //$term->slug; ?>">			
			<?php
			for ( $i = $start - 1; $i < $start - 1 + $apps_per_page; $i ++ ) {
				if ( isset( $apparray[ $i ] ) ) {
					?>
					<div class="webcontainer <?php the_ID(); ?>">
						<div id="webimage">
							<img class="scale-with-grid" src="<?php echo $apparray[ $i ]['image_url']; ?>"
							     alt="<?php echo $apparray[ $i ]['image_alt']; ?>">
						</div>
						<div id="webcontent">
							<h2><a href="<?php
								echo $apparray[ $i ]['field_application_url']; ?>">
									<?php echo $apparray[ $i ]['title']; ?>
								</a></h2>

							<div class='content'>
								<div id="webtext">
									<?php echo $apparray[ $i ]['conent']; ?>
								</div>
							</div>
						</div>
						<br clear="all"/>
					</div>
				<?php
				}
			}
			?>
			<br clear="all"/>
		</div>

	</div>
	<div class='pagination'>
		<p class="counter">
			<?php printf( __( 'Page %1$s of %2$s' ), $currentpage, $total_pages ); ?>
		</p>
		<?php
		customPagination( 'developer-apps-showcase', $currentpage, $total_pages, true );
		?>
	</div>
</div>

<?php

function customPagination( $base_url, $cur_page, $number_of_pages, $prev_next = false ) {
	$ends_count   = 1; //how many items at the ends (before and after [...])
	$middle_count = 2; //how many items before and after current page
	$dots         = false;
	$nextpage     = $cur_page + 1;
	$prevpage     = $cur_page - 1;
	$output       = "<ul class='pagination'>";
	?>

	<?php
	if ( $prev_next && $cur_page && 1 < $cur_page ) { //print previous button?
		$output .= "<li class='pagination-prev'><a class='prev page-numbers pagenav local-link' href='?currentpage=$prevpage'>Previous</a> </li>";
	}
	for ( $i = 1; $i <= $number_of_pages; $i ++ ) {
		if ( $i == $cur_page ) {
			$output .= "<li><span class='page-numbers pagenav current'> $i </span></li>";
			$dots = true;
		} else {
			if ( $i <= $ends_count || ( $cur_page && $i >= $cur_page - $middle_count && $i <= $cur_page + $middle_count ) || $i > $number_of_pages - $ends_count ) {
				$output .= "<li><a class='page-numbers pagenav' href='?currentpage=$i'> $i </a></li>";
				$dots = true;
			} elseif ( $dots ) {
				$output .= '<li><span class="page-numbers dots">' . __( '&hellip;' ) . '</span></li>';
				$dots = false;
			}
		}
	}
	if ( $prev_next && $cur_page && ( $cur_page < $number_of_pages || - 1 == $number_of_pages ) ) { //print next button?
		$output .= " <li class='pagination-next'> <a href='?currentpage=$nextpage'> Next</a></li> ";
	}
	?>
	<?php
	$output .= "</ul>";
	print $output;
}

?>