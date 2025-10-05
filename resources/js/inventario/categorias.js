// Función para confirmar eliminación con SweetAlert2
function confirmDeleteCategoria(id, nombre) {
    if(typeof Swal !== 'undefined') {
        Swal.fire({
            title: '¿Eliminar categoría?',
            text: `Se eliminará: "${nombre}"`,
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
                form.action = `/inventario/categorias/${id}`;
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
            form.action = `/inventario/categorias/${id}`;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                <input type="hidden" name="_method" value="DELETE">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
}

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

    // SweetAlert confirmaciones de borrado (para compatibilidad con código existente)
    document.querySelectorAll('form[action*="categorias"]').forEach(f => {
        if(f.querySelector('input[name="_method"][value="DELETE"]')){
            f.addEventListener('submit', async e => {
                e.preventDefault();
                const result = await Swal.fire({
                    title: '¿Eliminar categoría?',
                    text: "Esta acción no se puede deshacer",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                });
                if (result.isConfirmed) {
                    f.submit();
                }
            });
        }
    });

    // Alertas de éxito/error (si vienen de session)
    if(window.flashSuccess) {
        Swal.fire({
            title: '¡Éxito!',
            text: window.flashSuccess,
            icon: 'success',
            timer: 3000,
            showConfirmButton: false
        });
    }

    if(window.flashError) {
        Swal.fire({
            title: 'Error',
            text: window.flashError,
            icon: 'error'
        });
    }

    // Paginación client-side
    const table = document.querySelector('.categorias-table tbody');
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

    // Filtro rápido mejorado
    const filtro = document.getElementById('filtro-categorias');
    if(filtro){
        filtro.addEventListener('input', () => {
            const val = filtro.value.toLowerCase();
            const rows = document.querySelectorAll('.categorias-table tbody tr');
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

    // Función para ver categoría
    window.viewCategoria = function(id, nombre, productosCount, status, createdBy, updatedBy, createdAt, updatedAt) {
        document.getElementById('view_categoria_id').textContent = id || '-';
        document.getElementById('view_categoria_nombre').textContent = nombre || '-';
        document.getElementById('view_categoria_productos').textContent = (productosCount || 0) + ' productos';
        document.getElementById('view_categoria_estado').textContent = (status == 1) ? 'ACTIVO' : 'INACTIVO';
        document.getElementById('view_categoria_created_by').textContent = createdBy || '-';
        document.getElementById('view_categoria_updated_by').textContent = updatedBy || '-';
        document.getElementById('view_categoria_created_at').textContent = createdAt || '-';
        document.getElementById('view_categoria_updated_at').textContent = updatedAt || '-';
        
        // Calcular tiempo en sistema
        if (createdAt) {
            const fechaCreacion = new Date(createdAt.replace(/(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2})/, '$3-$2-$1T$4:$5:00'));
            const hoy = new Date();
            const diasEnSistema = Math.ceil((hoy - fechaCreacion) / (1000 * 60 * 60 * 24));
            document.getElementById('view_categoria_tiempo_sistema').textContent = diasEnSistema + ' días';
        } else {
            document.getElementById('view_categoria_tiempo_sistema').textContent = '-';
        }
        
        // Calcular última actividad
        if (updatedAt) {
            const fechaActualizacion = new Date(updatedAt.replace(/(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2})/, '$3-$2-$1T$4:$5:00'));
            const hoy = new Date();
            const diasUltimaActividad = Math.ceil((hoy - fechaActualizacion) / (1000 * 60 * 60 * 24));
            
            if (diasUltimaActividad === 0) {
                document.getElementById('view_categoria_ultima_actividad').textContent = 'Hoy';
            } else if (diasUltimaActividad === 1) {
                document.getElementById('view_categoria_ultima_actividad').textContent = 'Ayer';
            } else {
                document.getElementById('view_categoria_ultima_actividad').textContent = 'Hace ' + diasUltimaActividad + ' días';
            }
        } else {
            document.getElementById('view_categoria_ultima_actividad').textContent = '-';
        }
    };

    // Función para editar categoría
    window.editCategoria = function(id, nombre) {
        document.getElementById('edit_nombre').value = nombre;
        document.getElementById('editCategoriaForm').action = `/inventario/categorias/${id}`;
    };

    // Validación en tiempo real
    const createInput = document.getElementById('create_nombre');
    const editInput = document.getElementById('edit_nombre');

    [createInput, editInput].forEach(input => {
        if(input) {
            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
                if(this.value.length > 0) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            });
        }
    });
});
