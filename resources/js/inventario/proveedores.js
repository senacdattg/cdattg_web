/**
 * Proveedores.js - Funcionalidades específicas para proveedores
 * Utiliza inventario-common.js para funcionalidades compartidas
 */

// Función para confirmar eliminación (usa la función común)
function confirmDeleteProveedor(id, nombre) {
    window.confirmDelete(id, nombre, 'proveedor', 'inventario/proveedores');
}

document.addEventListener('DOMContentLoaded', () => {
    // Configurar funciones específicas de proveedores
    window.configurarProveedores();
});