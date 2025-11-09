/**
 * imagen.js - Funcionalidad del modal de imagen expandible
 * Incluye zoom, drag & drop, y controles interactivos
 */

document.addEventListener('DOMContentLoaded', function() {
    const elements = obtenerElementosViewer();
    const state = crearEstadoViewer();

    configurarControlesZoom(elements, state);
    configurarDescarga(elements);
    configurarArrastre(elements, state);
    configurarZoomRueda(elements, state);
    configurarEventosModal(elements, state);
    configurarEventosImagen(elements, state);
    configurarAtajosTeclado(elements);
    configurarDobleClick(elements, state);
    configurarPrevencionSeleccion(elements, state);
});

function obtenerElementosViewer() {
    return {
        modal: document.getElementById('imageModal'),
        expandedImage: document.getElementById('expandedImage'),
        imageInfo: document.getElementById('imageInfo'),
        imageContainer: document.getElementById('imageContainer'),
        zoomInBtn: document.getElementById('zoomIn'),
        zoomOutBtn: document.getElementById('zoomOut'),
        resetZoomBtn: document.getElementById('resetZoom'),
        downloadBtn: document.getElementById('downloadImage')
    };
}

function crearEstadoViewer() {
    return {
        scale: 1,
        isDragging: false,
        startX: 0,
        startY: 0,
        translateX: 0,
        translateY: 0
    };
}

function configurarControlesZoom(elements, state) {
    const { zoomInBtn, zoomOutBtn, resetZoomBtn } = elements;

    zoomInBtn?.addEventListener('click', () => ajustarZoom(state, 0.2, elements));
    zoomOutBtn?.addEventListener('click', () => ajustarZoom(state, -0.2, elements));
    resetZoomBtn?.addEventListener('click', () => restablecerVista(state, elements));
}

function configurarDescarga(elements) {
    const { downloadBtn, expandedImage } = elements;

    downloadBtn?.addEventListener('click', () => {
        const imageSrc = expandedImage?.src;
        if (!imageSrc) return;

        const link = document.createElement('a');
        link.href = imageSrc;
        link.download = `producto-${Date.now()}.png`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        mostrarNotificacion('Imagen descargada correctamente', 'success');
    });
}

function configurarArrastre(elements, state) {
    const { imageContainer } = elements;

    imageContainer?.addEventListener('mousedown', (event) => iniciarArrastre(event, elements, state));
    document.addEventListener('mousemove', (event) => continuarArrastre(event, elements, state));
    document.addEventListener('mouseup', () => finalizarArrastre(elements, state));
}

function configurarZoomRueda(elements, state) {
    const { imageContainer } = elements;

    imageContainer?.addEventListener('wheel', (event) => {
        event.preventDefault();
        const delta = event.deltaY > 0 ? -0.1 : 0.1;
        ajustarZoom(state, delta, elements);
    }, { passive: false });
}

function configurarEventosModal(elements, state) {
    const { modal, expandedImage, imageInfo } = elements;
    if (!modal || typeof $ === 'undefined') return;

    $(modal).on('hidden.bs.modal', () => {
        restablecerVista(state, elements);
        if (expandedImage) expandedImage.src = '';
        if (imageInfo) imageInfo.textContent = '';
    });

    $(modal).on('shown.bs.modal', () => {
        if (expandedImage?.complete) {
            actualizarInformacionImagen(elements, state);
        }
    });
}

function configurarEventosImagen(elements, state) {
    const { expandedImage, imageInfo } = elements;
    if (!expandedImage) return;

    expandedImage.addEventListener('load', () => actualizarInformacionImagen(elements, state));
    expandedImage.addEventListener('error', () => {
        if (imageInfo) {
            imageInfo.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <span class="text-danger">Error al cargar la imagen</span>';
        }
    });
}

function configurarAtajosTeclado(elements) {
    const { modal, zoomInBtn, zoomOutBtn, resetZoomBtn } = elements;
    if (typeof $ === 'undefined') return;

    document.addEventListener('keydown', (event) => {
        if (!$(modal).hasClass('show')) return;

        const acciones = {
            '+': () => zoomInBtn?.click(),
            '=': () => zoomInBtn?.click(),
            '-': () => zoomOutBtn?.click(),
            '_': () => zoomOutBtn?.click(),
            '0': () => resetZoomBtn?.click(),
            'Escape': () => $(modal).modal('hide')
        };

        if (acciones[event.key]) {
            event.preventDefault();
            acciones[event.key]();
        }
    });
}

function configurarDobleClick(elements, state) {
    const { imageContainer } = elements;
    if (!imageContainer) return;

    imageContainer.addEventListener('dblclick', () => {
        if (state.scale === 1) {
            state.scale = 2;
        } else {
            state.scale = 1;
            state.translateX = 0;
            state.translateY = 0;
        }
        actualizarTransformacionImagen(elements, state);
        actualizarCursor(elements, state);
    });
}

function configurarPrevencionSeleccion(elements, state) {
    const { imageContainer } = elements;

    imageContainer?.addEventListener('selectstart', (event) => {
        if (state.isDragging) {
            event.preventDefault();
        }
    });
}

function ajustarZoom(state, delta, elements) {
    state.scale = Math.max(0.5, Math.min(3, state.scale + delta));
    if (state.scale <= 1) {
        state.translateX = 0;
        state.translateY = 0;
    }
    actualizarTransformacionImagen(elements, state);
    actualizarCursor(elements, state);
}

function iniciarArrastre(event, elements, state) {
    if (state.scale <= 1) return;

    state.isDragging = true;
    state.startX = event.clientX - state.translateX;
    state.startY = event.clientY - state.translateY;
    elements.imageContainer?.classList.add('dragging');
}

function continuarArrastre(event, elements, state) {
    if (!state.isDragging) return;

    state.translateX = event.clientX - state.startX;
    state.translateY = event.clientY - state.startY;
    actualizarTransformacionImagen(elements, state);
}

function finalizarArrastre(elements, state) {
    if (!state.isDragging) return;

    state.isDragging = false;
    elements.imageContainer?.classList.remove('dragging');
}

function actualizarTransformacionImagen(elements, state) {
    const { expandedImage } = elements;
    if (!expandedImage) return;

    expandedImage.style.transform = `translate(${state.translateX}px, ${state.translateY}px) scale(${state.scale})`;
    actualizarInformacionImagen(elements, state);
}

function actualizarCursor(elements, state) {
    const { imageContainer } = elements;
    if (!imageContainer) return;

    if (state.scale > 1) {
        imageContainer.classList.add('draggable');
    } else {
        imageContainer.classList.remove('draggable');
    }
}

function actualizarInformacionImagen(elements, state) {
    const { expandedImage, imageInfo } = elements;
    if (!expandedImage || !imageInfo) return;

    const width = expandedImage.naturalWidth;
    const height = expandedImage.naturalHeight;
    const zoomPercent = Math.round(state.scale * 100);

    if (width && height) {
        imageInfo.innerHTML = `
            <span>
                <i class="fas fa-expand-arrows-alt"></i> ${width} × ${height} px
            </span>
            <span>
                <i class="fas fa-search-plus"></i> ${zoomPercent}%
            </span>
        `;
    } else {
        imageInfo.innerHTML = '<span class="text-muted">Información no disponible</span>';
    }
}

function restablecerVista(state, elements) {
    state.scale = 1;
    state.translateX = 0;
    state.translateY = 0;
    actualizarTransformacionImagen(elements, state);
    actualizarCursor(elements, state);
}

function mostrarNotificacion(message, type = 'info') {
    if (typeof Swal === 'undefined') return;

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