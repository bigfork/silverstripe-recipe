const gulp = require('gulp');
const bigfork = require(process.env.HOME + '/bigfork.json');
const path = require('path');
const handle = require('./handlers');
const plumber = require('gulp-plumber');
const watch = require('gulp-watch');
const browsersync = require('browser-sync').create();
const sass = require('gulp-sass');
const autoprefix = require('gulp-autoprefixer');
const postcss = require('gulp-postcss');
const cssimport = require('gulp-cssimport');
const mqpacker = require('css-mqpacker');
const sortCSSmq = require('sort-css-media-queries');
const cssglob = require('gulp-css-globbing');
const cssnano = require('gulp-cssnano');
const uglify = require('gulp-uglify');
const browserify = require('browserify');
const buffer = require('vinyl-buffer');
const tinypng = require('gulp-tinypng-compress');
const tap = require('gulp-tap');
const rename = require('gulp-rename');

const opt = {
  css: {
    imports: {
      includePaths: ['node_modules']
    },
    src: [
      'scss/editor.scss',
      'scss/style*.scss'
    ],
    dest: 'css/'
  },
  png: {
    src: ['images/src/**/*.png'],
    dest: 'images/'
  },
  js: {
    src: 'js/src/*.js',
    dest: 'js/'
  }
};

// compile scss into css
const cssTask = () => {
  const mediaQueryPacker = mqpacker({
    sort: sortCSSmq
  });

  return gulp.src(opt.css.src)
    .pipe(cssglob({
      extensions: ['.scss']
    }))
    .pipe(sass(opt.css.imports).on('error', handle.sassReporter))
    .pipe(postcss([mediaQueryPacker]))
    .pipe(autoprefix())
    .pipe(cssimport(opt.css.imports))
    .pipe(cssnano({
      autoprefixer: false,
      mergeRules: true,
      reduceIdents: {
        keyframes: false
      },
      zindex: false
    }))
    .pipe(handle.notify('CSS compiled - <%= file.relative %>'))
    .pipe(handle.pipeLog('compiled'))
    .pipe(gulp.dest(opt.css.dest))
    .pipe(browsersync.stream());
};
gulp.task('css', cssTask);

// lint and uglify javascript
const jsTask = () => {
  return gulp.src(opt.js.src, {read: false})
    .on('error', handle.genericReporter)
    .pipe(plumber({errorHandler: handle.genericReporter}))
    .pipe(tap((file) => {
      file.contents = browserify(file.path, {
        debug: true
      }).transform('babelify', {
        presets: ['@babel/preset-env']
      }).bundle();
    }))
    .pipe(buffer())
    .pipe(uglify())
    .pipe(handle.notify('JS compiled - <%= file.relative %>'))
    .pipe(handle.pipeLog('compiled'))
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest(opt.js.dest));
};
gulp.task('js', jsTask);

// compress pngs
const pngTask = () => {
  return gulp.src(opt.png.src)
    .pipe(plumber({errorHandler: handle.genericReporter}))
    .pipe(tinypng({
      key: bigfork.tinypng,
      checkSigs: true,
      sigFile: 'images/.tinypng-sigs'
    }))
    .pipe(handle.pipeLog('compressed'))
    .pipe(handle.notify('Images compressed'))
    .pipe(gulp.dest(opt.png.dest));
};
gulp.task('png', pngTask);

gulp.task('default', ['css', 'js']);

// watch tasks
gulp.task('watch', () => {
  let sitepath = path.join(__dirname, '/../../'),
    parent = path.basename(path.join(sitepath, '../'));

  if (parent === 'Devsites') {
    browsersync.init({
      proxy: 'http://' + path.basename(sitepath) + '.test'
    });
  } else {
    handle.log('Not in Devsites - skipping BrowserSync', {type: 'bad'});
  }

  watch('scss/**/*.scss', () => {
    cssTask();
  });

  watch('js/src/**/*.js', () => {
    jsTask();
  });

  watch('images/src/**/*.png', () => {
    pngTask();
  });
});
