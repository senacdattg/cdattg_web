// paginacion-simple.js
// Paginación frontend para tablas de inventario

window.setupSimplePagination = function(tableSelector, paginationContainerSelector, rowsPerPage = 9) {
    const table = document.querySelector(tableSelector);
    const container = document.querySelector(paginationContainerSelector);
    if (!table || !container) return;

    const tbody = table.querySelector('tbody');
    let rows = Array.from(tbody.querySelectorAll('tr'));
    // Excluir filas de "sin registros" o "no results"
    rows = rows.filter(row => !row.classList.contains('no-results-row'));
    const totalRows = rows.length;
    let currentPage = 1;
    const totalPages = Math.ceil(totalRows / rowsPerPage);

    function renderPage(page) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        rows.forEach((row, i) => {
            row.style.display = (i >= start && i < end) ? '' : 'none';
        });
        renderPagination(page);
    }

    function renderPagination(page) {
        let html = '';
        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }
        html += `<nav><ul class="pagination justify-content-center">`;
        html += `<li class="page-item${page === 1 ? ' disabled' : ''}"><a class="page-link" href="#" data-page="${page-1}">Anterior</a></li>`;
        for (let i = 1; i <= totalPages; i++) {
            html += `<li class="page-item${i === page ? ' active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }
        html += `<li class="page-item${page === totalPages ? ' disabled' : ''}"><a class="page-link" href="#" data-page="${page+1}">Siguiente</a></li>`;
        html += `</ul></nav>`;
        container.innerHTML = html;
        // Eventos
        container.querySelectorAll('a.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const p = parseInt(this.getAttribute('data-page'));
                if (p >= 1 && p <= totalPages) {
                    currentPage = p;
                    renderPage(currentPage);
                }
            });
        });
    }

    renderPage(currentPage);

    // Función para actualizar paginación después de filtrado
    window.updatePaginationAfterSearch = function() {
        // Recalcular filas visibles
        rows = Array.from(tbody.querySelectorAll('tr')).filter(row => 
            !row.classList.contains('no-results-row') && 
            row.style.display !== 'none'
        );
        const newTotalRows = rows.length;
        const newTotalPages = Math.ceil(newTotalRows / rowsPerPage);
        
        if (newTotalPages <= 1) {
            container.innerHTML = '';
            return;
        }
        
        currentPage = 1;
        renderPage(currentPage);
    };
    
    return { updatePagination: window.updatePaginationAfterSearch };
};

document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.marcas-table')) {
        window.setupSimplePagination('.marcas-table', '#pagination-container', 9);
    }
    if (document.querySelector('.categorias-table')) {
        window.setupSimplePagination('.categorias-table', '#pagination-container', 9);
    }
    if (document.querySelector('.proveedores-table')) {
        window.setupSimplePagination('.proveedores-table', '#pagination-container', 9);
    }
    if (document.querySelector('.contratos-table')) {
        window.setupSimplePagination('.contratos-table', '#pagination-container', 9);
    }
});
