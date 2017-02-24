<?php

if (is_category()) {
    $cat_ID = get_query_var('cat');

    $category  = get_category($cat_ID);
    $term_name = $category->cat_name;
    $term_slug = $category->slug;

} elseif ($category_slug = get_query_var('category_name')) {
    if (strpos($category_slug, '/')) {
        list(, $sub_category_slug) = explode('/', $category_slug);
        $category_slug = $sub_category_slug;
    }
    $category  = get_category_by_slug($category_slug);
    $term_name = $category->cat_name;
    $term_slug = $category_slug;
    if (strpos($term_slug, '/') !== false) {
        $term_slug = substr(strstr($term_slug, '/'), 1);
    }
} else {
    if(!empty($cat_ID)){
     $category =  get_category( $cat_ID );
     $term_name = $category->cat_name;
     $term_slug = $category_name;
 } else {
     $category = get_the_category();
     $category = $category[0];
     $term_slug = $category->slug;
     $term_name = $category->cat_name;
 }
}


// show Links associated to a community
// we need to build $args based either term_name or term_slug
$args = array(
    'category_name' => $term_slug,
    'categorize'    => 0,
    'title_li'      => 0,
    'echo'          => 0,
    'orderby'       => 'rating'
);
if (!empty($term_slug)) {
    $subnav = wp_list_bookmarks($args);
}

if (strcasecmp($term_name, $term_slug) != 0) {
    $args = array(
        'category_name' => $term_name,
        'categorize'    => 0,
        'title_li'      => 0,
        'echo'          => 0,
        'orderby'       => 'rating',
    );

    $subnav_extra = wp_list_bookmarks($args);
}
$allowed_slug_arrays = array(
    "climate-ecosystems",
    "coastalflooding",
    "energysupply",
    "foodsupply",
    "humanhealth",
    "transportation",
    "water",
    "climate"
);
if ($subnav || (isset($subnav_extra) && $subnav_extra)):
    ?>

    <div class="subnav banner">
        <div class="container">

            <?php if ($subnav): ?>

                <nav class="topic-subnav" role="navigation">
                    <ul class="nav navbar-nav">
                        <?php
                        if (in_array($term_slug, $allowed_slug_arrays)) {
                            wp_nav_menu(
                                array(
                                    'theme_location' => 'climate_navigation',
                                    'menu_class'     => 'nav',
                                    'items_wrap'     => '%3$s'
                                )
                            );
                        }
                        ?>
                        <?php echo $subnav ?>
                    </ul>
                </nav>

            <?php endif; ?>

            <?php if (isset($subnav_extra) && $subnav_extra): ?>
                <nav class="topic-subnav" role="navigation">
                    <ul class="nav navbar-nav">
                        <?php
                        if (in_array($term_slug, $allowed_slug_arrays)) {
                            wp_nav_menu(
                                array(
                                    'theme_location' => 'climate_navigation',
                                    'menu_class'     => 'nav',
                                    'items_wrap'     => '%3$s'
                                )
                            );
                        }
                        ?>
                        <?php echo $subnav_extra ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>

<?php else: ?>


    <?php

    $valid_sub_menu = false;

    if (!empty($term_slug)) {

        $sub_menu = wp_nav_menu(
            array('menu' => $term_slug, 'echo' => false, 'fallback_cb' => '', 'menu_class' => 'nav navbar-nav')
        );

        $expected_html = 'ul id="menu-' . $term_slug;

        // if there's no menu, check to see if there's one for the parent category
        if (!empty($category->category_parent) && (empty($sub_menu) || (!empty($sub_menu) && strpos(
                        $sub_menu,
                        $expected_html
                    ) != 1))
        ) {
            $parent_category = get_category($category->category_parent);
            $expected_html   = 'ul id="menu-' . $parent_category->slug;
            $sub_menu        = wp_nav_menu(
                array(
                    'menu'        => $parent_category->slug,
                    'echo'        => false,
                    'fallback_cb' => '',
                    'menu_class'  => 'nav navbar-nav'
                )
            );
        }

        if (!empty($sub_menu) && strpos($sub_menu, $expected_html) == 1) {
            $valid_sub_menu = true;

            if (!empty($parent_category)) {
                $term_slug = $parent_category->slug;
            }
        }

    }

    ?>


    <?php if ($valid_sub_menu): ?>


        <div class="subnav banner">
            <div class="container">
                <nav class="topic-subnav" role="navigation">
                    <?php echo $sub_menu; ?>
                </nav>
            </div>
        </div>

    <?php endif; ?>


<?php endif; ?>
<!-- adding parameter to url which passes the term slug for the cross site navigation-->
<script type="text/javascript">
    jQuery(function ($) {
        var comm = 'topic=<?php echo $term_slug; ?>_navigation';

        $('#main a[href*="catalog.data.gov"]').add('#main a[href*="fe-data.reisys.com"]').not('#main a[href*="#topic"]').each(function () {
            var newHref = $(this).attr('href') + '#topic=<?php echo $term_slug; ?>_navigation';
            $(this).attr('href', newHref);
        });
    });
</script>
