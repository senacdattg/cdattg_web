#!/bin/bash
set -euo pipefail

echo "🚀 Iniciando contenedor de aplicación..."
cd /var/www/html || exit 1

# ==================================================
# === Funciones auxiliares
# ==================================================
should_run() {
    case "${1:-}" in
        1|true|TRUE|True|yes|YES|Yes) return 0 ;;
        *) return 1 ;;
    esac
}

ensure_permissions() {
    local path="$1"
    if [ -d "$path" ]; then
        echo "🔧 Ajustando permisos en $path"
        chown -R www-data:www-data "$path"
        chmod -R 775 "$path"
    fi
}

wait_for_database() {
    local host="${DB_HOST:-}"
    local port="${DB_PORT:-3306}"
    local timeout=90
    local waited=0

    if [ -z "$host" ]; then
        echo "⚠️ No se definió DB_HOST, omitiendo espera de base de datos."
        return
    fi

    echo "⏳ Esperando conexión a MySQL (${host}:${port})..."

    until php -r "try {
        \$pdo = new PDO('mysql:host=${host};port=${port}', '${DB_USERNAME:-root}', '${DB_PASSWORD:-root}');
        exit(0);
    } catch (Throwable \$e) { exit(1); }" >/dev/null 2>&1; do
        sleep 2
        waited=$((waited+2))
        echo "   ...esperando ($waited s)"
        if [ "$waited" -ge "$timeout" ]; then
            echo "❌ Timeout de ${timeout}s esperando a MySQL"
            exit 1
        fi
    done
    echo "✅ Base de datos disponible."
}

get_env_app_key() {
    if [ ! -f ".env" ]; then
        return 1
    fi

    local env_app_key
    env_app_key=$(grep -E '^APP_KEY=' .env | tail -n 1 | cut -d '=' -f2-)
    env_app_key="${env_app_key%%#*}"
    env_app_key="${env_app_key%\"}"
    env_app_key="${env_app_key#\"}"
    env_app_key="${env_app_key%\'}"
    env_app_key="${env_app_key#\'}"
    env_app_key=$(echo "$env_app_key" | xargs)

    if [ -n "$env_app_key" ]; then
        printf '%s' "$env_app_key"
        return 0
    fi

    return 1
}

# ==================================================
# === Inicialización
# ==================================================
app_env="${APP_ENV:-local}"
echo "📦 Entorno actual: $app_env"

if [ "${WAIT_FOR_DB,,}" != "false" ]; then
    wait_for_database
fi

mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

ensure_permissions storage
ensure_permissions bootstrap/cache

log_file="storage/logs/laravel.log"
if [ ! -f "$log_file" ]; then
    touch "$log_file"
fi
chown www-data:www-data "$log_file" || true
chmod 664 "$log_file" || true

# ==================================================
# === Dependencias Composer (si faltan)
# ==================================================
if [ ! -f "vendor/autoload.php" ]; then
    echo "📦 Instalando dependencias de Composer..."
    local composer_cmd="composer install --no-interaction --prefer-dist --no-scripts --no-progress"
    if [ "$app_env" = "production" ]; then
        composer_cmd="$composer_cmd --no-dev --optimize-autoloader"
    fi
    COMPOSER_ALLOW_SUPERUSER=1 $composer_cmd
fi

# ==================================================
# === Migraciones / Seeders
# ==================================================
if should_run "${RUN_MIGRATIONS:-false}"; then
    echo "🗄️ Ejecutando migraciones..."
if php artisan list --raw | grep -q '^migrate:module'; then
        if should_run "${RUN_MIGRATIONS_FRESH:-false}"; then
            php artisan migrate:module --all --fresh || exit 1
        else
            php artisan migrate:module --all || exit 1
        fi
    else
        php artisan migrate --force || exit 1
    fi
fi

if should_run "${RUN_SEEDERS:-false}"; then
    echo "🌱 Ejecutando seeders..."
    php artisan db:seed --force || exit 1
fi

# ==================================================
# === Cacheo Laravel
# ==================================================
cache_bootstrap=false
if should_run "${CACHE_BOOTSTRAP:-false}" || [ "$app_env" = "production" ]; then
    cache_bootstrap=true
fi

if [ "$cache_bootstrap" = true ]; then
    echo "🧩 Refrescando caches de la aplicación..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# ==================================================
# === Generar APP_KEY si falta
# ==================================================
env_app_key="$(get_env_app_key || true)"

if [ -z "$env_app_key" ]; then
    echo "🔑 Generando APP_KEY..."
    php artisan key:generate --force || true
    env_app_key="$(get_env_app_key || true)"
fi

# Asegurar que APP_KEY esté exportada en el entorno actual
if [ -z "${APP_KEY:-}" ] && [ -n "$env_app_key" ]; then
    export APP_KEY="$env_app_key"
fi

# ==================================================
# === Build frontend (opcional)
# ==================================================
if should_run "${RUN_BUILD_ASSETS:-false}"; then
    echo "🎨 Compilando assets frontend..."
    if [ -f "package.json" ]; then
        if command -v npm >/dev/null 2>&1; then
            if [ -f "package-lock.json" ]; then
                npm ci && npm run build || exit 1
            else
                npm install && npm run build || exit 1
            fi
        else
            echo "⚠️ npm no disponible en esta imagen. Usa RUN_BUILD_ASSETS=false o recompila con Node."
            exit 1
        fi
    fi
fi

# ==================================================
# === Descubrimiento de paquetes
# ==================================================
if [ -f "artisan" ]; then
    php artisan package:discover --ansi || true
fi

echo "✅ Inicialización completada"
exec "$@"
