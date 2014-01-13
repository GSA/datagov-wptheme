<?php
/**
 * Output the navigation footer and primary menu too a json file
 */
require_once('../../../wp-load.php');
header('content-type application/json charset=utf-8');
// For Footer
$menu_name = 'footer_navigation';
$json = ' jsonCallback({';
if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
    $menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
    $menu_items = wp_get_nav_menu_items($menu->term_id);
    $json .= '"Footer":[';
    $count = 0;
    $count_menu_items = count($menu_items);
    foreach ( (array) $menu_items as $key => $menu_item ) {
        if($menu_item->menu_item_parent==0)
            $json .= '{'.'"'.'name'.'":'.'"'.esc_attr($menu_item->title).'",'.'"'.'link'.'":'.'"'.esc_attr($menu_item->url).'",'.'"'.'id'.'":'.'"'.$menu_item->db_id.'"'.'}';
        else
            $json .= '{'.'"'.'name'.'":'.'"'.esc_attr($menu_item->title).'",'.'"'.'link'.'":'.'"'.esc_attr($menu_item->url).'",'.'"'.'id'.'":'.'"'.$menu_item->db_id.'",'.'"'.'parent_id'.'":'.'"'.$menu_item->menu_item_parent.'"'.'}';
        if($count!=$count_menu_items-1)
            $json .=",";

        $count++;
    }
    $json .= ']';
}


// primary links
$menu_name = 'primary_navigation';
if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
    $menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
    $menu_items = wp_get_nav_menu_items($menu->term_id);
    $json .= ', "Primary":[';
    $count = 0;
    $count_menu_items = count($menu_items);
    foreach ( (array) $menu_items as $key => $menu_item ) {
        if($menu_item->menu_item_parent==0)
            $json .= '{'.'"'.'name'.'":'.'"'.esc_attr($menu_item->title).'",'.'"'.'link'.'":'.'"'.esc_attr($menu_item->url).'",'.'"'.'id'.'":'.'"'.$menu_item->db_id.'"'.'}';
        else
            $json .= '{'.'"'.'name'.'":'.'"'.esc_attr($menu_item->title).'",'.'"'.'link'.'":'.'"'.esc_attr($menu_item->url).'",'.'"'.'id'.'":'.'"'.$menu_item->db_id.'",'.'"'.'parent_id'.'":'.'"'.$menu_item->menu_item_parent.'"'.'}';
        if($count!=$count_menu_items-1)
            $json .=",";

        $count++;
    }
    $json .= ']';
}
$json .= '});';
print(prettyPrint($json));


function prettyPrint( $json ){
    $result = '';
    $level = 0;
    $prev_char = '';
    $in_quotes = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if( $char === '"' && $prev_char != '\\' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                $level--;
                $ends_line_level = NULL;
                $new_line_level = $level;
                break;

                case '{': case '[':
                $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                $char = "";
                $ends_line_level = $new_line_level;
                $new_line_level = NULL;
                break;
            }
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
        $prev_char = $char;
    }

    return $result;
}

