<?php
$category  = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>
<?php include('category-subnav.php'); ?>
<div class="container">
	<!-- Featured News -->
	<?php $category = get_the_category();
	$cat_slug       = $category[0]->slug;

	?>
	<?php

	$args = array(

		'announcements_and_news' => '	new_features-10',
	);
	$apps = new WP_Query( $args );
	if ( $apps->have_posts() ) {

		while ( $apps->have_posts() ) {

			$apps->the_post();
			?>
			<div id="cat-posts" class="All-cat-post horizontal_dotted_line cat-post">
				<div class="core">
					<div class="title">
						<?php the_title() ?>
					</div>
					<div class="body">
						<?php the_content() ?>
					</div>
					<br clear="all"/>
				</div>
			</div>
		<?php
		}
	}

	?>
	<br clear="all"/>
	<!-- Announcements -->
	<h1>Announcements</h1>
	<?php $category = get_the_category();
	$cat_slug       = $category[0]->slug;

	$args = array(

		'announcements_and_news' => 'announcement-10',
	);
	$apps = new WP_Query( $args );
	if ( $apps->have_posts() ) {

		while ( $apps->have_posts() ) {

			$apps->the_post();
			?>
			<div id="cat-posts" class="All-cat-post horizontal_dotted_line cat-post">
				<div class="core">
					<div class="title"><a href="<?php echo get_post_meta( $post->ID, 'link_to_url', true ); ?>">
							<?php the_title() ?>
						</a></div>
					<?php $postdate = strtotime( get_post_meta( $post->ID, 'field_original_post_date', true ) ); ?>
					<span><?php echo date( "m/d/y", $postdate ); ?></span>

					<div class="body">
						<?php the_content() ?>
					</div>
					<br clear="all"/>
				</div>
			</div>
		<?php
		}
	}

	?>

</div>