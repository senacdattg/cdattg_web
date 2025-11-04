/**
 * Script para filtrar municipios por departamento
 * Uso: Inicializar con initFiltroMunicipios(municipioOriginal)
 */

function initFiltroMunicipios(municipioOriginal = null) {
    const departamentoSelect = document.getElementById('departamento_id');
    const municipioSelect = document.getElementById('municipio_id');

    if (!departamentoSelect || !municipioSelect) {
        console.error('No se encontraron los elementos departamento_id o municipio_id');
        return;
    }

    // Función para filtrar municipios
    function filtrarMunicipios() {
        const departamentoId = departamentoSelect.value;

        if (!departamentoId) {
            // Si no hay departamento seleccionado, mostrar todos
            municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
            const municipiosArray = window.municipiosData || [];
            municipiosArray.forEach(function(municipio) {
                const option = document.createElement('option');
                option.value = municipio.id;
                option.textContent = municipio.municipio + ' (' + municipio.departamento + ')';
                if (municipioOriginal == municipio.id) option.selected = true;
                municipioSelect.appendChild(option);
            });
            return;
        }

        // Obtener municipios del departamento seleccionado
        fetch(`/inventario/proveedores/municipios/${departamentoId}`)
            .then(response => response.json())
            .then(municipios => {
                municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
                municipios.forEach(function(municipio) {
                    const option = document.createElement('option');
                    option.value = municipio.id;
                    option.textContent = municipio.municipio + ' (' + municipio.departamento + ')';
                    if (municipioOriginal == municipio.id) option.selected = true;
                    municipioSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar municipios:', error));
    }

    // Evento change en departamento
    departamentoSelect.addEventListener('change', function() {
        filtrarMunicipios();
    });

    // Ejecutar al cargar la página
    filtrarMunicipios();
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initFiltroMunicipios();
});