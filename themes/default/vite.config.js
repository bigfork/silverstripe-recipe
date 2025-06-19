import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import {ViteImageOptimizer} from 'vite-plugin-image-optimizer';
import VitePluginSvgSpritemap from '@spiriit/vite-plugin-svg-spritemap';

export default defineConfig({
  base: '/_resources/themes/default/dist/',
  publicDir: '/dist/',
  plugins: [
    laravel({
      input: [
        'src/scss/style.scss',
        'src/scss/editor.scss',
        'src/js/app.js',
      ],
      publicDirectory: '../default',
      buildDirectory: 'dist',
      refresh: [
        'templates/**/*.ss',
      ],
    }),

    VitePluginSvgSpritemap('src/images/icons/*.svg', {
      gutter: 10,
      injectSvgOnDev: true,
      svgo: true,
      styles: 'src/scss/spritemap.scss',
    }),

    ViteImageOptimizer({
      test: /\.svg$/i,
      exclude: /spritemap\.svg$/i,
    }),
  ],
  server: {
    // Respond to all network requests
    host: "0.0.0.0",
    port: 5173,
    strictPort: true,
    // Defines the origin of the generated asset URLs during development, this must be set to the
    // Vite dev server URL and selected Vite port. In general, `process.env.DDEV_PRIMARY_URL` will
    // give us the primary URL of the DDEV project, e.g. "https://test-vite.ddev.site". But since
    // DDEV can be configured to use another port (via `router_https_port`), the output can also be
    // "https://test-vite.ddev.site:1234". Therefore we need to strip a port number like ":1234"
    // before adding Vites port to achieve the desired output of "https://test-vite.ddev.site:5173".
    origin: `${process.env.DDEV_PRIMARY_URL.replace(/:\d+$/, "")}:5173`,
    // Configure CORS securely for the Vite dev server to allow requests from *.ddev.site domains,
    // supports additional hostnames (via regex). If you use another `project_tld`, adjust this.
    cors: {
      origin: /https?:\/\/([A-Za-z0-9\-\.]+)?(\.ddev\.site)(?::\d+)?$/,
    },
  },
})
