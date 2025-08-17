let carrito = [];

// Función para agregar un producto al carrito
function agregarProducto(producto) {
    const productoExistente = carrito.find(p => p.id === producto.id);
    if (productoExistente) {
        productoExistente.cantidad += 1; // Aumentar la cantidad si ya existe
    } else {
        producto.cantidad = 1; // Inicializar cantidad
        carrito.push(producto);
    }
    actualizarCarrito();
}

// Función para eliminar un producto del carrito
function eliminarProducto(productoId) {
    carrito = carrito.filter(producto => producto.id !== productoId);
    actualizarCarrito();
}

// Función para actualizar el carrito en la interfaz
function actualizarCarrito() {
    const carritoContenedor = document.getElementById('carrito-contenedor');
    carritoContenedor.innerHTML = ''; // Limpiar el contenedor

    carrito.forEach(producto => {
        const productoElemento = document.createElement('div');
        productoElemento.innerHTML = `
            <h3>${producto.nombre} (Cantidad: ${producto.cantidad})</h3>
            <button onclick="eliminarProducto(${producto.id})">Eliminar</button>
        `;
        carritoContenedor.appendChild(productoElemento);
    });

    // Mostrar resumen del carrito
    const resumenElemento = document.getElementById('resumen');
    resumenElemento.innerText = `Total de productos: ${carrito.reduce((total, p) => total + p.cantidad, 0)}`;
}

// Ejemplo de uso
agregarProducto({ id: 1, nombre: 'Producto 1' });
agregarProducto({ id: 2, nombre: 'Producto 2' });
agregarProducto({ id: 1, nombre: 'Producto 1' }); // Agregar nuevamente para aumentar la cantidad
