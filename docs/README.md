# 📚 Documentación del Proyecto CDATTG Asistence Web

Bienvenido a la documentación del sistema de gestión de asistencias y programas complementarios del SENA.

## 📖 Tabla de Contenidos

### 🚀 Despliegue
- [Docker](deployment/docker.md) - Configuración y despliegue con Docker
- [WebSocket](deployment/websocket.md) - Configuración de notificaciones en tiempo real

### 💻 Desarrollo
- [Refactorización](development/refactoring.md) - Comando de refactorización automática SonarQube
- [Blade Components](development/blade-components.md) - Componentes reutilizables
- [Table Refactoring](development/table-refactoring.md) - Guía de refactorización de tablas
- [Migraciones por Módulos](development/migrations-modules.md) - Sistema modular de migraciones
- [Reorganización de Migraciones](development/migrations-reorganization.md) - Resumen de reorganización

### 📚 Guías de Usuario
- [Sistema de Inventario](guides/sistema-inventario.md) - Sistema híbrido de inventario
- [Días de Formación](guides/dias-formacion.md) - Gestión de días de formación
- [Instructor - Días](guides/instructor-dias.md) - Asignación de días a instructores

### 🔧 Correcciones y Mejoras
- [Vista Editar](fixes/CORRECCIONES_VISTA_EDITAR.md) - Correcciones en vistas de edición
- [Resumen de Correcciones](fixes/resumen-correcciones.md) - Historial de correcciones
- [Implementación Días Instructor](fixes/implementacion-dias-instructor.md) - Implementación completa
- [Integración Días Instructor](fixes/integracion-dias-instructor.md) - Integración de días
- [Resumen Final](fixes/resumen-final.md) - Resumen de implementaciones
- [Resumen Días Instructor](fixes/resumen-dias-instructor.md) - Resumen específico

### 🌐 API
- [Documentación API](api/API.md) - Endpoints y especificaciones

### 🔗 Enlaces Útiles
- [Repositorio Principal](../) - Volver al README principal
- [Código Fuente](../app/) - Estructura de la aplicación

## 🏗️ Arquitectura del Proyecto

Este es un proyecto Laravel 12+ con las siguientes características:

- **Backend**: Laravel + PHP 8.2+
- **Frontend**: Blade Templates + Alpine.js
- **Base de Datos**: MySQL 8.0
- **Cache**: Redis
- **WebSocket**: Laravel Reverb
- **Contenedores**: Docker + Docker Compose
- **Mobile**: API REST para Flutter

## 🤝 Contribuir

Para contribuir al proyecto:

1. Lee la [documentación de desarrollo](development/)
2. Ejecuta las pruebas: `php artisan test`
3. Ejecuta el análisis de código: `php artisan refactor:sonarqube --dry-run`
4. Sigue las convenciones de código del proyecto

## 📝 Agregar Nueva Documentación

Para agregar nueva documentación:

1. Crea un archivo `.md` en la carpeta correspondiente:
   - `deployment/` - Para documentación de despliegue
   - `development/` - Para documentación de desarrollo
2. Actualiza este índice con un enlace al nuevo documento
3. Usa formato Markdown con emojis para mejor legibilidad

## 🆘 Soporte

Si necesitas ayuda:
- Revisa la documentación en esta carpeta
- Consulta los logs: `storage/logs/laravel.log`
- Ejecuta diagnósticos: `php artisan config:clear && php artisan cache:clear`

