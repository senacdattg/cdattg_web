// Función para confirmar eliminación con SweetAlert2
window.confirmDelete = function(id, nombre) {
    if(typeof Swal !== 'undefined') {
        Swal.fire({
            title: '¿Eliminar contrato/convenio?',
            text: `Esta accion no se puede deshacer`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            backdrop: true,
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/inventario/contratos-convenios/${id}`;
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
            form.action = `/inventario/contratos-convenios/${id}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
}

// Función para ver contrato
window.viewContrato = function(id, name, codigo, fechaInicio, fechaFin, proveedor, estado, createdBy, updatedBy, createdAt, updatedAt) {
    document.getElementById('view_name').textContent = name || '-';
    document.getElementById('view_codigo').textContent = codigo || '-';
    document.getElementById('view_proveedor').textContent = proveedor || '-';
    document.getElementById('view_estado').textContent = estado || '-';
    document.getElementById('view_fecha_inicio').textContent = fechaInicio || '-';
    document.getElementById('view_fecha_fin').textContent = fechaFin || '-';
    document.getElementById('view_created_by').textContent = createdBy || '-';
    document.getElementById('view_updated_by').textContent = updatedBy || '-';
    document.getElementById('view_created_at').textContent = createdAt || '-';
    document.getElementById('view_updated_at').textContent = updatedAt || '-';
    
    // Calcular vigencia y días restantes
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
    } else {
        document.getElementById('view_vigencia').textContent = '-';
        document.getElementById('view_dias_restantes').textContent = '-';
    }
};

// Función para editar contrato
window.editContrato = function(id, name, codigo, fechaInicio, fechaFin, proveedorId, estadoId) {
    // Debug: Verificar que se reciben los datos
    console.log('Datos recibidos:', {id, name, codigo, fechaInicio, fechaFin, proveedorId, estadoId});
    
    // Poblar los campos del modal
    const nameField = document.getElementById('edit_name');
    const codigoField = document.getElementById('edit_codigo');
    const fechaInicioField = document.getElementById('edit_fecha_inicio');
    const fechaFinField = document.getElementById('edit_fecha_fin');
    const proveedorField = document.getElementById('edit_proveedor_id');
    const estadoField = document.getElementById('edit_estado_id');
    
    if (nameField) nameField.value = name || '';
    if (codigoField) codigoField.value = codigo || '';
    if (fechaInicioField) fechaInicioField.value = fechaInicio || '';
    if (fechaFinField) fechaFinField.value = fechaFin || '';
    if (proveedorField) proveedorField.value = (proveedorId && proveedorId !== 'null') ? proveedorId : '';
    if (estadoField) estadoField.value = (estadoId && estadoId !== 'null') ? estadoId : '';
    
    // Actualizar la action del formulario
    const form = document.getElementById('editContratoForm');
    if (form) {
        form.action = `/inventario/contratos-convenios/${id}`;
    }
};

document.addEventListener('DOMContentLoaded', () => {
    // Mostrar mensajes flash con SweetAlert2
    if(typeof Swal !== 'undefined') {
        if(window.flashSuccess) {
            Swal.fire({
                title: '¡Éxito!',
                text: window.flashSuccess,
                icon: 'success',
                confirmButtonText: 'OK',
                timer: 3000,
                timerProgressBar: true
            });
        }
        if(window.flashError) {
            Swal.fire({
                title: 'Error',
                text: window.flashError,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }

    // Paginación client-side (igual que categorías)
    const table = document.querySelector('.contratos-table tbody');
    if(table) {
        const rowsPerPage = 9;
        const rows = Array.from(table.querySelectorAll('tr'));
        let currentPage = 1;
        const totalPages = Math.ceil(rows.length / rowsPerPage);

        function showPage(page) {
            rows.forEach((row, index) => {
                const start = (page - 1) * rowsPerPage;
                const end = start + rowsPerPage;
                row.style.display = (index >= start && index < end) ? '' : 'none';
            });

            // Actualizar info paginación
            const info = document.getElementById('pagination-info');
            if(info) {
                const showing = Math.min(rows.length, page * rowsPerPage);
                const from = rows.length > 0 ? ((page - 1) * rowsPerPage) + 1 : 0;
                info.textContent = `Mostrando ${from} a ${showing} de ${rows.length} registros`;
            }

            // Actualizar botones
            updatePaginationButtons();
        }

        function updatePaginationButtons() {
            const prevBtn = document.getElementById('prev-page');
            const nextBtn = document.getElementById('next-page');
            const pageNumbers = document.getElementById('page-numbers');

            if(prevBtn) prevBtn.disabled = currentPage === 1;
            if(nextBtn) nextBtn.disabled = currentPage === totalPages;

            if(pageNumbers) {
                pageNumbers.innerHTML = '';
                for(let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
                    const btn = document.createElement('button');
                    btn.className = `btn btn-sm ${i === currentPage ? 'btn-primary' : 'btn-outline-primary'}`;
                    btn.textContent = i;
                    btn.onclick = () => {
                        currentPage = i;
                        showPage(currentPage);
                    };
                    pageNumbers.appendChild(btn);
                }
            }
        }

        // Eventos paginación
        document.getElementById('prev-page')?.addEventListener('click', () => {
            if(currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        });

        document.getElementById('next-page')?.addEventListener('click', () => {
            if(currentPage < totalPages) {
                currentPage++;
                showPage(currentPage);
            }
        });

        // Mostrar primera página
        if(rows.length > 0) showPage(1);
    }

    // Filtro rápido mejorado (igual que categorías)
    const filtro = document.getElementById('filtro-contratos');
    if(filtro){
        filtro.addEventListener('input', () => {
            const val = filtro.value.toLowerCase();
            const rows = document.querySelectorAll('.contratos-table tbody tr');
            let visibleCount = 0;

            rows.forEach(tr => {
                const texto = tr.innerText.toLowerCase();
                const visible = texto.includes(val);
                tr.style.display = visible ? '' : 'none';
                if(visible) visibleCount++;
            });

            // Actualizar contador de filtrados
            const counter = document.getElementById('filter-counter');
            if(counter) {
                counter.textContent = val ? `${visibleCount} coincidencias` : '';
            }
        });
    }
});