<?php get_header(); ?>


	<div class="banner disclaimer">
	<p>This is a demonstration site exploring the future of Data.gov. <span> Give us your feedback on <a href="https://twitter.com/ProjectOpenData">Twitter</a> or <a href="http://quora.com">Quora</a></span></p>
	</div>


	<!-- Header Background Color, Image, or Visualization
	================================================== -->
	
	<div class="next-header category <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">
	</div>


	<!-- Navigation & Search
	================================================== -->

	<div class="container">
		<div class="next-top category <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">

			<?php get_template_part('navigation'); ?>
			<?php get_template_part('category-search'); ?>

		</div> <!-- top -->

	</div>

		<div class="page-nav">	
		</div>

<div class="container">


		<div class="sixteen columns page-nav-items">

			<?php
			$args = array(
				'category_name'=>get_query_var('category_name'), 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
			wp_list_bookmarks($args); ?>

		</div>

	<!-- WordPress Content
	================================================== -->

		<div class="content">

			<div class="sixteen columns">

			<?php if (have_posts()) : ?>  
				    <?php while (have_posts()) : the_post(); ?>

				    <div class="single-post">
		
						<div class="title"><?php the_title(); ?></div>
						<div class="body">
										<?php the_content('Read the rest of this entry »'); ?>  
						</div>
					</div>

				    <?php endwhile; ?>  

					<?php else : ?>  

					    <h2 class="center">Not Found</h2>  
					    <p class="center">Sorry, but you are looking for something that isn't here.</p>  
					    <?php include (TEMPLATEPATH . "/searchform.php"); ?>  

			<?php endif; ?>  

			

			</div>

			</div> <!-- sixteen columns -->

<?php get_footer(); ?>
