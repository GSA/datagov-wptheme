<?php
/*
Plugin Name: ArcGis Map Validation
Description: This plugin validates map using the the map id and group id 
*/
    if (!session_id())
        session_start();
    add_action('save_post','validate_map_id' );
    add_action( 'admin_notices', 'my_admin_notices');

    function validate_map_id($post_id,$post){
        $slug = "arcgis_maps";
        $_SESSION['my_admin_notices']="";
        if ( $slug != $_POST['post_type'] ) {
            return true;
        } else {
            $server = get_post_meta( get_the_ID(), 'arcgis_server_address', true );
            $map_id = get_post_meta( get_the_ID(), 'map_id', true );
            $map_id = preg_replace('/[^A-Fa-f0-9]+/', '', $map_id);
            if (empty($server) || empty($map_id))  {
                // TODO let default validate catch it.
                return;
            }
            $request = get_arcgis_map_info($server, $map_id, '', 0);
            if ($request['message']!="OK") {
                $_SESSION['my_admin_notices'] .= '<div class="error"><p>Error fetching map. Please check accuracy of the server address and map ID.</p></div>';
                return false;
            }
        }
    }

    function my_admin_notices(){
        if(!empty($_SESSION['my_admin_notices'])) print  $_SESSION['my_admin_notices'];
        unset ($_SESSION['my_admin_notices']);
    }

    function get_arcgis_map_info($server, $map_id, $group_id=NULL, $display) {
        if(!empty($map_id)){
            $url =  $server. "/sharing/content/items/$map_id/info/iteminfo.xml";

        }
           /* elseif(!empty($group_id) && $display == 0) {
                $url =  $server. "/sharing/community/groups?q=id:".$group_id."&f=json&_=".time();
            }
            elseif(!empty($group_id) && $display == 1) {
                $url =  $server."/sharing/search?_=".time()."&q=group:".$group_id."&f=json&sortField=uploaded&sortOrder=desc&num=99&start=1";
                $url2 =  $server. "/sharing/community/groups?q=id:".$group_id."&f=json&_=".time();
                $result[1] = drupal_http_request($url2);
            }*/
        $response = wp_remote_get( $url );
        $result['body'] = wp_remote_retrieve_body($response) ;
        $result['code'] = wp_remote_retrieve_response_code( $response );
        $result['message'] = wp_remote_retrieve_response_message( $response );
        return $result;
    }


function arcgis_map_process_info($server, $map_id, $group_id,$display=null) {
        // call the info based on national or regional type


        $request = get_arcgis_map_info($server, $map_id, $group_id, 1);
        if ($request['message']!="OK") {
            $returnval= '<div class="error"><p>An error occurred and processing did not complete.</p><br></div>';
            return;
        }

            // Parse the xml. TODO put into a function.
        $map = new SimpleXMLElement($request['body']);

        $vars['title'] = strip_tags($map->title->asXML());
        $vars['description'] = strip_tags($map->description->asXML());
        $vars['snippet'] = strip_tags($map->snippet->asXML());
        $vars['thumbnail_src'] = $server . '/sharing/content/items/' . $map_id . '/info/' . strip_tags($map->thumbnail->asXML());
        $vars['type'] = 'Map';
        if($map->type == "Web Map")
            $vars['img_href'] = $server . '/home/webmap/viewer.html?webmap=' . $map_id;
        elseif($map->type == "Map Service" || $map->type == "WMS")
            $vars['img_href'] = $server . '/home/webmap/viewer.html?services=' . $map_id;
        else
            $vars['img_href'] = $server . '/home/item.html?id=' . $map_id;
        return $vars;
}






?>
