import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig(({ mode }) => ({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
    ],
    optimizeDeps: {
        include: ['pusher-js', 'laravel-echo'],
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: {
                app: './resources/js/app.js',
            },
            output: {
                entryFileNames: 'assets/[name].[hash].js',
                chunkFileNames: 'assets/[name].[hash].js',
                assetFileNames: 'assets/[name].[hash].[ext]',
            },
        },
        commonjsOptions: {
            include: ['pusher-js', 'laravel-echo', /node_modules/],
            transformMixedEsModules: true,
        },
        minify: mode === 'production' ? 'esbuild' : false,
        sourcemap: mode !== 'production',
    },
    resolve: {
        alias: {
            '@': resolve(__dirname, './resources/js'),
            'ziggy-js': 'vendor/tightenco/ziggy/dist/vue.es.js',
        },
    },
    server: {
        hmr: {
            host: 'localhost',
        },
    },
}));
