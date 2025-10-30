/**
 * card.js - Funcionalidad para la vista de catálogo de productos (ecommerce)
 * Maneja búsqueda, filtrado, ordenamiento y acciones de productos
 */

// Configuración global
const API_BASE_URL = '/inventario';
const STORAGE_KEY = 'inventario_carrito';

// Estado del carrito
let cart = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];

/**
 * Inicialización cuando el DOM está listo
 */
document.addEventListener('DOMContentLoaded', function() {
    initializeCardView();
    updateCartCount();
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
            filterProducts();
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
        filterProducts();
    });
}

/**
 * Configurar filtro por marca
 */
function setupBrandFilter() {
    const brandSelect = document.getElementById('filter-brand');
    if (!brandSelect) return;

    brandSelect.addEventListener('change', function() {
        filterProducts();
    });
}

/**
 * Configurar ordenamiento de productos
 */
function setupSortFilter() {
    const sortSelect = document.getElementById('sort-by');
    if (!sortSelect) return;

    sortSelect.addEventListener('change', function() {
        sortProducts(this.value);
    });
}

/**
 * Filtrar productos según criterios de búsqueda
 */
function filterProducts() {
    const searchTerm = document.getElementById('search-product')?.value.toLowerCase() || '';
    const categoryId = document.getElementById('filter-category')?.value || '';
    const brandId = document.getElementById('filter-brand')?.value || '';

    const productCards = document.querySelectorAll('.product-card');
    let visibleCount = 0;

    productCards.forEach(card => {
        const name = card.dataset.name || '';
        const code = card.dataset.code || '';
        const category = card.dataset.category || '';
        const brand = card.dataset.brand || '';

        // Verificar criterios de filtrado
        const matchesSearch = !searchTerm || 
                            name.includes(searchTerm) || 
                            code.includes(searchTerm);
        const matchesCategory = !categoryId || category === categoryId;
        const matchesBrand = !brandId || brand === brandId;

        // Mostrar u ocultar producto
        if (matchesSearch && matchesCategory && matchesBrand) {
            card.style.display = '';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    // Mostrar mensaje si no hay resultados
    toggleNoResults(visibleCount === 0);
}

/**
 * Ordenar productos
 */
function sortProducts(sortBy) {
    const grid = document.getElementById('products-grid');
    if (!grid) return;

    const cards = Array.from(grid.querySelectorAll('.product-card'));
    
    cards.sort((a, b) => {
        switch(sortBy) {
            case 'name':
                return a.dataset.name.localeCompare(b.dataset.name);
            
            case 'stock-asc':
                const stockA = parseInt(a.querySelector('.badge-success, .badge-warning, .badge-danger')?.textContent) || 0;
                const stockB = parseInt(b.querySelector('.badge-success, .badge-warning, .badge-danger')?.textContent) || 0;
                return stockA - stockB;
            
            case 'stock-desc':
                const stockDescA = parseInt(a.querySelector('.badge-success, .badge-warning, .badge-danger')?.textContent) || 0;
                const stockDescB = parseInt(b.querySelector('.badge-success, .badge-warning, .badge-danger')?.textContent) || 0;
                return stockDescB - stockDescA;
            
            case 'newest':
                return parseInt(b.dataset.id) - parseInt(a.dataset.id);
            
            default:
                return 0;
        }
    });

    // Reorganizar elementos
    cards.forEach(card => grid.appendChild(card));
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
    const modal = new bootstrap.Modal(document.getElementById('productDetailModal'));
    const contentDiv = document.getElementById('product-detail-content');

    // Mostrar loading
    contentDiv.innerHTML = `
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-3x"></i>
            <p class="mt-3">Cargando detalles...</p>
        </div>
    `;
    
    modal.show();

    try {
        const response = await fetch(`${API_BASE_URL}/productos/${productId}`);
        
        if (!response.ok) {
            throw new Error('Error al cargar los detalles del producto');
        }

        const html = await response.text();
        
        // Extraer solo el contenido del card-body si viene en formato completo
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const cardBody = doc.querySelector('.card-body');
        
        if (cardBody) {
            contentDiv.innerHTML = cardBody.innerHTML;
        } else {
            contentDiv.innerHTML = html;
        }

    } catch (error) {
        console.error('Error:', error);
        contentDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                Error al cargar los detalles del producto. Por favor, intenta nuevamente.
            </div>
        `;
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

    filterProducts();
}

// Exportar funciones para uso externo si es necesario
window.inventarioCard = {
    filterProducts,
    sortProducts,
    clearFilters,
    addToCart,
    updateCartCount
};