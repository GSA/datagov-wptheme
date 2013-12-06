<?php
                    while( have_posts() ) {
                        the_post();
                        ?>


      <?php the_title();?>

    <?php the_content();   ?>
    <?php }?>
<div class="inner">
  <?php
                                    $args = array(
                                        'orderby'          => '',
                                        'meta_key'         => 'field_alias',
                                        'orderby'          => 'meta_value',
                                        'order'            => 'ASC',
                                        'post_type'        => 'regional_planning',
                                        'post_status'      => 'publish',
                                        'suppress_filters' => true );
                                    $query = null;
                                    $query = new WP_Query($args);
                                    if( $query->have_posts() ) {
                                        echo '<h2 class="block-title">Planning Regions</h2>';
                                        echo '<div class="panecontent">';
                                        echo '<div class="item-list">';
                                        echo '<ul>';
                                        while ($query->have_posts()) : $query->the_post();
                                            $link="/ocean/page/regional-planning?field_alias_value=".get_post_meta($post->ID, 'field_alias',TRUE)
                                            ?>
  <li><a href="<?php echo $link?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
    <?php the_title(); ?>
    </a></li>
  <?php
                                        endwhile;
                                    }?>
</div>
