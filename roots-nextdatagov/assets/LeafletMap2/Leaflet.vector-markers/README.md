Leaflet.vector-markers
======================

Vector SVG markers for Leaflet, with an option for Font Awesome/Twitter Bootstrap/Maki icons.

Thanks to https://github.com/lvoogdt for https://github.com/lvoogdt/Leaflet.awesome-markers.

Version 0.0.6 of Leaflet.vector-markers is tested with:
- Bootstrap 3
- Font Awesome 4.3
- Leaflet 0.7.7
- Maki icon 0.4.2

## Example

Please have a look at the examples or check out this example: http://codepen.io/anon/pen/Jdayb.

### Twitter Bootstrap/Font-Awesome icons
This plugin depends on either Bootstrap or Font-Awesome for the rendering of the icons. See these urls for more information:

For Font-Awesome
- http://fortawesome.github.com/Font-Awesome/
- http://fortawesome.github.com/Font-Awesome/#integration

For Twitter bootstrap:
- http://getbootstrap.com

For Maki icons:
- https://www.mapbox.com/maki/

## Using the plugin
- 1) First, follow the steps for including Font-Awesome or Twitter bootstrap into your application.

For Font-Awesome, steps are located here:

http://fortawesome.github.io/Font-Awesome/get-started/

For Twitter bootstrap, steps are here:

http://getbootstrap.com/getting-started/


- 2) Next, copy the leaflet-vector-markers.css, and leaflet-vector-markers.js from dist/ to your project and include them:
````xml
<link rel="stylesheet" href="css/leaflet-vector-markers.css">
````
````xml
<script src="js/leaflet-vector-markers.js"></script>
````

- 3) Now use the plugin to create a marker like this:
````js
  // Creates a red marker with the coffee icon
  var redMarker = L.VectorMarkers.icon({
    icon: 'coffee',
    markerColor: 'red'
  });

  L.marker([48.15491,11.54183], {icon: redMarker}).addTo(map);
````

Or for Maki icons

````js
  // Creates a red marker with the bus icon
  var redMarker = L.VectorMarkers.icon({
    icon: 'bus',
    prefix: '',
    extraClasses: 'maki-icon',
    markerColor: 'red'
  });

  L.marker([48.15491,11.54183], {icon: redMarker}).addTo(map);
````

### Properties

| Property        | Description            | Default Value | Possible  values                                     |
| --------------- | ---------------------- | ------------- | ---------------------------------------------------- |
| icon            | Name of the icon       | 'home'        | See glyphicons or font-awesome                       |
| prefix          | Select de icon library | 'fa'          | 'fa' for font-awesome or 'glyphicon' for bootstrap 3 |
| markerColor     | Color of the marker    | 'blue'        | Any HEX color you can find                           |
| iconColor       | Color of the icon      | 'white'       | 'white', 'black' or css code (hex, rgba etc) |
| spin            | Make the icon spin     | false         | true or false. Font-awesome required |
| extraClasses    | Additional classes in the created <i> tag | '' | 'fa-rotate90 myclass' eller other custom configuration |


### Supported icons
The 'icon' property supports these strings:
- 'home'
- 'glass'
- 'flag'
- 'star'
- 'bookmark'
- .... and many more, see: http://fortawesome.github.io/Font-Awesome/icons/
- Or: http://getbootstrap.com/components/#glyphicons

## Todo
- SVG shadows
- Adding more shapes
- Support for custom SVG

## License
- Leaflet.VectorMarkers and colored markers are licensed under the MIT License - http://opensource.org/licenses/mit-license.html.
- Font Awesome: http://fortawesome.github.io/Font-Awesome/license/
- Twitter Bootstrap: http://getbootstrap.com/

## Contact
- Website: http://www.hiasinho.com
