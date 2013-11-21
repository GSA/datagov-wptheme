<?php get_header(); ?>

<?php
$category = get_the_category();
$term_name = $category[0]->cat_name;
$term_slug = $category[0]->slug;
?>
<?php
$cat_name = $category[0]->cat_name;
$cat_slug = $category[0]->slug;
?>

<body class="single">


<div class="banner disclaimer">
    <p>This is a demonstration site exploring the future of Data.gov. <span id="stop-disclaimer"> Give us your feedback on <a href="https://twitter.com/usdatagov">Twitter</a>, <a href="http://quora.com">Quora</a></span>, <a href="https://github.com/GSA/datagov-design/">Github</a>, or <a href="http://www.data.gov/contact-us">contact us</a></p>
</div>


	<!-- Header Background Color, Image, or Visualization
	================================================== -->
    <div class="menu-container">
        <div class="header-next-top" >


            <?php get_template_part('navigation'); ?>



        </div>
    </div>
	<div class="next-header category <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">
	</div>


	<!-- Navigation & Search
	================================================== -->

	<div class="container">
		<div class="next-top category <?php foreach( get_the_category() as $cat ) { echo $cat->slug . '  '; } ?>">

			<?php get_template_part('category-search'); ?>

		</div> <!-- top -->

	</div>

		<div class="page-nav">	
		</div>

<div class="container">


    <div class="sixteen columns page-nav-items">

        <?php



        // show Links associated to a community
        // we need to build $args based either term_name or term_slug
        $args = array(
            'category_name'=> $term_slug, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
        wp_list_bookmarks($args);
        if (strcasecmp($term_name,$term_slug)!=0) {
            $args = array(
                'category_name'=> $term_name, 'categorize'=>0, 'title_li'=>0,'orderby'=>'rating');
            wp_list_bookmarks($args);
        }
        ?>

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
                            <?php if (get_post_format() == 'image'): ?>
                            <img class="scale-with-grid" src="<?php the_field('dataset_image'); ?>">
                            <?php elseif (get_post_format() == 'gallery'): ?>
                            <?php
                            $imagefile=get_field_object('field_52432c4d9b06f');
                            ?>

                            <img class="scale-with-grid" src="<?php echo $imagefile['value']; ?>">
                            <?php endif; ?>

                        </div>
					</div>
                                    <?php comments_template( '', true ); ?>
				    <?php endwhile; ?>  


					<?php else : ?>  

					    <h2 class="center">Not Found</h2>  
					    <p class="center">Sorry, but you are looking for something that isn't here.</p>  
					    <?php include (TEMPLATEPATH . "/searchform.php"); ?>  

			<?php endif; ?>  

			

			</div>

			</div> <!-- sixteen columns -->
<?php get_footer(); ?>
