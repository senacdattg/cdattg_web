#!/bin/bash
set -euo pipefail

echo "🚀 Iniciando contenedor de aplicación..."

cd /var/www/html

wait_for_db=${WAIT_FOR_DB:-true}

if [ "${wait_for_db,,}" != "false" ] && [ -n "${DB_HOST:-}" ]; then
    echo "⏳ Esperando a que MySQL (${DB_HOST}:${DB_PORT:-3306}) esté listo..."
    until php -r "try {
            \$pdo = new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306}', '${DB_USERNAME:-root}', '${DB_PASSWORD:-root}');
            \$stmt = \$pdo->query(\"SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '${DB_DATABASE:-cdattg}' LIMIT 1\");
            if (!\$stmt || !\$stmt->fetch()) {
                throw new Exception('database not ready');
            }
            exit(0);
        } catch (Throwable \$e) {
            fwrite(STDERR, \$e->getMessage());
            exit(1);
        }" 2>/dev/null; do
        sleep 2
        echo "   esperando conexión a MySQL..."
    done
    echo "✅ MySQL disponible"
    sleep 2
fi

ensure_permissions() {
    local path="$1"
    if [ -d "$path" ]; then
        local owner
        owner=$(stat -c '%U:%G' "$path" 2>/dev/null || echo "")
        if [ "$owner" != "www-data:www-data" ]; then
            chown -R www-data:www-data "$path"
        fi
        chmod -R 775 "$path"
    fi
}

ensure_permissions storage
ensure_permissions bootstrap/cache

should_run() {
    case "${1:-}" in
        1|true|TRUE|True|yes|YES|Yes) return 0 ;;
        *) return 1 ;;
    esac
}

if should_run "${RUN_MIGRATIONS:-false}"; then
    if should_run "${RUN_MIGRATIONS_FRESH:-false}"; then
        echo "🗄️ Ejecutando migraciones de módulos (fresh)..."
        php artisan migrate:module --all --fresh || {
            echo "❌ Migraciones fresh de módulos fallaron";
            exit 1;
        }
    else
        echo "🗄️ Ejecutando migraciones de módulos..."
        php artisan migrate:module --all || {
            echo "❌ Migraciones de módulos fallaron";
            exit 1;
        }
    fi
fi

if should_run "${RUN_SEEDERS:-false}"; then
    echo "🌱 Ejecutando seeders..."
    php artisan db:seed --force || {
        echo "❌ Seeders fallaron";
        exit 1;
    }
fi

if should_run "${CACHE_BOOTSTRAP:-false}"; then
    echo "🧩 Refrescando caches de la aplicación..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

if [ -f "artisan" ]; then
    echo "📦 Descubriendo paquetes de Laravel..."
    php artisan package:discover --ansi || true
fi

# Ejecutar build de frontend si se solicita
if should_run "${RUN_BUILD_ASSETS:-false}"; then
    echo "🎨 Construyendo assets frontend con npm run build..."
    if [ -f "package.json" ]; then
        npm run build || {
            echo "❌ Falló npm run build";
            exit 1;
        }
    else
        echo "⚠️ No se encontró package.json, omitiendo build de assets"
    fi
fi

if [ -z "${APP_KEY:-}" ] && { [ ! -f ".env" ] || ! grep -q "^APP_KEY=" .env 2>/dev/null || grep -q "^APP_KEY=$" .env 2>/dev/null; }; then
    echo "🔑 Generando APP_KEY..."
    php artisan key:generate --force || true
fi

echo "✅ Inicialización completada"

exec "$@"

