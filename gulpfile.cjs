const { src, dest, series, parallel } = require('gulp');
const concat = require('gulp-concat'); // For bundling files
const CSS = require('gulp-minify-css'); // For minifying CSS
const rename = require('gulp-rename'); // For renaming files
const sourcemaps = require('gulp-sourcemaps'); // For generating source maps

const uglify = require('gulp-uglify');
const order  = require('gulp-order');


const paths = {
  scripts: 'assets/admin/js/*.js', // Source JavaScript files
  styles: 'assets/admin/css/*.css', // Source CSS files
  output: 'assets/admin', // Output directory
};


function bundleStyles() {
  return src(paths.styles)
        .pipe(
          order(
            [
              'styles.css',
            ],
          )
      )
      .pipe(sourcemaps.init()) // Initialize source maps
      .pipe(concat('bundle.css')) // Concatenate all CSS files
      .pipe(CSS()) // Minify CSS
      .pipe(rename({ suffix: '.min' })) // Add `.min` suffix
      .pipe(sourcemaps.write('.')) // Write source maps
      .pipe(dest(`${paths.output}/css`)); // Output to `dist/css`
}

function bundleScripts() {
  return src(paths.scripts)
        .pipe(
          order(
            [
              'lwjs.js',
              'chat.js'
            ],
          )
      )
      .pipe(sourcemaps.init()) // Initialize source maps
      .pipe(concat('bundle.js')) // Concatenate all JS files
      .pipe(uglify()) // Minify them
      .pipe(rename({ suffix: '.min' })) // Add `.min` suffix
      .pipe(sourcemaps.write('.')) // Write source maps
      .pipe(dest(`${paths.output}/js`)); // Output to `dist/css`
}

// Task: Watch files for changes (optional)
function watchFiles() {
//   const { watch } = require('gulp');
//   watch(paths.scripts, bundleScripts);
//   watch(paths.styles, bundleStyles);
}

// gulp.task('scripts', function () {
//     return gulp
//         .src('public/assets/js/files/*.js') // Path to your JS files
//         .pipe(
//             order(
//               [
//                 'constants.js',
//                 'full-screen.js',
//                 'marquee.js',
//                 'messi.min.js',
//                 'notifications.js',
//                 'dump.js',
//                 'main.js'
//               ],
//               { base: '/public/assets/js/files' } // Base directory to resolve paths
//             )
//          )
//         .pipe(concat('bundle.min.js')) // Combine files
//         .pipe(uglify()) // Minify them
//         .pipe(gulp.dest('public/assets/js')); // Output directory
// });


// Default task: Run both bundling tasks in parallel
exports.default = parallel(bundleScripts, bundleStyles);

// Build task: Run both bundling tasks sequentially
exports.build = series(bundleScripts, bundleStyles);

// Watch task: Watch for file changes
exports.watch = watchFiles;