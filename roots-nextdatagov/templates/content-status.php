<article <?php post_class(); ?>>
    <header>
        <div class="tweet-author">
            <a class="fa fa-twitter" class="tweet-permalink" href="<?php the_field('link_to_tweet'); ?>">
                <span class="sr-only">Tweet</a>
            </a>
        <span class="author-image">
            <img src="<?php echo get_bloginfo('template_directory'); ?>/assets/img/twitter.png" class="iconApp" alt="twitter">
        </span>
            <a class="author-link" href="https://twitter.com/<?php the_field('twitter_handle'); ?>">
                <div>
                <span class="author-name">
                    <?php the_field('persons_name'); ?>
                </span>
                <span class="author-handle">
                    @<?php the_field('twitter_handle'); ?>
                </span>
                </div>
            </a>
        </div>
        <div class="tweet-date">
            <?php the_time('F j, Y') ?>
        </div>
    </header>

  <div class="tweet-body">
        <?php the_content('Read the rest of this entry »'); ?>
  </div>

</article>
