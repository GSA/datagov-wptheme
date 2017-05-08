'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.Icon = exports.VectorMarkers = undefined;

var _leaflet = require('leaflet');

var _leaflet2 = _interopRequireDefault(_leaflet);

var _VectorMarkers2 = require('./VectorMarkers');

var _VectorMarkers3 = _interopRequireDefault(_VectorMarkers2);

var _Icon2 = require('./Icon');

var _Icon3 = _interopRequireDefault(_Icon2);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.VectorMarkers = _VectorMarkers3.default;
exports.Icon = _Icon3.default;


_leaflet2.default.VectorMarkers = _VectorMarkers3.default;