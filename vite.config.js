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
                'registro_css': 'resources/css/pages/registro.css',
                'dashboard_superadmin': 'resources/css/dashboards/dashboard-superadmin.css',
                'caracter_selecter': 'resources/css/Asistencia/caracter_selecter.css',
                // Inventario CSS files
                'inventario_base_css': 'resources/css/inventario/shared/base.css',
                'inventario_card_css': 'resources/css/inventario/card.css',
                'inventario_carrito_css': 'resources/css/inventario/carrito.css',
                'inventario_imagen_css': 'resources/css/inventario/imagen.css',
                'inventario_inventario_css': 'resources/css/inventario/inventario.css',
                'inventario_modal_orden_css': 'resources/css/inventario/modal-orden.css',
                'inventario_modal_producto_css': 'resources/css/inventario/modal-producto.css',
                'inventario_notificaciones_css': 'resources/css/inventario/notificaciones.css',
                'inventario_orden_css': 'resources/css/inventario/orden.css',
                // Complementarios CSS files
                'formulario_inscripcion_css': 'resources/css/formulario_inscripcion.css',
                'gestion_aspirantes_css': 'resources/css/complementario/gestion_aspirantes.css',
                'ver_aspirantes_css': 'resources/css/complementario/ver_aspirantes.css',
                'procesar_documentos_css': 'resources/css/complementario/procesar_documentos.css',
                // 'navbar': 'resources/css/shared/navbar.css',
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
                'inventario_aprobaciones': 'resources/js/inventario/aprobaciones.js',
                'inventario_card': 'resources/js/inventario/card.js',
                'inventario_carrito': 'resources/js/inventario/carrito.js',
                'inventario_escaner': 'resources/js/inventario/escaner.js',
                'inventario_filtro_departamento': 'resources/js/inventario/filtro-departamento.js',
                'inventario_imagen': 'resources/js/inventario/imagen.js',
                'inventario_notificaciones': 'resources/js/inventario/notificaciones.js',
                'inventario_solicitud': 'resources/js/inventario/solicitud.js',
                // Complementarios files
                'estadisticas_complementarios': 'resources/js/complementarios/estadisticas.js',
                // Personas files
                'personas-import': 'resources/js/pages/personas-import.js',
                'formularios-generico': 'resources/js/pages/formularios-generico.js',
                'formularios-select-dinamico': 'resources/js/pages/formularios-select-dinamico.js',
                'talento-humano': 'resources/js/pages/talento-humano.js',
                'personas-form': 'resources/js/personas/form.js',
            },
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',  // Escucha en todas las interfaces de red
        port: 5173,
        strictPort: true,
        watch: {
            ignored: [
                /(^|[/\\])vendor([/\\]|$)/,
                /(^|[/\\])storage([/\\]|$)/,
                /(^|[/\\])bootstrap[/\\]cache([/\\]|$)/,
                /(^|[/\\])routes([/\\]|$)/,
                /(^|[/\\])database([/\\]|$)/,
                /(^|[/\\])public([/\\]|$)/,
            ],
        },
        hmr: {
            host: 'localhost',  // Usa localhost para HMR en el navegador
        },
    },
});
