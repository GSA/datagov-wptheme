<?php
/**
 * Page titles
 */
function roots_title()
{
    if (is_home()) {
        if (get_option('page_for_posts', true)) {
            return get_the_title(get_option('page_for_posts', true));
        } else {
            return __('Latest Posts', 'roots');
        }
    } elseif (is_archive() ||  is_single() ||  is_page()) {
        if (is_single() ||  is_page()) {
            global $post;
            if ($post) {
                $term = get_the_category($post->ID);

                // if this is a page without a category
                if (!$term) {
                    return get_the_title();
                }

                // if this is a page with a category called "uncategorized"
                if (is_array($term)) {
                    foreach ($term as $uncategory_check) {
                        if ($uncategory_check->slug == 'uncategorized') {
                            return get_the_title();
                        }
                    }
                }
            }
        } else {
            $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
        }


        if (is_category()) {
            $term = get_category(get_query_var('cat'), false);
        }

        if (is_array($term)) {
            $url_slug = get_query_var('category_name');
            if ($url_slug) {
                if (strpos($url_slug, '/')) {
                    list(, $sub_category_slug) = explode('/', $url_slug);
                    $url_slug = $sub_category_slug;
                }

                $term = try_to_guess_category_name($term, $url_slug);
            } else {
                $term = $term[0];
            }
        }

        if (!empty($term->category_parent)) {
            $parent = get_category($term->category_parent);
        }


        if ($term) {
            $title = '';

            if (!empty($parent)) {
                $title .= '<span class="category-header topic-' . $parent->slug . '"><a href="' . home_url(
                        '/' . $parent->slug
                    ) . '"><div><i></i></div><span>' . $parent->name . '</span></a></span> &nbsp; &mdash; &nbsp; ';
            }


            $title .= '<span class="category-header topic-' . $term->slug . '"><a href="' . home_url(
                    '/' . $parent->slug . '/' . $term->slug
                ) . '/"><div><i></i></div><span>' . $term->name . '</span></a></span>';

            // try to get the post's parent/child category/term in case the post url has been customized
            // through custom_permalinks
            if (get_post_meta($post->ID, 'custom_permalink', true) && $parent->slug == "" && $child_slug == "") {

                $custom_permalink = str_replace(home_url() . '/', '', get_permalink($post->ID));
                $custom_permalink = explode('/', $custom_permalink);
                $custom_permalink_category = $custom_permalink[0];
                $custom_permalink_term = $custom_permalink[1];

                $category_exists = (term_exists($custom_permalink_category, 'category') != 0)
                    && term_exists($custom_permalink_category, 'category') != null;

                $term_exists = (term_exists($custom_permalink_term, '', 'category') != 0)
                    && term_exists($custom_permalink_term, '', 'category') != null;

                if ($category_exists && $term_exists) {
                    $title .= ' &nbsp; &mdash; &nbsp; <span class="category-header topic-' .
                        (isset($custom_permalink_child) ? $custom_permalink_child : '') . '"><a href="' . home_url(
                            '/' . $custom_permalink_category . '/' . $custom_permalink_term
                        ) . '"><div><i></i></div><span>' . $custom_permalink_term . '</span></a></span>';
                }

            }

            return apply_filters('single_term_title', $title);
        } elseif (is_post_type_archive()) {
            return apply_filters('the_title', get_queried_object()->labels->name);
        } elseif (is_day()) {
            return sprintf(__('Daily Archives: %s', 'roots'), get_the_date());
        } elseif (is_month()) {
            return sprintf(__('Monthly Archives: %s', 'roots'), get_the_date('F Y'));
        } elseif (is_year()) {
            return sprintf(__('Yearly Archives: %s', 'roots'), get_the_date('Y'));
        } elseif (is_author()) {
            $author = get_queried_object();

            return sprintf(__('Author Archives: %s', 'roots'), $author->display_name);
        } else {
            return single_cat_title('', false);
        }
    } elseif (is_search()) {
        return sprintf(__('Search Results for %s', 'roots'), get_search_query());
    } elseif (is_404()) {
        return __('Not Found', 'roots');
    } else {
        return get_the_title();
    }
}

function try_to_guess_category_name($terms, $url_slug)
{
    foreach ($terms as $term) {
        if (strtolower(trim($term->slug)) == strtolower(trim($url_slug))) {
            return $term;
        }
    }

    return $terms[0];
}
