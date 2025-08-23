// Import jQuery
const $ = require("jquery")

$(document).ready(() => {
  // Variables globales
  const currentPage = 1
  const totalPages = 1
  let searchTimeout

  // Datos de ejemplo
  const sampleRequests = [
    {
      id: 1,
      tipo: "prestamo",
      producto: "Laptop Dell Inspiron 15",
      producto_id: 1,
      cantidad: 1,
      solicitante: "Juan Pérez",
      departamento: "Sistemas",
      fecha_solicitud: "2025-01-15",
      fecha_devolucion: "2025-01-22",
      estado: "aprobada",
      proposito: "Trabajo remoto por una semana",
      aprobado_por: "Ana García",
      fecha_aprobacion: "2025-01-16",
    },
    {
      id: 2,
      tipo: "salida",
      producto: "Silla Ergonómica",
      producto_id: 2,
      cantidad: 2,
      solicitante: "María González",
      departamento: "Administración",
      fecha_solicitud: "2025-01-14",
      fecha_devolucion: null,
      estado: "pendiente",
      proposito: "Equipamiento de nueva oficina",
      aprobado_por: null,
      fecha_aprobacion: null,
    },
    {
      id: 3,
      tipo: "prestamo",
      producto: "Taladro Inalámbrico",
      producto_id: 3,
      cantidad: 1,
      solicitante: "Carlos Rodríguez",
      departamento: "Mantenimiento",
      fecha_solicitud: "2025-01-10",
      fecha_devolucion: "2025-01-12",
      estado: "devuelta",
      proposito: "Reparación de oficinas",
      aprobado_por: "Ana García",
      fecha_aprobacion: "2025-01-10",
    },
  ]

  const sampleProducts = [
    { id: 1, nombre: "Laptop Dell Inspiron 15", stock: 5 },
    { id: 2, nombre: "Silla Ergonómica", stock: 1 },
    { id: 3, nombre: "Taladro Inalámbrico", stock: 0 },
    { id: 4, nombre: "Proyector Epson", stock: 3 },
  ]

  // Inicializar
  initializeView()

  // Event listeners
  $("#search-request").on("input", () => {
    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(performSearch, 300)
  })

  $("#type-filter, #status-filter, #date-from, #date-to").on("change", performSearch)

  // Formulario de nueva solicitud
  $("#new-request-form").on("submit", handleNewRequest)
  $("#request-type").on("change", handleTypeChange)
  $("#request-product").on("change", handleProductChange)

  // Inicializar vista
  function initializeView() {
    loadProducts()
    performSearch()
    setMinDate()
  }

  // Cargar productos en el select
  function loadProducts() {
    const select = $("#request-product")
    select.empty().append('<option value="">Seleccionar producto</option>')

    sampleProducts.forEach((product) => {
      select.append(`<option value="${product.id}" data-stock="${product.stock}">${product.nombre}</option>`)
    })
  }

  // Establecer fecha mínima
  function setMinDate() {
    const today = new Date().toISOString().split("T")[0]
    $("#return-date").attr("min", today)
    $("#date-from, #date-to").attr("max", today)
  }

  // Manejar cambio de tipo
  function handleTypeChange() {
    const type = $("#request-type").val()
    const returnDateGroup = $("#return-date-group")
    const returnDateInput = $("#return-date")

    if (type === "prestamo") {
      returnDateGroup.show()
      returnDateInput.prop("required", true)
    } else {
      returnDateGroup.hide()
      returnDateInput.prop("required", false).val("")
    }
  }

  // Manejar cambio de producto
  function handleProductChange() {
    const selectedOption = $("#request-product option:selected")
    const stock = selectedOption.data("stock") || 0
    const stockSpan = $("#available-stock")

    stockSpan.text(stock)

    // Actualizar clase según stock
    stockSpan.removeClass("stock-warning stock-danger")
    if (stock === 0) {
      stockSpan.addClass("stock-danger")
    } else if (stock <= 2) {
      stockSpan.addClass("stock-warning")
    }

    // Actualizar cantidad máxima
    $("#request-quantity").attr("max", stock)
  }

  // Realizar búsqueda
  function performSearch() {
    showLoading()

    const searchTerm = $("#search-request").val().toLowerCase()
    const typeFilter = $("#type-filter").val()
    const statusFilter = $("#status-filter").val()
    const dateFrom = $("#date-from").val()
    const dateTo = $("#date-to").val()

    const filteredRequests = sampleRequests.filter((request) => {
      const matchesSearch =
        !searchTerm ||
        request.id.toString().includes(searchTerm) ||
        request.producto.toLowerCase().includes(searchTerm) ||
        request.solicitante.toLowerCase().includes(searchTerm)

      const matchesType = !typeFilter || request.tipo === typeFilter
      const matchesStatus = !statusFilter || request.estado === statusFilter

      const matchesDateFrom = !dateFrom || request.fecha_solicitud >= dateFrom
      const matchesDateTo = !dateTo || request.fecha_solicitud <= dateTo

      return matchesSearch && matchesType && matchesStatus && matchesDateFrom && matchesDateTo
    })

    setTimeout(() => {
      hideLoading()
      displayResults(filteredRequests)
    }, 500)
  }

  // Mostrar loading
  function showLoading() {
    $("#requests-tbody").html(`
            <tr>
                <td colspan="9" class="text-center">
                    <div class="loading-spinner">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando solicitudes...</p>
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
  function displayResults(requests) {
    const tbody = $("#requests-tbody")

    if (requests.length === 0) {
      tbody.html(`
                <tr>
                    <td colspan="9" class="no-results">
                        <i class="fas fa-search"></i>
                        <h5>No se encontraron solicitudes</h5>
                        <p>Intenta ajustar los filtros de búsqueda</p>
                    </td>
                </tr>
            `)
      return
    }

    let html = ""
    requests.forEach((request) => {
      const typeBadge = getTypeBadge(request.tipo)
      const statusBadge = getStatusBadge(request.estado)
      const expirationAlert = getExpirationAlert(request)
      const actions = getActionButtons(request)

      html += `
                <tr>
                    <td><strong>#${request.id}</strong></td>
                    <td>${typeBadge}</td>
                    <td>
                        ${request.producto}
                        ${expirationAlert}
                    </td>
                    <td><span class="badge badge-info">${request.cantidad}</span></td>
                    <td>
                        ${request.solicitante}
                        <br><small class="text-muted">${request.departamento || "N/A"}</small>
                    </td>
                    <td>${formatDate(request.fecha_solicitud)}</td>
                    <td>${request.fecha_devolucion ? formatDate(request.fecha_devolucion) : "N/A"}</td>
                    <td>${statusBadge}</td>
                    <td>${actions}</td>
                </tr>
            `
    })

    tbody.html(html)
  }

  // Obtener badge de tipo
  function getTypeBadge(tipo) {
    const badges = {
      prestamo: '<span class="badge badge-prestamo">Préstamo</span>',
      salida: '<span class="badge badge-salida">Salida</span>',
    }
    return badges[tipo] || '<span class="badge badge-secondary">N/A</span>'
  }

  // Obtener badge de estado
  function getStatusBadge(estado) {
    const badges = {
      pendiente: '<span class="badge badge-pendiente">Pendiente</span>',
      aprobada: '<span class="badge badge-aprobada">Aprobada</span>',
      rechazada: '<span class="badge badge-rechazada">Rechazada</span>',
      entregada: '<span class="badge badge-entregada">Entregada</span>',
      devuelta: '<span class="badge badge-devuelta">Devuelta</span>',
    }
    return badges[estado] || '<span class="badge badge-secondary">N/A</span>'
  }

  // Obtener alerta de vencimiento
  function getExpirationAlert(request) {
    if (request.tipo !== "prestamo" || !request.fecha_devolucion || request.estado === "devuelta") {
      return ""
    }

    const today = new Date()
    const returnDate = new Date(request.fecha_devolucion)
    const diffTime = returnDate - today
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

    if (diffDays < 0) {
      return '<div class="alert-vencimiento alert-vencido mt-1"><i class="fas fa-exclamation-triangle"></i> Vencido</div>'
    } else if (diffDays <= 2) {
      return '<div class="alert-vencimiento mt-1"><i class="fas fa-clock"></i> Vence pronto</div>'
    }

    return ""
  }

  // Obtener botones de acción
  function getActionButtons(request) {
    let buttons = `<button class="btn btn-view btn-action" onclick="viewRequest(${request.id})" title="Ver detalles">
            <i class="fas fa-eye"></i>
        </button>`

    if (request.estado === "pendiente") {
      buttons += `
                <button class="btn btn-approve btn-action" onclick="approveRequest(${request.id})" title="Aprobar">
                    <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-reject btn-action" onclick="rejectRequest(${request.id})" title="Rechazar">
                    <i class="fas fa-times"></i>
                </button>
            `
    } else if (request.estado === "aprobada" && request.tipo === "prestamo") {
      buttons += `
                <button class="btn btn-deliver btn-action" onclick="deliverRequest(${request.id})" title="Marcar como entregada">
                    <i class="fas fa-truck"></i>
                </button>
            `
    }

    return buttons
  }

  // Formatear fecha
  function formatDate(dateString) {
    const date = new Date(dateString)
    return date.toLocaleDateString("es-ES")
  }

  // Manejar nueva solicitud
  function handleNewRequest(e) {
    e.preventDefault()

    // Validar formulario
    if (!validateForm()) {
      return
    }

    // Simular envío
    const submitBtn = $(this).find('button[type="submit"]')
    const originalText = submitBtn.html()

    submitBtn.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Creando...')

    setTimeout(() => {
      // Simular éxito
      $("#new-request-modal").modal("hide")
      $("#new-request-form")[0].reset()
      $("#return-date-group").hide()

      // Mostrar mensaje de éxito
      showAlert("success", "Solicitud creada exitosamente")

      // Recargar datos
      performSearch()

      submitBtn.prop("disabled", false).html(originalText)
    }, 1500)
  }

  // Validar formulario
  function validateForm() {
    let isValid = true

    // Limpiar errores previos
    $(".form-control").removeClass("is-invalid")
    $(".invalid-feedback").remove()

    // Validar campos requeridos
    $("#new-request-form [required]").each(function () {
      if (!$(this).val()) {
        $(this).addClass("is-invalid")
        $(this).after('<div class="invalid-feedback">Este campo es requerido</div>')
        isValid = false
      }
    })

    // Validar cantidad vs stock
    const productId = $("#request-product").val()
    const quantity = Number.parseInt($("#request-quantity").val())
    const availableStock = Number.parseInt($("#available-stock").text())

    if (productId && quantity > availableStock) {
      $("#request-quantity").addClass("is-invalid")
      $("#request-quantity").after(
        '<div class="invalid-feedback">La cantidad no puede ser mayor al stock disponible</div>',
      )
      isValid = false
    }

    return isValid
  }

  // Mostrar alerta
  function showAlert(type, message) {
    const alertClass = type === "success" ? "alert-success" : "alert-danger"
    const alert = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `

    $(".content-header .container-fluid").prepend(alert)

    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
      $(".alert").alert("close")
    }, 5000)
  }

  // Funciones globales para botones
  window.viewRequest = (requestId) => {
    const request = sampleRequests.find((r) => r.id === requestId)
    if (!request) return

    const detailsHtml = `
            <div class="row">
                <div class="col-md-6">
                    <div class="request-detail-item">
                        <div class="request-detail-label">ID de Solicitud:</div>
                        <div class="request-detail-value">#${request.id}</div>
                    </div>
                    <div class="request-detail-item">
                        <div class="request-detail-label">Tipo:</div>
                        <div class="request-detail-value">${getTypeBadge(request.tipo)}</div>
                    </div>
                    <div class="request-detail-item">
                        <div class="request-detail-label">Producto:</div>
                        <div class="request-detail-value">${request.producto}</div>
                    </div>
                    <div class="request-detail-item">
                        <div class="request-detail-label">Cantidad:</div>
                        <div class="request-detail-value">${request.cantidad}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="request-detail-item">
                        <div class="request-detail-label">Solicitante:</div>
                        <div class="request-detail-value">${request.solicitante}</div>
                    </div>
                    <div class="request-detail-item">
                        <div class="request-detail-label">Departamento:</div>
                        <div class="request-detail-value">${request.departamento || "N/A"}</div>
                    </div>
                    <div class="request-detail-item">
                        <div class="request-detail-label">Estado:</div>
                        <div class="request-detail-value">${getStatusBadge(request.estado)}</div>
                    </div>
                    <div class="request-detail-item">
                        <div class="request-detail-label">Fecha de Solicitud:</div>
                        <div class="request-detail-value">${formatDate(request.fecha_solicitud)}</div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="request-detail-item">
                        <div class="request-detail-label">Propósito:</div>
                        <div class="request-detail-value">${request.proposito}</div>
                    </div>
                </div>
            </div>
        `

    $("#request-details-content").html(detailsHtml)
    $("#request-details-modal").modal("show")
  }

  window.approveRequest = (requestId) => {
    if (confirm("¿Está seguro de que desea aprobar esta solicitud?")) {
      // Simular aprobación
      showAlert("success", "Solicitud aprobada exitosamente")
      performSearch()
    }
  }

  window.rejectRequest = (requestId) => {
    const reason = prompt("Ingrese el motivo del rechazo:")
    if (reason) {
      // Simular rechazo
      showAlert("success", "Solicitud rechazada exitosamente")
      performSearch()
    }
  }

  window.deliverRequest = (requestId) => {
    if (confirm("¿Confirma que el producto ha sido entregado?")) {
      // Simular entrega
      showAlert("success", "Producto marcado como entregado")
      performSearch()
    }
  }
})
