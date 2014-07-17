<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>
<?php include('category-subnav.php'); ?>
<div class="container">
<!--  News -->
<?php $category = get_the_category();
					$cat_slug = $category[0]->slug;
						
				?>
<?php
	
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$args = array(
			'posts_per_page' => 20,
			'announcements_and_news'=>'in_the_news-10',
			'paged' => $paged
	);	
		$apps = new WP_Query( $args );
		$my_post_count = $apps->post_count;
		
		if( $apps->have_posts() ) {
			
			while( $apps->have_posts() ) {
			
				$apps->the_post();
				?>
<div id="cat-posts" class="All-cat-post horizontal_dotted_line cat-post">
  <div class="core">
    <div class="title"> <a href="<?php echo get_post_meta($post->ID, 'link_to_url', TRUE ); ?>">
      <?php the_title() ?>
      </a> </div>
    <?php $postdate=strtotime(get_post_meta($post->ID, 'field_original_post_date', TRUE )); ?>
    <span><?php echo date("m/d/y", $postdate); ?></span>
    <div class="body">
      <?php the_content() ?>
    </div>
    <br clear="all" />
  </div>
</div>
<?php
			}
		}
		
		$big = 999999999; // need an unlikely integer

echo paginate_links( array(
	'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
	'format' => '?paged=%#%',
	'current' => max( 1, get_query_var('paged') ),
	'total' => $apps->max_num_pages
) );
?>
</div>
