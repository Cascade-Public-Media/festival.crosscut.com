const gulp = require('gulp');
const sass = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');
const cleanCSS = require('gulp-clean-css');
const del = require('del');
const rename = require('gulp-rename');
const crass = require('gulp-crass');
const plumber = require('gulp-plumber');
const browserSync = require('browser-sync');
const imagemin = require('gulp-imagemin');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const render = require('gulp-nunjucks-render');
const data = require('gulp-data');
sass.compiler = require('node-sass');

const paths = {
  src: './src',
  dist: './dist',
  nm: './node_modules',
  data: './src/nunjucks',
  templates: './src/templates',
  pages: './src/pages',
  styles_src: './src/sass',
  vendorStore: './src/vendor',
  styles_dest: './dist/css',
  scripts_src: './src/js',
  scripts_dest: './dist/js',
  image_src: './src/images',
  image_dest: './dist/images'
};

// BrowserSync Configuration
const server = browserSync.create();

function serve(done) {
  server.init({
    server: {
      baseDir: './dist',
      https: true
    }
  });
  done();
}

function clean() {
  return del(['dist']);
}

// HTML: compile nunjucks templates
gulp.task('nunjucks', function() {
    return gulp.src('src/pages/**/*.html')
        .pipe(data( function() {
            return require('./src/nunjucks/data.json')
        }))
        .pipe(render({
            path: ['src/templates']
        }))
        .pipe(gulp.dest( paths.dist ))
        .pipe( server.stream() );
});

// HTML: compile templates
function nunjucks() {
    return gulp.src( paths.pages + '/**/*.html' )
        .pipe(data( function() {
            return require( paths.data + '/data.json' )
        }))
        .pipe(render({
            path: [ paths.templates ]
        }))
        .pipe(gulp.dest( paths.dist ))
        .pipe( server.stream() );
}

// Styles: compile sass, add vendor prefixes, minify CSS
function styles() {
  return gulp.src(paths.styles_src + '/*.scss')
    .pipe( plumber() )
    .pipe( sass().on( 'error', sass.logError ) )
    .pipe( autoprefixer( 'last 2 versions') )
    .pipe( gulp.dest( paths.styles_dest ) )
    .pipe( plumber() )
    .pipe( crass( { pretty: false, sourceMap: true } ) )
    .pipe( rename( { suffix: '.min' } ) )
    .pipe( gulp.dest( paths.styles_dest ) )
    .pipe( server.stream() );
}

// Images: optimize
function images() {
    return gulp.src(paths.image_src + '/*')
        .pipe( imagemin() )
        .pipe( gulp.dest( paths.image_dest ) )
        .pipe( server.stream() );
}

// Scripts: minify
function scripts() {
  return gulp.src(paths.scripts_src + '/custom/*.js')
      .pipe( plumber() )
      .pipe( uglify() )
      .pipe( rename( { suffix: '.min' } ) )
      .pipe( gulp.dest( paths.scripts_dest ) )
      .pipe( server.stream() );
}

// Set up vendorStore
// Copy vendor styles from node_modules to vendorStore
gulp.task('vendorStore:bootstrapSass', function() {
  return gulp.src([
      paths.nm + '/bootstrap/scss/**/*',
  ])
    .pipe( gulp.dest( paths.vendorStore + '/scss/bootstrap' ) );
});

gulp.task('vendorStore:owlSass', function() {
    return gulp.src([
        paths.nm + '/owl.carousel/src/scss/*',
    ])
        .pipe( gulp.dest( paths.vendorStore + '/scss/owl' ) );
});

// Copy vendor scripts from node_modules to vendorStore
gulp.task('vendorStore:js', function() {
  return gulp.src([
    paths.nm + '/bootstrap/js/dist/*',
    paths.nm + '/jquery/dist/*',
    paths.nm + '/owl.carousel/dist/*.js'
  ])
    .pipe( gulp.dest( paths.vendorStore + '/js' ) );
});

// Concat necessary vendor scripts, minify, and copy from vendorStore to dest
gulp.task('vendorScripts', function () {
    return gulp.src( [
        paths.vendorStore + '/js/jquery.js',
        paths.vendorStore + '/js/owl.carousel.js',
    ])
        .pipe( plumber() )
        .pipe( concat( 'vendor.js' ) )
        .pipe( uglify() )
        .pipe( rename( { suffix: '.min' } ) )
        .pipe( gulp.dest( paths.scripts_dest ) );
});

// Set up vendorStore, prepare vendor scripts
gulp.task( 'vendor', gulp.series( gulp.parallel( 'vendorStore:bootstrapSass', 'vendorStore:owlSass', 'vendorStore:js' ), 'vendorScripts') );

gulp.task( 'watchAll', function() {
    gulp.watch( paths.styles_src + '/**/*.scss', styles );
    gulp.watch( paths.image_src + '/*', images );
    gulp.watch( paths.data + '/*.json', nunjucks);
    gulp.watch( paths.src + '/**/*.html', nunjucks);
    gulp.watch( paths.scripts_src + '/**/*.js', scripts);

});

// Run `gulp watch` to start watch task
gulp.task( 'watch', gulp.parallel('watchAll', serve));

gulp.task( 'build', gulp.parallel(nunjucks, images, styles, scripts));

// Initial Build
gulp.task( 'init', gulp.series( clean, 'vendor', 'build', 'watch'));