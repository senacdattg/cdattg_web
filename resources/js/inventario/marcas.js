/**
 * Marcas.js - Funcionalidades específicas para marcas
 * Utiliza inventario-common.js para funcionalidades compartidas
 */

// Función para confirmar eliminación (usa la función común)
function confirmDeleteMarca(id, nombre) {
    window.confirmDelete(id, nombre, 'marca', 'inventario/marcas');
}

document.addEventListener('DOMContentLoaded', () => {
    // Configurar funciones específicas de marcas
    window.configurarMarcas();
});