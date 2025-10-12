/**
 * Categorias.js - Funcionalidades específicas para categorías
 * Utiliza inventario-common.js para funcionalidades compartidas
 */

// Función para confirmar eliminación (usa la función común)
function confirmDeleteCategoria(id, nombre) {
    window.confirmDelete(id, nombre, 'categoría', 'inventario/categorias');
}

document.addEventListener('DOMContentLoaded', () => {
    // Configurar funciones específicas de categorías
    window.configurarCategorias();
});