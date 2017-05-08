const L = require.requireActual('leaflet')
const LeafletMock = jest.genMockFromModule('leaflet')

class MapMock extends LeafletMock.Map {
  constructor(id, options = {}) {
    super();
    assign(this, L.Mixin.Events);

    this.options = {...L.Map.prototype.options, ...options};
    this._container = id;

    if (options.bounds) {
      this.fitBounds(options.bounds, options.boundsOptions);
    }

    if (options.maxBounds) {
      this.setMaxBounds(options.maxBounds);
    }

    if (options.center && options.zoom !== undefined) {
      this.setView(L.latLng(options.center), options.zoom);
    }
  }

  _limitZoom(zoom) {
    const min = this.getMinZoom();
    const max = this.getMaxZoom();
    return Math.max(min, Math.min(max, zoom));
  }

  _resetView(center, zoom) {
    this._initialCenter = center;
    this._zoom = zoom;
  }

  fitBounds(bounds, options) {
    this._bounds = bounds;
    this._boundsOptions = options;
  }

  getBounds() {
    return this._bounds;
  }

  getCenter() {
    return this._initialCenter;
  }

  getMaxZoom() {
    return this.options.maxZoom === undefined ? Infinity : this.options.maxZoom;
  }

  getMinZoom() {
    return this.options.minZoom === undefined ? 0 : this.options.minZoom;
  }

  getZoom() {
    return this._zoom;
  }

  setMaxBounds(bounds) {
    bounds = L.latLngBounds(bounds);
    this.options.maxBounds = bounds;
    return this;
  }

  setView(center, zoom) {
    zoom = zoom === undefined ? this.getZoom() : zoom;
    this._resetView(L.latLng(center), this._limitZoom(zoom));
    return this;
  }

  setZoom(zoom) {
    return this.setView(this.getCenter(), zoom);
  }
}

module.exports = {
  ...LeafletMock,
  LatLng: L.LatLng,
  latLng: L.latLng,
  LatLngBounds: L.LatLngBounds,
  latLngBounds: L.latLngBounds,
  Map: MapMock,
  map: (id, options) => new MapMock(id, options),
}
