@echo off
REM =============================================================================
REM Script para ejecutar migraciones por módulos en Windows
REM Autor: Sistema de Asistencia CDATTG
REM Fecha: 27 de octubre de 2025
REM =============================================================================

echo.
echo ===============================================
echo   SISTEMA DE MIGRACIONES POR MODULOS
echo ===============================================
echo.

if "%1"=="" goto menu
if "%1"=="list" goto list
if "%1"=="all" goto all
if "%1"=="fresh" goto fresh
if "%1"=="help" goto help
goto module

:menu
echo Opciones disponibles:
echo.
echo   1. Listar modulos disponibles
echo   2. Migrar todos los modulos
echo   3. Resetear y migrar todo (fresh)
echo   4. Migrar un modulo especifico
echo   5. Ver ayuda
echo   6. Salir
echo.
set /p opcion="Selecciona una opcion (1-6): "

if "%opcion%"=="1" goto list
if "%opcion%"=="2" goto all
if "%opcion%"=="3" goto fresh
if "%opcion%"=="4" goto ask_module
if "%opcion%"=="5" goto help
if "%opcion%"=="6" goto end
echo Opcion invalida!
goto menu

:list
echo.
echo Listando modulos disponibles...
echo.
php artisan migrate:module --list
goto end

:all
echo.
echo Migrando todos los modulos en orden...
echo.
php artisan migrate:module --all
goto end

:fresh
echo.
echo *** ADVERTENCIA ***
echo Esta accion ELIMINARA todos los datos de la base de datos!
set /p confirm="Estas seguro? (S/N): "
if /i "%confirm%"=="S" (
    echo.
    echo Reseteando base de datos y migrando todo...
    echo.
    php artisan migrate:module --all --fresh
) else (
    echo Operacion cancelada.
)
goto end

:ask_module
echo.
echo Modulos disponibles:
echo   - batch_01_sistema_base
echo   - batch_02_permisos
echo   - batch_03_ubicaciones
echo   - batch_04_personas
echo   - batch_05_infraestructura
echo   - batch_06_programas
echo   - batch_07_instructores_aprendices
echo   - batch_08_fichas
echo   - batch_09_relaciones
echo   - batch_10_jornadas_horarios
echo   - batch_11_asistencias
echo   - batch_12_competencias
echo   - batch_13_evidencias
echo   - batch_14_logs_auditoria
echo   - batch_15_parametros
echo.
set /p module="Ingresa el nombre del modulo: "
goto module

:module
echo.
echo Migrando modulo: %module%
echo.
php artisan migrate:module %module%
goto end

:help
echo.
echo ===============================================
echo   AYUDA - MIGRACIONES POR MODULOS
echo ===============================================
echo.
echo USO:
echo   migrate_modules.bat              - Menu interactivo
echo   migrate_modules.bat list         - Listar modulos
echo   migrate_modules.bat all          - Migrar todo
echo   migrate_modules.bat fresh        - Resetear y migrar
echo   migrate_modules.bat [modulo]     - Migrar modulo especifico
echo.
echo EJEMPLOS:
echo   migrate_modules.bat
echo   migrate_modules.bat list
echo   migrate_modules.bat all
echo   migrate_modules.bat batch_01_sistema_base
echo.
goto end

:end
echo.
pause

