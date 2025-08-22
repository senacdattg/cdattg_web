// Import jQuery
const $ = require("jquery")

$(document).ready(() => {
  // Variables globales
  let currentPage = 1
  let totalPages = 1
  let searchTimeout

  // Datos de ejemplo (en producción vendrían del servidor)
  const sampleProducts = [
    {
      id: 1,
      codigo: "ELEC001",
      nombre: "Laptop Dell Inspiron 15",
      categoria: "Electrónica",
      stock_actual: 5,
      stock_minimo: 2,
      ubicacion: "Almacén A",
      estado: "disponible",
      descripcion: "Laptop para uso administrativo",
      precio: 2500000,
    },
    {
      id: 2,
      codigo: "OFIC002",
      nombre: "Silla Ergonómica",
      categoria: "Oficina",
      stock_actual: 1,
      stock_minimo: 3,
      ubicacion: "Almacén B",
      estado: "stock-bajo",
      descripcion: "Silla de oficina con soporte lumbar",
      precio: 450000,
    },
    {
      id: 3,
      codigo: "HERR003",
      nombre: "Taladro Inalámbrico",
      categoria: "Herramientas",
      stock_actual: 0,
      stock_minimo: 1,
      ubicacion: "Almacén A",
      estado: "agotado",
      descripcion: "Taladro para mantenimiento",
      precio: 180000,
    },
  ]

  // Inicializar la búsqueda
  initializeSearch()

  // Event listeners
  $("#search-input").on("input", () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(performSearch, 300)
  })

  $("#category-filter, #status-filter, #location-filter").on("change", performSearch)

  // Función para inicializar la búsqueda
  function initializeSearch() {
    performSearch()
  }

  // Función principal de búsqueda
  function performSearch() {
    showLoading()

    // Obtener valores de filtros
    const searchTerm = $("#search-input").val().toLowerCase()
    const categoryFilter = $("#category-filter").val()
    const statusFilter = $("#status-filter").val()
    const locationFilter = $("#location-filter").val()

    // Filtrar productos (simulación)
    const filteredProducts = sampleProducts.filter((product) => {
      const matchesSearch =
        !searchTerm ||
        product.nombre.toLowerCase().includes(searchTerm) ||
        product.codigo.toLowerCase().includes(searchTerm) ||
        product.descripcion.toLowerCase().includes(searchTerm)

      const matchesCategory = !categoryFilter || product.categoria.toLowerCase() === categoryFilter

      const matchesStatus = !statusFilter || product.estado === statusFilter

      const matchesLocation = !locationFilter || product.ubicacion.toLowerCase().replace(" ", "-") === locationFilter

      return matchesSearch && matchesCategory && matchesStatus && matchesLocation
    })

    // Simular delay de servidor
    setTimeout(() => {
      hideLoading()
      displayResults(filteredProducts)
      updatePagination(filteredProducts.length)
    }, 500)
  }

  // Mostrar loading
  function showLoading() {
    $("#products-tbody").html(`
            <tr>
                <td colspan="8" class="text-center">
                    <div class="loading-spinner">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2">Buscando productos...</p>
                    </div>
                </td>
            </tr>
        `)
  }

  // Ocultar loading
  function hideLoading() {
    $(".loading-spinner").hide()
  }

  // Mostrar resultados
  function displayResults(products) {
    const tbody = $("#products-tbody")

    if (products.length === 0) {
      tbody.html(`
                <tr>
                    <td colspan="8" class="no-results">
                        <i class="fas fa-search"></i>
                        <h5>No se encontraron productos</h5>
                        <p>Intenta ajustar los filtros de búsqueda</p>
                    </td>
                </tr>
            `)
      return
    }

    let html = ""
    products.forEach((product) => {
      const statusBadge = getStatusBadge(product.estado, product.stock_actual, product.stock_minimo)
      const stockAlert = getStockAlert(product.stock_actual, product.stock_minimo)

      html += `
                <tr>
                    <td><strong>${product.codigo}</strong></td>
                    <td>
                        ${product.nombre}
                        ${stockAlert}
                    </td>
                    <td>${product.categoria}</td>
                    <td>
                        <span class="badge ${getStockBadgeClass(product.stock_actual, product.stock_minimo)}">
                            ${product.stock_actual}
                        </span>
                    </td>
                    <td>${product.stock_minimo}</td>
                    <td>${product.ubicacion}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-view btn-action" onclick="viewProduct(${product.id})" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-edit btn-action" onclick="editProduct(${product.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
            `
    })

    tbody.html(html)
  }

  // Obtener badge de estado
  function getStatusBadge(estado, stockActual, stockMinimo) {
    let badgeClass = "badge-disponible"
    let text = "Disponible"

    if (stockActual === 0) {
      badgeClass = "badge-agotado"
      text = "Agotado"
    } else if (stockActual <= stockMinimo) {
      badgeClass = "badge-stock-bajo"
      text = "Stock Bajo"
    }

    return `<span class="badge ${badgeClass}">${text}</span>`
  }

  // Obtener alerta de stock
  function getStockAlert(stockActual, stockMinimo) {
    if (stockActual === 0) {
      return '<div class="stock-alert stock-critical mt-1"><i class="fas fa-exclamation-triangle"></i> Producto agotado</div>'
    } else if (stockActual <= stockMinimo) {
      return '<div class="stock-alert mt-1"><i class="fas fa-exclamation-circle"></i> Stock bajo</div>'
    }
    return ""
  }

  // Obtener clase de badge para stock
  function getStockBadgeClass(stockActual, stockMinimo) {
    if (stockActual === 0) return "badge-danger"
    if (stockActual <= stockMinimo) return "badge-warning"
    return "badge-success"
  }

  // Actualizar paginación
  function updatePagination(totalItems) {
    const itemsPerPage = 10
    totalPages = Math.ceil(totalItems / itemsPerPage)

    $("#products-info").text(`Mostrando ${Math.min(totalItems, itemsPerPage)} de ${totalItems} entradas`)

    let paginationHtml = ""
    if (totalPages > 1) {
      // Botón anterior
      paginationHtml += `<a class="paginate_button ${currentPage === 1 ? "disabled" : ""}" onclick="changePage(${currentPage - 1})">Anterior</a>`

      // Números de página
      for (let i = 1; i <= totalPages; i++) {
        paginationHtml += `<a class="paginate_button ${i === currentPage ? "current" : ""}" onclick="changePage(${i})">${i}</a>`
      }

      // Botón siguiente
      paginationHtml += `<a class="paginate_button ${currentPage === totalPages ? "disabled" : ""}" onclick="changePage(${currentPage + 1})">Siguiente</a>`
    }

    $("#products-pagination").html(paginationHtml)
  }

  // Cambiar página
  window.changePage = (page) => {
    if (page < 1 || page > totalPages || page === currentPage) return
    currentPage = page
    performSearch()
  }

  // Ver detalles del producto
  window.viewProduct = (productId) => {
    const product = sampleProducts.find((p) => p.id === productId)
    if (!product) return

    const detailsHtml = `
            <div class="row">
                <div class="col-md-6">
                    <div class="product-detail-item">
                        <div class="product-detail-label">Código:</div>
                        <div class="product-detail-value">${product.codigo}</div>
                    </div>
                    <div class="product-detail-item">
                        <div class="product-detail-label">Nombre:</div>
                        <div class="product-detail-value">${product.nombre}</div>
                    </div>
                    <div class="product-detail-item">
                        <div class="product-detail-label">Categoría:</div>
                        <div class="product-detail-value">${product.categoria}</div>
                    </div>
                    <div class="product-detail-item">
                        <div class="product-detail-label">Precio:</div>
                        <div class="product-detail-value">$${product.precio.toLocaleString()}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="product-detail-item">
                        <div class="product-detail-label">Stock Actual:</div>
                        <div class="product-detail-value">
                            <span class="badge ${getStockBadgeClass(product.stock_actual, product.stock_minimo)}">
                                ${product.stock_actual}
                            </span>
                        </div>
                    </div>
                    <div class="product-detail-item">
                        <div class="product-detail-label">Stock Mínimo:</div>
                        <div class="product-detail-value">${product.stock_minimo}</div>
                    </div>
                    <div class="product-detail-item">
                        <div class="product-detail-label">Ubicación:</div>
                        <div class="product-detail-value">${product.ubicacion}</div>
                    </div>
                    <div class="product-detail-item">
                        <div class="product-detail-label">Estado:</div>
                        <div class="product-detail-value">${getStatusBadge(product.estado, product.stock_actual, product.stock_minimo)}</div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="product-detail-item">
                        <div class="product-detail-label">Descripción:</div>
                        <div class="product-detail-value">${product.descripcion}</div>
                    </div>
                </div>
            </div>
        `

    $("#product-details-content").html(detailsHtml)
    $("#product-details-modal").modal("show")
  }

  // Editar producto
  window.editProduct = (productId) => {
    // Redirigir a la página de edición
    window.location.href = `/inventario/productos/editar/${productId}`
  }

  // buscar_producto.js

  document.addEventListener('DOMContentLoaded', function () {
    // Elementos de filtro
    const searchInput = document.getElementById('search-input');
    const categoryFilter = document.getElementById('category-filter');
    const statusFilter = document.getElementById('status-filter');
    const locationFilter = document.getElementById('location-filter');
    const productsTbody = document.getElementById('products-tbody');
    const productsInfo = document.getElementById('products-info');

    // Función para buscar productos
    function buscarProductos() {
        const params = {
            query: searchInput.value,
            categoria: categoryFilter.value,
            estado: statusFilter.value,
            ubicacion: locationFilter.value
        };

        fetch('/inventario/buscar-producto?' + new URLSearchParams(params), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            productsTbody.innerHTML = '';
            if (data.productos && data.productos.length > 0) {
                data.productos.forEach(producto => {
                    productsTbody.innerHTML += `
                        <tr>
                            <td>${producto.codigo_barras}</td>
                            <td>${producto.producto}</td>
                            <td>${producto.categoria}</td>
                            <td>${producto.cantidad}</td>
                            <td>${producto.stock_minimo || '-'}</td>
                            <td>${producto.ubicacion || '-'}</td>
                            <td>${producto.estado}</td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="verDetallesProducto(${producto.id})">Ver</button>
                            </td>
                        </tr>
                    `;
                });
                productsInfo.textContent = `Mostrando 1 a ${data.productos.length} de ${data.total} entradas`;
            } else {
                productsTbody.innerHTML = '<tr><td colspan="8">No se encontraron productos.</td></tr>';
                productsInfo.textContent = 'Mostrando 0 a 0 de 0 entradas';
            }
        })
        .catch(error => {
            productsTbody.innerHTML = '<tr><td colspan="8">Error al buscar productos.</td></tr>';
            productsInfo.textContent = '';
        });
    }

    // Eventos de filtro
    searchInput.addEventListener('input', buscarProductos);
    categoryFilter.addEventListener('change', buscarProductos);
    statusFilter.addEventListener('change', buscarProductos);
    locationFilter.addEventListener('change', buscarProductos);

    // Búsqueda inicial
    buscarProductos();
  });

  // Función para ver detalles del producto (puedes implementar el modal)
  window.verDetallesProducto = (id) => {
    // Aquí puedes hacer una petición AJAX para obtener los detalles y mostrarlos en el modal
    // Ejemplo:
    // fetch(`/inventario/productos/${id}`)
    //   .then(response => response.text())
    //   .then(html => {
    //     document.getElementById('product-details-content').innerHTML = html;
    //     $('#product-details-modal').modal('show');
    //   });
  }
})
