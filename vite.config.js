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
                'instructores_css': 'resources/css/instructores.css',
                'guias_aprendizaje_css': 'resources/css/guias_aprendizaje.css',
                'resultados_aprendizaje_css': 'resources/css/resultados-aprendizaje.css',
                'dias_formacion_css': 'resources/css/dias-formacion.css',
                'dashboard_superadmin': 'resources/css/dashboards/dashboard-superadmin.css',
                'caracter_selecter': 'resources/css/Asistencia/caracter_selecter.css',
                'navbar': 'resources/css/shared/navbar.css',
                'inventario_listas_css': 'resources/css/inventario/inventario_listas.css',
                'inventario_base_css': 'resources/css/inventario/shared/base.css',
                'inventario_card_css': 'resources/css/inventario/card.css',
                'inventario_modal_producto_css': 'resources/css/inventario/modal-producto.css',
                'inventario_imagen_css': 'resources/css/inventario/imagen.css',
                'inventario_sidebar_fix': 'resources/css/inventario/sidebar-fix.css',
                
                // JavaScript files
                'app': 'resources/js/app.js',
                'dashboard': 'resources/js/dashboard.js',
                'parametros': 'resources/js/parametros.js',
                'tema': 'resources/js/tema.js',
                'logout': 'resources/js/logout.js',
                'charts-scripts': 'resources/js/dashboards/superadmin/charts-scripts.js',
                'widgets': 'resources/js/dashboards/superadmin/widgets.js',
                'municipios': 'resources/js/municipios.js',
                'inventario_card': 'resources/js/inventario/card.js',
                'inventario_carrito': 'resources/js/inventario/carrito.js',
            },
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',  // Escucha en todas las interfaces de red
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',  // Usa localhost para HMR en el navegador
        },
    },
});
