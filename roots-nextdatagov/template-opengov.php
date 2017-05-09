<?php
/*
Template Name: OpenGov
*/
?>
<?php get_template_part('templates/page', 'header'); ?>
<?php get_template_part('templates/content', 'opengov'); ?> 

<?php 
	wp_enqueue_style('leaflet', get_template_directory_uri().'/assets/LeafletMap2/leaflet/leaflet.css');
	wp_enqueue_style('leaflet-search', get_template_directory_uri().'/assets/LeafletMap2/leaflet-search/src/leaflet-search.css');
	wp_enqueue_style( 'leaflet.defaultextent', get_template_directory_uri().'/assets/LeafletMap2/Leaflet.defaultextent/dist/leaflet.defaultextent.css');
	wp_enqueue_style( 'L.Control.Pan', get_template_directory_uri().'/assets/LeafletMap2/Leaflet.Pancontrol/src/L.Control.Pan.css');
	wp_enqueue_style( 'leaflet-vector-markers', get_template_directory_uri().'/assets/LeafletMap2/Leaflet.vector-markers/dist/leaflet-vector-markers.css');
	wp_enqueue_style( 'style', get_template_directory_uri().'/assets/LeafletMap2/style.css');

	wp_enqueue_script( 'leaflet', get_template_directory_uri().'/assets/LeafletMap2/leaflet/leaflet.js');
	wp_enqueue_script( 'leaflet-search', get_template_directory_uri().'/assets/LeafletMap2/leaflet-search/src/leaflet-search.js');
	wp_enqueue_script( 'leaflet.defaultextent', get_template_directory_uri().'/assets/LeafletMap2/Leaflet.defaultextent/dist/leaflet.defaultextent.js');
	wp_enqueue_script( 'L.Control.Pan', get_template_directory_uri().'/assets/LeafletMap2/Leaflet.Pancontrol/src/L.Control.Pan.js');
	wp_enqueue_script( 'leaflet-vector-markers.min', get_template_directory_uri().'/assets/LeafletMap2/Leaflet.vector-markers/dist/leaflet-vector-markers.min.js');
	wp_enqueue_script( 'international', get_template_directory_uri().'/assets/LeafletMap2/data/international.js');
	wp_enqueue_script( 'us_states', get_template_directory_uri().'/assets/LeafletMap2/data/us_states.js');
	wp_enqueue_script( 'us_cities_counties', get_template_directory_uri().'/assets/LeafletMap2/data/us_cities_counties.js');
?>
<!-- <script src="../app/themes/roots-nextdatagov/assets/LeafletMap2/Leaflet.ZoomBox/L.Control.ZoomBox.min.js"></script> -->
<div class = "container-fluid">
    <div class = "col-sm-12">	
        <div id="mapid"></div>
    </div>
</div>

<?php
	wp_enqueue_script('main', get_template_directory_uri().'/assets/LeafletMap2/main.js');
?>
