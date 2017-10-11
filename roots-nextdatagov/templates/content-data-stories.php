<?php
$args = array(
    'post_type' => 'impact',
    'public' => true
);

$the_query = new WP_Query($args);

function remove_more_link_scroll($link)
{
    $link = preg_replace('|#more-[0-9]+|', '', $link);
    return $link;
}

add_filter('the_content_more_link', 'remove_more_link_scroll');

?>

<div class="wrap container impact">

    <?php if (!$the_query->have_posts()) : ?>
        <div class="alert alert-warning">
            <?php _e('Sorry, no results were found.', 'roots'); ?>
        </div>
        <?php get_search_form(); ?>
    <?php endif; ?>

    <?php while ($the_query->have_posts()) : $the_query->the_post(); ?>
        <article class="post-20808812 post type-post status-publish format-standard hentry category-ecosystems">
            <header>
                <h2 class="entry-title">
                    <a id="post-title-20808812" href="<?php echo get_permalink(); ?>">
                        <?php the_title(); ?>
                    </a>
                </h2>
                <div class="entry-meta" xmlns="//www.w3.org/1999/html">
                    <time class="published" datetime="<?php echo get_the_time('c'); ?>"><?php echo get_the_date(); ?>
                        &nbsp;&nbsp;<i>Agency: <?php echo get_field('agency_name'); ?></i></time>
                </div>

            </header>
            <div class="entry-summary">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</div>
