// Creates the map, sets default feature styling, custom marker, and gets map layers from Stamen and OpenStreetMaps
	var mymap = L.map('mapid').setView([40, 0], 1.5),
		myStyle = {
	    "color": "black",
	    "weight": 1,
	    "opacity": 0.8,
	    'fillColor': '#4679B2',
	    'fillOpacity': 0.8
	},
		blankStyle = {
			opacity: 0,
			fillOpacity: 0
	},
	newMarker = L.VectorMarkers.icon({
	    icon:'none',
		markerColor: 'rgb(252, 239, 126)',
		iconSize: [15, 25],
		iconAnchor: [7, 25], 
		shadowSize: [0, 0]
	}),
	geoDatas = [{value: internationalGeoJson, geoLayer: ""}, {value: statesGeoJson, geoLayer: ""}, {value: localGeoJson, geoLayer: ""}],
	// layer = new L.StamenTileLayer("toner-lite"),
	layer2 = L.tileLayer('http://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
	    attribution: '<a href="http://www.openstreetmap.org/copyright">&#169; OpenStreetMap contributors</a>',
	    maxZoom: 15
	});
	// This is the dfault layer---------------------------------------------------
	mymap.addLayer(layer2);
	// ---------------------------------------------------------------------------
	// This maps the geoJson Data onto the Map------------------------------------
	for(var item in geoDatas){
		geoDatas[item].geoLayer = L.geoJSON(geoDatas[item].value, {
		    style: myStyle,
		    pointToLayer: function (feature, latlng) {
		        return L.marker(latlng, {icon: newMarker});
		    },
		    onEachFeature: function(feature, layer) {
		    	layer.bindPopup('<h3>'+ feature.properties.Item +'</h3>' + "<h4>" + "<a target='blank' href='"+feature.properties.Link+"'>"+feature.properties.Link+"</a><h4>" );
			    layer.on({
	                contextmenu: function(e){
			    		var popup = L.popup().setLatLng(e.latlng).setContent('<h3>'+ feature.properties.Item +'</h3>' + "<h4>" + "<a target='blank' href='"+feature.properties.Link+"'>"+feature.properties.Link+"</a><h4>")
					    popup.openOn(mymap);
					},
					mousemove: function(e){
						var id = setTimeout(function(){ 
							var popup = L.popup().setLatLng(e.latlng).setContent('<h3>'+ feature.properties.Item +'</h3>' + "<h4>" + "<a target='blank' href='"+feature.properties.Link+"'>"+feature.properties.Link+"</a><h4>")
						    popup.openOn(mymap);	
						}, 300);
						while (id--) {
						    window.clearTimeout(id);
						}
					},
					mouseover: function(e) {
	                	if(layer.setStyle){
		                	layer.setStyle({
					            fillColor: 'orange',
					            fillOpacity: 0.7
					        })
	                	}
	                },
					mouseout: function (e) {
	                	if(layer.setStyle){
		                	layer.setStyle({
					            fillColor: '#4679B2',
					            fillOpacity: .8
					        })
	                	}
			        },
			        click: function (e) {
			            window.open(feature.properties.Link,'_blank');
			        }
	            });
			}
		})	
	}
	//---allGeoJson is all three geoJson datas combined, made specifically for the search feature. Otherwise it is redundant, but a needed workaround for search feature
	var allGeoJson = L.geoJson([internationalGeoJson, localGeoJson, statesGeoJson], {
		style: blankStyle,
		onEachFeature: function(feature, layer) {
			if(layer.setOpacity) {
				layer.setOpacity(0)
			}
			layer.bindPopup('<h3>'+ feature.properties.Item +'</h3>' + "<h4>" + "<a target='blank' href='"+feature.properties.Link+"'>"+feature.properties.Link+"</a><h4>" );
			layer.on({
				contextmenu: function(e){
		    		var popup = L.popup().setLatLng(e.latlng).setContent('<h3>'+ feature.properties.Item +'</h3>' + "<h4>" + "<a target='blank' href='"+feature.properties.Link+"'>"+feature.properties.Link+"</a><h4>")
				    popup.openOn(mymap);
				},
				mousemove: function(e){
					var id = setTimeout(function(){ 
						var popup = L.popup().setLatLng(e.latlng).setContent('<h3>'+ feature.properties.Item +'</h3>' + "<h4>" + "<a target='blank' href='"+feature.properties.Link+"'>"+feature.properties.Link+"</a><h4>")
					    popup.openOn(mymap);	
					}, 400);
					while (id--) {
					    window.clearTimeout(id);
					}
				},
				mouseover: function(e) {
                	if(layer.setStyle){
	                	layer.setStyle({
				            fillColor: 'orange',
				            fillOpacity: 0.7
				        })
                	}
                },
				mouseout: function (e) {
					if(layer.setStyle){
	                	layer.setStyle({
				            fillColor: '#4679B2',
				            fillOpacity: 0
				        })
					}
					var id = setTimeout(function(){ 
						mymap.closePopup();
					}, 410);
		        },
		        click: function (e) {
		            window.open(feature.properties.Link,'_blank');
		        }
            });
		}
	});
	//--------------------------------------------------------------------------
	//This here sets up the layering feature------------------------------------
	var baseMaps = {
	    // "Text Map": layer,
	    'Geographic Map': layer2
	},
	overlayMaps = {
	    "International": geoDatas[0].geoLayer,
	    "State": geoDatas[1].geoLayer,
	    "US Cities and Counties": geoDatas[2].geoLayer
	};
	mymap.addLayer(geoDatas[0].geoLayer);
	mymap.addLayer(geoDatas[1].geoLayer);
	mymap.addLayer(geoDatas[2].geoLayer);
	L.control.layers(baseMaps, overlayMaps).addTo(mymap);
	//--------------------------------------------------------------------------
	//This here sets up the search feature -------------------------------------
	var searchControl = new L.Control.Search({
		moveToLocation: function(latlng, title, map) {
			if(latlng.layer.getBounds){
				var zoom = map.getBoundsZoom(latlng.layer.getBounds());
			} else {
				var zoom = 7;
			}
  			map.setView(latlng, zoom); // access the zoom
		},
		position: 'topright',
		layer: allGeoJson,
		propertyName: 'Item',
		marker: false
	}).on('search:locationfound', function(e) {
		if(e.layer.setStyle){
			e.layer.setStyle({fillColor: 'orange', fillOpacity: .6});
		}
		if(e.layer._popup){
			e.layer.openPopup();
		}
	})
	mymap.addControl( searchControl );
	//--------------------------------------------------------------------------
	//This here makes sure the allGeojson layer is always on top of state and international layer
	geoDatas[2].geoLayer.bringToFront();
	mymap.on("overlayadd", function (event) {
	  allGeoJson.bringToFront();
	  geoDatas[2].geoLayer.bringToFront();
	});
	//--------------------------------------------------------------------------
	//This adds the arrow pan feature
	L.control.pan({
		position: 'bottomleft',
		className: "panBox"
	}).addTo(mymap);
	//--------------------------------------------------------------------------
	//This adds the return to original view feature
	L.control.defaultExtent().addTo(mymap);
	//--------------------------------------------------------------------------
	//This adds the zoombox feature
	// var zoomBoxControl = L.control.zoomBox({
	//     modal: true
	// });
	// mymap.addControl(zoomBoxControl);
	//--------------------------------------------------------------------------
	//This here makes the map responsive----------------------------------------
	$(window).on("resize", function() {
	    $("#mapid").height($(window).height()*.8).width($(window).width()*.7);
	    mymap.invalidateSize();
	}).trigger("resize");
	//--------------------------------------------------------------------------
