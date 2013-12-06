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

if (($highlight_posts->have_posts())):
?>

<section id="highlights" class="wrap wrap-lightblue">
<div class="container">
    <div class="page-header">
      <h1>Highlights</h1>
    </div>

<?php while ($highlight_posts->have_posts()) : $highlight_posts->the_post(); ?>
    <div class="highlight <?php get_category_by_slug( $slug ) ?>">
        <h2 class="entry-title"><?php the_title(); ?></h2></div>
    		<?php the_content(); ?>                  
    </div><!--/.highlight-->
<?php endwhile; ?>

<?php
endif;
wp_reset_postdata();    
?>

</div><!--/.container-->
</section><!--/.wrap-lightblue-->