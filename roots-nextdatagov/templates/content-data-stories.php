<div class="wraper impact container">

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
    <?php if ($the_query->found_posts > $stories_per_page ) : ?>
        <div class="col-md-3 col-md-offset-9 more-stories"><a href="/stories" class="btn btn-lg">
                <i class="fa fa-book" aria-hidden="true"></i><span>More stories &gt;&gt;</span></a>
        </div>
    <?php endif; ?>
</div>
