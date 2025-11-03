document.addEventListener('DOMContentLoaded', function() {
    const inputCodigo = document.getElementById('inputCodigoBarras');
    const resultadoDiv = document.getElementById('resultadoBusqueda');

    // Enfocar automáticamente al abrir el modal
    $('#modalEscanear').on('shown.bs.modal', function () {
        inputCodigo.focus();
        inputCodigo.value = '';
        resultadoDiv.innerHTML = '';
    });

    // Detectar "Enter" después de escanear
    inputCodigo.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            const codigo = inputCodigo.value.trim();

            if (codigo === '') return;

            resultadoDiv.innerHTML = '<p class="text-info">🔍 Buscando producto...</p>';

            fetch(`/inventario/productos/buscar/${codigo}`)
                .then(response => {
                    if (!response.ok) throw new Error('Producto no encontrado');
                    return response.json();
                })
                .then(producto => {
                    resultadoDiv.innerHTML = `
                        <div class="alert alert-success mt-3">
                            Producto encontrado: <strong>${producto.producto}</strong>. Redirigiendo...
                        </div>
                    `;
                    setTimeout(() => {
                        window.location.href = `/inventario/productos/${producto.id}`;
                    }, 1000);
                })
                .catch(error => {
                    resultadoDiv.innerHTML = `
                        <div class="alert alert-danger mt-3">
                                No se encontró ningún producto con el código <strong>${codigo}</strong>.
                        </div>
                    `;
                });
        }
    });
});
