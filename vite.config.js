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
                'inventario_listas_css': 'resources/css/inventario/inventario_listas.css',
                'inventario_base_css': 'resources/css/inventario/shared/base.css',
                
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
                'inventario_js': 'resources/js/inventario/inventario_listas.js',
                'marcas': 'resources/js/inventario/marcas.js',
                'categorias': 'resources/js/inventario/categorias.js',
                'proveedores': 'resources/js/inventario/proveedores.js',
                'contratos_convenios': 'resources/js/inventario/contratos_convenios.js',
                'paginacion': 'resources/js/inventario/paginacion.js',
            },
            refresh: true,
        }),
    ],
});
