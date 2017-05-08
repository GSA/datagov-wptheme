
(function(root, factory) {
  if (typeof define === 'function' && define.amd) {
    define(["leaflet"], factory);
  } else if (typeof exports === 'object') {
    module.exports = factory(require('leaflet'));
  } else {
    root.L.Control.DefaultExtent = factory(root.L);
  }
}(this, function(L) {

return (function () {
  /* global L */
  'use strict';
  L.Control.DefaultExtent = L.Control.extend({
    options: {
      position: 'topleft',
      text: 'Default Extent',
      title: 'Zoom to default extent',
      className: 'leaflet-control-defaultextent'
    },
    onAdd: function (map) {
      this._map = map;
      return this._initLayout();
    },
    setCenter: function (center) {
      this._center = center;
      return this;
    },
    setZoom: function (zoom) {
      this._zoom = zoom;
      return this;
    },
    _initLayout: function () {
      var container = L.DomUtil.create('div', 'leaflet-bar ' +
        this.options.className);
      this._container = container;
      this._fullExtentButton = this._createExtentButton(container);

      L.DomEvent.disableClickPropagation(container);

      this._map.whenReady(this._whenReady, this);

      return this._container;
    },
    _createExtentButton: function () {
      var link = L.DomUtil.create('a', this.options.className + '-toggle',
        this._container);
      link.href = '#';
      link.innerHTML = this.options.text;
      link.title = this.options.title;

      L.DomEvent
        .on(link, 'mousedown dblclick', L.DomEvent.stopPropagation)
        .on(link, 'click', L.DomEvent.stop)
        .on(link, 'click', this._zoomToDefault, this);
      return link;
    },
    _whenReady: function () {
      if (!this._center) {
        this._center = this._map.getCenter();
      }
      if (!this._zoom) {
        this._zoom = this._map.getZoom();
      }
      return this;
    },
    _zoomToDefault: function () {
      this._map.setView(this._center, this._zoom);
    }
  });

  L.Map.addInitHook(function () {
    if (this.options.defaultExtentControl) {
      this.addControl(new L.Control.DefaultExtent());
    }
  });

  L.control.defaultExtent = function (options) {
    return new L.Control.DefaultExtent(options);
  };

  return L.Control.DefaultExtent;

}());
;

}));
