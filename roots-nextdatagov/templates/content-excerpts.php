<section class="updates">


	<div class="page-header">
		<h1>Updates</h1>
	</div>


	<?php
	$args = array(
		'post_type'           => 'post',
		'ignore_sticky_posts' => 1,
		'cat'				  => '-33884',
		'tax_query'           => array(
			array(
				'taxonomy' => 'post_format',
				'field'    => 'slug',
				'terms'    => array(
					'post-format-link',
					'post-format-status',
					'post-format-gallery',
					'post-format-image'
				),
				'operator' => 'NOT IN'
			),
			array(
				'taxonomy' => 'featured',
				'field'    => 'slug',
				'terms'    => array( 'highlights' ),
				'operator' => 'NOT IN'
			),
			array( // This filter is in case an intro Page is accidentally added as a Post
				'taxonomy' => 'featured',
				'field'    => 'slug',
				'terms'    => array( 'browse' ),
				'operator' => 'NOT IN'
			)
		),
		'posts_per_page'      => 3
	);

	$new_query = new WP_Query( $args );

	?>

	<?php while ( $new_query->have_posts() ) : $new_query->the_post(); ?>

		<article <?php post_class(); ?>>
			<header>
				<?php $category = get_the_category() ?>
				<h5 class="category category-header topic-<?php echo $category[0]->slug; ?>"><a
						href="/<?php echo $category[0]->slug; ?>"><i></i><span><?php echo $category[0]->cat_name; ?></span></a>
				</h5>

				<h2 class="entry-title"><a id="<?php echo 'post-title-' . get_the_ID(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<?php get_template_part( 'templates/entry-meta-author' ); ?>
			</header>
			<div class="entry-summary">
				<?php
				remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );
				add_filter( 'get_the_excerpt', 'datagov_custom_keep_my_links' );
				the_excerpt();
				?>
			</div>
		</article>

	<?php endwhile; ?>

	<?php
	wp_reset_postdata();
	?>

	<?php
	//'terms' => array( 'post-format-link', 'post-format-status', 'post-format-gallery', 'post-format-image' ),

	$args = array(
		'post_type'           => 'post',
		'orderby'             => 'date',
		'ignore_sticky_posts' => 1,
		'tax_query'           => array(
			array(
				'taxonomy' => 'post_format',
				'field'    => 'slug',
				'terms'    => array( 'post-format-status' ),
			),
			array(
				'taxonomy' => 'featured',
				'field'    => 'slug',
				'terms'    => array( 'highlights' ),
				'operator' => 'NOT IN'
			)
		),
		'meta_query'          => array(
			array(
				'key'     => 'twitter_handle',
				'value'   => 'usdatagov',
				'compare' => '='
			)
		),
		'posts_per_page'      => 3
	);

	$new_query = new WP_Query( $args );

	?>

	<?php while ( $new_query->have_posts() ) : $new_query->the_post(); ?>

		<article <?php post_class( 'col-md-4 col-lg-4' ); ?>>
            <header>
                <div class="tweet-author">
                <span class="author-image">
                    <img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/twitter.png"  alt="twitter">
                </span>
                    <a class="author-link" href="https://twitter.com/<?php the_field( 'twitter_handle' ); ?>">
                        <div>
                    <span class="author-name">
                        <?php the_field( 'persons_name' ); ?>
                    </span>
                        <span class="author-handle">
                            @<?php the_field( 'twitter_handle' ); ?>
                        </span>
                        </div>
                    </a>
                </div>
                <div class="tweet-date">
                    <?php the_time('F j, Y') ?>
                </div>
            </header>
			<div class="body">
				<?php the_content( 'Read the rest of this entry »' ); ?>
			</div>
		</article>

	<?php endwhile; ?>

	<?php
	wp_reset_postdata();
	?>
</section>

