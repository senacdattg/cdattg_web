<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\FichaCaracterizacion;

class TestUserPermissions extends Command
{
    protected $signature = 'test:user-permissions {user_id} {ficha_id}';
    protected $description = 'Test user permissions for ficha deletion';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $fichaId = $this->argument('ficha_id');

        $user = User::find($userId);
        $ficha = FichaCaracterizacion::find($fichaId);

        if (!$user) {
            $this->error("Usuario con ID {$userId} no encontrado");
            return;
        }

        if (!$ficha) {
            $this->error("Ficha con ID {$fichaId} no encontrada");
            return;
        }

        $this->info("=== INFORMACIÓN DEL USUARIO ===");
        $this->info("ID: {$user->id}");
        $this->info("Nombre: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("Roles: " . $user->roles->pluck('name')->implode(', '));
        
        $this->info("\n=== PERMISOS DEL USUARIO ===");
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        foreach ($permissions as $permission) {
            $this->line("- {$permission}");
        }

        $this->info("\n=== VERIFICACIÓN DE AUTORIZACIÓN ===");
        
        // Verificar si es superadmin
        $esSuperadmin = $user->hasRole('SUPERADMIN');
        $this->info("Es SUPERADMIN: " . ($esSuperadmin ? 'SÍ' : 'NO'));
        
        // Verificar permiso específico
        $tienePermiso = $user->can('ELIMINAR FICHA CARACTERIZACION');
        $this->info("Tiene permiso ELIMINAR FICHA CARACTERIZACION: " . ($tienePermiso ? 'SÍ' : 'NO'));
        
        // Verificar usando política
        try {
            $user->can('delete', $ficha);
            $this->info("Puede eliminar ficha (política): SÍ");
        } catch (\Exception $e) {
            $this->info("Puede eliminar ficha (política): NO - " . $e->getMessage());
        }

        $this->info("\n=== INFORMACIÓN DE LA FICHA ===");
        $this->info("ID: {$ficha->id}");
        $this->info("Número: {$ficha->ficha}");
        $this->info("Estado: " . ($ficha->status ? 'Activa' : 'Inactiva'));
        $this->info("Tiene aprendices: " . ($ficha->tieneAprendices() ? 'SÍ (' . $ficha->contarAprendices() . ')' : 'NO'));
    }
}