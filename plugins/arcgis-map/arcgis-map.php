<?php
/*
Plugin Name: ArcGis Map Validation
Description: This plugin validates map using the the map id and group id 
*/

add_action('admin_menu', 'arcgis_map_settings');

if (!session_id())
    session_start();
add_action('save_post', 'validate_map_id');
add_action('admin_notices', 'my_admin_notices');
add_filter('redirect_post_location', 'arcgismap_redirect_location', 10, 2);

function arcgis_national_map_process_details()
{
    global $map_results;

    $map_results = array();
    $args = array(
        'meta_key' => 'map_category',
        'meta_value' => 'national',
        'post_type' => 'arcgis_maps',
        'post_status' => 'publish'
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) : $query->the_post();
            $map_category = get_post_meta(get_the_ID(), 'map_category', TRUE);
            $server = get_post_meta(get_the_ID(), 'arcgis_server_address', TRUE);
            $map_id = get_post_meta(get_the_ID(), 'map_id', TRUE);
            $group_id = get_post_meta(get_the_ID(), 'group_id', TRUE);
            $map_results[] = arcgis_map_process_info($server, $map_id, $group_id, 1);
        endwhile;
        // var_dump($map_results);
        wp_reset_query();
    }
}

function arcgis_regional_map_process_details()
{
    global $regional_map_results;

    $regional_map_results = array();

    $args = array(
        'meta_key' => 'map_category',
        'meta_value' => 'regional',
        'post_type' => 'arcgis_maps',
        'post_status' => 'publish'
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) : $query->the_post();
            $map_category = get_post_meta(get_the_ID(), 'map_category', TRUE);
            $server = get_post_meta(get_the_ID(), 'arcgis_server_address', TRUE);
            $map_id = get_post_meta(get_the_ID(), 'map_id', TRUE);
            $group_id = get_post_meta(get_the_ID(), 'group_id', TRUE);
            $regional_map_results[] = arcgis_map_process_info($server, $map_id, $group_id, 1);
        endwhile;
        wp_reset_query();
    }
}

function arcgis_map_settings()
{

    add_menu_page('ArcGis Map Settings', 'ArcGis Map Settings', 'administrator', 'map_config', 'arcgis_map_config');

}

function arcgis_map_config()
{

    $arcgis_maps_per_page = (get_option('arcgis_maps_per_page') != '') ? get_option('arcgis_maps_per_page') : '4';
    $arcgis_default_server = (get_option('arcgis_default_server') != '') ? get_option('arcgis_default_server') : 'http://www.geoplatform.gov';
    $arcgis_refresh_cache = (get_option('arcgis_refresh_cache') != '') ? get_option('arcgis_refresh_cache') : '15';
    ?>
    <form action="options.php" method="post" name="options">
        <h2>ArcGis Map Configuration</h2> <?php wp_nonce_field('update-options'); ?>
        <div>
            <div class="form-item form-type-select form-item-mapsperpage">
                <label for="edit-mapsperpage">Maps per page <span title="This field is required." class="form-required">*</span></label>
                <select class="form-select required" name="arcgis_maps_per_page" id="arcgis_maps_per_page">
                    <option value="1" <?php if ($arcgis_maps_per_page == 1) {
                        echo 'selected="selected"';
                    } ?>> 1
                    </option>
                    <option value="2" <?php if ($arcgis_maps_per_page == 2) {
                        echo 'selected="selected"';
                    } ?>> 2
                    </option>
                    <option value="3" <?php if ($arcgis_maps_per_page == 3) {
                        echo 'selected="selected"';
                    } ?>> 3
                    </option>
                    <option value="4" <?php if ($arcgis_maps_per_page == 4) {
                        echo 'selected="selected"';
                    } ?>>4
                    </option>
                    <option value="8" <?php if ($arcgis_maps_per_page == 8) {
                        echo 'selected="selected"';
                    } ?>>8
                    </option>
                    <option value="12" <?php if ($arcgis_maps_per_page == 12) {
                        echo 'selected="selected"';
                    } ?>>12
                    </option>
                    <option value="16" <?php if ($arcgis_maps_per_page == 16) {
                        echo 'selected="selected"';
                    } ?>>16
                    </option>
                    <option value="24" <?php if ($arcgis_maps_per_page == 24) {
                        echo 'selected="selected"';
                    } ?>>24
                    </option>
                </select>
            </div>
            <div class="form-item form-type-textfield form-item-server-info">
                <label for="edit-server-info">Default Server <span title="This field is required."
                                                                   class="form-required">*</span></label>
                <input type="text" class="form-text required" maxlength="128" size="60"
                       value="<?= $arcgis_default_server ?>" name="arcgis_default_server" id="arcgis_default_server">

                <div class="description">Please enter the server info. Example: http://www.geoplatform.gov</div>
            </div>
            <div class="form-item form-type-select form-item-refresh-cache">
                <label for="edit-refresh-cache">Refresh Cache </label>
                <select class="form-select" name="arcgis_refresh_cache" id="arcgis_refresh_cache">
                    <option value="5" <?php if($arcgis_refresh_cache == 5) { echo 'selected="selected"'; }?>>5 mins</option>
                    <option value="15" <?php if($arcgis_refresh_cache == 15) { echo 'selected="selected"'; }?>>15 mins</option>
                    <option value="30" <?php if($arcgis_refresh_cache == 30) { echo 'selected="selected"'; }?>>30 mins</option>
                    <option value="1440" <?php if($arcgis_refresh_cache == 1440) { echo 'selected="selected"'; }?>>1 day</option>
                    <option value="2880" <?php if($arcgis_refresh_cache == 2880) { echo 'selected="selected"'; }?>>2 days</option>
                    <option value="4320" <?php if($arcgis_refresh_cache == 4320) { echo 'selected="selected"'; }?>>3 days</option>
                </select>

                <div class="description">Please select time interval after which cache will be refereshed.</div>
            </div>
        </div>
        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="page_options"
               value="arcgis_maps_per_page,arcgis_default_server,arcgis_refresh_cache"/>
        <input type="submit" name="Submit" value="Update"/>
    </form>
<?php
}

function validate_map_id($post_id)
{
    $slug = "arcgis_maps";
    $_SESSION['my_admin_notices'] = "";
    $prevent_publish = false;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!isset($_POST['post_type']) || ($slug != $_POST['post_type'])) {
        return true;
    } else {
        $server = get_post_meta(get_the_ID(), 'arcgis_server_address', true);
        $add_maps = get_post_meta(get_the_ID(), 'add_maps', true);
        $map_id = get_post_meta(get_the_ID(), 'map_id', true);
        $group_id = get_post_meta(get_the_ID(), 'group_id', true);
        if ($add_maps == "group_id")
            unset($map_id);
        else
            unset($group_id);
        $map_id = preg_replace('/[^A-Fa-f0-9]+/', '', $map_id);
        if (empty($server) || (empty($map_id) && empty($group_id))) {
            /* $_SESSION['my_admin_notices'] .= '<div class="error"><p>Error fetching map. Please check accuracy of the server address and map ID/group ID.</p></div>';
             remove_action('save_post', 'validate_map_id');
             wp_update_post(array('ID' => $post_id, 'post_status' => 'draft'));
             add_action('save_post', 'save_post');
             return ;*/
            // TODO let default validate catch it.
            //return;
        }
        $request = get_arcgis_map_info($server, $map_id, $group_id, 0);
        if($add_maps == "group_id"){
            if($request['total'] == 0){
                $prevent_publish = true;
                $_SESSION['my_admin_notices'] .= "<div class='error'><p>Error fetching map. Please check accuracy of the server address and map ID/group ID.</p></div>";
            }
        } else if ($add_maps == "map_id") {
            if($request['message'] != "OK"){
                $prevent_publish = true;
                $_SESSION['my_admin_notices'] .= '<div class="error"><p>Error fetching map. Please check accuracy of the server address and map ID/group ID.</p></div>';
            }

        }
    }
    if ($prevent_publish) {
        remove_action('save_post', 'validate_map_id');
        wp_update_post(array('ID' => $post_id, 'post_status' => 'draft'));
        add_action('save_post', 'save_post');
        return;
    } else {
        unset($_SESSION['my_admin_notices']);
    }

}

function my_admin_notices($post_id)
{
    if (!empty($_SESSION['my_admin_notices'])) print  $_SESSION['my_admin_notices'];
    unset ($_SESSION['my_admin_notices']);
}

function arcgismap_redirect_location($location, $post_id)
{
    if (isset($_POST['publish'])) {
        $status = get_post_status($post_id);
        if ($status == 'draft')
            $location = add_query_arg('message', 10, $location);
    }
    return $location;
}

function get_arcgis_map_info($server, $map_id = null, $group_id = null, $display)
{
    if (!empty($map_id)) {
        $url = $server . "/sharing/content/items/$map_id/info/iteminfo.xml";

    }
    if (!empty($group_id) && $display == 0) {
        $url = $server . "/sharing/search?_=" . time() . "&q=group:" . $group_id . "&f=json&sortField=uploaded&sortOrder=desc&num=99&start=1";
    } elseif (!empty($group_id) && $display == 1) {
        $url = $server . "/sharing/search?_=" . time() . "&q=group:" . $group_id . "&f=json&sortField=uploaded&sortOrder=desc&num=99&start=1";
        $url2 = $server . "/sharing/community/groups?q=id:" . $group_id . "&f=json&_=" . time();
        //$result[1] = drupal_http_request($url2);
    }
    $response = wp_remote_get($url);

//        wp_remote_get returns WP_Error object on failure
    if (!is_array($response)) {
        return false;
    }

    $result['body'] = wp_remote_retrieve_body($response);
    $result['code'] = wp_remote_retrieve_response_code($response);
    $result['message'] = wp_remote_retrieve_response_message($response);
    if (!empty($group_id)) {
        $result['total'] = json_decode($response["body"])->total;
    }
    return $result;
}


function arcgis_map_process_info($server, $map_id, $group_id, $display)
{
    /*$arg_list = func_get_args();
    $numargs = func_num_args();
    for ($i = 0; $i < $numargs; $i++) {
        echo "Argument $i is: " . $arg_list[$i] . "<br />\n";
    }*/
    // call the info based on national or regional type
    //global $vars;
    $request = get_arcgis_map_info($server, $map_id, $group_id, $display);
    if ($request['message'] != "OK") {
//        $returnval= '<div class="error"><p>An error occurred and processing did not complete.</p><br></div>';
        return;
    }
    $count = 0;
    if (!empty($group_id) && isJson($request['body'])) {
        $count++;
        $info = (array)json_decode($request['body']);
        $vars['info']['title'] = strip_tags($info["results"][0]->title);
        $vars['info']['description'] = strip_tags($info["results"][0]->description);
        $vars['info']['snippet'] = strip_tags($info["results"][0]->snippet);
        $vars['info']['thumbnail_src'] = $server . '/sharing/community/groups/' . strip_tags($info["results"][0]->id) . '/info/' . strip_tags($info["results"][0]->thumbnail);
        $vars['info']['type'] = 'Group';
        $res = sizeof($info['results']);
        $vars['map_info']['total_maps'] = $res;

        for ($i = 0; $i < $res; $i++) {

            $title = $info['results'][$i]->title;
            if (empty($title)) {
                continue;
            }
            $vars['map_info'][$i]['title'] = $title;
            if ($info['results'][$i]->type == "Web Map")
                $vars['map_info'][$i]['img_href'] = $server . '/home/webmap/viewer.html?webmap=' . $info['results'][$i]->id;
            elseif ($info['results'][$i]->type == "Map Service" || $info['results'][$i]->type == "WMS")
                $vars['map_info'][$i]['img_href'] = $server . '/home/webmap/viewer.html?services=' . $info['results'][$i]->id;
            else
                $vars['map_info'][$i]['img_href'] = $server . '/home/item.html?id=' . $info['results'][$i]->id;
            $vars['map_info'][$i]['map_details'] = $server . '/home/item.html?id=' . $info['results'][$i]->id;
            $vars['map_info'][$i]['img_src'] = $server . '/sharing/content/items/' . $info['results'][$i]->id . '/info/' . $info['results'][$i]->thumbnail;
        }
    } else {
        try {
            $map = new SimpleXMLElement($request['body']);
            $vars['info']['title'] = strip_tags($map->title->asXML());
            $vars['info']['description'] = strip_tags($map->description->asXML());
            $vars['info']['snippet'] = strip_tags($map->snippet->asXML());
            $vars['info']['thumbnail_src'] = $server . '/sharing/content/items/' . $map_id . '/info/' . strip_tags($map->thumbnail->asXML());
            $vars['info']['type'] = 'Map';
            // Pass info into theme.
            $vars['map_info'][0]['title'] = strip_tags($map->title->asXML());
            if ($map->type == "Web Map")
                $vars['map_info'][0]['img_href'] = $server . '/home/webmap/viewer.html?webmap=' . $map_id;
            elseif ($map->type == "Map Service" || $map->type == "WMS")
                $vars['map_info'][0]['img_href'] = $server . '/home/webmap/viewer.html?services=' . $map_id;
            else
                $vars['map_info'][0]['img_href'] = $server . '/home/item.html?id=' . $map_id;
            $vars['map_info'][0]['map_details'] = $server . '/home/item.html?id=' . $map_id;
            $vars['map_info'][0]['img_src'] = $server . '/sharing/content/items/' . $map_id . '/info/' . strip_tags($map->thumbnail->asXML());
        } catch (Exception $x) {
            error_log($x->getMessage(), E_WARNING);
            //return array();
        }

    }
    return $vars;
}

function isJson($string)
{
    //check if input is string
    if (is_string($string))
        json_decode($string);

    return (json_last_error() == JSON_ERROR_NONE);
}

function subval_sort($a, $subkey)
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