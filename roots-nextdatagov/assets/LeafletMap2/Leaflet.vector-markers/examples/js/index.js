import { VectorMarkers } from '../../src';
import css from "!style!css!sass!../../src/leaflet-vector-markers.scss"

var map = L.map('map').setView([48.15491,11.54183], 14)

L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
  attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
  detectRetina: true
}).addTo(map)

L.marker([48.15491,11.54183], {icon: L.VectorMarkers.icon({icon: 'spinner', prefix: 'fa', markerColor: '#cb4b16', spin: true}) }).addTo(map)

L.marker([48.15391,11.53283], {icon: L.VectorMarkers.icon({icon: 'rocket', prefix: 'fa', markerColor: '#002b36', iconColor: '#eee8d5'}) }).addTo(map)

L.marker([48.15391,11.52283], {icon: L.VectorMarkers.icon({icon: 'rocket', prefix: 'fa', markerColor: '#eee8d5', iconColor: '#002b36'}) }).addTo(map)
