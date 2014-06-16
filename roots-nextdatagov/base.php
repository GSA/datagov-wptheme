<?php get_template_part('templates/head'); ?>
<body <?php body_class(); ?>>
<?php
if ($post_disclaimer = get_post('128733')) {
    echo $post_disclaimer->post_content;
}
?>

<!--[if lt IE 8]>
<div class="alert alert-warning">
    <?php _e('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your
    browser</a> to improve your experience.', 'roots'); ?>
</div>
<![endif]-->

<?php
do_action('get_header');
// Use Bootstrap's navbar if enabled in config.php
if (current_theme_supports('bootstrap-top-navbar')) {
    get_template_part('templates/header-top-navbar');
} else {
    get_template_part('templates/header');
}

?>

<div role="document">
    <div class="content">
        <main class="main" role="main" id="main">
            <?php include roots_template_path(); ?>
        </main>
        <!-- /.main -->

        <?php if (roots_display_sidebar()) : ?>
            <aside class="sidebar <?php echo roots_sidebar_class(); ?>" role="complementary">
                <?php include roots_sidebar_path(); ?>
            </aside><!-- /.sidebar -->
        <?php endif; ?>


    </div>
    <!-- /.content -->
</div>
<!-- /.wrap -->

<?php get_template_part('templates/footer'); ?>
<div id="survey_target" style="border:none !important;"></div>
<noscript>
    <iframe src="http://survey.usa.gov/surveys/161"></iframe>
</noscript>
<style>

    .banner{border:none;  padding-bottom:0px;}
    .frontpage-search, body.home .header.banner.page-heading {
        border-bottom: 1px solid #CCCCCC;

    }
</style>
<script type="text/javascript" src="http://survey.usa.gov/widget/161/invitation.js?target_id=survey_target&stylesheet=<?php echo get_template_directory_uri() . '/assets/css/survey.css'; ?>"></script>
</body>
</html>
