
<h1>News</h1>


<?php
//query_posts('meta_key=featured_datagov&meta_value=Yes&ignore_sticky_posts=1&posts_per_page=3');
$args = array( 
                'post_type' => 'post',
                'ignore_sticky_posts' => 1,                
                'tax_query' => array(
                	                array(
                	                'taxonomy' => 'post_format',
                	                'field' => 'slug',
                	                'terms' => array( 'post-format-link', 'post-format-status', 'post-format-gallery', 'post-format-image' ),
                	                'operator' => 'NOT IN'
                	                )
                                ),               
                'meta_query' => array(
                                    array(
                                    'key' => 'featured_datagov',
                                    'value' => 'Yes',
                                    'operator' => '=='
                                    )
                                ),                 
                'posts_per_page' => 3 );

$new_query = new WP_Query($args);

?>


<p>
Latests posts from data.gov and topics
</p>



<?php while ($new_query->have_posts()) : $new_query->the_post(); ?>

<article <?php post_class(); ?>>
  <header>
    <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
    <?php get_template_part('templates/entry-meta'); ?>
  </header>
  <div class="entry-summary">
    <?php the_excerpt(); ?>
  </div>
</article>

<?php endwhile; ?>

<?php
wp_reset_postdata();    
?>




<?php
//'terms' => array( 'post-format-link', 'post-format-status', 'post-format-gallery', 'post-format-image' ),


$args = array( 
                'post_type' => 'post',
                'ignore_sticky_posts' => 1,                
                'tax_query' => array(
                	                array(
                	                'taxonomy' => 'post_format',
                	                'field' => 'slug',
                	                'terms' => array( 'post-format-status'),
                	                )
                                ),               
                'meta_query' => array(
                                    array(
                                    'key' => 'featured_datagov',
                                    'value' => 'Yes',
                                    'operator' => '=='
                                    )
                                ),                 
                'posts_per_page' => 3 );

$new_query = new WP_Query($args);

?>



<?php while ($new_query->have_posts()) : $new_query->the_post(); ?>

<article <?php post_class(); ?>>
    <div class="author-details">
        <?php the_field('persons_name'); ?> - <a href="<?php the_field('link_to_tweet'); ?>">@<?php the_field('twitter_handle'); ?></a>
    </div>
    <div class="body">
        <?php the_content('Read the rest of this entry »'); ?>
    </div>
</article>

<?php endwhile; ?>

<?php
wp_reset_postdata();    
?>



