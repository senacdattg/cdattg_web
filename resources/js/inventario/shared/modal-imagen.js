/**
 * Modal de Imagen Expandible
 * Permite expandir imágenes con la clase .img-expandable al hacer clic
 */

document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("imageModal");
    
    if (!modal) {
        console.warn('Modal de imagen no encontrado. Asegúrate de incluir image-modal.blade.php');
        return;
    }

    const modalImg = document.getElementById("expandedImage");
    
    if (!modalImg) {
        console.warn('Elemento expandedImage no encontrado en el modal');
        return;
    }

    // Función para inicializar los listeners de las imágenes
    function initImageListeners() {
        document.querySelectorAll(".img-expandable").forEach(img => {
            // Remover listeners previos para evitar duplicados
            img.removeEventListener("click", expandImage);
            // Agregar listener
            img.addEventListener("click", expandImage);
        });
    }

    // Función para expandir la imagen
    function expandImage(event) {
        const imgSrc = this.src || this.getAttribute('data-src');
        if (imgSrc) {
            modalImg.src = imgSrc;
            $(modal).modal('show');
        }
    }

    // Inicializar listeners al cargar
    initImageListeners();

    // Reinicializar si se agregan nuevas imágenes dinámicamente
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                initImageListeners();
            }
        });
    });

    // Observar cambios en el body
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Limpiar src cuando se cierra el modal
    $(modal).on('hidden.bs.modal', function () {
        modalImg.src = '';
    });
});
