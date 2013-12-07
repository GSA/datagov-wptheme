<section class="updates">


<div class="page-header">
  <h1>Updates</h1>
</div>


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
                	                ),
                                    array(
                	                'taxonomy' => 'featured',
                	                'field' => 'slug',
                	                'terms' => array( 'highlights'),
                	                'operator' => 'NOT IN'
                	                )                	                
                                ),                                                                             
                'posts_per_page' => 3 );

$new_query = new WP_Query($args);

?>

<?php while ($new_query->have_posts()) : $new_query->the_post(); ?>

<article <?php post_class(); ?>>
  <header>
    <h5 class="category"><?php $category = get_the_category(); echo $category[0]->cat_name; ?></h5>
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
                	                ),
                                    array(
                	                'taxonomy' => 'featured',
                	                'field' => 'slug',
                	                'terms' => array( 'highlights'),
                	                'operator' => 'NOT IN'
                	                )
                                ),                
                'posts_per_page' => 3 );

$new_query = new WP_Query($args);

?>

<?php while ($new_query->have_posts()) : $new_query->the_post(); ?>

<article <?php post_class('col-md-4 col-lg-4'); ?>>

    <div class="author-details">
        <img alt="" src="<?php the_field('twitter_photo'); ?>" height="40" width="40">        
        <a href="<?php the_field('link_to_tweet'); ?>"><?php the_field('persons_name'); ?> @<?php the_field('twitter_handle'); ?></a>
    </div>    
    
    <div class="body">
        <?php the_content('Read the rest of this entry »'); ?>
    </div>
</article>

<?php endwhile; ?>

<?php
wp_reset_postdata();    
?>

</section>

