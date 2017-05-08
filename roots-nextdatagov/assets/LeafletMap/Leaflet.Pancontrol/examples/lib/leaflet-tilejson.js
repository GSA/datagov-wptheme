L.TileJSON = (function() {
    var semver = "\\s*[v=]*\\s*([0-9]+)"    // major
        + "\\.([0-9]+)"                  // minor
        + "\\.([0-9]+)"                  // patch
        + "(-[0-9]+-?)?"                 // build
        + "([a-zA-Z-][a-zA-Z0-9-\.:]*)?"; // tag
    var semverRegEx = new RegExp("^\\s*"+semver+"\\s*$");
    
    var parse = function(v) {
        return v.match(semverRegEx); 
    };

   function defined(o){
        return (typeof o !== "undefined" && o !== null);
    }

    function validateVersion(tileJSON) {
        if (!tileJSON.tilejson) {
            throw new Exception('Missing property "tilejson".');
        }
        
        v = parse(tileJSON.tilejson);
        if (!v || v[1] != 2) {
            throw new Exception('This parser supports version 2 '
                                + 'of TileJSON. (Provided version: "'
                                + tileJSON.tilejson + '"');
        }
    };

    function parseZoom(tileJSON, cfg) {
        if (tileJSON.minzoom) {
            cfg.minZoom = parseInt(tileJSON.minzoom);
        }

        if (tileJSON.maxzoom) {
            cfg.maxZoom = parseInt(tileJSON.maxzoom);
        } else {
            cfg.maxZoom = 22;
        }

        return cfg;
    }

    function createMapConfig(tileJSON, cfg) {
        validateVersion(tileJSON);

        if (!defined(cfg)){
	    cfg = {};
	}

        parseZoom(tileJSON, cfg);
        
        if (tileJSON.center) {
            var center = tileJSON.center;
            cfg.center = new L.LatLng(center[1], center[0]);
            cfg.zoom = center[2];
        }

        if (tileJSON.attribution) {
            cfg.attributionControl = true;
        }

        if (tileJSON.projection) {
            var t = tileJSON.transform;
            cfg.crs = 
                L.CRS.proj4js(tileJSON.crs,
                              tileJSON.projection,
                              new L.Transformation(t[0], t[1], t[2], t[3]));
            // FIXME: This might not be true for all projections, actually
            cfg.continuousWorld = true;
        }

        if (tileJSON.scales) {
            var s = tileJSON.scales;
            cfg.scale = function(zoom) {
                return s[zoom];
            }
        }
        
        return cfg;
    };

    function createTileLayerConfig(tileJSON, cfg) {
        validateVersion(tileJSON);

        if (!defined(cfg)){
	    cfg = {};
	}
        
        parseZoom(tileJSON, cfg);
        
        if (tileJSON.attribution) {
            cfg.attribution = tileJSON.attribution;
        }
        
        if (tileJSON.scheme) {
            cfg.scheme = tileJSON.scheme;
        }

        if (tileJSON.projection) {
            // FIXME: This might not be true for all projections, actually
            cfg.continuousWorld = true;
        }
        
        return cfg;
    };


 

    function createTileLayer(tileJSON) {
        var tileUrl = tileJSON.tiles[0].replace(/\$({[sxyz]})/g, '$1');
        return new L.TileLayer(tileUrl, createTileLayerConfig(tileJSON));
    };

    function createMap(id, tileJSON, options) {
	var mapConfig;
	var tileLayerConfig;
	
	if(defined(options)){	
	    mapConfig = options.mapOptions;
	    tileLayerConfig = options.tileLayerOptions;
	} else {
	    mapConfig = {};
	    tileLayerConfig = {};
	}

        var mapConfig = createMapConfig(tileJSON, mapConfig);
        mapConfig.layers = [createTileLayer(tileJSON, tileLayerConfig)];
        return new L.Map(id, mapConfig);
    }

    return {
        createMapConfig: createMapConfig,

        createTileLayerConfig: createTileLayerConfig,

        createTileLayer: createTileLayer,

        createMap: createMap
    }
}());