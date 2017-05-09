/* global L */

var mapCenter = [29.76, -95.38];

// Set up map
var map = L.map('map', {
  defaultExtentControl: true,
  center: mapCenter,
  zoom: 14
});

// Another way to add to map
// L.control.defaultExtent().addTo(map);

L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
  maxZoom: 18,
  id: 'examples.map-i86knfo3'
}).addTo(map);
