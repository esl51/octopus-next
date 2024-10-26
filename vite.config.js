import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import { defineConfig } from 'vite'
import eslintPlugin from 'vite-plugin-eslint'
import svgLoader from 'vite-svg-loader'

export default defineConfig({
  plugins: [
    eslintPlugin(),
    laravel({
      input: ['resources/sass/app.scss', 'resources/js/app.ts'],
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
    svgLoader(),
  ],
  css: {
    preprocessorOptions: {
      scss: {
        api: 'modern-compiler',
        quietDeps: true,
      },
    },
  },
  resolve: {
    alias: {
      '~bootstrap': 'bootstrap',
    },
  },
  define: {
    __VUE_I18N_FULL_INSTALL__: true,
    __VUE_I18N_LEGACY_API__: false,
    __INTLIFY_PROD_DEVTOOLS__: false,
  },
})
