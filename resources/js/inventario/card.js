/**
 * card.js - Funcionalidad para la vista de catálogo de productos (ecommerce)
 * Maneja búsqueda, filtrado, ordenamiento y acciones de productos
 */

// Configuración global
const API_BASE_URL = '/inventario';
const STORAGE_KEY = 'inventario_carrito';

// Estado del carrito
let cart = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];

// Estado de la vista del catálogo
let originalGridHTML = '';
let originalPaginationHTML = '';
let showingSearchResults = false;
let currentFetchController = null;
let currentFetchedProducts = [];

/**
 * Helper para mostrar/ocultar modales compatible con Bootstrap 4 y 5
 */
function showModal(elementId) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    element.style.display = 'flex';
}

function closeProductModal() {
    const modal = document.getElementById('productDetailModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

/**
 * Expandir imagen en modal
 */
function expandirImagen(imageSrc) {
    const expandedImage = document.getElementById('expandedImage');
    const imageModal = document.getElementById('imageModal');
    
    if (expandedImage && imageModal) {
        expandedImage.src = imageSrc;
        // Usar jQuery si está disponible
        if (typeof $ !== 'undefined') {
            $(imageModal).modal('show');
        }
    }
}

/**
 * Agregar producto al carrito desde el modal
 */
function agregarAlCarritoDesdeModal(productId, productName, productStock) {
    try {
        addToCart(productId, productName, productStock);
        closeProductModal();
    } catch (error) {
        console.error('Error al agregar al carrito:', error);
        alert('Error al agregar al carrito. Por favor intente de nuevo.');
    }
}

/**
 * Inicialización cuando el DOM está listo
 */
document.addEventListener('DOMContentLoaded', function() {
    captureInitialState();
    initializeCardView();
    updateCartCount();
    setupModalDismissHandlers();
});

/**
 * Inicializa la vista de catálogo
 */
function initializeCardView() {
    setupSearchFilter();
    setupCategoryFilter();
    setupBrandFilter();
    setupSortFilter();
    setupProductActions();
    updateCartCount();
}

/**
 * Configura los métodos para cerrar el modal de detalles
 */
function setupModalDismissHandlers() {
    const modal = document.getElementById('productDetailModal');
    if (modal && !modal.dataset.dismissInitialized) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeProductModal();
            }
        });
        modal.dataset.dismissInitialized = 'true';
    }

    if (!document.body.dataset.productModalEscapeListener) {
        document.addEventListener('keydown', handleProductModalEscape);
        document.body.dataset.productModalEscapeListener = 'true';
    }
}

function handleProductModalEscape(event) {
    if (event.key === 'Escape') {
        closeProductModal();
    }
}

/**
 * Guardar el estado inicial renderizado por Blade
 */
function captureInitialState() {
    const grid = document.getElementById('products-grid');
    const pagination = document.getElementById('catalog-pagination');

    if (grid) {
        originalGridHTML = grid.innerHTML;
    }

    if (pagination) {
        originalPaginationHTML = pagination.innerHTML;
    }
}

/**
 * Configurar búsqueda de productos
 */
function setupSearchFilter() {
    const searchInput = document.getElementById('search-product');
    if (!searchInput) return;

    // Búsqueda en tiempo real con debounce
    let searchTimeout;
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            handleFiltersChange();
        }, 300);
    });
}

/**
 * Configurar filtro por categoría
 */
function setupCategoryFilter() {
    const categorySelect = document.getElementById('filter-category');
    if (!categorySelect) return;

    categorySelect.addEventListener('change', function() {
        handleFiltersChange();
    });
}

/**
 * Configurar filtro por marca
 */
function setupBrandFilter() {
    const brandSelect = document.getElementById('filter-brand');
    if (!brandSelect) return;

    brandSelect.addEventListener('change', function() {
        handleFiltersChange();
    });
}

/**
 * Configurar ordenamiento de productos
 */
function setupSortFilter() {
    const sortSelect = document.getElementById('sort-by');
    if (!sortSelect) return;

    sortSelect.addEventListener('change', function() {
        handleFiltersChange();
    });
}

/**
 * Gestionar filtros: decide cuándo usar búsqueda AJAX y cuándo restaurar la vista inicial
 */
function handleFiltersChange() {
    const searchTerm = document.getElementById('search-product')?.value.trim() || '';
    const categoryId = document.getElementById('filter-category')?.value || '';
    const brandId = document.getElementById('filter-brand')?.value || '';
    const sortBy = document.getElementById('sort-by')?.value || 'name';

    const hasActiveFilters = Boolean(searchTerm) || Boolean(categoryId) || Boolean(brandId);

    if (!hasActiveFilters) {
        if (showingSearchResults) {
            restoreInitialState();
        }
        sortProducts(sortBy);
        setPaginationVisibility(true);
        return;
    }

    fetchAndRenderProducts({ searchTerm, categoryId, brandId, sortBy });
}

/**
 * Restaurar la grilla original renderizada por Blade
 */
function restoreInitialState() {
    const grid = document.getElementById('products-grid');
    const pagination = document.getElementById('catalog-pagination');

    if (grid && originalGridHTML) {
        grid.innerHTML = originalGridHTML;
    }

    if (pagination && originalPaginationHTML !== '') {
        pagination.innerHTML = originalPaginationHTML;
        pagination.style.display = '';
    }

    showingSearchResults = false;
    currentFetchedProducts = [];
    setupProductActions();
    toggleNoResults(false);
}

/**
 * Consultar al backend para traer los productos filtrados
 */
async function fetchAndRenderProducts({ searchTerm, categoryId, brandId, sortBy }) {
    const grid = document.getElementById('products-grid');
    if (!grid) return;

    toggleNoResults(false);
    setPaginationVisibility(false);

    if (currentFetchController) {
        currentFetchController.abort();
    }

    currentFetchController = new AbortController();
    const params = new URLSearchParams();

    if (searchTerm) params.append('search', searchTerm);
    if (categoryId) params.append('category_id', categoryId);
    if (brandId) params.append('brand_id', brandId);

    grid.innerHTML = `
        <div class="col-12 text-center py-5">
            <i class="fas fa-spinner fa-spin fa-3x mb-3"></i>
            <p>Buscando productos...</p>
        </div>
    `;

    try {
        const response = await fetch(`${API_BASE_URL}/productos/buscar?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            signal: currentFetchController.signal
        });

        if (!response.ok) {
            throw new Error('No se pudo obtener la información de productos');
        }

        const data = await response.json();
        const productos = Array.isArray(data.productos) ? data.productos : [];

        currentFetchedProducts = sortProductData(productos, sortBy);
        renderProducts(currentFetchedProducts, true);
        showingSearchResults = true;
    } catch (error) {
        if (error.name === 'AbortError') {
            return;
        }

        console.error('Error al buscar productos:', error);
        grid.innerHTML = `
            <div class="col-12">
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                    <p>Error al buscar productos.</p>
                    <small>${error.message}</small>
                </div>
            </div>
        `;
    }
}

/**
 * Ordenar productos
 */
function sortProducts(sortBy) {
    if (showingSearchResults) {
        currentFetchedProducts = sortProductData(currentFetchedProducts, sortBy);
        renderProducts(currentFetchedProducts, true);
        return;
    }

    const grid = document.getElementById('products-grid');
    if (!grid) return;

    const cards = Array.from(grid.querySelectorAll('.product-card'));
    
    cards.sort((a, b) => compareCards(a, b, sortBy));

    // Reorganizar elementos
    cards.forEach(card => grid.appendChild(card));
}

function sortProductData(products, sortBy) {
    const sorted = [...products];

    sorted.sort((a, b) => {
        switch (sortBy) {
            case 'stock-asc':
                return (a.cantidad || 0) - (b.cantidad || 0);
            case 'stock-desc':
                return (b.cantidad || 0) - (a.cantidad || 0);
            case 'newest':
                return (b.id || 0) - (a.id || 0);
            case 'name':
            default:
                return (a.producto || '').toLowerCase().localeCompare((b.producto || '').toLowerCase());
        }
    });

    return sorted;
}

function compareCards(a, b, sortBy) {
    switch (sortBy) {
        case 'stock-asc':
            return extractCardStock(a) - extractCardStock(b);
        case 'stock-desc':
            return extractCardStock(b) - extractCardStock(a);
        case 'newest':
            return parseInt(b.dataset.id) - parseInt(a.dataset.id);
        case 'name':
        default:
            return (a.dataset.name || '').localeCompare(b.dataset.name || '');
    }
}

function extractCardStock(card) {
    const stockBadge = card.querySelector('.badge-success, .badge-warning, .badge-danger');
    if (!stockBadge) return 0;
    const matches = stockBadge.textContent.match(/\d+/);
    return matches ? parseInt(matches[0], 10) : 0;
}

function renderProducts(products, skipSort = false) {
    const grid = document.getElementById('products-grid');
    if (!grid) return;

    if (!Array.isArray(products) || products.length === 0) {
        grid.innerHTML = '';
        toggleNoResults(true);
        return;
    }

    toggleNoResults(false);

    if (!skipSort) {
        const sortBy = document.getElementById('sort-by')?.value || 'name';
        products = sortProductData(products, sortBy);
    }

    const cardsHTML = products.map(product => createProductCardHTML(product)).join('');
    grid.innerHTML = cardsHTML;

    setupProductActions();
}

function createProductCardHTML(product) {
    const stockClass = getStockClass(product.cantidad);
    const categoriaNombre = product?.categoria?.name || 'Sin categoría';
    const marcaNombre = product?.marca?.name || '';
    const descripcion = product.descripcion || 'Sin descripción disponible';
    const codigoBarras = product.codigo_barras || 'S/N';
    const imagenSrc = product.imagen_url || product.imagen || null;
    const productoNombre = product.producto || '';

    return `
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4 product-card"
             data-id="${product.id}"
             data-category="${product.categoria_id || ''}"
             data-brand="${product.marca_id || ''}"
             data-name="${productoNombre.toLowerCase()}"
             data-code="${(product.codigo_barras || '').toLowerCase()}">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="product-image-container">
                    ${imagenSrc ? `
                        <img src="${imagenSrc}" class="card-img-top product-image" alt="${productoNombre}">
                    ` : `
                        <div class="no-image-placeholder">
                            <i class="fas fa-box fa-4x text-muted"></i>
                            <p class="text-muted mt-2">Sin imagen</p>
                        </div>
                    `}
                    <span class="badge stock-badge stock-badge-${stockClass}"></span>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-tag"></i> ${categoriaNombre}
                        </small>
                        ${marcaNombre ? `
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-copyright"></i> ${marcaNombre}
                            </small>
                        ` : ''}
                    </div>
                    <h5 class="card-title font-weight-bold mb-2">
                        ${truncateText(productoNombre, 50)}
                    </h5>
                    <p class="card-text text-muted small flex-grow-1">
                        ${truncateText(descripcion, 80)}
                    </p>
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="fas fa-barcode"></i>
                            <span class="badge badge-secondary">${codigoBarras}</span>
                        </small>
                    </div>
                    <div class="mb-3">
                        <strong>Stock: </strong>
                        <span class="badge badge-${stockClass}">
                            ${product.cantidad || 0} unidades
                        </span>
                    </div>
                    <div class="btn-group d-flex" role="group">
                        <button type="button"
                                class="btn btn-sm btn-info btn-view-details w-50"
                                data-id="${product.id}"
                                title="Ver detalles">
                            <i class="fas fa-eye"></i> Detalles
                        </button>
                        ${(product.cantidad || 0) > 0 ? `
                            <button type="button"
                                    class="btn btn-sm btn-success btn-add-to-cart w-50"
                                    data-id="${product.id}"
                                    data-name="${productoNombre}"
                                    data-stock="${product.cantidad}"
                                    title="Agregar al carrito">
                                <i class="fas fa-cart-plus"></i> Agregar
                            </button>
                        ` : `
                            <button type="button" class="btn btn-sm btn-secondary w-50" disabled>
                                <i class="fas fa-ban"></i> Agotado
                            </button>
                        `}
                    </div>
                </div>
            </div>
        </div>
    `;
}

function truncateText(text, maxLength) {
    const value = text || '';
    if (value.length <= maxLength) {
        return value;
    }
    return `${value.slice(0, maxLength)}...`;
}

function getStockClass(quantity) {
    const qty = quantity || 0;
    if (qty <= 0) {
        return 'danger';
    }
    if (qty <= 5) {
        return 'warning';
    }
    return 'success';
}

function setPaginationVisibility(show) {
    const pagination = document.getElementById('catalog-pagination');
    if (!pagination) return;
    pagination.style.display = show ? '' : 'none';
}

/**
 * Configurar acciones de productos
 */
function setupProductActions() {
    // Botones de ver detalles
    document.querySelectorAll('.btn-view-details').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.id;
            showProductDetails(productId);
        });
    });

    // Botones de agregar al carrito
    document.querySelectorAll('.btn-add-to-cart').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.dataset.id;
            const productName = this.dataset.name;
            const productStock = parseInt(this.dataset.stock);
            addToCart(productId, productName, productStock);
        });
    });
}

/**
 * Mostrar detalles del producto en modal
 */
async function showProductDetails(productId) {
    const contentDiv = document.getElementById('product-detail-content');

    // Mostrar loading
    contentDiv.innerHTML = `
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-3x"></i>
            <p class="mt-3">Cargando detalles...</p>
        </div>
    `;

    try {
        // Usar la ruta /detalles en lugar de la ruta show normal
        const response = await fetch(`${API_BASE_URL}/productos/detalles/${productId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        });
        
        if (!response.ok) {
            throw new Error('Error al cargar los detalles del producto');
        }

        const html = await response.text();
        contentDiv.innerHTML = html;
        
        // Mostrar el modal DESPUÉS de cargar el contenido
        showModal('productDetailModal');
        
    } catch (error) {
        console.error('Error:', error);
        contentDiv.innerHTML = `
            <div class="alert alert-danger text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <p>Error al cargar los detalles del producto</p>
                <small>${error.message}</small>
            </div>
        `;
        // Mostrar el modal incluso con error para que vea el mensaje
        showModal('productDetailModal');
    }
}

/**
 * Agregar producto al carrito
 */
function addToCart(productId, productName, productStock) {
    // Verificar si el producto ya está en el carrito
    const existingItem = cart.find(item => item.id === productId);

    if (existingItem) {
        // Verificar stock disponible
        if (existingItem.quantity >= productStock) {
            showStockAlert(productName, productStock);
            return;
        }
        // Incrementar cantidad
        existingItem.quantity++;
    } else {
        // Agregar nuevo producto
        cart.push({
            id: productId,
            name: productName,
            quantity: 1,
            maxStock: productStock
        });
    }

    // Guardar en localStorage
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));

    // Actualizar contador
    updateCartCount();

    // Mostrar notificación
    showSuccessNotification(`"${productName}" agregado al carrito`);
}

/**
 * Actualizar contador del carrito
 */
function updateCartCount() {
    const countBadge = document.getElementById('cart-count');
    if (!countBadge) return;

    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    countBadge.textContent = totalItems;

    // Animar el badge si hay items
    if (totalItems > 0) {
        countBadge.classList.add('badge-warning');
        countBadge.classList.remove('badge-light');
    } else {
        countBadge.classList.remove('badge-warning');
        countBadge.classList.add('badge-light');
    }
}

/**
 * Mostrar/ocultar mensaje de "no hay resultados"
 */
function toggleNoResults(show) {
    const noResultsDiv = document.getElementById('no-results');
    const gridDiv = document.getElementById('products-grid');
    
    if (noResultsDiv && gridDiv) {
        if (show) {
            noResultsDiv.classList.remove('d-none');
            gridDiv.classList.add('d-none');
        } else {
            noResultsDiv.classList.add('d-none');
            gridDiv.classList.remove('d-none');
        }
    }
}

/**
 * Mostrar alerta de stock insuficiente
 */
function showStockAlert(productName, maxStock) {
    Swal.fire({
        icon: 'warning',
        title: 'Stock Insuficiente',
        html: `
            <p>Ya has agregado la cantidad máxima disponible de <strong>"${productName}"</strong></p>
            <p class="text-muted">Stock disponible: ${maxStock} unidades</p>
        `,
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#3085d6'
    });
}

/**
 * Mostrar notificación de éxito
 */
function showSuccessNotification(message) {
    // Toast notification
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: 'success',
        title: message
    });
}

/**
 * Limpiar todos los filtros
 */
function clearFilters() {
    const searchInput = document.getElementById('search-product');
    const categorySelect = document.getElementById('filter-category');
    const brandSelect = document.getElementById('filter-brand');
    const sortSelect = document.getElementById('sort-by');

    if (searchInput) searchInput.value = '';
    if (categorySelect) categorySelect.value = '';
    if (brandSelect) brandSelect.value = '';
    if (sortSelect) sortSelect.value = 'name';

    handleFiltersChange();
}

// Exportar funciones para uso externo si es necesario
window.inventarioCard = {
    handleFiltersChange,
    sortProducts,
    clearFilters,
    addToCart,
    updateCartCount
};

// Exponer funciones utilizadas por atributos onclick en el HTML renderizado por Blade
window.closeProductModal = closeProductModal;
window.agregarAlCarritoDesdeModal = agregarAlCarritoDesdeModal;
window.expandirImagen = expandirImagen;