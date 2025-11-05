# 🐳 Configuración Docker - CDATTG Asistencia Web

## 📋 Resumen

Este documento describe la configuración de Docker implementada para el proyecto CDATTG Asistencia Web. La configuración incluye solo los servicios de base de datos (MySQL) y Node.js para desarrollo frontend, mientras que el backend PHP será dockerizado por otro desarrollador.

## 🏗️ Arquitectura

### Servicios Docker Implementados

1. **MySQL 8.0** - Base de datos
2. **Node.js 24 Alpine** - Servidor de desarrollo Vite

### Servicios NO Implementados (Serán dockerizados por otro desarrollador)

- PHP/Laravel Backend


## 📁 Archivos de Configuración

### `docker-compose.yml`

Configuración principal de Docker Compose con los siguientes servicios:

#### MySQL
- **Imagen**: `mysql:8.0`
- **Puerto externo**: `3307` (para evitar conflictos con MySQL de Laragon)
- **Puerto interno**: `3306`
- **Volumen**: `mysql_data` (persistencia de datos)
- **Variables de entorno**:
  - `MYSQL_DATABASE`: `cdattg_db`
  - `MYSQL_ROOT_PASSWORD`: `root`
  - `MYSQL_USER`: `cdattg_user`
  - `MYSQL_PASSWORD`: `password`

#### Node.js
- **Imagen**: `node:24-alpine`
- **Puerto**: `5173` (Vite Dev Server)
- **Volumen**: `node_modules` (para persistir dependencias)
- **Comando**: Instala dependencias y ejecuta servidor de desarrollo Vite

### `.dockerignore`

Archivo que excluye archivos innecesarios del contexto de Docker para optimizar los builds:
- Archivos de Git
- node_modules y vendor
- Archivos de configuración local
- Logs y archivos temporales

## 🔧 Configuración del Entorno (.env)

### Variables Requeridas para Docker

```env
# Base de datos - IMPORTANTE: DB_HOST debe ser 'mysql' para Docker interno
# o 'localhost' si ejecutas desde Laragon
DB_CONNECTION=mysql
DB_HOST=mysql              # Nombre del servicio en docker-compose
DB_PORT=3306               # Puerto interno del contenedor
DB_DATABASE=cdattg_db
DB_USERNAME=cdattg_user    # ⚠️ NO puede ser "root"
DB_PASSWORD=password

# Para conexión desde Laragon (desarrollo local)
# DB_HOST=localhost
# DB_PORT=3307              # Puerto externo mapeado
```

### ⚠️ Configuración Crítica

- **DB_USERNAME**: Debe ser diferente de "root". MySQL no permite usar "root" como usuario regular.
- **DB_HOST**: 
  - `mysql` - Para conexión desde otros contenedores Docker
  - `localhost` - Para conexión desde Laragon o máquina local

## 🚀 Uso

### Comandos Principales

#### Levantar contenedores
```bash
docker-compose up -d
```

#### Ver estado de contenedores
```bash
docker-compose ps
```

#### Ver logs
```bash
# Todos los servicios
docker-compose logs -f

# Solo MySQL
docker-compose logs -f mysql

# Solo Node
docker-compose logs -f node
```

#### Detener contenedores
```bash
docker-compose down
```

#### Detener y eliminar volúmenes (elimina datos)
```bash
docker-compose down -v
```

#### Reiniciar un servicio específico
```bash
docker-compose restart mysql
docker-compose restart node
```

### Ejecutar Migraciones

Como el backend PHP no está dockerizado aún, las migraciones se ejecutan desde Laragon:

```bash
# Usando el script de Windows
migrate_modules.bat all

# O directamente con Artisan
php artisan migrate:module --all

# Para resetear desde cero
php artisan migrate:module --all --fresh
```

**Nota**: Asegúrate de que tu `.env` tenga `DB_HOST=localhost` y `DB_PORT=3307` para ejecutar desde Laragon.

## 🌐 Acceso a los Servicios

- **MySQL**: `localhost:3307` (puerto externo) o `mysql:3306` (desde otros contenedores)
- **Vite Dev Server**: `http://localhost:5173`

## 🔍 Solución de Problemas

### Error: "MYSQL_USER='root' is not allowed"

**Causa**: `DB_USERNAME` está configurado como "root" en el `.env`

**Solución**: Cambia `DB_USERNAME` a cualquier valor diferente de "root" (ej: `cdattg_user`)

**Solución**: El `docker-compose.yml` ya está configurado para usar el puerto `3307` externamente

### Error: "Cannot create property 'name' on boolean 'true'" (Node)

**Causa**: Problema con `package-lock.json` o volumen `node_modules`

**Solución**: 
```bash
docker-compose down -v
docker-compose up -d
```

### Los contenedores se reinician constantemente


## 📝 Notas Importantes

1. **Versión de Node**: Se usa `node:24-alpine` (la más reciente disponible). Si hay problemas de compatibilidad, se puede cambiar a `node:22-alpine` (LTS).

2. **Persistencia de datos**: Los datos de MySQL se guardan en el volumen `mysql_data`. Si eliminas el volumen con `docker-compose down -v`, perderás todos los datos.

3. **node_modules**: Se almacena en un volumen separado para evitar conflictos entre el contenedor y el sistema host.

4. **Backend PHP**: Cuando el backend esté dockerizado, asegúrate de que tenga acceso a la misma red Docker (`cdattg_network`) y use `DB_HOST=mysql` en su configuración.

## 🔄 Próximos Pasos

Cuando el backend PHP esté dockerizado:

1. El servicio PHP deberá conectarse a `mysql` (nombre del servicio) en lugar de `localhost`
2. Ejecutar migraciones desde el contenedor PHP:
   ```bash
   docker-compose exec app php artisan migrate:module --all
   ```
3. Configurar el `.env` del backend con `DB_HOST=mysql`

## 📚 Referencias

- [Docker Compose Documentation](https://docs.docker.com/compose/)
- [MySQL Docker Image](https://hub.docker.com/_/mysql)
- [Node.js Docker Image](https://hub.docker.com/_/node)

---

**Fecha de implementación**: Noviembre 2025  
**Versión de Docker Compose**: 3.8+  
**Última actualización**: 2025-11-04

