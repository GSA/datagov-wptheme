<?php
$category = get_the_category();
if ($category && $category[0]->cat_name != 'Uncategorized') {

    $slug = $wp_query->query_vars['category_name'];
}
$ckan_default_server = (get_option('ckan_default_server') != '') ? get_option('ckan_default_server') : 'catalog.data.gov/dataset';
$ckan_default_server = strstr($ckan_default_server, '://') ? $ckan_default_server : ('//' . $ckan_default_server);


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


            <div class="col-md-6 col-lg-6">
                <div class="footer-logo">
                    <a class="logo-brand" href="<?php echo home_url(); ?>/" alt="Data.gov"><?php bloginfo('name'); ?></a>
                </div>
                <?php if (has_nav_menu('footer2_navigation')) :

                wp_nav_menu(array('theme_location' => 'footer2_navigation', 'menu_class' => 'nav'));

                 endif; ?>
            </div>

            <?php if (has_nav_menu('footer_navigation')) :
                //add_filter('wp_nav_menu_items', 'add_login_logout_link', 10, 2);
                ?>
                <nav class="col-md-3 col-lg-3" role="navigation">
                    <?php
                    wp_nav_menu(array('theme_location' => 'footer_navigation', 'menu_class' => 'nav'));
                    ?>
                </nav>

            <?php endif; ?>



            <div class="col-md-3 col-lg-3 social-nav">

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
                            $attribute = $menu_item->attr_title;

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

                            $a_title = '';
                            if (trim($attribute)) {
                                $a_title = 'title="'.esc_html(trim($attribute)).'"';
                            }
                            $menu_list .= '<li><a href="' . $url . '" '.$a_title.'><i class="' . $class . '" ></i><span>' . $title . '</span></a></li>' . "\n";
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
