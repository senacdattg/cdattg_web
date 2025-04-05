import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/style.css',
                'resources/css/temas.css',
                'resources/js/app.js',
                'resources/js/dashboard.js',
                'resources/js/parametros.js',
                'resources/js/tema.js',
                'resources/js/logout.js',
                'resources/js/dashboards/superadmin/charts-scripts.js',
                'resources/js/dashboards/superadmin/widgets.js',
                'resources/css/dashboards/dashboard_superadmin.css'
            ],
            refresh: true,
        }),
    ],
});
