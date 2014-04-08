<?php
$category = get_the_category();
if ($category && $category[0]->cat_name != 'Uncategorized') {

    $slug = $wp_query->query_vars['category_name'];
}
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$ckan_default_server = (get_option('ckan_default_server') != '') ? get_option('ckan_default_server') : 'catalog.data.gov/dataset';

?>
<footer class="content-info" role="contentinfo">

    <div class="container">


        <div class="row">

            <!--
    <div class="col-lg-4">
      <?php dynamic_sidebar('sidebar-footer'); ?>
      <p>&copy; <?php echo date('Y') . ' ';
            bloginfo('name'); ?></p>
    </div>
    -->


            <div class="col-md-4 col-lg-4">

                <form role="search" method="get" style="display: block;" class="search-form form-inline"
                      action="<?php echo $protocol.$ckan_default_server ?>">
                    <div class="input-group">
                        <label class="sr-only" for="search-footer"><?php _e('Search for:', 'roots'); ?></label>
                        <input type="search" id="search-footer" value="<?php if (is_search()) {
                            echo get_search_query();
                        } ?>" name="q" class="search-field form-control"
                               placeholder="<?php _e('Search', 'roots'); ?> <?php bloginfo('name'); ?>">
              <span class="input-group-btn">
                <button type="submit" class="search-submit btn btn-default">
                    <i class="fa fa-search"></i>
                    <span class="sr-only"><?php _e('Search', 'roots'); ?></span>
                </button>
            </span>
                    </div>
                </form>

                <div class="footer-logo">
                    <a class="logo-brand" href="<?php echo home_url(); ?>/"><?php bloginfo('name'); ?></a>
                </div>

            </div>

            <?php if (has_nav_menu('primary_navigation')) : ?>
                <nav class="col-md-2 col-lg-2" role="navigation">
                    <?php wp_nav_menu(array('theme_location' => 'primary_navigation', 'menu_class' => 'nav')); ?>
                </nav>
            <?php endif; ?>

            <?php if (has_nav_menu('footer_navigation')) :
                //add_filter('wp_nav_menu_items', 'add_login_logout_link', 10, 2);
                ?>
                <nav class="col-md-2 col-lg-2" role="navigation">
                    <?php
                    wp_nav_menu(array('theme_location' => 'footer_navigation', 'menu_class' => 'nav'));
                    ?>
                </nav>
            <?php endif; ?>


            <div class="col-md-3 col-md-offset-1 col-lg-3 col-lg-offset-1 social-nav">

                <?php

                $menu_name = 'social_navigation';

                if (($locations = get_nav_menu_locations()) && isset($locations[$menu_name])) {

                    $menu = wp_get_nav_menu_object($locations[$menu_name]);
                    if ($menu) {
                        $menu_items = wp_get_nav_menu_items($menu->term_id);
                        $menu_list = '<ul id="menu-' . $menu_name . '" class="nav">';

                        foreach ((array)$menu_items as $key => $menu_item) {
                            $title = $menu_item->title;
                            $url = $menu_item->url;

                            switch (strtolower($title)) {
                                case 'twitter':
                                    $class = 'fa fa-twitter';
                                    break;
                                case 'github':
                                    $class = 'fa fa-github';
                                    break;
                                case 'stack exchange':
                                    $class = 'fa fa-stack-exchange';
                                    break;
                            }

                            $menu_list .= '<li><a href="' . $url . '"><i class="' . $class . '"></i><span>' . $title . '</span></a></li>' . "\n";
                        }

                        $menu_list .= '</ul>';
                    } else {
                        $menu_list = '<ul><li>Menu "' . $menu_name . '" not defined.</li></ul>';
                    }

                } else {
                    $menu_list = '<ul><li>Menu "' . $menu_name . '" not defined.</li></ul>';
                }

                ?>



                <?php if ($menu_list) : ?>
                    <nav role="navigation">
                        <?php echo $menu_list; ?>
                    </nav>
                <?php endif; ?>

            </div>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
