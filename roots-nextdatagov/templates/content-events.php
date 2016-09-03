<div class="wrap container">
<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <header>
      <h1 class="entry-title"><?php the_title(); ?></h1>
      <?php get_template_part('templates/entry-meta'); ?>
    </header>
    <div class="entry-content">
      <?php the_content(); ?>
     <?php echo "<strong>Group:</strong>&nbsp;";
                    ?>
                    <?php
                    $values = false;
                    if (function_exists('get_field')){
                      $values = get_field('group');
                    }
                    $taxanomy='category';
                    if($values)
                    {
                        echo '<ul>';
                        foreach($values as $value)
                        {

                            $term = get_term( $value, $taxanomy );
                            $name = $term->name;
                            echo '<li>' . $name . '</li>';
                        }

                        echo '</ul>';
                    }

                    // always good to see exactly what you are working with
                    //var_dump($values);

                    ?>
                    <?php echo "<strong>Start Date and Time:</strong>&nbsp; ".date("l jS \of F Y h:i A",get_post_meta($post->ID, 'start_date_and_time',TRUE)); ?> <br />
                    <?php echo "<strong>End Date and Time:</strong>&nbsp; ";
                    if(get_post_meta($post->ID, 'end_date_and_time',TRUE)) {
                        echo date("l jS \of F Y h:i A",get_post_meta($post->ID, 'end_date_and_time',TRUE));
                    }
                    else{
                        echo "-" ;
                    }
                    ?><br />
                    <?php echo "<strong>Location:</strong>&nbsp; ".get_post_meta($post->ID, 'location', TRUE ); ?> <br />
                    <?php echo "<strong>Description: ".get_post_meta($post->ID, 'body', TRUE ); ?> <br />
                    <br />
                    <?php echo "<strong>Time Zone:</strong>&nbsp; " .strtoupper(get_post_meta($post->ID, 'time_zone',TRUE));?> <br />
    </div>
    <footer>
      <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
    </footer>
    <?php comments_template('/templates/comments.php'); ?>
  </article>
<?php endwhile; ?>
</div>
