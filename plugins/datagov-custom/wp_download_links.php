<?php
/**
 * Output the navigation footer and primary menu too a json file
 */
require_once('../../../wp-load.php');
header('content-type application/json charset=utf-8');
// For Footer
$menu_name = 'footer_navigation';
$json = '{';
if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
    $menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
    $menu_items = wp_get_nav_menu_items($menu->term_id);
    $json .= '"Footer":[';
    foreach ( (array) $menu_items as $key => $menu_item ) {
        $title = $menu_item->title;
        $url = $menu_item->url;
        $json .= '{'.'"'.'name'.'":'.'"'.esc_attr($title).'",'.'"'.'link'.'":'.'"'.esc_attr($url).'"'.'}';
    }
    $json .= ']';
}


// primary links
$menu_name = 'primary_navigation';
if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
    $menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
    $menu_items = wp_get_nav_menu_items($menu->term_id);
    $json .= '"Primary":[';
    foreach ( (array) $menu_items as $key => $menu_item ) {
        $title = $menu_item->title;
        $url = $menu_item->url;
        $json .= '{'.'"'.'name'.'":'.'"'.esc_attr($title).'",'.'"'.'link'.'":'.'"'.esc_attr($url).'"'.'}';
    }
    $json .= ']';
}
$json .= '};';
print($json);
