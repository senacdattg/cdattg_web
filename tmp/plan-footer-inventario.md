## Estado actual
- El footer `layout.footer` usa un `d-flex` que en pantallas grandes deja elementos sin alineación consistente (ej. badge de SonarQube queda desfasado).

## Estado final
- El footer debe mantener todos los bloques alineados y centrados en pantallas grandes, y apilarse ordenadamente en pantallas pequeñas.
- El contenedor del footer abarcará todo el ancho visible del módulo de inventario, incluso cuando el sidebar esté expandido.

## Archivos a modificar
- `resources/views/layout/footer.blade.php`

## Tareas
- Reemplazar el contenedor flex por una grilla responsiva con columnas de Bootstrap.
- Ajustar las clases utilitarias para preservar alineación horizontal en pantallas grandes y espaciado vertical en pantallas pequeñas.
- Sobrescribir los estilos de AdminLTE que limitan el ancho del footer cuando el sidebar está desplegado.

