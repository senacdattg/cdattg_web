# 🎓 CDATTG Asistence Web

Sistema de gestión de asistencias y programas complementarios para el SENA (Servicio Nacional de Aprendizaje).

## 📋 Descripción

Aplicación web desarrollada en Laravel para la gestión integral de:
- ✅ Asistencias de aprendices e instructores
- 📚 Programas de formación complementaria
- 👥 Gestión de personas e instructores
- 📊 Reportes y estadísticas
- 🔔 Notificaciones en tiempo real (WebSocket)
- 📱 API REST para aplicación móvil Flutter

## 🚀 Inicio Rápido

### Requisitos Previos
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js 18+
- Redis (opcional, para cache y WebSocket)

### Instalación

```bash
# Clonar repositorio
git clone [url-del-repositorio]
cd cdattg_asistence_web

# Instalar dependencias
composer install
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Migrar base de datos
php artisan migrate --seed

# Iniciar servidor
php artisan serve
```

## 📚 Documentación

Toda la documentación del proyecto está organizada en la carpeta [`docs/`](docs/README.md):

### 🚀 Despliegue
- [Docker](docs/deployment/docker.md) - Configuración con contenedores
- [WebSocket](docs/deployment/websocket.md) - Notificaciones en tiempo real

### 💻 Desarrollo
- [Refactorización](docs/development/refactoring.md) - Comando de refactorización automática
- [Blade Components](docs/development/blade-components.md) - Componentes reutilizables
- [Table Refactoring](docs/development/table-refactoring.md) - Refactorización de tablas
- [Migraciones Modulares](docs/development/migrations-modules.md) - Sistema modular de base de datos

### 📚 Guías
- [Sistema de Inventario](docs/guides/sistema-inventario.md) - Sistema híbrido
- [Días de Formación](docs/guides/dias-formacion.md) - Gestión de horarios

### 🌐 API
- [Documentación API](docs/api/API.md) - Endpoints REST

**📖 [Ver índice completo de documentación →](docs/README.md)**

## 🛠️ Stack Tecnológico

- **Framework**: Laravel 12+
- **PHP**: 8.2+
- **Base de Datos**: MySQL 8.0
- **Cache**: Redis
- **Frontend**: Blade Templates, Alpine.js, TailwindCSS
- **WebSocket**: Laravel Reverb
- **Contenedores**: Docker + Docker Compose
- **API Mobile**: Flutter (cliente móvil)

## 🔧 Comandos Útiles

```bash
# Desarrollo
php artisan serve                          # Iniciar servidor
npm run dev                                # Compilar assets en desarrollo
php artisan test                           # Ejecutar tests

# Calidad de código
php artisan refactor:sonarqube --dry-run  # Analizar código (sin cambios)
php artisan refactor:sonarqube            # Corregir problemas automáticamente

# Producción
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🐳 Docker

Para despliegue con Docker, consulta la [documentación de Docker](docs/deployment/docker.md).

```bash
# Inicio rápido con Docker
docker-compose up -d
```

## 🔔 WebSocket (Notificaciones en Tiempo Real)

El sistema incluye notificaciones en tiempo real usando Laravel Reverb.

Para más información, consulta la [documentación de WebSocket](docs/deployment/websocket.md).

## 🧪 Testing

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar tests específicos
php artisan test --filter=InstructorTest

# Con cobertura
php artisan test --coverage
```

## 📦 Estructura del Proyecto

```
cdattg_asistence_web/
├── app/                    # Código de la aplicación
│   ├── Console/           # Comandos Artisan
│   ├── Http/              # Controllers, Middleware, Requests
│   ├── Models/            # Modelos Eloquent
│   └── Services/          # Lógica de negocio
├── docs/                   # Documentación del proyecto
├── docker/                 # Configuración Docker
├── resources/             # Vistas y assets
├── routes/                # Definición de rutas
└── tests/                 # Tests automatizados
```

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add: amazing feature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

### Estándares de Código

- Seguir PSR-12
- Ejecutar refactorización: `php artisan refactor:sonarqube --dry-run`
- Escribir tests para nuevas funcionalidades
- Documentar cambios importantes

## 📄 Licencia

Este proyecto es propiedad del SENA - CDATTG.

## 👥 Equipo

Desarrollado por el equipo de desarrollo del CDATTG.

## 📞 Contacto

Para soporte o consultas, contacta al equipo de desarrollo del CDATTG.

---

**Nota**: Este sistema está en desarrollo activo. Para más información, consulta la [documentación completa](docs/).
