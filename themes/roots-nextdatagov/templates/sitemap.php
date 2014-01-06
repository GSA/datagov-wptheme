<?php
/*
Template Name: Sitemap
*/
?>
<div class="container">
    <?php if (has_nav_menu('primary_navigation')) : ?>
        <h3>Header</h3>
        <? wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'nav', 'depth' => 1));
    endif;
    ?>

    <?php if (has_nav_menu('topics_navigation')) : ?>
        <h3>Topics</h3>
        <? wp_nav_menu(array('theme_location' => 'topics_navigation', 'menu_class' => 'nav'));
    endif;
    ?>


    <?php if (has_nav_menu('footer_navigation')) : ?>
        <h3>Footer</h3>
            <?php wp_nav_menu(array('theme_location' => 'footer_navigation', 'menu_class' => 'nav')); ?>
    <?php endif; ?>

    <p>
        Click <a href="http://dev-wp-datagov.reisys.com/detailed-sitemap/" title="Detailed Sitemap">here</a> for a Detailed Sitemap
    </p>
</div>