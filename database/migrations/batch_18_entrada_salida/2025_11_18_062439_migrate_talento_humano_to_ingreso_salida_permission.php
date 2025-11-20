<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    private const MODEL_TYPE_USER = 'App\Models\User';

    /**
     * Run the migrations.
     *
     * Migra el permiso "VER TALENTO HUMANO" a "VER INGRESO SALIDA"
     * de forma segura sin perder datos existentes.
     */
    public function up(): void
    {
        // Verify that the permissions table exists
        if (!Schema::hasTable('permissions')) {
            Log::warning('La tabla permissions no existe. Esta migración requiere que batch_02_permisos haya sido ejecutado primero.');
            return;
        }

        DB::transaction(function () {
            $nuevoPermiso = $this->crearNuevoPermiso();
            $permisoAntiguo = Permission::where('name', 'VER TALENTO HUMANO')->first();

            if ($permisoAntiguo) {
                $this->migrarPermisosDeRoles($permisoAntiguo, $nuevoPermiso);
                $this->migrarPermisosDeUsuarios($permisoAntiguo, $nuevoPermiso);
                $this->registrarMigracionCompletada($permisoAntiguo, $nuevoPermiso);
            } else {
                Log::warning('Permiso VER TALENTO HUMANO no encontrado, solo se creó el nuevo permiso');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * Revierte la migración asignando el permiso antiguo a quienes tienen el nuevo.
     * NO elimina el nuevo permiso para mantener compatibilidad.
     */
    public function down(): void
    {
        // Verify that the permissions table exists
        if (!Schema::hasTable('permissions')) {
            Log::warning('La tabla permissions no existe. Esta migración requiere que batch_02_permisos haya sido ejecutado primero.');
            return;
        }

        DB::transaction(function () {
            $permisoNuevo = Permission::where('name', 'VER INGRESO SALIDA')->first();
            $permisoAntiguo = Permission::firstOrCreate(
                ['name' => 'VER TALENTO HUMANO'],
                ['guard_name' => 'web']
            );

            if ($permisoNuevo) {
                $this->restaurarPermisosDeRoles($permisoNuevo, $permisoAntiguo);
                $this->restaurarPermisosDeUsuarios($permisoNuevo, $permisoAntiguo);
                $this->registrarReversionCompletada($permisoNuevo, $permisoAntiguo);
            }
        });
    }

    /**
     * Crea el nuevo permiso si no existe
     */
    private function crearNuevoPermiso(): Permission
    {
        $nuevoPermiso = Permission::firstOrCreate(
            ['name' => 'VER INGRESO SALIDA'],
            ['guard_name' => 'web']
        );

        Log::info('Permiso VER INGRESO SALIDA creado o encontrado', [
            'permission_id' => $nuevoPermiso->id
        ]);

        return $nuevoPermiso;
    }

    /**
     * Migra permisos de roles del antiguo al nuevo
     */
    private function migrarPermisosDeRoles(Permission $permisoAntiguo, Permission $nuevoPermiso): void
    {
        $rolesConPermisoAntiguo = $permisoAntiguo->roles;

        foreach ($rolesConPermisoAntiguo as $rol) {
            if (!$rol->hasPermissionTo($nuevoPermiso)) {
                $rol->givePermissionTo($nuevoPermiso);
                Log::info('Nuevo permiso asignado al rol', [
                    'rol' => $rol->name,
                    'permiso' => $nuevoPermiso->name
                ]);
            }
        }
    }

    /**
     * Migra permisos de usuarios del antiguo al nuevo
     */
    private function migrarPermisosDeUsuarios(Permission $permisoAntiguo, Permission $nuevoPermiso): void
    {
        $usuariosConPermisoAntiguo = DB::table('model_has_permissions')
            ->where('permission_id', $permisoAntiguo->id)
            ->where('model_type', self::MODEL_TYPE_USER)
            ->get();

        foreach ($usuariosConPermisoAntiguo as $usuarioPermiso) {
            $usuario = \App\Models\User::find($usuarioPermiso->model_id);
            if ($usuario && !$usuario->hasPermissionTo($nuevoPermiso)) {
                $usuario->givePermissionTo($nuevoPermiso);
                Log::info('Nuevo permiso asignado al usuario', [
                    'user_id' => $usuario->id,
                    'permiso' => $nuevoPermiso->name
                ]);
            }
        }
    }

    /**
     * Registra el log de migración completada
     */
    private function registrarMigracionCompletada(Permission $permisoAntiguo, Permission $nuevoPermiso): void
    {
        $rolesMigrados = $permisoAntiguo->roles->count();
        $usuariosMigrados = DB::table('model_has_permissions')
            ->where('permission_id', $permisoAntiguo->id)
            ->where('model_type', self::MODEL_TYPE_USER)
            ->count();

        Log::info('Migración de permisos completada', [
            'permiso_antiguo' => $permisoAntiguo->name,
            'permiso_nuevo' => $nuevoPermiso->name,
            'roles_migrados' => $rolesMigrados,
            'usuarios_migrados' => $usuariosMigrados
        ]);
    }

    /**
     * Restaura permisos de roles del nuevo al antiguo
     */
    private function restaurarPermisosDeRoles(Permission $permisoNuevo, Permission $permisoAntiguo): void
    {
        $rolesConPermisoNuevo = $permisoNuevo->roles;

        foreach ($rolesConPermisoNuevo as $rol) {
            if (!$rol->hasPermissionTo($permisoAntiguo)) {
                $rol->givePermissionTo($permisoAntiguo);
                Log::info('Permiso antiguo restaurado al rol', [
                    'rol' => $rol->name,
                    'permiso' => $permisoAntiguo->name
                ]);
            }
        }
    }

    /**
     * Restaura permisos de usuarios del nuevo al antiguo
     */
    private function restaurarPermisosDeUsuarios(Permission $permisoNuevo, Permission $permisoAntiguo): void
    {
        $usuariosConPermisoNuevo = DB::table('model_has_permissions')
            ->where('permission_id', $permisoNuevo->id)
            ->where('model_type', self::MODEL_TYPE_USER)
            ->get();

        foreach ($usuariosConPermisoNuevo as $usuarioPermiso) {
            $usuario = \App\Models\User::find($usuarioPermiso->model_id);
            if ($usuario && !$usuario->hasPermissionTo($permisoAntiguo)) {
                $usuario->givePermissionTo($permisoAntiguo);
                Log::info('Permiso antiguo restaurado al usuario', [
                    'user_id' => $usuario->id,
                    'permiso' => $permisoAntiguo->name
                ]);
            }
        }
    }

    /**
     * Registra el log de reversión completada
     */
    private function registrarReversionCompletada(Permission $permisoNuevo, Permission $permisoAntiguo): void
    {
        $rolesRestaurados = $permisoNuevo->roles->count();
        $usuariosRestaurados = DB::table('model_has_permissions')
            ->where('permission_id', $permisoNuevo->id)
            ->where('model_type', self::MODEL_TYPE_USER)
            ->count();

        Log::info('Reversión de migración completada', [
            'permiso_antiguo' => $permisoAntiguo->name,
            'permiso_nuevo' => $permisoNuevo->name,
            'roles_restaurados' => $rolesRestaurados,
            'usuarios_restaurados' => $usuariosRestaurados
        ]);
    }
};
