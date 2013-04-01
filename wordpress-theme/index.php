<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->


<?php get_template_part('header'); ?>

<body>



	<!-- Header Background Color, Image, or Visualization
	================================================== -->
	
	<div class="header">
	</div>


	<!-- Navigation & Search
	================================================== -->

	<div class="container">
		<div class="top">

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



				<?php if (have_posts()) : ?>  
				    <?php while (have_posts()) : the_post(); ?>

				    <?php if (get_post_format() == 'status'): ?>

				    	<!-- Content - Tweet -->
						<div class="post small tweet">
							<div class="core">
								<div class="tweet-author">
									<div class="author-image">
										<img src="https://si0.twimg.com/profile_images/2277589677/bh8ffk8e7xar0tk7bmiw.jpeg" height="40" width="40">
									</div>
									<div class="author-details">
										Valerie Jarrett - <a href="http://twitter.com/vj44">@vj44</a>
									</div>
								</div>
								<div class="body">
									<?php the_content('Read the rest of this entry »'); ?> 
								</div>
							</div>
							<div class="meta">
								<div class="timestamp"><?php the_time('F jS, Y') ?></div>
								<div class="corner">
									<div class="block"></div>
									<div class="topic">Health</div>
								</div>
							</div>
						</div>

					<?php elseif (get_post_format() == 'link'): ?>

						<!-- Content - Link -->
						<div class="post small link">
							<div class="core">
								<div class="source"><a href="#">New York Times</a></div>
								<div class="body">
									<?php the_content('Read the rest of this entry »'); ?>
								</div>
							</div>
							<div class="meta">
								<div class="timestamp"><?php the_time('F jS, Y') ?></div>
								<div class="corner">
									<div class="block"></div>
									<div class="topic">Health</div>
								</div>
							</div>
						</div>

					<?php elseif (get_post_format() == 'image'): ?>

						<!-- Content - dataset -->
						<div class="post small dataset">
							<div class="lead">
								<div class="lead-image"><img class="scale-with-grid" src="<?php echo get_bloginfo('template_directory'); ?>/assets/dataset.png"></div>
							</div>
							<div class="core">
								<div class="title"><?php the_title(); ?></div>
								<div class="body">
									<?php the_content('Read the rest of this entry »'); ?>
								</div>
							</div>
							<div class="meta">
								<div class="timestamp"><?php the_time('F jS, Y') ?></div>
								<div class="corner">
									<div class="block"></div>
									<div class="topic">Health</div>
								</div>
							</div>
						</div>

					<?php elseif (get_post_format() == ''): ?>

						<!-- Content - Blog Post -->
					    <div class="post medium blog" id="post-<?php the_ID(); ?>">  

							<div class="core">
								<div class="title"><?php the_title(); ?></div>
								<div class="body">
									<?php the_content('Read the rest of this entry »'); ?>  
								</div>
							</div>
							<div class="meta">
								<div class="timestamp"><?php the_time('F jS, Y') ?></div>
								<div class="corner">
									<div class="block"></div>
									<div class="topic">Health</div>
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

		</div> <!-- content -->

	</div><!-- container -->

<script>
$(window).load(function(){
  $('#posts').masonry({
    // options
    columnWidth: 287,
    itemSelector : '.post',
    isResizable: true,
    isAnimated: true,
    gutterWidth: 25
  });
});
</script>


<!-- End Document
================================================== -->
</body>


</html>