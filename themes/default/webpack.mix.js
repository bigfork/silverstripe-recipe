const mix = require('laravel-mix');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const ImageminPlugin = require('imagemin-webpack-plugin').default;
const imageminMozjpeg = require('imagemin-mozjpeg');
const imageminPngquant = require('imagemin-pngquant');
const imageminSvgo = require('imagemin-svgo');
const path = require('path');

// Options for existing plugins
mix.options({
  cssNano: {
    mergeRules: true,
    reduceIdents: { keyframes: false },
    zindex: false
  },
  processCssUrls: false
});

// Set up base tasks
mix
  .js('src/js/app.js', 'dist/js')
  .sass('src/scss/editor.scss', 'dist/css')
  .sass('src/scss/style.scss', 'dist/css')
  .sourceMaps()
  .copyDirectory('src/webfonts', 'dist/webfonts');

// Glob loading for SASS ("@import dir/**/*.scss")
mix.webpackConfig({
  module: {
    rules: [{ test: /\.scss$/, loader: 'import-glob-loader' }]
  }
});

// Configure browsersync
const sitepath = path.join(__dirname, '/../../');
const parent = path.basename(path.join(sitepath, '../'));
if (parent === 'Devsites') {
  mix.browserSync({
    files: ['dist/**/*'],
    proxy: `${path.basename(sitepath)}.test`
  });
}

// Remove stale assets from folders which are blindly copied
mix.webpackConfig({
  plugins: [
    new CleanWebpackPlugin({
      cleanOnceBeforeBuildPatterns: [
        'dist/images/**/*',
        '!dist/images/.gitkeep',
        'dist/webfonts/**/*',
        '!dist/webfonts/.gitkeep'
      ],
      cleanStaleWebpackAssets: false,
      verbose: false
    })
  ]
});

// Setup task to copy + compress images
mix.webpackConfig({
  plugins: [
    new CopyWebpackPlugin([{
      from: 'src/images',
      to: 'dist/images',
      ignore: ['*.DS_Store']
    }]),
    new ImageminPlugin({
      test: /\.(jpe?g|png|gif|svg)$/i,
      plugins: [
        imageminMozjpeg({ quality: 80, }),
        imageminPngquant(),
        imageminSvgo({ plugins: [{ removeViewBox: false }] })
      ]
    })
  ]
});
