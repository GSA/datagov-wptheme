var uglifyjs = require("uglify-js");
var compressor  = require('node-minify');

task('default', [], function(){
  jake.logger.log('Compressing JavaScript files...');
  new compressor.minify({
    type: 'uglifyjs',
    fileIn: ['L.Control.ZoomBox.js'],
    fileOut: 'L.Control.ZoomBox.min.js',
    callback: function(error){
      if(error){
        jake.logger.console.log(error);
      }
    }
  });
});