<div class="wrap container">
<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <header>
      <h1 class="entry-title"><?php the_title(); ?></h1>
      <?php get_template_part('templates/entry-meta'); ?>
    </header>
    <div class="entry-content">
      <?php the_content('Read the rest of this entry »'); ?>
                        <?php echo "<strong>Application URL:</strong>&nbsp;".get_post_meta($post->ID, 'field_application_url', TRUE ); ?>
                        <br />
                        <?php echo "<strong>Application Image:<strong> " ?>
                        <?php
                        $imagefile=get_field_object('field_5240b9c982f41');
                        ?>

                        <img class="scale-with-grid" src="<?php echo $imagefile['value']['url']; ?>" alt="<?php echo $imagefile['value']['alt']; ?>">

                        <br />
                        <?php echo "<strong>Android App Download URL:</strong>&nbsp;".get_post_meta($post->ID, 'field_android_app_download_url', TRUE ); ?>
                        <br />
                        <?php echo "<strong>Blackberry App Download URL:</strong>&nbsp;".get_post_meta($post->ID, 'field_blackberry_app_download_ur', TRUE ); ?>
                        <br />
                        <?php echo "<strong>iOS App Download URL:</strong>&nbsp;".get_post_meta($post->ID, 'field_ios_app_download_url', TRUE ); ?>
                        <br />
                        <br />
                        <?php echo "<strong>Windows Phone App Download URL:</strong>&nbsp;".get_post_meta($post->ID, 'field_windows_phone_app_download', TRUE ); ?>
                        <br />
                        <?php echo "<strong>Mobile Web URL:</strong>&nbsp;".get_post_meta($post->ID, 'field_mobile_web_url', TRUE ); 
						
	/*$args = array( 'taxonomy' => 'my_term' );

$terms = get_terms('my_term', $args);

$count = count($terms); $i=0;
if ($count > 0) {
    $cape_list = '<p class="my_term-archive">';
    foreach ($terms as $term) {
        $i++;
    	$term_list .= '<a href="' . get_term_link( $term ) . '" title="' . sprintf(__('View all post filed under %s', 'my_localization_domain'), $term->name) . '">' . $term->name . '</a>';
    	if ($count != $i) $term_list .= ' &middot; '; else $term_list .= '</p>';
    }
    echo $term_list;
}					
		*/				
						
						
	?>
    </div>
    <footer>
      <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
    </footer>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?>
