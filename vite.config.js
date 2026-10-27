import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
// import react from '@vitejs/plugin-react';
// import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        hmr: {
            host: 'localhost'
        }
    },
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                'resources/css/legacy.css',
                'resources/css/app.css',
                'resources/js/app.js',
                // Schwere Bibliotheken, nur auf ihren Seiten
                'resources/js/charts.js',
                'resources/js/cropper.js',
                // Seitenskripte, eingebunden per @vite in der jeweiligen Ansicht (Tests: tests/js)
                'resources/js/pages/survey-autosave.js',
                'resources/js/pages/standard-texts.js',
                'resources/js/pages/users-import.js',
                'resources/js/pages/user-search.js',
                'resources/js/pages/counter-up.js',
            ],
            refresh: true
        }),
        // react(),
        // vue({
        //     template: {
        //         transformAssetUrls: {
        //             base: null,
        //             includeAbsolute: false,
        //         },
        //     },
        // }),
        {
            name: 'blade',
            handleHotUpdate({file, server}) {
                if (file.endsWith('.blade.php')) {
                    server.ws.send({
                        type: 'full-reload',
                        path: '*',
                    });
                }
            },
        },
    ],
});
