/**
 * Script para gestión de aprobaciones de productos
 * Incluye funcionalidades para aprobar/rechazar productos individuales y órdenes completas
 */

import Swal from 'sweetalert2';

/**
 * Aprobar un producto individual
 * @param {number} detalleId - ID del detalle de la orden
 * @param {string} nombreProducto - Nombre del producto a aprobar
 */
function aprobarProducto(detalleId, nombreProducto) {
    Swal.fire({
        title: '¿Aprobar producto?',
        html: `¿Está seguro de aprobar este producto?<br><strong>${nombreProducto}</strong><br><br>El stock será descontado automáticamente.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check"></i> Sí, aprobar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear formulario y enviarlo
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/inventario/aprobaciones/${detalleId}/aprobar`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

/**
 * Rechazar un producto individual
 * @param {number} detalleId - ID del detalle de la orden
 * @param {string} nombreProducto - Nombre del producto a rechazar
 */
function rechazarProducto(detalleId, nombreProducto) {
    Swal.fire({
        title: '¿Rechazar producto?',
        html: `¿Está seguro de rechazar este producto?<br><strong>${nombreProducto}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-times"></i> Sí, rechazar',
        cancelButtonText: '<i class="fas fa-ban"></i> Cancelar',
        input: 'textarea',
        inputLabel: 'Motivo del rechazo (obligatorio)',
        inputPlaceholder: 'Explique el motivo del rechazo...',
        inputAttributes: {
            'aria-label': 'Motivo del rechazo',
            'required': 'required'
        },
        inputValidator: (value) => {
            if (!value) {
                return 'Debe indicar el motivo del rechazo';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear formulario y enviarlo
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/inventario/aprobaciones/${detalleId}/rechazar`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            const motivoInput = document.createElement('input');
            motivoInput.type = 'hidden';
            motivoInput.name = 'motivo_rechazo';
            motivoInput.value = result.value;
            form.appendChild(motivoInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

/**
 * Aprobar una orden completa con todos sus productos
 * @param {number} ordenId - ID de la orden
 * @param {string} productos - Lista de productos en formato texto
 */
function aprobarOrden(ordenId, productos) {
    Swal.fire({
        title: '¿Aprobar toda la orden?',
        html: `¿Está seguro de aprobar TODOS los productos de esta orden?<br><br><strong>Productos:</strong> ${productos}<br><br>El stock será descontado automáticamente para todos los productos.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check"></i> Sí, aprobar todo',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear formulario y enviarlo
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/inventario/aprobaciones/orden/${ordenId}/aprobar`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

/**
 * Rechazar una orden completa con todos sus productos
 * @param {number} ordenId - ID de la orden
 * @param {string} productos - Lista de productos en formato texto
 */
function rechazarOrden(ordenId, productos) {
    Swal.fire({
        title: '¿Rechazar toda la orden?',
        html: `¿Está seguro de rechazar TODOS los productos de esta orden?<br><br><strong>Productos:</strong> ${productos}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-times"></i> Sí, rechazar todo',
        cancelButtonText: '<i class="fas fa-ban"></i> Cancelar',
        input: 'textarea',
        inputLabel: 'Motivo del rechazo (obligatorio)',
        inputPlaceholder: 'Explique el motivo del rechazo de toda la orden...',
        inputAttributes: {
            'aria-label': 'Motivo del rechazo',
            'required': 'required'
        },
        inputValidator: (value) => {
            if (!value) {
                return 'Debe indicar el motivo del rechazo';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear formulario y enviarlo
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/inventario/aprobaciones/orden/${ordenId}/rechazar`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);

            const motivoInput = document.createElement('input');
            motivoInput.type = 'hidden';
            motivoInput.name = 'motivo_rechazo';
            motivoInput.value = result.value;
            form.appendChild(motivoInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Exponer funciones para uso en vistas Blade
window.aprobarProducto = aprobarProducto;
window.rechazarProducto = rechazarProducto;
window.aprobarOrden = aprobarOrden;
window.rechazarOrden = rechazarOrden;
