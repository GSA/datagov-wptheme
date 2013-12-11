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
  } elseif (is_archive() OR is_single()) {
    if(is_single()) {        
        global $post;                
        if ($post) {
            $term = get_the_category($post->ID);
        }
    } else {
        $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));   
        $link = '/' . get_query_var('term');
    }
    
    if(is_array($term)) {
        $term = $term[0];
    }
   
    if ($term) {
      $term = (isset($link)) ? '<a href="' . $link . '">' . $term->name . '</a>' : $term->name;
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