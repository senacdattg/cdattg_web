// JS para listado de órdenes

document.addEventListener('DOMContentLoaded', function() {
    // Filtros avanzados por estado, usuario y tipo en una fila
    const filtrosDiv = document.createElement('div');
    filtrosDiv.className = 'row mb-2';
    filtrosDiv.innerHTML = `
        <div class="col-md-4">
            <select id="filtroEstado" class="form-control">
                <option value="">Filtrar por estado</option>
                <option value="Aprobada">Aprobada</option>
                <option value="Pendiente">Pendiente</option>
                <option value="Rechazada">Rechazada</option>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" id="filtroUsuario" class="form-control" placeholder="Filtrar por usuario">
        </div>
        <div class="col-md-4">
            <input type="text" id="filtroTipo" class="form-control" placeholder="Filtrar por tipo de orden">
        </div>
    `;
    const cardBody = document.querySelector('.card-ordenes .card-body');
    if(cardBody) cardBody.insertBefore(filtrosDiv, cardBody.firstChild);

    // Filtro de texto en una fila aparte
    const filtroInputRow = document.createElement('div');
    filtroInputRow.className = 'row mb-3';
    filtroInputRow.innerHTML = `<div class="col-12"><input type="text" id="filtroTextoOrden" class="form-control" placeholder="Buscar orden..."></div>`;
    if(cardBody) cardBody.insertBefore(filtroInputRow, filtrosDiv.nextSibling);

    // Filtro de texto funcional
    const filtroInput = filtroInputRow.querySelector('#filtroTextoOrden');
    filtroInput.addEventListener('input', function() {
        const valor = filtroInput.value.toLowerCase();
        document.querySelectorAll('.table-ordenes tbody tr').forEach(function(row) {
            row.style.display = row.textContent.toLowerCase().includes(valor) ? '' : 'none';
        });
    });

    // Ordenar por columna al hacer click en el th
    document.querySelectorAll('.table-ordenes th').forEach(function(th, idx) {
        th.style.cursor = 'pointer';
        th.addEventListener('click', function() {
            const rows = Array.from(document.querySelectorAll('.table-ordenes tbody tr'));
            const asc = th.classList.toggle('asc');
            rows.sort(function(a, b) {
                const valA = a.children[idx].textContent.trim().toLowerCase();
                const valB = b.children[idx].textContent.trim().toLowerCase();
                return asc ? valA.localeCompare(valB) : valB.localeCompare(valA);
            });
            rows.forEach(row => row.parentNode.appendChild(row));
        });
    });

    // Resaltar fila seleccionada y redirigir al hacer click
    document.querySelectorAll('.table-ordenes tbody tr').forEach(function(row) {
        row.addEventListener('click', function() {
            document.querySelectorAll('.table-ordenes tbody tr').forEach(r => r.classList.remove('selected-row'));
            row.classList.add('selected-row');
            // Obtener el id de la orden (primer td)
            const id = row.children[0].textContent.trim();
            if(id) {
                window.location.href = '/inventario/salida/aprobar/' + id;
            }
        });
    });

    function aplicarFiltros() {
        const estado = document.getElementById('filtroEstado').value.toLowerCase();
        const usuario = document.getElementById('filtroUsuario').value.toLowerCase();
        const tipo = document.getElementById('filtroTipo').value.toLowerCase();
        document.querySelectorAll('.table-ordenes tbody tr').forEach(function(row) {
            const estadoText = row.children[5]?.textContent.toLowerCase() || '';
            const usuarioText = row.children[4]?.textContent.toLowerCase() || '';
            const tipoText = row.children[2]?.textContent.toLowerCase() || '';
            const matchEstado = !estado || estadoText.includes(estado);
            const matchUsuario = !usuario || usuarioText.includes(usuario);
            const matchTipo = !tipo || tipoText.includes(tipo);
            row.style.display = (matchEstado && matchUsuario && matchTipo) ? '' : 'none';
        });
    }
    document.getElementById('filtroEstado').addEventListener('change', aplicarFiltros);
    document.getElementById('filtroUsuario').addEventListener('input', aplicarFiltros);
    document.getElementById('filtroTipo').addEventListener('input', aplicarFiltros);

    // Indicadores visuales dinámicos para estado
    document.querySelectorAll('.table-ordenes tbody tr').forEach(function(row) {
        const estadoTd = row.children[5];
        if(estadoTd) {
            const estado = estadoTd.textContent.trim().toLowerCase();
            estadoTd.innerHTML = '';
            if(estado === 'aprobada') {
                estadoTd.innerHTML = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Aprobada</span>';
            } else if(estado === 'pendiente') {
                estadoTd.innerHTML = '<span class="badge badge-warning"><i class="fas fa-clock"></i> Pendiente</span>';
            } else if(estado === 'rechazada') {
                estadoTd.innerHTML = '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Rechazada</span>';
            } else {
                estadoTd.innerHTML = '<span class="badge badge-secondary">' + estadoTd.textContent + '</span>';
            }
        }
    });
});
