const gulp = require('gulp');
const sass = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const del = require('del');
const rename = require('gulp-rename');
const crass = require('gulp-crass');
const plumber = require('gulp-plumber');
const browserSync = require('browser-sync');
sass.compiler = require('node-sass');

const paths = {
  styles_src: './src/sass',
  styles_dest: './dist/css',
  node_modules: './node_modules',
}

// BrowserSync Configuration
const server = browserSync.create();

function reload(done) {
  server.reload();
  done();
}

function serve(done) {
  server.init({
    server: {
      baseDir: './'
    }
  });
  done();
}

function clean() {
  return del(['dist']);
}

// compile sass, add vendor prefixes, minify CSS
function styles() {
  return gulp.src( paths.styles_src + '/*.scss' )
    .pipe( plumber() )
    .pipe( sass().on('error', sass.logError) )
    .pipe( autoprefixer( 'last 2 versions') )
    .pipe( gulp.dest( paths.styles_dest ) )
    .pipe( plumber() )
    .pipe( crass( { pretty: false, sourceMap: true } ) )
    .pipe( rename( { suffix: '.min'} ) )
    .pipe( gulp.dest( paths.styles_dest) )
    .pipe( server.stream() );
}

function watchFiles() {
  gulp.watch( paths.styles_src + '/**/*.scss', styles );
}

// Run `gulp watch` to start watch task
gulp.task( 'watch', gulp.parallel( watchFiles, serve ) );
