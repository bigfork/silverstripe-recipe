import scss from 'rollup-plugin-scss';
import postcss from 'postcss';
import autoprefixer from 'autoprefixer';
const sortMediaQueries = require('postcss-sort-media-queries');
import {resolve} from 'path';

const importer = (url, _prev, done) => {
  console.log(url);
  if (url[0] !== '~') {
    return null;
  }
  const info = {file: resolve(`node_modules/${url.substr(1)}`)};
  if (done) {
    done(info);
  }
  return info;
};


const buildConfigSCSS = (inputFileName, outputFileName) => {
  return {
    input: `src/scss/${inputFileName}.scss`,
    output: {
      dir: `dist/css`
    },
    plugins: [
      scss(
        {
          importer,
          sourceMap: true,
          fileName: `${outputFileName}.css`,
          processor: () => postcss({
              plugins: [
                autoprefixer(),
                sortMediaQueries()
              ],
              use: [
                [
                  'sass', {
                  includePaths: [resolve('node_modules')]
                }
                ]
              ]
            }
          ),
        }
      ),
    ],
  };
};

const buildConfigJS = (inputFileName, outputFileName) => {
  return {
    input: `src${inputFileName}`,
    output: {
      sourcemap: true,
      format: 'iife',
      name: 'app',
      file: `dist/${outputFileName}.js`
    },
    plugins: [],
  };
};

export default [
  buildConfigSCSS('style', 'style'),
  buildConfigSCSS('editor', 'editor'),
];
