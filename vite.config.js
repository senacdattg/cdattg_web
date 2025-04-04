import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/style.css',
                'resources/js/app.js',
                'resources/js/dashboard.js',
                'resources/js/parametros.js',
                'resources/js/tema.js',
                'resources/js/logout.js',
            ],
            refresh: true,
        }),
    ],
});
