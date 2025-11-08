/**
 * imagen.js - Funcionalidad del modal de imagen expandible
 * Incluye zoom, drag & drop, y controles interactivos
 */

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('imageModal');
    const expandedImage = document.getElementById('expandedImage');
    const imageInfo = document.getElementById('imageInfo');
    const imageContainer = document.getElementById('imageContainer');
    
    // Variables de estado
    let scale = 1;
    let isDragging = false;
    let startX, startY, translateX = 0, translateY = 0;
    
    // ========================================
    // Controles de Zoom
    // ========================================
    
    // Zoom In
    const zoomInBtn = document.getElementById('zoomIn');
    if (zoomInBtn) {
        zoomInBtn.addEventListener('click', function() {
            scale = Math.min(scale + 0.2, 3);
            updateImageTransform();
            updateCursor();
        });
    }
    
    // Zoom Out
    const zoomOutBtn = document.getElementById('zoomOut');
    if (zoomOutBtn) {
        zoomOutBtn.addEventListener('click', function() {
            scale = Math.max(scale - 0.2, 0.5);
            updateImageTransform();
            updateCursor();
        });
    }
    
    // Reset Zoom
    const resetZoomBtn = document.getElementById('resetZoom');
    if (resetZoomBtn) {
        resetZoomBtn.addEventListener('click', function() {
            resetView();
        });
    }
    
    // ========================================
    // Descargar Imagen
    // ========================================
    const downloadBtn = document.getElementById('downloadImage');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function() {
            const imageSrc = expandedImage.src;
            if (imageSrc) {
                const link = document.createElement('a');
                link.href = imageSrc;
                link.download = 'producto-' + Date.now() + '.png';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Mostrar notificación
                showNotification('Imagen descargada correctamente', 'success');
            }
        });
    }
    
    // ========================================
    // Función para actualizar transformación
    // ========================================
    function updateImageTransform() {
        if (expandedImage) {
            expandedImage.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
            updateImageInfo();
        }
    }
    
    // ========================================
    // Drag & Drop para mover la imagen
    // ========================================
    if (imageContainer) {
        imageContainer.addEventListener('mousedown', function(e) {
            if (scale > 1) {
                isDragging = true;
                startX = e.clientX - translateX;
                startY = e.clientY - translateY;
                imageContainer.classList.add('dragging');
            }
        });
    }
    
    document.addEventListener('mousemove', function(e) {
        if (isDragging) {
            translateX = e.clientX - startX;
            translateY = e.clientY - startY;
            updateImageTransform();
        }
    });
    
    document.addEventListener('mouseup', function() {
        if (isDragging) {
            isDragging = false;
            if (imageContainer) {
                imageContainer.classList.remove('dragging');
            }
        }
    });
    
    // ========================================
    // Zoom con scroll del mouse
    // ========================================
    if (imageContainer) {
        imageContainer.addEventListener('wheel', function(e) {
            e.preventDefault();
            
            const delta = e.deltaY > 0 ? -0.1 : 0.1;
            scale = Math.max(0.5, Math.min(3, scale + delta));
            
            updateImageTransform();
            updateCursor();
        }, { passive: false });
    }
    
    // ========================================
    // Actualizar cursor según el zoom
    // ========================================
    function updateCursor() {
        if (imageContainer) {
            if (scale > 1) {
                imageContainer.classList.add('draggable');
            } else {
                imageContainer.classList.remove('draggable');
            }
        }
    }
    
    // ========================================
    // Reset al cerrar modal
    // ========================================
    if (modal) {
        $(modal).on('hidden.bs.modal', function() {
            resetView();
            if (expandedImage) expandedImage.src = '';
            if (imageInfo) imageInfo.textContent = '';
        });
        
        // Mostrar información al abrir
        $(modal).on('shown.bs.modal', function() {
            if (expandedImage && expandedImage.complete) {
                updateImageInfo();
            }
        });
    }
    
    // ========================================
    // Event listener para cuando carga la imagen
    // ========================================
    if (expandedImage) {
        expandedImage.addEventListener('load', function() {
            updateImageInfo();
        });
        
        expandedImage.addEventListener('error', function() {
            if (imageInfo) {
                imageInfo.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <span class="text-danger">Error al cargar la imagen</span>';
            }
        });
    }
    
    // ========================================
    // Función para actualizar información de la imagen
    // ========================================
    function updateImageInfo() {
        if (expandedImage && imageInfo) {
            const width = expandedImage.naturalWidth;
            const height = expandedImage.naturalHeight;
            const zoomPercent = Math.round(scale * 100);
            
            if (width && height) {
                imageInfo.innerHTML = `
                    <span>
                        <i class="fas fa-expand-arrows-alt"></i> ${width} × ${height} px
                    </span>
                    <span>
                        <i class="fas fa-search-plus"></i> ${zoomPercent}%
                    </span>
                `;
            }
        }
    }
    
    // ========================================
    // Función para resetear vista
    // ========================================
    function resetView() {
        scale = 1;
        translateX = 0;
        translateY = 0;
        updateImageTransform();
        updateCursor();
    }
    
    // ========================================
    // Función para mostrar notificaciones
    // ========================================
    function showNotification(message, type = 'info') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    }
    
    // ========================================
    // Atajos de teclado
    // ========================================
    document.addEventListener('keydown', function(e) {
        // Solo si el modal está abierto
        if ($(modal).hasClass('show')) {
            switch(e.key) {
                case '+':
                case '=':
                    e.preventDefault();
                    if (zoomInBtn) zoomInBtn.click();
                    break;
                case '-':
                case '_':
                    e.preventDefault();
                    if (zoomOutBtn) zoomOutBtn.click();
                    break;
                case '0':
                    e.preventDefault();
                    if (resetZoomBtn) resetZoomBtn.click();
                    break;
                case 'Escape':
                    $(modal).modal('hide');
                    break;
            }
        }
    });
    
    // ========================================
    // Doble click para zoom
    // ========================================
    if (imageContainer) {
        imageContainer.addEventListener('dblclick', function(e) {
            if (scale === 1) {
                scale = 2;
            } else {
                scale = 1;
                translateX = 0;
                translateY = 0;
            }
            updateImageTransform();
            updateCursor();
        });
    }
    
    // ========================================
    // Prevenir selección de texto durante drag
    // ========================================
    if (imageContainer) {
        imageContainer.addEventListener('selectstart', function(e) {
            if (isDragging) {
                e.preventDefault();
            }
        });
    }
});