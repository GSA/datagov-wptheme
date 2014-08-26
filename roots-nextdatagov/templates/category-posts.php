<?php
$categories = get_the_category();
$separator = ' ';
$output = '';
if($categories){
    foreach($categories as $category) {
        $category_array[] .= $category->slug;
    }
}
$urlslice = explode("/", $_SERVER[REQUEST_URI]);
if(!empty($category_array) && in_array($urlslice[1],$category_array) && $urlslice[2]=="page" ){
    ?>
<script>
    jQuery(document).ready(function($){
        var slug = "/<?php echo $urlslice[1]?>/";
        jQuery('.topic-subnav ul.nav a').each(function() {
            if (jQuery(this).attr('href')  ===  slug) {
                jQuery(this).addClass('active');
            }
        });
    });
</script>
<?php
}
?>
<div class="container">
	<div class="page-header">
		<h1>Updates</h1>
	</div>

	<?php
    $cat_id = get_query_var( 'cat' );
    if (!$cat_id) {
        $cat_id = $categories[0]->cat_ID;
    }
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 0;
	$args  = array(
		'post_type'      => 'post',
		'cat'            => $cat_id,
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

	<?php while ( $category_query->have_posts() ) : $category_query->the_post(); ?>
		<?php get_template_part( 'templates/content', get_post_format() ); ?>
	<?php endwhile; ?>

	<?php if ( $category_query->max_num_pages > 1 ) : ?>
		<nav class="post-nav">
			<?php your_pagination( $category_query ); ?>
		</nav>
	<?php endif; ?>
</div>
