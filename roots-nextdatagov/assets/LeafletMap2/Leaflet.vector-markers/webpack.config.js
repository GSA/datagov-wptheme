/* eslint-disable */

var path = require('path');
var ExtractTextPlugin = require("extract-text-webpack-plugin");
var autoprefixer = require('autoprefixer');

module.exports = {
  output: {
    library: 'VectorMarkers',
    libraryTarget: 'umd',
  },
  externals: [
    {
      leaflet: {
        amd: 'leaflet',
        commonjs: 'leaflet',
        commonjs2: 'leaflet',
        root: 'L'
      }
    }
  ],
  module: {
    loaders: [
      {test: /\.js$/, exclude: /node_modules/, loader: 'babel'},
      {
        test: /\.scss$/,
        loader: ExtractTextPlugin.extract(
          'style',
          'css!postcss-loader!sass'
        )
      }
    ]
  },
  plugins: [
    new ExtractTextPlugin("leaflet-vector-markers.css")
  ],
  sassLoader: {
    includePaths: [path.resolve(__dirname, "./src")]
  },
  postcss: [ autoprefixer({ browsers: ['last 3 version', '> 10%', 'IE 8'] }) ]
};
