const mix = require('laravel-mix');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const ImageMinimizerPlugin = require('image-minimizer-webpack-plugin');
const sortMediaQueries = require('postcss-sort-media-queries');
const SVGSpritemapPlugin = require('svg-spritemap-webpack-plugin');

// Options for existing plugins
mix.options({
  cssNano: {
    mergeRules: true,
    reduceIdents: { keyframes: false },
    zindex: false
  },
  postCss: [
    sortMediaQueries()
  ],
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
    rules: [{ test: /\.scss$/, loader: 'glob-import-loader' }]
  }
});

// Update babel loader to ensure code imported from node_modules is transpiled
// See https://github.com/JeffreyWay/laravel-mix/issues/1906#issuecomment-455241790
mix.webpackConfig({
  module: {
    rules: [
      {
        test: /\.jsx?$/,
        exclude: /(bower_components)/,
        use: [{
          loader: 'babel-loader',
          options: Config.babel()
        }]
      }
    ]
  }
});

// SVG sprite generation
mix.webpackConfig({
  plugins: [
    new SVGSpritemapPlugin('src/images/icons/**/*.svg', {
      output: { filename: 'dist/images/icons.svg' },
      sprite: {
        prefix: 'icon-',
        generate: {
          title: false,
        }
      }
    })
  ]
});

// Configure browsersync
const url = process.env.DDEV_HOSTNAME;
mix.browserSync({
  files: [
    'dist/**/*',
    'templates/**/*',
  ],
  ignore: [
    'dist/images/.gitkeep',
    'dist/webfonts/.gitkeep'
  ],
  proxy: 'localhost',
  host: url,
  open: false,
  ui: false
});

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
    new CopyWebpackPlugin({
      patterns: [
        {
          from: 'src/images',
          to: 'dist/images',
          noErrorOnMissing: true,
          globOptions: {
            dot: false,
            ignore: ['**/icons/**/*.svg']
          }
        }
      ]
    }),
    new ImageMinimizerPlugin({
      exclude: 'dist/images/icons.svg',
      minimizer: {
        implementation: ImageMinimizerPlugin.imageminMinify,
        options: {
          plugins: [
            [
              "svgo",
              {
                plugins: [
                  {
                    name: "preset-default",
                    params: {
                      overrides: {
                        removeViewBox: false
                      }
                    }
                  }
                ]
              }
            ]
          ]
        }
      }
    })
  ]
});

// Stop mix from generating a license file called app.js.LICENSE.txt
mix.options({
  terser: {
    extractComments: false,
  }
});
