const gulp = require('gulp');
const bigfork = require(process.env.HOME + '/bigfork.json');
const path = require('path');
const handle = require('./handlers');
const plumber = require('gulp-plumber');
const watch = require('gulp-watch');
const browsersync = require('browser-sync').create();
const scsslint = require('gulp-scss-lint');
const sass = require('gulp-sass');
const autoprefix = require('gulp-autoprefixer');
const postcss = require('gulp-postcss');
const cssimport = require("gulp-cssimport");
const mqpacker = require('css-mqpacker');
const sortCSSmq = require('sort-css-media-queries');
const cssglob = require('gulp-css-globbing');
const cssnano = require('gulp-cssnano');
const jshint  = require('gulp-jshint');
const uglify = require('gulp-uglify');
const babelify = require('babelify');
const browserify = require('browserify');
const source = require('vinyl-source-stream');
const buffer = require('vinyl-buffer');
const glob = require('glob');
const tinypng = require('gulp-tinypng-compress');

const opt = {
	scsslint: {
		src: ['scss/**/!(_reset|_normalize)*.scss']
	},
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

// lint css
gulp.task('scss-lint', function() {
	return gulp.src(opt.scsslint.src)
		.pipe(scsslint({
			'config': '.scss-lint.yml',
			'customReport': handle.lintReporter
		}));
});

// compile scss into css
gulp.task('css', ['scss-lint'], function() {
	const mediaQueryPacker = mqpacker({
		sort: sortCSSmq
	});

	return gulp.src(opt.css.src)
		.pipe(cssglob({
			extensions: ['.scss']
		}))
		.pipe(sass(opt.css.imports).on('error', handle.sassReporter))
		.pipe(postcss([mediaQueryPacker]))
		.pipe(autoprefix({
			browsers: ['ie >= 8', 'safari >= 8', '> 1%']
		}))
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
});

// lint and uglify javascript
gulp.task('js', function() {
	const babelifyTransform = babelify.configure({
		presets: ['env']
	});

	glob(opt.js.src, function(err, files) {
		files.map(function(entry) {
			return browserify({
				entries: entry,
				debug: true,
				transform: [babelifyTransform]
			}).bundle()
				.on('error', handle.genericReporter)
				.pipe(plumber({errorHandler: handle.genericReporter}))
				.pipe(source(path.basename(entry).replace(/\.js$/, '.min.js')))
				.pipe(buffer())
				.pipe(jshint({
					esnext: true
				}))
				.pipe(uglify())
				.pipe(handle.notify('JS compiled - <%= file.relative %>'))
				.pipe(handle.pipeLog('compiled'))
				.pipe(gulp.dest(opt.js.dest));
		})
	});
});

// compress pngs
gulp.task('png', function() {
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
});

gulp.task('default', function() {
	gulp.start(['css', 'js']);
});

// watch tasks
gulp.task('watch', function() {
	var sitepath = path.join(__dirname, '/../../'),
		parent = path.basename(path.join(sitepath, '../'));

	if(parent == 'Devsites') {
		browsersync.init({
			proxy: 'http://' + path.basename(sitepath) + '.test'
		});
	} else {
		handle.log('Not in Devsites - skipping BrowserSync', {type: 'bad'});
	}

	watch('scss/**/*.scss', function() {
		gulp.start('css');
	});

	watch('js/src/**/*.js', function() {
		gulp.start('js');
	});

	watch('images/src/**/*.png', function() {
		gulp.start('png');
	});
});
