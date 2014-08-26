<?php get_template_part( 'templates/category', 'subnav' ); ?>


<?php if ( get_query_var( 'paged' ) < 1 ): ?>

	<?php get_template_part( 'templates/category', 'intro' ); ?>
	<?php get_template_part( 'templates/content', 'highlights' ); ?>
	<?php get_template_part( 'templates/category', 'apps' ); ?>	

<?php endif; ?>


<?php get_template_part( 'templates/category', 'posts' ); ?>
