<?php
/**
 * Sample template for displaying single arcgis_map posts.
 * Save this file as as single-arcgis_map.php in your current theme.
 *
 * This sample code was based off of the Starkers Baseline theme: http://starkerstheme.com/
 */

get_header(); ?>

<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>



<h1><?php //the_title(); ?></h1>
<?php //the_content(); ?>
<?php
   $server=get_field('arcgis_server_address');
   $map_id = get_field('map_id');
   $request = get_arcgis_map_info($server, $map_id, $group_id, 1);
    $map = new SimpleXMLElement($request[0][body]);
    $vars['info']['title'] = strip_tags($map->title->asXML());
    $vars['info']['description'] = strip_tags($map->description->asXML());
    $vars['info']['snippet'] = strip_tags($map->snippet->asXML());
    $vars['info']['thumbnail_src'] = $server . '/sharing/content/items/' . $map_id . '/info/' . strip_tags($map->thumbnail->asXML());
    $vars['info']['type'] = 'Map';

    $output = "";

    if(isset($vars)) {
    $output .= '<div id="infoTitle">';
    if($vars['info']['type'] == "Map")
         $output .= "<h2>Map Information</h2>";
    else
         $output .= "<h2>Group Information</h2>";

    $output .= '</div>';
    $output .= '<div class="groupImg"><h3><img class="groupThumbnail" src="' . htmlspecialchars_decode($vars['info']['thumbnail_src']) . '" title="' . $vars['info']['title'] . '">';
    $output .= htmlspecialchars_decode($vars['info']['title']) . '</h3></div>';
    $output .=  '<div class="groupDesc"><div class="expandable">' . htmlspecialchars_decode($vars['info']['snippet']) . '</div></div>';
    }
    $output .= '<br><div id="galleryTitle"><h2>Map Gallery</h2></div>';
    $output .= '<div class="map-gallery-wrap">';

    echo $output;
?>
<!-- You have not associated any custom fields with this post-type. Be sure to add any desired custom fields to this post-type by clicking on the "Manage Custom Fields" link under the Custom Content Type menu and checking the fields that you want. -->

<?php endwhile; // end of the loop. ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>

<?php



function get_arcgis_map_info($server, $map_id, $group_id, $display) {
    $result ="";
    if(!empty($map_id)){
        $url =  $server. "/sharing/content/items/$map_id/info/iteminfo.xml";
    }
    elseif(!empty($group_id) && $display == 0) {
        $url =  $server. "/sharing/community/groups?q=id:".$group_id."&f=json&_=".time();
    }
    elseif(!empty($group_id) && $display == 1) {
        $url =  $server."/sharing/search?_=".time()."&q=group:".$group_id."&f=json&sortField=uploaded&sortOrder=desc&num=99&start=1";
        $url2 =  $server. "/sharing/community/groups?q=id:".$group_id."&f=json&_=".time();
        $result[1] = wp_remote_get($url2);
    }
    $result[0] = wp_remote_get($url);
    return $result;
}

?>