'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

var _Icon = require('./Icon');

var _Icon2 = _interopRequireDefault(_Icon);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

exports.default = {
  version: '1.0.0',

  Icon: _Icon2.default,

  icon: function icon(options) {
    return new _Icon2.default(options);
  }
};