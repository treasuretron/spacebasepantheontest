'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require('gulp-autoprefixer');
var importer = require('node-sass-globbing');
var plumber = require('gulp-plumber');  
var browserSync = require('browser-sync').create();

var sass_config = {
  importer: importer,
  includePaths: [
    'node_modules/breakpoint-sass/stylesheets/',
    'node_modules/singularitygs/stylesheets/',
    'node_modules/compass-mixins/lib/'
  ]
};

gulp.task('browser-sync', function(done) {
    browserSync.init({
        injectChanges: true,
        proxy: "spacebase.lndo.site"
    });
    gulp.watch("./scss/*.scss", gulp.task(["sass"]));
    gulp.watch(['./css/style.css']).on('change', browserSync.reload);
    done();
});

gulp.task('sass', function (done) {
  gulp.src('./scss/style.scss')
    .pipe(plumber())
    .pipe(sourcemaps.init())
    .pipe(sass(sass_config).on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: [
        'last 2 versions',
        'android 4',
        'opera 12'
      ]
    }))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('./css'));
    done();
});

var build = gulp.series('browser-sync', ['sass']);

gulp.task('default', build);
