import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: {
                // CSS files
                'style': 'resources/css/style.css',
                'temas': 'resources/css/temas.css',
                'temas': 'resources/css/parametros.css',
                'dashboard_superadmin': 'resources/css/dashboards/dashboard_superadmin.css',
                
                // JavaScript files
                'app': 'resources/js/app.js',
                'dashboard': 'resources/js/dashboard.js',
                'parametros': 'resources/js/parametros.js',
                'tema': 'resources/js/tema.js',
                'logout': 'resources/js/logout.js',
                'charts-scripts': 'resources/js/dashboards/superadmin/charts-scripts.js',
                'widgets': 'resources/js/dashboards/superadmin/widgets.js',
            },
            refresh: true,
        }),
    ],
});
