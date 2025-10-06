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
                'dashboard_superadmin': 'resources/css/dashboards/dashboard_superadmin.css',
                'caracter_selecter': 'resources/css/Asistencia/caracter_selecter.css',
                'navbar': 'resources/css/shared/navbar.css',
                'inventario_common_css': 'resources/css/inventario/inventario-common.css',
                
                // JavaScript files
                'app': 'resources/js/app.js',
                'dashboard': 'resources/js/dashboard.js',
                'parametros': 'resources/js/parametros.js',
                'tema': 'resources/js/tema.js',
                'logout': 'resources/js/logout.js',
                'charts-scripts': 'resources/js/dashboards/superadmin/charts-scripts.js',
                'widgets': 'resources/js/dashboards/superadmin/widgets.js',
                'municipios': 'resources/js/municipios.js',
                
                // Inventario files
                'inventario_common_js': 'resources/js/inventario/inventario-common.js',
                'marcas_simple': 'resources/js/inventario/marcas-simple.js',
                'categorias_simple': 'resources/js/inventario/categorias-simple.js',
                'proveedores_simple': 'resources/js/inventario/proveedores-simple.js',
                'contratos_convenios_simple': 'resources/js/inventario/contratos-convenios-simple.js',
                'paginacion_simple': 'resources/js/inventario/paginacion-simple.js',
            },
            refresh: true,
        }),
    ],
});
