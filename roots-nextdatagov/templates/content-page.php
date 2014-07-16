<?php include('category-subnav.php'); ?>


<div class="wrap container content-page">

	<?php while ( have_posts() ) : the_post(); ?>

		<?php if ( has_category() && get_the_category()[0]->slug !== 'uncategorized' ): ?>

			<h1 class="page-title">
				<?php the_title(); ?>
			</h1>

		<?php endif; ?>

		<?php the_content(); ?>
		<?php wp_link_pages( array( 'before' => '<nav class="pagination">', 'after' => '</nav>' ) ); ?>
	<?php endwhile; ?>
</div>
