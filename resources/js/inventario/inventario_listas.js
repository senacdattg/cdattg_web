
/**
 * Confirmar eliminación con SweetAlert2
 * @param {number} id - ID del elemento a eliminar
 * @param {string} nombre - Nombre del elemento
 * @param {string} tipo - Tipo de elemento (marca, categoría, proveedor, contrato)
 * @param {string} ruta - Ruta base para la eliminación
 */
window.confirmDelete = function(id, nombre, tipo, ruta) {
    if(typeof Swal !== 'undefined') {
        Swal.fire({
            title: `¿Eliminar ${tipo}?`,
            text: `Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<i class="fas fa-trash me-1"></i>Sí, eliminar',
            cancelButtonText: '<i class="fas fa-times me-1"></i>Cancelar',
            backdrop: true,
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/${ruta}/${id}`;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    } else {
        if(confirm(`¿Estás seguro de eliminar "${nombre}"?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/${ruta}/${id}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
};

/**
 * Mostrar mensajes flash con SweetAlert2
 */
window.showFlashMessages = function() {
    if(typeof Swal !== 'undefined') {
        if(window.flashSuccess) {
            Swal.fire({
                title: '¡Éxito!',
                text: window.flashSuccess,
                icon: 'success',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        if(window.flashError) {
            Swal.fire({
                title: 'Error',
                text: window.flashError,
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
        }

        if(window.flashWarning) {
            Swal.fire({
                title: 'Advertencia',
                text: window.flashWarning,
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
        }
    }
};

/**
 * Calcular días entre dos fechas
 * @param {string} fechaInicio - Fecha en formato dd/mm/yyyy hh:mm
 * @param {string} fechaFin - Fecha en formato dd/mm/yyyy hh:mm (opcional, por defecto hoy)
 * @returns {number} Días de diferencia
 */
window.calcularDiasEntreFechas = function(fechaInicio, fechaFin = null) {
    if (!fechaInicio) return 0;
    
    const inicio = new Date(fechaInicio.replace(/(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2})/, '$3-$2-$1T$4:$5:00'));
    const fin = fechaFin ? 
        new Date(fechaFin.replace(/(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2})/, '$3-$2-$1T$4:$5:00')) : 
        new Date();
    
    return Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24));
};

/**
 * Formatear días como texto legible
 * @param {number} dias - Número de días
 * @returns {string} Texto formateado
 */
window.formatearDiasComoTexto = function(dias) {
    if (dias === 0) {
        return 'Hoy';
    } else if (dias === 1) {
        return 'Ayer';
    } else if (dias === -1) {
        return 'Mañana';
    } else if (dias > 0) {
        return `Hace ${dias} días`;
    } else {
        return `En ${Math.abs(dias)} días`;
    }
};


/**
 * Función genérica para llenar campos de modal de visualización
 * @param {Object} datos - Objeto con los datos a mostrar
 * @param {string} prefijo - Prefijo para los IDs de los elementos (ej: 'view_marca_')
 */
window.llenarModalVisualizacion = function(datos, prefijo) {
    Object.keys(datos).forEach(key => {
        const elemento = document.getElementById(prefijo + key);
        if (elemento) {
            elemento.textContent = datos[key] || '-';
        }
    });
    
    // Cálculos especiales si existen fechas
    if (datos.created_at) {
        const diasEnSistema = window.calcularDiasEntreFechas(datos.created_at);
        const elementoTiempo = document.getElementById(prefijo + 'tiempo_sistema');
        if (elementoTiempo) {
            elementoTiempo.textContent = diasEnSistema + ' días';
        }
    }
    
    if (datos.updated_at) {
        const diasUltimaActividad = window.calcularDiasEntreFechas(datos.updated_at);
        const elementoActividad = document.getElementById(prefijo + 'ultima_actividad');
        if (elementoActividad) {
            elementoActividad.textContent = window.formatearDiasComoTexto(diasUltimaActividad);
        }
    }
};

/**
 * Función genérica para llenar campos de modal de edición
 * @param {Object} datos - Objeto con los datos a editar
 * @param {string} prefijo - Prefijo para los IDs de los elementos (ej: 'edit_')
 * @param {string} formId - ID del formulario
 * @param {string} ruta - Ruta base para la acción del formulario
 */
window.llenarModalEdicion = function(datos, prefijo, formId, ruta) {
    Object.keys(datos).forEach(key => {
        const elemento = document.getElementById(prefijo + key);
        if (elemento) {
            if (elemento.type === 'checkbox') {
                elemento.checked = datos[key];
            } else {
                elemento.value = datos[key] || '';
            }
        }
    });
    
    // Actualizar la action del formulario
    const form = document.getElementById(formId);
    if (form && datos.id) {
        form.action = `/${ruta}/${datos.id}`;
    }
};

/**
 * Aplicar validación en tiempo real a campos de entrada
 * @param {Array} inputIds - Array de IDs de inputs a validar
 */
window.aplicarValidacionTiempoReal = function(inputIds) {
    inputIds.forEach(inputId => {
        const input = document.getElementById(inputId);
        if(input) {
            input.addEventListener('input', function() {
                if(this.value.length > 0) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                }
            });
            
            input.addEventListener('blur', function() {
                if(this.hasAttribute('required') && this.value.length === 0) {
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                }
            });
        }
    });
};


document.addEventListener('DOMContentLoaded', function() {
    // Mostrar mensajes flash automáticamente
    window.showFlashMessages();
    
    // Aplicar validación a campos comunes
    const camposComunes = [
        'create_nombre', 'edit_nombre',
        'create_proveedor', 'edit_proveedor', 
        'create_nit', 'edit_nit',
        'create_email', 'edit_email'
    ];
    
    window.aplicarValidacionTiempoReal(camposComunes);
    
    // Configurar tooltips si Bootstrap está disponible
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Inicializar configuradores por módulo (para funciones view*/edit* de los modales)
    try {
        if (document.querySelector('.marcas-table') && typeof window.configurarMarcas === 'function') {
            window.configurarMarcas();
        }
        if (document.querySelector('.categorias-table') && typeof window.configurarCategorias === 'function') {
            window.configurarCategorias();
        }
        if (document.querySelector('.proveedores-table') && typeof window.configurarProveedores === 'function') {
            window.configurarProveedores();
        }
        if (document.querySelector('.contratos-table') && typeof window.configurarContratos === 'function') {
            window.configurarContratos();
        }
    } catch (e) {
        console.error('Error inicializando configuradores de inventario:', e);
    }
});

/**
 * Configurar funciones específicas para marcas
 */
window.configurarMarcas = function() {
    window.viewMarca = function(id, nombre, productosCount, status, createdBy, updatedBy, createdAt, updatedAt) {
        const datos = {
            id: id,
            nombre: nombre,
            productos: (productosCount || 0) + ' productos',
            estado: (status == 1) ? 'ACTIVO' : 'INACTIVO',
            created_by: createdBy,
            updated_by: updatedBy,
            created_at: createdAt,
            updated_at: updatedAt
        };
        
        window.llenarModalVisualizacion(datos, 'view_marca_');
    };
    
    window.editMarca = function(id, nombre) {
        window.llenarModalEdicion({id: id, nombre: nombre}, 'edit_', 'editMarcaForm', 'inventario/marcas');
    };
};

/**
 * Configurar funciones específicas para categorías
 */
window.configurarCategorias = function() {
    window.viewCategoria = function(id, nombre, productosCount, status, createdBy, updatedBy, createdAt, updatedAt) {
        const datos = {
            id: id,
            nombre: nombre,
            productos: (productosCount || 0) + ' productos',
            estado: (status == 1) ? 'ACTIVO' : 'INACTIVO',
            created_by: createdBy,
            updated_by: updatedBy,
            created_at: createdAt,
            updated_at: updatedAt
        };
        
        window.llenarModalVisualizacion(datos, 'view_categoria_');
    };
    
    window.editCategoria = function(id, nombre) {
        window.llenarModalEdicion({id: id, nombre: nombre}, 'edit_', 'editCategoriaForm', 'inventario/categorias');
    };
};

/**
 * Configurar funciones específicas para proveedores
 */
window.configurarProveedores = function() {
    window.viewProveedor = function(id, nombre, nit, email, contratosCount, status, createdBy, updatedBy, createdAt, updatedAt) {
        const datos = {
            id: id,
            nombre: nombre,
            nit: nit,
            email: email,
            contratos: (contratosCount || 0) + ' contratos/convenios',
            estado: (status == 1) ? 'ACTIVO' : 'INACTIVO',
            created_by: createdBy,
            updated_by: updatedBy,
            created_at: createdAt,
            updated_at: updatedAt
        };
        
        window.llenarModalVisualizacion(datos, 'view_proveedor_');
    };
    
    window.editProveedor = function(id, nombre, nit, email) {
        window.llenarModalEdicion(
            {id: id, proveedor: nombre, nit: nit, email: email}, 
            'edit_', 
            'editProveedorForm', 
            'inventario/proveedores'
        );
    };
};

/**
 * Configurar funciones específicas para contratos/convenios
 */
window.configurarContratos = function() {
    window.viewContrato = function(id, name, codigo, fechaInicio, fechaFin, proveedor, estado, createdBy, updatedBy, createdAt, updatedAt) {
        const datos = {
            name: name,
            codigo: codigo,
            proveedor: proveedor,
            estado: estado,
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            created_by: createdBy,
            updated_by: updatedBy,
            created_at: createdAt,
            updated_at: updatedAt
        };
        
        window.llenarModalVisualizacion(datos, 'view_');
        
        // Cálculos específicos para contratos
        if (fechaInicio && fechaFin) {
            const inicio = new Date(fechaInicio);
            const fin = new Date(fechaFin);
            const hoy = new Date();
            
            // Calcular vigencia total
            const vigenciaDias = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24));
            document.getElementById('view_vigencia').textContent = vigenciaDias + ' días';
            
            // Calcular días restantes
            const diasRestantes = Math.ceil((fin - hoy) / (1000 * 60 * 60 * 24));
            const diasRestantesElement = document.getElementById('view_dias_restantes');
            
            if (diasRestantes > 0) {
                diasRestantesElement.textContent = diasRestantes + ' días';
                diasRestantesElement.className = 'form-control-plaintext text-success';
            } else if (diasRestantes === 0) {
                diasRestantesElement.textContent = 'Vence hoy';
                diasRestantesElement.className = 'form-control-plaintext text-warning';
            } else {
                diasRestantesElement.textContent = 'Vencido (' + Math.abs(diasRestantes) + ' días)';
                diasRestantesElement.className = 'form-control-plaintext text-danger';
            }
        }
    };
    
    window.editContrato = function(id, name, codigo, fechaInicio, fechaFin, proveedorId, estadoId) {
        window.llenarModalEdicion(
            {
                id: id, 
                name: name, 
                codigo: codigo, 
                fecha_inicio: fechaInicio, 
                fecha_fin: fechaFin,
                proveedor_id: (proveedorId && proveedorId !== 'null') ? proveedorId : '',
                estado_id: (estadoId && estadoId !== 'null') ? estadoId : ''
            }, 
            'edit_', 
            'editContratoForm', 
            'inventario/contratos-convenios'
        );
    };
};

/**
 * Funciones globales para confirmar eliminación
 */
window.confirmDeleteMarca = function(id, nombre) {
    window.confirmDelete(id, nombre, 'marca', 'inventario/marcas');
};

window.confirmDeleteCategoria = function(id, nombre) {
    window.confirmDelete(id, nombre, 'categoría', 'inventario/categorias');
};

window.confirmDeleteProveedor = function(id, nombre) {
    window.confirmDelete(id, nombre, 'proveedor', 'inventario/proveedores');
};

window.confirmDeleteContrato = function(id, nombre) {
    window.confirmDelete(id, nombre, 'contrato/convenio', 'inventario/contratos-convenios');
};

/**
 * Función de búsqueda simple en tabla
 */
window.setupSimpleSearch = function(inputId, tableSelector, counterSelector) {
    const input = document.getElementById(inputId);
    const table = document.querySelector(tableSelector);
    const counter = document.querySelector(counterSelector);
    
    if (!input || !table) return;
    
    const tbody = table.querySelector('tbody');
    const rows = tbody.querySelectorAll('tr');
    const totalRows = rows.length;
    
    // Función para actualizar contador
    function updateCounter(visible, total) {
        if (counter) {
            if (visible === total) {
                counter.textContent = `${total} registros`;
            } else {
                counter.textContent = `${visible} de ${total} registros`;
            }
        }
    }
    
    // Inicializar contador
    updateCounter(totalRows, totalRows);
    
    // Función de filtrado
    input.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleCount = 0;
        
        rows.forEach(row => {
            // Saltar filas vacías (empty state)
            if (row.querySelector('.text-muted') && row.cells.length === 1) {
                row.style.display = 'none';
                return;
            }
            
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        updateCounter(visibleCount, totalRows);
        
        // Mostrar mensaje si no hay resultados
        const emptyRow = tbody.querySelector('.no-results-row');
        if (visibleCount === 0 && searchTerm !== '') {
            if (!emptyRow) {
                const newRow = document.createElement('tr');
                newRow.className = 'no-results-row';
                newRow.innerHTML = `
                    <td colspan="100%" class="text-center text-muted py-4">
                        <i class="fas fa-search fa-2x mb-2 d-block"></i>
                        No se encontraron resultados para "${searchTerm}"
                    </td>
                `;
                tbody.appendChild(newRow);
            }
        } else if (emptyRow) {
            emptyRow.remove();
        }
        
        // Actualizar paginación después de filtrar
        if (typeof window.updatePaginationAfterSearch === 'function') {
            window.updatePaginationAfterSearch();
        }
    });
};

// Inicializar búsquedas cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Configurar búsquedas para cada módulo
    if (document.getElementById('filtro-marcas')) {
        window.setupSimpleSearch('filtro-marcas', '.marcas-table', '#filter-counter');
    }
    
    if (document.getElementById('filtro-categorias')) {
        window.setupSimpleSearch('filtro-categorias', '.categorias-table', '#filter-counter');
    }
    
    if (document.getElementById('filtro-proveedores')) {
        window.setupSimpleSearch('filtro-proveedores', '.proveedores-table', '#filter-counter');
    }
    
    if (document.getElementById('filtro-contratos')) {
        window.setupSimpleSearch('filtro-contratos', '.contratos-table', '#filter-counter');
    }
});