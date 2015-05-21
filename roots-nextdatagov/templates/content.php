<article <?php post_class(); ?>>
    <header>
        <h2 class="entry-title">
	    <a id="<?php echo 'post-title-' . get_the_ID(); ?>" href="<?php echo generate_post_url($post->post_name); ?>"><?php the_title(); ?></a>
	</h2>
        <?php get_template_part('templates/entry-meta-author'); ?>
    </header>
    <div class="entry-summary">
        <?php
        remove_filter('get_the_excerpt', 'wp_trim_excerpt');
        add_filter('get_the_excerpt', 'datagov_custom_keep_my_links');
        $more_tag = strpos($post->post_content, '<!--more-->');
        ($more_tag) ? the_content('Continued') : the_excerpt();
        ?>
    </div>
</article>
