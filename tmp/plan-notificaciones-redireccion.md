## Estado actual
- La vista `inventario/notificaciones/index` lista las notificaciones pero no ofrece un acceso directo al recurso relacionado (ordenes, productos con stock bajo o aprobaciones).
- El script `resources/js/inventario/notificaciones.js` solo gestiona acciones de lectura/eliminación sin redireccionar.

## Estado final
- Cada notificación debe exponer una acción que permita abrir el recurso correspondiente (orden específica, detalle de producto o panel de aprobaciones) directamente.
- Al abrir la notificación, si está sin leer, se marca automáticamente como leída antes de la redirección.

## Archivos a modificar
- `resources/views/inventario/notificaciones/index.blade.php`
- `resources/js/inventario/notificaciones.js`

## Tareas
1. Determinar la URL destino según el tipo de notificación y exponerla en la vista.
2. Añadir un control de interfaz para abrir la notificación y propagar la URL calculada.
3. Actualizar el script JS para marcar la notificación como leída (si aplica) y redirigir al recurso.
4. Verificar funcionamiento manual y mantener estilos existentes.

