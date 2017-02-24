<header>
<div class="banner navbar navbar-default navbar-static-top yamm" role="banner">
  <div class="container">

        <div class="searchbox-row skip-navigation">
            <div class="sr-only skip-link">
                <a href="#main">Jump to Content</a>
            </div>


            <div>
                <?php if(!is_front_page()): ?>
                <?php get_search_form(); ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="<?php echo home_url(); ?>/" alt="Data.gov"><h1 class="navbar-brand"><?php bloginfo('name'); ?></h1></a>
        </div>

        <nav class="collapse navbar-collapse" role="navigation">
            <?php
            if (has_nav_menu('primary_navigation')) :
                wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'nav navbar-nav navbar-right', 'walker' => new Datagov_Nav_Walker));
            endif;
            ?>
        </nav>

  </div>
    <div id="external_disclaimer" class="tooltip" role="tooltip" aria-hidden="true">This link will direct you to an external website that may have different content and privacy policies from Data.gov.
    </div>
</div>

<div class="container hidden print-logo">
    <img src="/app/themes/roots-nextdatagov/assets/img/logo.svg" />
</div>

<?php if(is_front_page()): ?>

<?php while (have_posts()) : the_post(); ?>

<div class="jumbotron">
  <div class="container">
    <?php the_content(); ?>
  </div><!--/.container-->
</div><!--/.jumbotron-->

<?php endwhile; ?>


<div class="header banner frontpage-search">
    <div class="container">
        <div class="text-center getstarted">
            <h4><label for="search-header">Get Started<br>
                    <small>Search over <a href="/metrics"><?php echo number_format(get_option('ckan_total_count', 107111)); ?> datasets</a>
                    </small>
                    <br/><i class="fa fa-caret-down"></i></label></h4>
        </div>
        <?php get_search_form(); ?>
    </div><!--/.container-->
</div>
<?php endif; ?>


    <?php if (!is_front_page()): ?>
    <div class="header banner page-heading">
        <div class="container">
            <div class="page-header">


                <?php if (is_category() || is_tax()): ?>
                <div class="tagline">
                    <?php echo category_description(); ?>
                </div>
                <?php endif; ?>
                <h1>
                    <?php if (tribe_is_month()) {
                    echo 'Events';
                } else if (tribe_is_event() && !tribe_is_day() && !is_single()) {
                    echo 'Events';
                } else if ( is_singular( 'tribe_events' )) {
                    echo 'Events';
                } else if (tribe_is_day()) {
                    echo 'Events';
                }
                else if (tribe_is_upcoming()) {
                    echo 'Events';
                }
                else if (tribe_is_past()) {
                    echo 'Events';
                }else{ echo roots_title(); }
                    ?>
                </h1>

            </div>
        </div>
    </div>
    <?php endif; ?>


</header>


