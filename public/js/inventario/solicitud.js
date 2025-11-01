/**
 * solicitud.js - Funcionalidad para carga de datos del carrito y control de formulario de solicitud
 */

// Esperar a que el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', cargarDatosCarrito);
} else {
    cargarDatosCarrito();
}

/**
 * Cargar datos del carrito desde sessionStorage
 */
function cargarDatosCarrito() {
    // ===== Cargar datos del carrito si viene desde la ruta =====
    const urlParams = new URLSearchParams(window.location.search);
    const desdeCarrito = urlParams.get('desde_carrito');
    
    if (desdeCarrito === 'true') {
        const carritoData = sessionStorage.getItem('carrito_data');
        
        if (carritoData) {
            try {
                const data = JSON.parse(carritoData);
                
                // Actualizar estadísticas
                const totalProductosEl = document.getElementById('carrito-total-productos');
                const totalItemsEl = document.getElementById('carrito-total-items');
                const carritoResumenStatsEl = document.getElementById('carrito-resumen-stats');
                const carritoItemsCardEl = document.getElementById('carrito-items-card');
                const carritoItemsTbodyEl = document.getElementById('carrito-items-tbody');
                
                if (totalProductosEl) {
                    totalProductosEl.textContent = data.totalProductos;
                }
                if (totalItemsEl) {
                    totalItemsEl.textContent = data.totalItems;
                }
                
                // Mostrar tarjeta de resumen si no está visible
                if (carritoResumenStatsEl) {
                    carritoResumenStatsEl.style.display = 'grid';
                }
                
                // Llenar tabla de productos
                if (carritoItemsTbodyEl && data.items && data.items.length > 0) {
                    let html = '';
                    data.items.forEach(item => {
                        html += `
                            <tr>
                                <td>
                                    <strong>${item.name || 'Producto'}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-primary">${item.quantity}</span>
                                </td>
                            </tr>
                        `;
                    });
                    carritoItemsTbodyEl.innerHTML = html;
                    
                    // Mostrar tarjeta de items
                    if (carritoItemsCardEl) {
                        carritoItemsCardEl.classList.remove('d-none');
                    }
                }
                
                // Mostrar alerta informativa
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Carrito cargado',
                        html: `Se han cargado <strong>${data.totalProductos} producto(s)</strong> con <strong>${data.totalItems} ítems</strong><br><small class="text-muted">Completa los datos faltantes</small>`,
                        timer: 3500,
                        showConfirmButton: false
                    });
                }
                
                // Limpiar sessionStorage
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
    
    // ===== Lógica para mostrar/ocultar fecha de devolución =====
    setupFechaDevolucionToggle();
}

/**
 * Configurar toggle de fecha de devolución según el tipo seleccionado
 */
function setupFechaDevolucionToggle() {
    const tipo = document.getElementById('tipo');
    const grupoFecha = document.getElementById('grupo-fecha-devolucion');
    const fechaDevolucion = document.getElementById('fecha_devolucion');
    
    function updateFechaEntregaVisibility() {
        if (!tipo) return;
        
        if (tipo.value === 'prestamo') {
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
