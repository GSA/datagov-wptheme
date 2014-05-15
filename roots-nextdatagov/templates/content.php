<article <?php post_class(); ?>>
    <header>
        <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <?php get_template_part('templates/entry-meta'); ?>
    </header>
    <div class="entry-summary">
        <?php
        remove_filter('get_the_excerpt', 'wp_trim_excerpt');
        add_filter('get_the_excerpt', 'datagov_custom_keep_my_links');
        $more_tag = strpos($post->post_content, '<!--more-->');
        ($more_tag) ? the_content('Continued') : the_excerpt();
        $author_name = get_field('author_name');
        if(empty($author_name))
            $author_name = get_the_author();
        ?>
        <em>By <?php echo $author_name;?></em>
    </div>
</article>
