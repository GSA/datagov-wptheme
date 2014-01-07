<?php
/**
 * Cleaner walker for wp_nav_menu()
 *
 * Walker_Nav_Menu (WordPress default) example output:
 *   <li id="menu-item-8" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-8"><a href="/">Home</a></li>
 *   <li id="menu-item-9" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-9"><a href="/sample-page/">Sample Page</a></l
 *
 * Roots_Nav_Walker example output:
 *   <li class="menu-home"><a href="/">Home</a></li>
 *   <li class="menu-sample-page"><a href="/sample-page/">Sample Page</a></li>
 */
class Roots_Nav_Walker extends Walker_Nav_Menu {
  function check_current($classes) {
    return preg_match('/(current[-_])|active|dropdown/', $classes);
  }

  function start_lvl(&$output, $depth = 0, $args = array()) {
    $output .= "\n<ul class=\"dropdown-menu\">\n";
  }

  function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    $item_html = '';
    parent::start_el($item_html, $item, $depth, $args);

    if ($item->is_dropdown && ($depth === 0)) {
      $item_html = str_replace('<a', '<a class="dropdown-toggle" data-toggle="dropdown" data-target="#"', $item_html);
      $item_html = str_replace('</a>', ' <b class="caret"></b></a>', $item_html);
    }
    elseif (stristr($item_html, 'li class="divider')) {
      $item_html = preg_replace('/<a[^>]*>.*?<\/a>/iU', '', $item_html);
    }
    elseif (stristr($item_html, 'li class="dropdown-header')) {
      $item_html = preg_replace('/<a[^>]*>(.*)<\/a>/iU', '$1', $item_html);
    }

    $item_html = apply_filters('roots_wp_nav_menu_item', $item_html);
    $output .= $item_html;
  }

  function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) {
    $element->is_dropdown = ((!empty($children_elements[$element->ID]) && (($depth + 1) < $max_depth || ($max_depth === 0))));

    if ($element->is_dropdown) {
      $element->classes[] = 'dropdown';
    }

    parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
  }
}


class Datagov_Nav_Walker extends Roots_Nav_Walker {

  function start_lvl(&$output, $depth = 0, $args = array()) {
    $output .= "\n<ul class=\"dropdown-menu topics\">\n";
  }

  function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    $item_html = '';
    parent::start_el($item_html, $item, $depth, $args);

    if ($depth === 1) {
      $item_html = preg_replace('/(<a[^>]*>)(.*)<\/a>/iU', '$1<i></i><span>$2</span></a>', $item_html);
      
      $s = explode('">',$item_html);
      foreach($s as $k){
         if (strpos($k,"href")!==FALSE){
              $url = preg_replace('/.*href="|/ms',"",$k);
              break;
         }
      }

      $slug = parse_url($url, PHP_URL_PATH);
      $slug = str_replace('/', '', $slug);

      $menu_slug = sanitize_title($item->title);
      $class = 'menu-' . $menu_slug;
      $new_class = $class . ' topic-' . $slug;
      $item_html = preg_replace('/'.$class.'/iU', $new_class, $item_html);      
    }

    $item_html = apply_filters('roots_wp_nav_menu_item', $item_html);
    $output .= $item_html;
  }

  function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) {
    $element->is_dropdown = ((!empty($children_elements[$element->ID]) && (($depth + 1) < $max_depth || ($max_depth === 0))));

    if ($element->is_dropdown) {
      $element->classes[] = 'dropdown';
      $element->classes[] = 'yamm-fw';      
    }

    parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
  }

}



/**
 * Remove the id="" on nav menu items
 * Return 'menu-slug' for nav menu classes
 */
function roots_nav_menu_css_class($classes, $item) {
  $slug = sanitize_title($item->title);
  $classes = preg_replace('/(current(-menu-|[-_]page[-_])(item|parent|ancestor))/', 'active', $classes);
  $classes = preg_replace('/^((menu|page)[-_\w+]+)+/', '', $classes);

  $classes[] = 'menu-' . $slug;

  $classes = array_unique($classes);

  return array_filter($classes, 'is_element_empty');
}
add_filter('nav_menu_css_class', 'roots_nav_menu_css_class', 10, 2);
add_filter('nav_menu_item_id', '__return_null');

/**
 * Clean up wp_nav_menu_args
 *
 * Remove the container
 * Use Roots_Nav_Walker() by default
 */
function roots_nav_menu_args($args = '') {
  $roots_nav_menu_args['container'] = false;

  if (!$args['items_wrap']) {
    $roots_nav_menu_args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';
  }

  if (current_theme_supports('bootstrap-top-navbar') && !$args['depth']) {
    $roots_nav_menu_args['depth'] = 2;
  }

  if (!$args['walker']) {
    $roots_nav_menu_args['walker'] = new Roots_Nav_Walker();
  }

  return array_merge($args, $roots_nav_menu_args);
}
add_filter('wp_nav_menu_args', 'roots_nav_menu_args');



/**
 * Pagination
 */
 if ( !function_exists('your_pagination')) :
   function your_pagination($custom_query) {

     if ( !$current_page = get_query_var( 'paged' ) ) $current_page = 1;

     $permalinks = get_option( 'permalink_structure' );
     if( is_front_page() ) {
       $format = empty( $permalinks ) ? '?paged=%#%' : 'page/%#%/';
     } else {
       $format = empty( $permalinks ) || is_search() ? '&paged=%#%' : 'page/%#%/';
     }

   $big = 999999999; // need an unlikely integer

   $pagination = paginate_links( array(
     'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
     'format' => $format,
     'current' => $current_page,
     'total' => $custom_query->max_num_pages,
     'mid_size' => '4',
     'type' => 'list',
     'next_text' => __( 'Next' ),
     'prev_text' => __( 'Previous' )
     ) );

   $pagination = explode( "\n", $pagination );
   $pagination_mod = array();

   foreach ( $pagination as $item ) {
     ( preg_match( '/<ul class=\'page-numbers\'>/i', $item ) ) ? $item = str_replace( '<ul class=\'page-numbers\'>', '<ul class=\'pagination\'>', $item ) : $item;
     ( preg_match( '/class="prev/i', $item ) ) ? $item = str_replace( '<li', '<li class="pagination-prev"', $item ) : $item;
     ( preg_match( '/class="next/i', $item ) ) ? $item = str_replace( '<li', '<li class="pagination-next"', $item ) : $item;
     ( preg_match( '/page-numbers/i', $item ) ) ? $item = str_replace( 'page-numbers', 'page-numbers pagenav', $item ) : $item;
     $pagination_mod[] .= $item;
   }

   ?>

   <div class="pagination">

     <p class="counter">
       <?php printf( __( 'Page %1$s of %2$s' ), $current_page, $custom_query->max_num_pages ); ?>
     </p>

     <?php foreach( $pagination_mod as $page ) {
       echo $page;
     } ?>

   </div>

   <?php }
 endif;
