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
sass.compiler = require('node-sass');

const paths = {
  src: './src',
  dist: './dist',
  nm: './node_modules',
  styles_src: './src/sass',
  vendor: './src/vendor',
  styles_dest: './dist/css',
  scripts_src: './src/js',
  scripts_dest: './dist/js',
  image_src: './src/images',
  image_dest: './dist/images'
};

// BrowserSync Configuration
const server = browserSync.create();

function reload(done) {
  server.reload();
  done();
}

function serve(done) {
  server.init({
    server: {
      baseDir: './dist'
    }
  });
  done();
}

function clean() {
  return del(['dist']);
}

function html() {
  return gulp.src(paths.src + '/*.html')
      .pipe( gulp.dest( paths.dist ) );
}

// compile sass, add vendor prefixes, minify CSS
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

function scripts() {
  return gulp.src(paths.scripts_src + '/*.js')
      .pipe( plumber() )
      .pipe( gulp.dest( paths.scripts_dest ) )
      .pipe( server.stream() );
}

// vendor
gulp.task('vendor:scss', function() {
  return gulp.src([paths.nm + '/bootstrap/scss/**/*'])
    .pipe( gulp.dest( paths.vendor + '/scss/bootstrap' ) );
});

gulp.task('vendor:js', function() {
  return gulp.src([
    paths.nm + '/bootstrap/js/dist/*',
    paths.nm + '/jquery/dist/*'
  ])
    .pipe( gulp.dest( paths.vendor + '/js' ) );
});

gulp.task( 'vendor', gulp.parallel( 'vendor:scss', 'vendor:js' ) );

// images
function images() {
    return gulp.src(paths.image_src + '/*')
        .pipe( imagemin() )
        .pipe( gulp.dest( paths.image_dest ) );
}

gulp.task( 'watchAll', function() {
    gulp.watch( paths.styles_src + '/**/*.scss', styles );
    gulp.watch( paths.image_src + '/*', images );
    gulp.watch( paths.src + '/*.html', html);
    gulp.watch( paths.scripts_src + '/*.js', scripts);

});

// Run `gulp watch` to start watch task
gulp.task( 'watch', gulp.parallel('watchAll', serve));

gulp.task( 'build', gulp.parallel(html, images, styles));

// Initial Build
gulp.task( 'init', gulp.series( clean, 'vendor', 'build', 'watch'));