/**
 * carrito.js - Funcionalidad del carrito de compras
 * Maneja la gestión del carrito, actualización de cantidades y envío de órdenes
 */

// Configuración global
const API_BASE_URL = '/inventario';
const STORAGE_KEY = 'inventario_carrito';
const DRAFT_KEY = 'inventario_draft';

// Estado del carrito
let cart = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
let productsDetails = {}; // Cache de detalles de productos

/**
 * Inicialización cuando el DOM está listo
 */
document.addEventListener('DOMContentLoaded', function() {
    initializeCart();
});

/**
 * Inicializa el carrito
 */
async function initializeCart() {
    await loadCartItems();
    setupCartActions();
    updateCartSummary();
}

/**
 * Cargar items del carrito y sus detalles
 */
async function loadCartItems() {
    if (cart.length === 0) {
        showEmptyCart();
        return;
    }

    // Mostrar la tabla del carrito
    document.getElementById('empty-cart-message')?.classList.add('d-none');
    document.getElementById('cart-items-table')?.classList.remove('d-none');

    const tbody = document.getElementById('cart-items-body');
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando productos...</td></tr>';

    try {
        // Cargar detalles de todos los productos
        await Promise.all(cart.map(item => loadProductDetails(item.id)));

        // Renderizar items
        renderCartItems();
    } catch (error) {
        console.error('Error al cargar productos:', error);
        showError('Error al cargar los productos del carrito');
    }
}

/**
 * Cargar detalles de un producto
 */
async function loadProductDetails(productId) {
    // Si ya tenemos los detalles, no volver a cargar
    if (productsDetails[productId]) {
        return productsDetails[productId];
    }

    try {
        const response = await fetch(`${API_BASE_URL}/productos/${productId}`);
        if (!response.ok) throw new Error('Producto no encontrado');
        
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Extraer información del producto
        const productData = {
            id: productId,
            name: doc.querySelector('h3')?.textContent.trim() || 'Producto',
            image: doc.querySelector('img')?.src || '',
            stock: parseInt(doc.querySelector('.badge-success, .badge-warning, .badge-danger')?.textContent) || 0,
            code: doc.querySelector('.badge-secondary')?.textContent.trim() || '',
            description: doc.querySelector('.card-text')?.textContent.trim() || ''
        };

        productsDetails[productId] = productData;
        return productData;
    } catch (error) {
        console.error(`Error al cargar producto ${productId}:`, error);
        return null;
    }
}

/**
 * Renderizar items del carrito
 */
function renderCartItems() {
    const tbody = document.getElementById('cart-items-body');
    if (!tbody) return;

    tbody.innerHTML = '';

    cart.forEach((item, index) => {
        const product = productsDetails[item.id];
        if (!product) return;

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <img src="${product.image || '/img/inventario/real.jpg'}" 
                     alt="${product.name}" 
                     class="img-thumbnail" 
                     style="max-width: 60px; max-height: 60px; object-fit: cover;"
                     onerror="this.src='/img/inventario/real.jpg'">
            </td>
            <td>
                <strong>${product.name}</strong>
                <br>
                <small class="text-muted">
                    <i class="fas fa-barcode"></i> ${product.code}
                </small>
            </td>
            <td class="text-center">
                <span class="badge badge-info">${product.stock} unidades</span>
            </td>
            <td class="text-center">
                <div class="input-group input-group-sm" style="max-width: 150px; margin: 0 auto;">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-secondary btn-decrease" 
                                data-index="${index}" 
                                type="button"
                                ${item.quantity <= 1 ? 'disabled' : ''}>
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                    <input type="number" 
                           class="form-control text-center quantity-input" 
                           data-index="${index}"
                           value="${item.quantity}" 
                           min="1" 
                           max="${product.stock}">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary btn-increase" 
                                data-index="${index}" 
                                type="button"
                                ${item.quantity >= product.stock ? 'disabled' : ''}>
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                ${item.quantity >= product.stock ? '<small class="text-warning d-block mt-1">Máximo alcanzado</small>' : ''}
            </td>
            <td class="text-center">
                <button class="btn btn-sm btn-danger btn-remove" 
                        data-index="${index}"
                        title="Eliminar del carrito">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(row);
    });

    // Reconfigurar event listeners
    setupQuantityControls();
}

/**
 * Configurar controles de cantidad
 */
function setupQuantityControls() {
    // Botones de disminuir
    document.querySelectorAll('.btn-decrease').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            decreaseQuantity(index);
        });
    });

    // Botones de aumentar
    document.querySelectorAll('.btn-increase').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            increaseQuantity(index);
        });
    });

    // Inputs de cantidad
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const index = parseInt(this.dataset.index);
            const newQuantity = parseInt(this.value);
            updateQuantity(index, newQuantity);
        });
    });

    // Botones de eliminar
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            const index = parseInt(this.dataset.index);
            removeItem(index);
        });
    });
}

/**
 * Configurar acciones del carrito
 */
function setupCartActions() {
    // Botón de vaciar carrito
    const emptyCartBtn = document.getElementById('btn-empty-cart');
    if (emptyCartBtn) {
        emptyCartBtn.addEventListener('click', confirmEmptyCart);
    }

    // Botón de confirmar orden
    const confirmBtn = document.getElementById('btn-confirm-order');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', confirmOrder);
    }

    // Botón de guardar borrador
    const saveDraftBtn = document.getElementById('btn-save-draft');
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', saveDraft);
    }

    // Botón de confirmación final
    const finalConfirmBtn = document.getElementById('btn-final-confirm');
    if (finalConfirmBtn) {
        finalConfirmBtn.addEventListener('click', submitOrder);
    }
}

/**
 * Disminuir cantidad de un item
 */
function decreaseQuantity(index) {
    if (cart[index].quantity > 1) {
        cart[index].quantity--;
        saveCart();
        renderCartItems();
        updateCartSummary();
    }
}

/**
 * Aumentar cantidad de un item
 */
function increaseQuantity(index) {
    const product = productsDetails[cart[index].id];
    if (cart[index].quantity < product.stock) {
        cart[index].quantity++;
        saveCart();
        renderCartItems();
        updateCartSummary();
    } else {
        showStockWarning(product.name, product.stock);
    }
}

/**
 * Actualizar cantidad de un item
 */
function updateQuantity(index, newQuantity) {
    const product = productsDetails[cart[index].id];
    
    if (newQuantity < 1) {
        newQuantity = 1;
    } else if (newQuantity > product.stock) {
        newQuantity = product.stock;
        showStockWarning(product.name, product.stock);
    }

    cart[index].quantity = newQuantity;
    saveCart();
    renderCartItems();
    updateCartSummary();
}

/**
 * Eliminar item del carrito
 */
function removeItem(index) {
    const product = productsDetails[cart[index].id];
    
    Swal.fire({
        title: '¿Eliminar producto?',
        html: `¿Estás seguro de eliminar <strong>"${product.name}"</strong> del carrito?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            cart.splice(index, 1);
            saveCart();
            
            if (cart.length === 0) {
                showEmptyCart();
            } else {
                renderCartItems();
            }
            
            updateCartSummary();
            
            Swal.fire({
                icon: 'success',
                title: 'Producto eliminado',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

/**
 * Confirmar vaciar todo el carrito
 */
function confirmEmptyCart() {
    if (cart.length === 0) return;

    Swal.fire({
        title: '¿Vaciar carrito?',
        text: 'Se eliminarán todos los productos del carrito',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, vaciar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            cart = [];
            saveCart();
            showEmptyCart();
            updateCartSummary();
            
            Swal.fire({
                icon: 'success',
                title: 'Carrito vaciado',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

/**
 * Guardar carrito en localStorage
 */
function saveCart() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
}

/**
 * Actualizar resumen del carrito
 */
function updateCartSummary() {
    const totalProducts = cart.length;
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);

    document.getElementById('total-products').textContent = totalProducts;
    document.getElementById('total-items').textContent = totalItems;

    // Habilitar/deshabilitar botón de confirmar
    const confirmBtn = document.getElementById('btn-confirm-order');
    if (confirmBtn) {
        confirmBtn.disabled = totalItems === 0;
    }
}

/**
 * Mostrar carrito vacío
 */
function showEmptyCart() {
    document.getElementById('empty-cart-message')?.classList.remove('d-none');
    document.getElementById('cart-items-table')?.classList.add('d-none');
}

/**
 * Confirmar orden (mostrar resumen)
 */
function confirmOrder() {
    if (cart.length === 0) return;

    // Generar resumen
    let summaryHTML = '<table class="table table-sm"><tbody>';
    
    cart.forEach(item => {
        const product = productsDetails[item.id];
        summaryHTML += `
            <tr>
                <td><strong>${product.name}</strong></td>
                <td class="text-right">${item.quantity} unidades</td>
            </tr>
        `;
    });
    
    summaryHTML += '</tbody></table>';
    
    const notes = document.getElementById('order-notes')?.value || '';
    if (notes) {
        summaryHTML += `
            <div class="mt-3">
                <strong>Notas:</strong>
                <p class="text-muted">${notes}</p>
            </div>
        `;
    }

    document.getElementById('order-summary-content').innerHTML = summaryHTML;
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('confirmOrderModal'));
    modal.show();
}

/**
 * Enviar orden al servidor
 */
async function submitOrder() {
    const notes = document.getElementById('order-notes')?.value || '';
    
    // Preparar datos de la orden
    const orderData = {
        items: cart.map(item => ({
            producto_id: item.id,
            cantidad: item.quantity
        })),
        notas: notes
    };

    try {
        // Mostrar loading
        Swal.fire({
            title: 'Procesando solicitud...',
            html: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const response = await fetch(`${API_BASE_URL}/carrito/agregar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(orderData)
        });

        const result = await response.json();

        if (response.ok) {
            // Limpiar carrito
            cart = [];
            saveCart();
            
            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('confirmOrderModal'))?.hide();
            
            // Mostrar éxito
            Swal.fire({
                icon: 'success',
                title: '¡Solicitud enviada!',
                text: 'Tu solicitud ha sido enviada correctamente',
                confirmButtonText: 'Ver catálogo'
            }).then(() => {
                window.location.href = '/inventario/productos/catalogo';
            });
        } else {
            throw new Error(result.message || 'Error al procesar la solicitud');
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Ocurrió un error al procesar tu solicitud'
        });
    }
}

/**
 * Guardar borrador
 */
function saveDraft() {
    const notes = document.getElementById('order-notes')?.value || '';
    
    const draft = {
        cart: cart,
        notes: notes,
        timestamp: new Date().toISOString()
    };

    localStorage.setItem(DRAFT_KEY, JSON.stringify(draft));

    Swal.fire({
        icon: 'success',
        title: 'Borrador guardado',
        text: 'Puedes continuar más tarde',
        timer: 2000,
        showConfirmButton: false
    });
}

/**
 * Mostrar advertencia de stock
 */
function showStockWarning(productName, maxStock) {
    const modal = new bootstrap.Modal(document.getElementById('stockWarningModal'));
    const content = document.getElementById('stock-warning-content');
    
    content.innerHTML = `
        <p>Has alcanzado la cantidad máxima disponible de:</p>
        <p class="text-center"><strong>${productName}</strong></p>
        <p class="text-muted text-center">Stock disponible: ${maxStock} unidades</p>
    `;
    
    modal.show();
}

/**
 * Mostrar error
 */
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message
    });
}

// Exportar funciones para uso externo
window.inventarioCarrito = {
    loadCartItems,
    updateCartSummary,
    confirmOrder,
    saveDraft
};