/**
 * solicitud.js - Funcionalidad para carga de datos del carrito y control de formulario de solicitud
 */

// Esperar a que el DOM esté listo
document.addEventListener('DOMContentLoaded', function () {
    cargarDatosCarrito();
});

/**
 * Cargar datos del carrito desde sessionStorage
 */
function cargarDatosCarrito() {
    const urlParams = new URLSearchParams(window.location.search);
    const desdeCarrito = urlParams.get('desde_carrito');

    if (desdeCarrito === 'true') {
        const carritoDataString = sessionStorage.getItem('carrito_data');

        if (carritoDataString) {
            try {
                const data = JSON.parse(carritoDataString);

                // Elementos del DOM
                const totalProductosEl = document.getElementById('carrito-total-productos');
                const totalItemsEl = document.getElementById('carrito-total-items');
                const carritoResumenStatsEl = document.getElementById('carrito-resumen-stats');
                const carritoItemsCardEl = document.getElementById('carrito-items-card');
                const carritoItemsTbodyEl = document.getElementById('carrito-items-tbody');

                // Actualizar totales
                if (totalProductosEl) totalProductosEl.textContent = data.totalProductos || 0;
                if (totalItemsEl) totalItemsEl.textContent = data.totalItems || 0;

                // Mostrar resumen si existe
                if (carritoResumenStatsEl) carritoResumenStatsEl.style.display = 'grid';

                // Llenar la tabla de productos
                if (carritoItemsTbodyEl && data.items && data.items.length > 0) {
                    let html = '';
                    data.items.forEach(item => {
                        html += `
                            <tr>
                                <td><strong>${item.name || 'Producto'}</strong></td>
                                <td class="text-center">
                                    <span class="badge badge-primary">${item.quantity}</span>
                                </td>
                            </tr>
                        `;
                    });
                    carritoItemsTbodyEl.innerHTML = html;

                    if (carritoItemsCardEl) carritoItemsCardEl.classList.remove('d-none');
                }

                // Crear input oculto con el carrito (para el backend)
                const form = document.querySelector('#form-solicitud');
                if (form) {
                    const inputHidden = document.createElement('input');
                    inputHidden.type = 'hidden';
                    inputHidden.name = 'carrito';
                    inputHidden.value = JSON.stringify(data.items || []);
                    form.appendChild(inputHidden);
                }

                // Mostrar alerta de éxito
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Carrito cargado',
                        html: `Se han cargado <strong>${data.totalProductos} producto(s)</strong> con <strong>${data.totalItems} ítems</strong><br><small class="text-muted">Completa los datos faltantes</small>`,
                        timer: 3000,
                        showConfirmButton: false
                    });
                }

                // Limpiar sessionStorage (para evitar duplicados)
                sessionStorage.removeItem('carrito_data');

            } catch (error) {
                console.error('Error al cargar datos del carrito:', error);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al cargar los datos del carrito: ' + error.message
                    });
                }
            }
        }
    }

    // Configurar comportamiento de la fecha de devolución
    setupFechaDevolucionToggle();
}

/**
 * Mostrar u ocultar fecha de devolución según el tipo
 */
function setupFechaDevolucionToggle() {
    const tipo = document.getElementById('tipo');
    const grupoFecha = document.getElementById('grupo-fecha-devolucion');
    const fechaDevolucion = document.getElementById('fecha_devolucion');

    function updateFechaEntregaVisibility() {
        if (!tipo || !grupoFecha || !fechaDevolucion) return;

        // Si el tipo es PRÉSTAMO (id=44)
        if (tipo.value === '44' || tipo.value.toLowerCase() === 'prestamo') {
            grupoFecha.classList.remove('d-none');
            fechaDevolucion.setAttribute('required', 'required');
        } else {
            grupoFecha.classList.add('d-none');
            fechaDevolucion.removeAttribute('required');
            fechaDevolucion.value = '';
        }
    }

    if (tipo) {
        tipo.addEventListener('change', updateFechaEntregaVisibility);
        updateFechaEntregaVisibility();
    }
}
