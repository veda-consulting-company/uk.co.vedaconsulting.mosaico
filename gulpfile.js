var gulp = require('gulp');
var sass = require('gulp-sass');
var cssnano = require('gulp-cssnano');
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require('gulp-autoprefixer');
var postcss = require('gulp-postcss');
var postcssPrefix = require('postcss-prefix-selector');
var postcssDiscardDuplicates = require('postcss-discard-duplicates');

var bootstrapNamespace = '#bootstrap-theme';

gulp.task('sass:main', function () {
  gulp.src('sass/main.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({
      outputStyle: 'compressed',
    }).on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: ['last 2 versions'],
      cascade: false
    }))
    .pipe(postcss([postcssPrefix({
      prefix: bootstrapNamespace + ' ',
      exclude: [/^html/, /^body/]
    })]))
    .pipe(cssnano())
    .pipe(sourcemaps.write('./'))
  .pipe(gulp.dest('./css/'))
});

gulp.task('sass:legacy', function () {
  gulp.src('sass/legacy.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({
      outputStyle: 'compressed',
    }).on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: ['last 2 versions'],
      cascade: false
    }))
    .pipe(postcss([postcssPrefix({
      prefix: bootstrapNamespace + ' ',
      exclude: [/^html/, /^body/]
    })]))
    .pipe(cssnano())
    .pipe(sourcemaps.write('./'))
  .pipe(gulp.dest('./css/'))
});

gulp.task('sass', ['sass:main','sass:legacy']);

gulp.task('watch', function () {
	gulp.watch('sass/**/*.scss', ['sass']);
});

gulp.task('default', ['sass', 'watch']);
