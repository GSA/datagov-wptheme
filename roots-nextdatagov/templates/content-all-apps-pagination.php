<?php
$category = get_the_category();
if ($category) {
    $cat_name = $term_name = $category[0]->cat_name;
    $cat_slug = $term_slug = $category[0]->slug;

// show Links associated to a community
// we need to build $args based either term_name or term_slug
    if (!empty($term_slug)) {
        $args = array(
            'category_name' => $term_slug,
            'categorize' => 0,
            'title_li' => 0,
            'orderby' => 'rating',
            'echo' => 0
        );
        $bookmarks = wp_list_bookmarks($args);
    }
    if (strcasecmp($term_name, $term_slug) != 0) {
        $args = array(
            'category_name' => $term_name,
            'categorize' => 0,
            'title_li' => 0,
            'orderby' => 'rating',
            'echo' => 0
        );
        $bookmarks = wp_list_bookmarks($args);
    }


    if ($bookmarks): ?>

        <div class="subnav banner">
            <div class="container">
                <nav role="navigation" class="topic-subnav">
                    <ul class="nav navbar-nav">
                        <?php

                        echo $bookmarks;

                        ?>
                    </ul>
                </nav>
            </div>
        </div>

    <?php endif;

}

$query = filter_var($_GET['q'], FILTER_SANITIZE_STRING);
?>

    <div class="intro">
        <div class="container">
            <?php while (have_posts()) : the_post(); ?>
                <div class="Apps-post">
                    <?php the_content(); ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <!-- Application featured taxonomy-->
<?php

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$per_page = -1;
if ('local' == WP_ENV) {
    $per_page = 2;
}

$args_featured = array(
    's' => $query,
    'post_type' => 'Applications',
    'posts_per_page' => $per_page,
    'post_status' => 'publish',
    'orderby' => 'modified',
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'featured',
            'field' => 'slug',
            'terms' => array('highlights'),
        ),
    ),
);

$args_nonfeatured = array(
    's' => $query,
    'post_type' => 'Applications',
    'posts_per_page' => $per_page,
    'post_status' => 'publish',
    'orderby' => 'modified',
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'featured',
            'field' => 'slug',
            'terms' => array('highlights'),
            'operator' => 'NOT IN'
        )
    ),
);

$current_app_agency = (get_query_var('app_agency')) ? get_query_var('app_agency') : '';
if ($current_app_agency) {
    $agency_tax_query = array(
        'taxonomy' => 'application_agencies',
        'field' => 'slug',
        'terms' => array($current_app_agency),
    );
    $args_featured['tax_query'][] = $agency_tax_query;
    $args_nonfeatured['tax_query'][] = $agency_tax_query;
}

$result_featured = new WP_Query($args_featured);
wp_reset_query();
$featured = array();
$i = 0;
while ($result_featured->have_posts()) {
    $result_featured->the_post();
    $terms = wp_get_post_terms($post->ID, 'application_agencies');
    if ($terms) {
        foreach ($terms as $term) {
            $featured[$i]['agencies'][] = $term->name;
        }
    }
    $featured[$i]['title'] = get_the_title($post->ID);
    $featured[$i]['conent'] = get_the_content($post->ID);
    $featured[$i]['field_application_url'] = get_post_meta($post->ID, 'field_application_url', true);
    $imagefile = get_field_object('field_5240b9c982f41');
    $featured[$i]['image_url'] = $imagefile['value']['url'];
    $featured[$i]['image_alt'] = $imagefile['value']['alt'];
    $featured[$i]['featured'] = true;
    $i++;
}

$result_nonfeatured = new WP_Query($args_nonfeatured);
$not_featured = array();
$i = 0;
while ($result_nonfeatured->have_posts()) {
    $result_nonfeatured->the_post();
    $not_featured[$i]['agencies'] = array();
    $terms = wp_get_post_terms($post->ID, 'application_agencies');
    if ($terms) {
        foreach ($terms as $term) {
            $not_featured[$i]['agencies'][] = $term->name;
        }
    }
    $not_featured[$i]['title'] = get_the_title($post->ID);
    $not_featured[$i]['conent'] = get_the_content($post->ID);
    $not_featured[$i]['field_application_url'] = get_post_meta($post->ID, 'field_application_url', true);
    $imagefile = get_field_object('field_5240b9c982f41');
    $not_featured[$i]['image_url'] = $imagefile['value']['url'];
    $not_featured[$i]['image_alt'] = $imagefile['value']['alt'];
    $not_featured[$i]['featured'] = false;
    $i++;
}
wp_reset_query();
$apparray = array_merge($featured, $not_featured);
$total_apps = count($apparray);
if ($total_apps > 0)
    $apparray = merged_array_sort($apparray, 'title');

$apps_per_page = 1200;
if (isset($apparray)) {
    $total_pages = ceil($total_apps / $apps_per_page);
} else {
    $total_pages = 1;
    $total_apps = 0;
}
if (isset($_GET['currentpage']) && is_numeric($_GET['currentpage'])) {
    $currentpage = (int)$_GET['currentpage'];
} else {
    $currentpage = 1;
}
if ($currentpage > $total_pages) {
    $currentpage = $total_pages;
}
if ($currentpage < 1) {
    $currentpage = 1;
}
$start = ($currentpage - 1) * $apps_per_page + 1;

$app_agencies = get_taxonomy_hierarchy('application_agencies');
$current_app_agency_obj = get_term_by('slug', $current_app_agency, 'application_agencies');

$q_prefix = $query ? 'q=' . $query . '&' : '';

if ($total_apps > 0) {
    ?>
    <div class="container">
        <form style="width:100%;" action="" class="search-app navbar-form navbar-left" method="get" role="search">
            <div class="input-group">
                <label class="sr-only" for="search-app">Search for:</label>
                <input type="search" placeholder="Search Applications" class="search-field form-control" name="q"
                       value="" id="search-app"/>
                <input type="hidden" value="score desc, name asc" name="sort"/>
                <span class="input-group-btn">
                    <button class="search-submit btn btn-default" type="submit"/>
                    <i class="fa fa-search"></i>
                    <span class="sr-only">Search</span>
                </span>
            </div>
            <?php if ($app_agencies): ?>
                <?php if ($current_app_agency && $current_app_agency_obj): ?>
                    <div class="col-md-12 text-right">
                        <br/>
                        Filtered by Agency:
                        <a href="?<?php echo $q_prefix ?>" type="button" class="btn btn-primary">
                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                            <?php echo $current_app_agency_obj->name; ?>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="col-md-12">
                        <br/>
                        <button type="button" class="pull-right btn btn-default dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            Filter by Agency <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu pull-right">
                            <?php foreach ($app_agencies as $parent_agency): ?>
                                <li>
                                    <a href="?<?php echo $q_prefix ?>app_agency=<?php echo $parent_agency->slug; ?>">
                                        <strong><?php echo $parent_agency->name; ?></strong>
                                    </a>
                                </li>
                                <?php if (sizeof($parent_agency->children)): ?>
                                    <?php foreach ($parent_agency->children as $agency): ?>
                                        <li>
                                            <a href="?<?php echo $q_prefix ?>app_agency=<?php echo $agency->slug; ?>">
                                                &nbsp;&ndash;&nbsp;<?php echo $agency->name; ?>
                                            </a>
                                        </li>
                                        <?php if (sizeof($agency->children)): ?>
                                            <?php foreach ($agency->children as $grandchild): ?>
                                                <li>
                                                    <a href="?<?php echo $q_prefix ?>app_agency=<?php echo $grandchild->slug; ?>">
                                                        <em>&nbsp;&nbsp;&mdash;&nbsp;<?php echo $grandchild->name; ?></em>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </form>
        <?php
        if (!empty($query)) {
            ?>
            <div class="search-results-alert">
                <div class="results-count"> <?php echo $total_apps; ?> results found for "<?php echo $query ?>"</div>
                <a class="local-link" href="/applications"> Clear Search </a>
            </div>
        <?php }
        ?>
        <br clear="all"/>
        <div class="Apps-wrapper">
            <div class="Mobile-post row">
                <?php
                for ($i = $start - 1; $i < $start - 1 + $apps_per_page; $i++) {
                    if (isset($apparray[$i])) {
                        ?>
                        <div class="webcontainer col-md-4 col-lg-3">
                            <div class="thumbnail" data-app-url="<?php echo $apparray[$i]['field_application_url']; ?>">
                                <div class="app-icon">
                                    <span class="middle-helper"></span>
                                    <img <?php if ($apparray[$i]['image_url'] == '') { ?>
                                        class="scale-with-grid noImage"
                                    <?php } else { ?>
                                        class="scale-with-grid" <?php } ?>
                                        src="<?php echo $apparray[$i]['image_url'] ?>"
                                        <?php if ($apparray[$i]['image_alt'] == '') { ?>
                                            alt="<?php echo $apparray[$i]['title'] ?>"
                                        <?php } else { ?>
                                            alt="<?php echo $apparray[$i]['image_alt'] ?>"
                                        <?php } ?> >
                                </div>
                                <div class="caption">
                                    <div class="text-center app-title"><a href="<?php
                                        echo $apparray[$i]['field_application_url']; ?>">
                                            <?php echo $apparray[$i]['title']; ?>
                                        </a></div>

                                    <div class='content'>
                                        <div class="webtext">
                                            <?php echo $apparray[$i]['conent']; ?>
                                        </div>
                                        <?php if (sizeof($apparray[$i]['agencies'])): ?>
                                            <div class="app-agencies">
                                                <br/>
                                                <small>
                                                    Agencies: <i><?php echo join('; ', $apparray[$i]['agencies']) ?></i>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
                <br clear="all"/>
            </div>

        </div>
        <!-- Modal -->
        <div class="modal fade" id="appDescription" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="gridSystemModalLabel">Modal title</h4>
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                        <a href="" target="_blank" class="go-to-app btn btn-primary pull-left">Go to app...</a>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($total_pages > 1): ?>
            <div class='pagination'>
                <p class="counter">
                    <?php printf(__('Page %1$s of %2$s'), $currentpage, $total_pages); ?>
                </p>
                <div class="pagination-controls">
                    <?php
                    customPagination($query, 'developer-apps-showcase', $currentpage, $total_pages, true);
                    ?>
                </div>
            </div>
        <?php endif ?>
    </div>
    <?php
} else { ?>
    <div class="search-results-alert">
        <div class="results-count">0 results found for "<?php echo $query ?>"</div>
        Sorry, no results found. Try entering fewer or broader query terms. <a class="local-link" href="/applications">
            Search Again </a>
    </div>
    <?php
}
function customPagination($query, $base_url, $cur_page, $number_of_pages, $prev_next = false)
{
    $ends_count = 1; //how many items at the ends (before and after [...])
    $middle_count = 2; //how many items before and after current page
    $dots = false;
    $nextpage = $cur_page + 1;
    $prevpage = $cur_page - 1;
    $output = "<ul class='pagination'>";
    ?>

    <?php
    if ($prev_next && $cur_page && 1 < $cur_page) { //print previous button?
        $output .= "<li class='pagination-prev'><a class='prev page-numbers pagenav local-link' href='?q=" . $query . "&currentpage=$prevpage'>Previous</a> </li>";
    }
    for ($i = 1; $i <= $number_of_pages; $i++) {
        if ($i == $cur_page) {
            $output .= "<li><span class='page-numbers pagenav current'> $i </span></li>";
            $dots = true;
        } else {
            if ($i <= $ends_count || ($cur_page && $i >= $cur_page - $middle_count && $i <= $cur_page + $middle_count) || $i > $number_of_pages - $ends_count) {
                $output .= "<li><a class='page-numbers pagenav' href='?q=" . $query . "&currentpage=$i'> $i </a></li>";
                $dots = true;
            } elseif ($dots) {
                $output .= '<li><span class="page-numbers dots">' . __('&hellip;') . '</span></li>';
                $dots = false;
            }
        }
    }
    if ($prev_next && $cur_page && ($cur_page < $number_of_pages || -1 == $number_of_pages)) { //print next button?
        $output .= " <li class='pagination-next'> <a href='?q=" . $query . "&currentpage=$nextpage'> Next</a></li> ";
    }
    ?>
    <?php
    $output .= "</ul>";
    print $output;
}

function merged_array_sort($a, $subkey)
{
    foreach ($a as $k => $v) {
        $b[$k] = strtolower($v[$subkey]);
    }
    asort($b);
    foreach ($b as $key => $val) {
        $c[] = $a[$key];
    }
    return $c;
}
