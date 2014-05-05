<?php
/**
 * Page titles
 */
function roots_title() {
  if (is_home()) {
    if (get_option('page_for_posts', true)) {
      return get_the_title(get_option('page_for_posts', true));
    } else {
      return __('Latest Posts', 'roots');
    }
  } elseif (is_archive() OR is_single() OR is_page() ) {
    if(is_single() OR is_page()) {        
        global $post;                
        if ($post) {
            $term = get_the_category($post->ID);

            // if this is a page without a category
            if(!$term) return get_the_title();

            // if this is a page with a category called "uncategorized"
            if(is_array($term)) {
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
      $term = get_category(get_query_var('cat'),false);
    }
  
    if(is_array($term)) {
        $term = $term[0];
    }

    if ($term) {
      $link = '/' . $term->slug;      
      $term = '<div class="category-header topic-' . $term->slug . '"><a href="' . $link . '"><div><i></i></div><span>' . $term->name . '</span></a></div>';
      return apply_filters('single_term_title', $term);
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