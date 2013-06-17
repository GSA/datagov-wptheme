<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->


<?php get_template_part('header'); ?>

<body>

	<div class="banner disclaimer">
	<p>This is a preview of the future direction of Data.gov. <span> Give us your feedback on <a href="https://twitter.com/ProjectOpenData">Twitter</a> or <a href="http://quora.com">Quora</a></span></p>
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
										<img src="<?php the_field('twitter_photo'); ?>" height="40" width="40">
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
								<div class="lead-image"><a href="<?php the_field('link_to_dataset'); ?>"><img class="scale-with-grid" src="<?php the_field('dataset_image'); ?>"></a></div>
							</div>
							<div class="core">
								<div class="title"><a href="<?php the_field('link_to_dataset'); ?>"><?php the_title(); ?></a></div>
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


<script>
$(function () {
  var
    $demo = $('#rotate-stats'),
    strings = JSON.parse($demo.attr('data-strings')).targets,
     randomString;

  randomString = function () {
    return strings[Math.floor(Math.random() * strings.length)];
  };

  $demo.fadeTo(randomString());
  setInterval(function () {
    $demo.fadeTo(randomString());
  }, 5000);
});
</script>

<script src="<?php echo get_bloginfo('template_directory'); ?>/js/v1.js"></script>
<script src="<?php echo get_bloginfo('template_directory'); ?>/js/autosize.js"></script>

<!-- End Document
================================================== -->
</body>


</html>