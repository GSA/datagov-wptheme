<?php
/*
Template Name: OpenGov
*/
?>
<?php get_template_part('templates/page', 'header'); ?>
<?php get_template_part('templates/content', 'opengov'); ?> 

<link rel="stylesheet" href="../app/themes/roots-nextdatagov/assets/LeafletMap2/leaflet/leaflet.css" />
<link rel="stylesheet" href="../app/themes/roots-nextdatagov/assets/LeafletMap2/leaflet-search/src/leaflet-search.css" />
<!-- <link rel="stylesheet" type="text/css" href="../app/themes/roots-nextdatagov/assets/LeafletMap2/Leaflet.ZoomBox/L.Control.ZoomBox.css"> -->
<link rel="stylesheet" type="text/css" href="../app/themes/roots-nextdatagov/assets/LeafletMap2/Leaflet.defaultextent/dist/leaflet.defaultextent.css">
<link rel="stylesheet" type="text/css" href="../app/themes/roots-nextdatagov/assets/LeafletMap2/Leaflet.Pancontrol/src/L.Control.Pan.css">
<link rel="stylesheet" type="text/css" href="../app/themes/roots-nextdatagov/assets/LeafletMap2/Leaflet.vector-markers/dist/leaflet-vector-markers.css">
<link rel="stylesheet" type="text/css" href="../app/themes/roots-nextdatagov/assets/LeafletMap2/style.css">

<script src="../app/themes/roots-nextdatagov/assets/LeafletMap2/leaflet/leaflet.js"></script>
<script src="../app/themes/roots-nextdatagov/assets/LeafletMap2/leaflet-search/src/leaflet-search.js"></script>
<!-- <script src="../app/themes/roots-nextdatagov/assets/LeafletMap2/Leaflet.ZoomBox/L.Control.ZoomBox.min.js"></script> -->
<script src="../app/themes/roots-nextdatagov/assets/LeafletMap2/Leaflet.defaultextent/dist/leaflet.defaultextent.js"></script>
<script src="../app/themes/roots-nextdatagov/assets/LeafletMap2/Leaflet.Pancontrol/src/L.Control.Pan.js"></script>
<script src="../app/themes/roots-nextdatagov/assets/LeafletMap2/Leaflet.vector-markers/dist/leaflet-vector-markers.min.js"></script>
<script src="../app/themes/roots-nextdatagov/assets/LeafletMap2/data/international.js"></script>
<script src="../app/themes/roots-nextdatagov/assets/LeafletMap2/data/us_states.js"></script>
<script src="../app/themes/roots-nextdatagov/assets/LeafletMap2/data/us_cities_counties.js"></script>

<div class = "container-fluid">
    <div class = "col-sm-12">	
        <div id="mapid"></div>
    </div>
</div>
	
<script src="../app/themes/roots-nextdatagov/assets/LeafletMap2/main.js"></script>
