<?php
$args = array( 
                'post_type' => 'post',
                'ignore_sticky_posts' => 1,  
                'tax_query' => array(
    		                        'relation' => 'AND',                    
                	                array(
                	                'taxonomy' => 'post_format',
                	                'field' => 'slug',
                	                'terms' => array( 'post-format-link', 'post-format-status', 'post-format-gallery'),
                	                'operator' => 'NOT IN'
                	                ), 
                	                array(
                	                'taxonomy' => 'featured',
                	                'field' => 'slug',
                	                'terms' => array( 'highlights'),
                	                'operator' => 'IN'
                	                )                	                
                                ),                 
                'posts_per_page' => 1 );
                
if (is_category()) $args['cat'] = get_query_var('cat');
         
$highlight_posts = new WP_Query($args);

?>

<section id="highlights" class="wrap wrap-lightblue">
<div class="container">

<?php while ($highlight_posts->have_posts()) : $highlight_posts->the_post(); ?>
    <div class="page-header">
      <div class="col-md-12"><h1>Highlights</h1></div>
    </div>
    <div class="highlight">
        <div class="col-md-12"><h2 class="entry-title"><?php the_title(); ?></h2></div>
    	<div class="col-md-8">
    		<?php the_content(); ?>                  
    	</div>
    	<div class="col-md-4">
    		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra metus vitae iaculis mattis. Nam gravida dictum dui, sit amet congue odio pulvinar in. Quisque vitae dictum elit. In non leo quis tellus</p>
    		<button>Button One</button>
    		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra metus vitae iaculis mattis. Nam gravida dictum dui, sit amet congue odio pulvinar in. Quisque vitae dictum elit. In non leo quis tellus</p>
    		<button>Button Two</button>
    	</div>
    </div><!--/.highlight-->

    <div class="highlight">
        <div class="col-md-12"><h2 class="entry-title">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</h2></div>
        <div class="col-md-12">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra metus vitae iaculis mattis. Nam gravida dictum dui, sit amet congue odio pulvinar in. Quisque vitae dictum elit. In non leo quis tellus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra metus vitae iaculis mattis. Nam gravida dictum dui, sit amet congue odio pulvinar in. Quisque vitae dictum elit. In non leo quis tellus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra metus vitae iaculis mattis. Nam gravida dictum dui, sit amet congue odio pulvinar in. Quisque vitae dictum elit. In non leo quis tellus. <a href="">Read more.</a>
        </div>
    </div><!--/.highlight-->

    <div class="highlight">
        <div class="col-md-12"><h2 class="entry-title">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</h2></div>
        <div class="col-md-8">
            <?php the_content(); ?>                  
        </div>
        <div class="col-md-4">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam viverra metus vitae iaculis mattis. Nam gravida dictum dui, sit amet congue odio pulvinar in. Quisque vitae dictum elit. In non leo quis tellus</p>
            <button>Button One</button>
        </div>
    </div><!--/.highlight-->

<?php endwhile; ?>

<?php
wp_reset_postdata();    
?>

</div><!--/.container-->
</section><!--/.wrap-lightblue-->