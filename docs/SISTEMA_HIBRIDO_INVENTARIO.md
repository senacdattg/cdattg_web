# Sistema Híbrido de Inventario - Documentación

## Descripción General

Se ha implementado un sistema híbrido para el módulo de inventario que combina:
1. **Vista Administrativa (Backend)** - Gestión completa de productos con tablas tradicionales
2. **Vista E-commerce (Frontend)** - Catálogo moderno tipo tienda online con carrito de compras

## Estructura de Archivos Implementados

### Vistas (Blade Templates)
- `resources/views/inventario/productos/card.blade.php` - Vista de catálogo tipo e-commerce
- `resources/views/inventario/carrito/carrito.blade.php` - Vista del carrito de compras

### JavaScript
- `resources/js/inventario/card.js` - Funcionalidad del catálogo (búsqueda, filtros, agregar al carrito)
- `resources/js/inventario/carrito.js` - Funcionalidad del carrito (gestión de items, cantidades, confirmación)

### CSS
- `resources/css/inventario/card.css` - Estilos modernos para las tarjetas de productos
- `resources/css/inventario/carrito.css` - Estilos para la vista del carrito

### Controladores
- `app/Http/Controllers/Inventario/ProductoController.php` - Métodos agregados:
  - `catalogo()` - Muestra el catálogo de productos
  - `buscar()` - Búsqueda AJAX de productos
  - `agregarAlCarrito()` - Valida productos antes de agregar al carrito
  
- `app/Http/Controllers/Inventario/CarritoController.php` - Métodos implementados:
  - `ecommerce()` - Vista del carrito tipo e-commerce
  - `agregar()` - Procesar solicitud de productos
  - `actualizar()` - Actualizar cantidades
  - `eliminar()` - Eliminar producto del carrito
  - `vaciar()` - Vaciar todo el carrito
  - `contenido()` - Obtener detalles de productos en el carrito

## Rutas Disponibles

### Vista Administrativa (Legacy)
```
GET /inventario/productos - Lista administrativa de productos
GET /inventario/carrito - Vista administrativa del carrito
```

### Vista E-commerce (Nueva)
```
GET /inventario/productos/catalogo - Catálogo de productos tipo tienda
GET /inventario/carrito-ecommerce - Carrito de compras moderno
```

### API Endpoints (AJAX)
```
POST /inventario/productos/agregar-carrito - Agregar producto al carrito
GET  /inventario/productos/buscar - Buscar productos
POST /inventario/carrito/agregar - Crear orden desde el carrito
PUT  /inventario/carrito/actualizar/{id} - Actualizar cantidad
DELETE /inventario/carrito/eliminar/{id} - Eliminar producto
POST /inventario/carrito/vaciar - Vaciar carrito
GET  /inventario/carrito/contenido - Obtener productos del carrito
```

## Características Principales

### Vista de Catálogo (card.blade.php)

#### Funcionalidades:
1. **Grid de Productos Responsivo**
   - Diseño en tarjetas (cards) moderno y atractivo
   - Adaptable a diferentes tamaños de pantalla
   - Animaciones suaves al hacer hover

2. **Sistema de Filtrado**
   - Búsqueda en tiempo real por nombre o código
   - Filtro por categoría
   - Filtro por marca
   - Ordenamiento (nombre, stock, más recientes)

3. **Información del Producto**
   - Imagen del producto (con placeholder si no existe)
   - Badge de estado de stock (Disponible, Bajo Stock, Agotado)
   - Nombre y descripción
   - Código de barras
   - Categoría y marca
   - Stock disponible

4. **Acciones**
   - Ver detalles en modal
   - Agregar al carrito (validando stock)
   - Contador de items en el carrito

### Vista del Carrito (carrito.blade.php)

#### Funcionalidades:
1. **Gestión de Productos**
   - Lista visual de productos agregados
   - Control de cantidad con botones +/-
   - Validación de stock máximo
   - Eliminación individual de productos
   - Opción de vaciar todo el carrito

2. **Resumen de Solicitud**
   - Total de productos únicos
   - Total de items (suma de cantidades)
   - Información del solicitante
   - Campo de notas adicionales

3. **Proceso de Confirmación**
   - Modal de confirmación con resumen
   - Validación de stock antes de enviar
   - Guardado de borrador (localStorage)
   - Mensajes de éxito/error claros

## Flujo de Uso

### Para el Usuario Final:

1. **Acceder al Catálogo**
   - Ir a `/inventario/productos/catalogo`
   - Navegar por los productos disponibles

2. **Buscar/Filtrar Productos**
   - Usar la barra de búsqueda
   - Seleccionar categoría o marca
   - Ordenar según preferencia

3. **Agregar al Carrito**
   - Click en "Agregar" en el producto deseado
   - El sistema valida stock disponible
   - Se muestra notificación de éxito
   - Contador del carrito se actualiza

4. **Revisar Carrito**
   - Click en "Ver Carrito" (badge muestra cantidad)
   - Ajustar cantidades según necesidad
   - Agregar notas si es necesario

5. **Confirmar Solicitud**
   - Click en "Confirmar Solicitud"
   - Revisar resumen en el modal
   - Confirmar envío
   - Recibir confirmación

### Para el Administrador:

1. **Vista Administrativa**
   - Acceder a `/inventario/productos`
   - Gestionar productos (CRUD completo)
   - Ver estadísticas y reportes

2. **Cambiar entre Vistas**
   - Botón "Vista E-commerce" en lista administrativa
   - Botón "Vista Administrativa" en catálogo

## Almacenamiento Local (localStorage)

El carrito utiliza localStorage para persistencia:

```javascript
// Estructura del carrito
{
  id: "producto_id",
  name: "Nombre del producto",
  quantity: 1,
  maxStock: 10
}

// Keys utilizadas
- inventario_carrito: Items del carrito
- inventario_draft: Borrador de orden guardada
```

## Tecnologías Utilizadas

- **Backend**: Laravel (PHP)
- **Frontend**: Blade Templates, JavaScript (Vanilla)
- **Estilos**: CSS3 (con variables CSS)
- **UI Framework**: AdminLTE + Bootstrap
- **Notificaciones**: SweetAlert2
- **Iconos**: FontAwesome

## Consideraciones de Seguridad

1. **Validación del lado del servidor** en todos los endpoints
2. **CSRF Token** en todas las peticiones POST/PUT/DELETE
3. **Autenticación requerida** mediante middleware `auth`
4. **Validación de stock** antes de procesar órdenes
5. **Sanitización de entradas** del usuario

## Responsive Design

El sistema está completamente optimizado para:
- **Desktop** (>992px): Grid de 4 columnas
- **Tablet** (768px-992px): Grid de 3 columnas
- **Móvil** (576px-768px): Grid de 2 columnas
- **Móvil pequeño** (<576px): Grid de 1 columna

## Próximas Mejoras Sugeridas

1. **Integración con Sistema de Órdenes**
   - Crear modelo `Orden` y `DetalleOrden`
   - Guardar órdenes en base de datos
   - Estado de órdenes (pendiente, aprobada, rechazada)

2. **Notificaciones**
   - Email al crear una orden
   - Notificaciones push para administradores
   - Historial de órdenes del usuario

3. **Reportes**
   - Dashboard de órdenes
   - Productos más solicitados
   - Estadísticas de uso del catálogo

4. **Mejoras UX**
   - Comparador de productos
   - Lista de favoritos
   - Búsqueda por voz
   - Sugerencias de productos relacionados

## Soporte y Mantenimiento

Para dudas o problemas:
1. Revisar logs en `storage/logs/laravel.log`
2. Verificar consola del navegador para errores JavaScript
3. Comprobar que las rutas estén correctamente definidas
4. Asegurar que Vite esté compilando los assets correctamente

## Compilación de Assets

Para compilar los archivos CSS y JS:

```bash
# Desarrollo
npm run dev

# Producción
npm run build

# Watch mode (desarrollo)
npm run watch
```

## Testing

Endpoints a probar:
```bash
# Catálogo
GET http://localhost/inventario/productos/catalogo

# Carrito
GET http://localhost/inventario/carrito-ecommerce

# Buscar productos
GET http://localhost/inventario/productos/buscar?search=producto

# Agregar al carrito (AJAX)
POST http://localhost/inventario/carrito/agregar
Content-Type: application/json
{
  "items": [
    {"producto_id": 1, "cantidad": 2}
  ],
  "notas": "Notas opcionales"
}
```

## Changelog

### Versión 1.0.0 (2025-01-30)
- ✅ Implementación inicial del sistema híbrido
- ✅ Vista de catálogo tipo e-commerce
- ✅ Carrito de compras funcional
- ✅ Sistema de búsqueda y filtrado
- ✅ Validación de stock en tiempo real
- ✅ Diseño responsive completo
- ✅ Integración con sistema de autenticación

---

**Desarrollado para**: CDATTG Assistance Web  
**Fecha**: Enero 2025  
**Framework**: Laravel + AdminLTE