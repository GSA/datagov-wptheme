<?php get_header(); ?>

	<div class="banner disclaimer">
	<p>This is a demonstration site exploring the future of Data.gov. <span id="stop-disclaimer"> Give us your feedback on <a href="https://twitter.com/ProjectOpenData">Twitter</a> or <a href="http://quora.com">Quora</a></span></p>
	</div>


	<!-- Header Background Color, Image, or Visualization
	================================================== -->
	
	<div class="next-header">

		<div id="data-viz"></div>

	</div>


	<!-- Navigation & Search
	================================================== -->

	<div class="container">
		<div class="next-top">

			<?php get_template_part('navigation'); ?>
			<?php get_template_part('primary-search'); ?>

		</div> <!-- top -->


	<!-- WordPress Content
	================================================== -->

		<div class="content">

			<div class="sixteen columns">
			<div id="posts">

				<!-- lead-image -->
				<!-- source -->
				<!-- title -->
				<!-- body -->
				<!-- timestamp -->
				<!-- topic -->


				<?php query_posts('meta_key=featured_datagov&meta_value=Yes&ignore_sticky_posts=1'); ?>

				<?php if (have_posts()) : ?>  
				    <?php while (have_posts()) : the_post(); ?>

				    <?php if (get_post_format() == 'status'): ?>

				    	<!-- Content - Tweet -->
						<div class="post small tweet">
							<div class="core">
								<div class="tweet-author">
									<div class="author-image">
										<img alt="" src="<?php the_field('twitter_photo'); ?>" height="40" width="40">
									</div>
									<div class="author-details">
										<?php the_field('persons_name'); ?> - <a href="<?php the_field('link_to_tweet'); ?>">@<?php the_field('twitter_handle'); ?></a>
									</div>
								</div>
								<div class="body">
									<?php the_content('Read the rest of this entry »'); ?> 
								</div>
							</div>
							<div class="meta">
								<div class="timestamp"><?php the_time('F jS, Y') ?></div>
								<div class="corner <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">
									<div class="block"></div>
									<div class="topic"><a href="<?php $category = get_the_category(); echo get_category_link($category[0]->cat_ID);?>"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></a></div>
								</div>
							</div>
						</div>

					<?php elseif (get_post_format() == 'link'): ?>

						<!-- Content - Link -->
						<div class="post small link">
							<div class="core">
								<div class="source"><a href="<?php the_field('link_to_url'); ?>"><?php the_field('source'); ?></a></div>
								<div class="body">
									<?php the_content('Read the rest of this entry »'); ?>
								</div>
							</div>
							<div class="meta">
								<div class="timestamp"><?php the_time('F jS, Y') ?></div>
								<div class="corner <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">
									<div class="block"></div>
									<div class="topic"><a href="<?php $category = get_the_category(); echo get_category_link($category[0]->cat_ID);?>"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></a></div>
								</div>
							</div>
						</div>

					<?php elseif (get_post_format() == 'image'): ?>

						<!-- Content - dataset -->
						<div class="post small dataset">
							<div class="lead">
								<div class="lead-image"><a href="<?php the_field('link_to_dataset'); ?>" target="_blank"><img class="scale-with-grid" src="<?php the_field('dataset_image'); ?>"></a></div>
							</div>
							<div class="core">
								<div class="title"><a href="<?php the_field('link_to_dataset'); ?>" target="_blank"><?php the_title(); ?></a></div>
								<div class="body">
									<?php the_content('Read the rest of this entry »'); ?>
								</div>
							</div>
							<div class="meta">
								<div class="timestamp"><?php the_time('F jS, Y') ?></div>
								<div class="corner <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">
									<div class="block"></div>
									<div class="topic"><a href="<?php $category = get_the_category(); echo get_category_link($category[0]->cat_ID);?>"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></a></div>
								</div>
							</div>
						</div>

					<?php elseif (get_post_format() == ''): ?>

						<!-- Content - Blog Post -->
					    <div class="post small blog" id="post-<?php the_ID(); ?>">  

							<div class="core">
								<div class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
								<div class="body">
									<?php the_content('Read the rest of this entry »'); ?>  
								</div>
							</div>
							<div class="meta">
								<div class="timestamp"><?php the_time('F jS, Y') ?></div>
								<div class="corner <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">
									<div class="block"></div>
									<div class="topic"><a href="<?php $category = get_the_category(); echo get_category_link($category[0]->cat_ID);?>"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></a></div>
								</div>
							</div>
					    </div>  

					<?php endif; ?>

				    <?php endwhile; ?>  

				<?php else : ?>  
				    <h2 class="center">Not Found</h2>  
				    <p class="center">Sorry, but you are looking for something that isn't here.</p>  
				    <?php include (TEMPLATEPATH . "/searchform.php"); ?>  
				<?php endif; ?>  


			</div> <!-- posts -->
			</div> <!-- sixteen columns -->

<?php get_footer(); ?>
