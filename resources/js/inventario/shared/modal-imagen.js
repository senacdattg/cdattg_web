/**
 * Modal de Imagen Expandible
 * Maneja la funcionalidad de expandir imágenes en modal
 */

document.addEventListener('DOMContentLoaded', function() {
    // Obtener todas las imágenes expandibles
    const expandableImages = document.querySelectorAll('.img-expandable, .clickable-img');
    const imageModal = document.getElementById('imageModal');
    const expandedImage = document.getElementById('expandedImage');
    
    if (expandableImages.length > 0 && imageModal && expandedImage) {
        expandableImages.forEach(img => {
            img.style.cursor = 'pointer';
            
            img.addEventListener('click', function() {
                expandedImage.src = this.src;
                $(imageModal).modal('show');
            });
        });
    }
    
    // Limpiar la imagen cuando se cierre el modal
    if (imageModal) {
        $(imageModal).on('hidden.bs.modal', function() {
            if (expandedImage) {
                expandedImage.src = '';
            }
        });
    }
});