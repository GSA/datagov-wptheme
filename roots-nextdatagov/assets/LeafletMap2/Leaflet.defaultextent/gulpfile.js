var gulp = require('gulp');
var wrap = require('gulp-wrap-umd');
var jshint = require('gulp-jshint');

var isWatching = false;

gulp.task('wrap', function () {
  var wrapOptions = {
    namespace: 'L.Control.DefaultExtent',
    deps: [
      {
        name: 'leaflet',
        globalName: 'L',
        paramName: 'L'
      }
    ]
  };
  gulp
    .src(['src/*.js'])
    .pipe(wrap(wrapOptions))
    .pipe(gulp.dest('dist/'));
});

gulp.task('jshint', function () {
  return gulp
    .src('src/*.js')
    .pipe(jshint())
    .pipe(jshint.reporter('default'));
});

gulp.task('default', ['jshint', 'wrap']);
