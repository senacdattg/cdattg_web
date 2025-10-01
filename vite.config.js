import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: {
                // CSS files
                'style': 'resources/css/style.css',
                'temas_css': 'resources/css/temas.css',
                'parametros_css': 'resources/css/parametros.css',
                'dashboard_superadmin': 'resources/css/dashboards/dashboard-superadmin.css',
                'caracter_selecter': 'resources/css/Asistencia/caracter_selecter.css',
                
                // JavaScript files
                'app': 'resources/js/app.js',
                'dashboard': 'resources/js/dashboard.js',
                'parametros': 'resources/js/parametros.js',
                'tema': 'resources/js/tema.js',
                'logout': 'resources/js/logout.js',
                'charts-scripts': 'resources/js/dashboards/superadmin/charts-scripts.js',
                'widgets': 'resources/js/dashboards/superadmin/widgets.js',
                'municipios': 'resources/js/municipios.js',
            },
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',  // Escucha en todas las interfaces de red
        port: 5173,
        strictPort: true,
        hmr: {
            host: '192.168.1.4',  // Tu IP de red local
        },
        cors: true,  // Habilita CORS para permitir peticiones cross-origin
    },
});
