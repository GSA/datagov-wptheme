<div class="wrap container">
    <?php get_template_part('templates/page', 'header'); ?>

    <div class="alert alert-warning">
        <?php _e('The page you are looking for is currently unavailable to view', 'roots'); ?>
    </div>

    <p><?php _e(
            'We have been upgrading our site. It is possible that this page has been moved or renamed. You can use your browser\'s Back button to return to the previous page, or <a href="/">go to the homepage</a>, and search for the information you are looking for.',
            'roots'
        ); ?></p>
    <?php _e(
        'If you think that you have reached this page due to an error on our part, <a href="/contact-us/">please contact us</a>.',
        'roots'
    ); ?>
    <br/>
    <br/>
    <?php get_search_form(); ?>
</div>