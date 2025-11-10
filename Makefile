##### ==========================================================
##### 📦 Makefile para CDATTG Asistencia (Laravel + Docker)
##### ==========================================================

# Detecta el comando correcto (docker compose vs docker-compose)
DOCKER_COMPOSE := $(shell docker compose version >/dev/null 2>&1 && echo "docker compose" || echo "docker-compose")

##### ==========================================================
##### 🌱 CONFIGURACIÓN BASE
##### ==========================================================

ENV_FILE=.env

# Lee APP_ENV actual desde .env (toma la última coincidencia por compatibilidad)
APP_ENV_VALUE := $(strip $(shell \
	if [ -f $(ENV_FILE) ]; then \
		awk -F= '\
			$$1 ~ /^[[:space:]]*APP_ENV[[:space:]]*$$/ { \
				val = $$2; \
				sub(/^[[:space:]]+/, "", val); \
				sub(/[[:space:]]*#.*/, "", val); \
				gsub(/^[\"\047]+|[\"\047]+$$/, "", val); \
				if (length(val)) last = val; \
			} \
			END { if (length(last)) print last; else print "local"; }' $(ENV_FILE); \
	else \
		echo local; \
	fi))

# Normaliza comportamiento según APP_ENV
ifeq ($(APP_ENV_VALUE),production)
	CURRENT_PROFILE:=produccion
	CURRENT_BUILD_ENV:=production
	CURRENT_NODE_ENV:=production
else ifeq ($(APP_ENV_VALUE),produccion)
	CURRENT_PROFILE:=produccion
	CURRENT_BUILD_ENV:=production
	CURRENT_NODE_ENV:=production
else ifeq ($(APP_ENV_VALUE),testing)
	CURRENT_PROFILE:=testing
	CURRENT_BUILD_ENV:=testing
	CURRENT_NODE_ENV:=testing
else
	CURRENT_PROFILE:=local
	CURRENT_BUILD_ENV:=local
	CURRENT_NODE_ENV:=development
endif

ENV_ARGS:=APP_ENV=$(APP_ENV_VALUE) BUILD_ENV=$(CURRENT_BUILD_ENV) NODE_ENV=$(CURRENT_NODE_ENV)

DEV_ENV_ARGS:=APP_ENV=local BUILD_ENV=local NODE_ENV=development
TEST_ENV_ARGS:=APP_ENV=testing BUILD_ENV=testing NODE_ENV=testing
PROD_ENV_ARGS:=APP_ENV=production BUILD_ENV=production NODE_ENV=production

##### ==========================================================
##### 🧑‍💻 COMANDOS PRINCIPALES
##### ==========================================================

## 🧑‍💻 Desarrollo local (frontend + backend con hot reload)
dev:
	@echo "🚀 Iniciando entorno LOCAL..."
	$(DEV_ENV_ARGS) $(DOCKER_COMPOSE) --env-file $(ENV_FILE) --profile local up --build

## 🧪 Testing (para CI/CD o pruebas automáticas)
test:
	@echo "🧪 Iniciando entorno TESTING..."
	$(TEST_ENV_ARGS) $(DOCKER_COMPOSE) --env-file $(ENV_FILE) --profile testing up --build --abort-on-container-exit

## 🏭 Producción (build optimizado sin frontend dev)
prod:
	@echo "🏭 Construyendo e iniciando entorno PRODUCCIÓN..."
	$(PROD_ENV_ARGS) $(DOCKER_COMPOSE) --env-file $(ENV_FILE) --profile produccion up -d --build

##### ==========================================================
##### 🧩 UTILIDADES GENERALES
##### ==========================================================

## 🔄 Iniciar contenedores
up:
	@echo "🔄 Iniciando contenedores (APP_ENV=$(APP_ENV_VALUE), perfil=$(CURRENT_PROFILE))..."
	$(ENV_ARGS) $(DOCKER_COMPOSE) --env-file $(ENV_FILE) --profile $(CURRENT_PROFILE) up -d

## 🔴 Detener contenedores activos
stop:
	@echo "🛑 Deteniendo contenedores..."
	$(ENV_ARGS) $(DOCKER_COMPOSE) --env-file $(ENV_FILE) --profile $(CURRENT_PROFILE) down

## 🔁 Reiniciar contenedores sin rebuild
restart:
	@echo "🔁 Reiniciando contenedores..."
	$(ENV_ARGS) $(DOCKER_COMPOSE) --env-file $(ENV_FILE) --profile $(CURRENT_PROFILE) restart

## 🧹 Eliminar contenedores, imágenes y volúmenes
clean:
	@echo "🧹 Limpiando entorno (containers, images, volumes)..."
	$(ENV_ARGS) $(DOCKER_COMPOSE) --env-file $(ENV_FILE) --profile $(CURRENT_PROFILE) down -v --rmi all --remove-orphans

## 🧱 Rebuild forzado (sin usar cache)
rebuild:
	@echo "🔨 Reconstruyendo imágenes sin caché..."
	$(ENV_ARGS) $(DOCKER_COMPOSE) --env-file $(ENV_FILE) --profile $(CURRENT_PROFILE) build --no-cache

## 📜 Logs del contenedor APP (Laravel)
logs:
	@echo "📜 Mostrando logs del contenedor APP..."
	$(ENV_ARGS) $(DOCKER_COMPOSE) --env-file $(ENV_FILE) --profile $(CURRENT_PROFILE) logs -f app

## 🧠 Ejecutar comandos Artisan dentro del contenedor APP
artisan:
	@echo "🧠 Ejecutando comando Artisan..."
	$(ENV_ARGS) $(DOCKER_COMPOSE) --env-file $(ENV_FILE) --profile $(CURRENT_PROFILE) exec app php artisan $(filter-out $@,$(MAKECMDGOALS))

## 🧪 Ejecutar pruebas PHPUnit dentro del contenedor APP
test-artisan:
	@echo "🧪 Ejecutando tests de Laravel..."
	$(ENV_ARGS) $(DOCKER_COMPOSE) --env-file $(ENV_FILE) --profile $(CURRENT_PROFILE) exec app php artisan test

## 🧰 Ejecutar shell interactivo dentro del contenedor APP
shell:
	@echo "🧰 Accediendo al contenedor APP..."
	$(ENV_ARGS) $(DOCKER_COMPOSE) --env-file $(ENV_FILE) --profile $(CURRENT_PROFILE) exec app bash

## 🧰 Mostrar estado de los contenedores
ps:
	$(ENV_ARGS) $(DOCKER_COMPOSE) --env-file $(ENV_FILE) --profile $(CURRENT_PROFILE) ps

##### ==========================================================
##### 🔧 Ayuda (por defecto)
##### ==========================================================
help:
	@echo ""
	@echo "📘 Comandos disponibles:"
	@echo "  make dev          → Entorno local (Laravel + Vite + Redis + MySQL)"
	@echo "  make test         → Entorno testing (CI/CD, migraciones fresh)"
	@echo "  make prod         → Entorno producción (optimizaciones activas)"
	@echo "  make up           → Iniciar contenedores"
	@echo "  make stop         → Detener todos los contenedores"
	@echo "  make restart      → Reiniciar contenedores"
	@echo "  make rebuild      → Rebuild sin caché"
	@echo "  make clean        → Eliminar imágenes y volúmenes"
	@echo "  make logs         → Ver logs del contenedor APP"
	@echo "  make artisan X    → Ejecutar comando artisan (ej: make artisan migrate)"
	@echo "  make shell        → Abrir bash dentro del contenedor APP"
	@echo "  make ps           → Ver estado de los contenedores"
	@echo ""

.PHONY: dev test prod stop restart clean rebuild logs artisan test-artisan shell ps help

