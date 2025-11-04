<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class CheckUserPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:check-permissions {userId? : ID del usuario a verificar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica los permisos de un usuario específico o lista todos los usuarios con sus roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('userId');

        if ($userId) {
            // Mostrar permisos de un usuario específico
            $this->showUserPermissions($userId);
        } else {
            // Listar todos los usuarios con sus roles
            $this->listAllUsers();
        }

        return 0;
    }

    private function showUserPermissions($userId)
    {
        $user = User::with(['persona', 'roles', 'permissions'])->find($userId);

        if (!$user) {
            $this->error("❌ Usuario con ID {$userId} no encontrado.");
            return;
        }

        $this->info("👤 Usuario: {$user->persona->nombre_completo} (ID: {$userId})");
        $this->info("📧 Email: {$user->email}");
        
        // Mostrar roles
        $roles = $user->roles->pluck('name')->toArray();
        if (empty($roles)) {
            $this->warn("⚠️  Sin roles asignados");
        } else {
            $this->info("🎭 Roles asignados: " . implode(', ', $roles));
        }

        // Obtener todos los permisos (directos y de roles)
        $allPermissions = $user->getAllPermissions();
        
        $this->info("✅ Permisos totales: " . $allPermissions->count());
        
        if ($allPermissions->isEmpty()) {
            $this->warn("⚠️  No tiene permisos asignados");
            return;
        }

        // Agrupar permisos por categoría
        $grouped = $this->groupPermissions($allPermissions);

        foreach ($grouped as $category => $permissions) {
            $this->newLine();
            $this->info("📦 {$category} ({$permissions->count()}):");
            foreach ($permissions as $permission) {
                $this->line("   ✓ {$permission->name}");
            }
        }
    }

    private function listAllUsers()
    {
        $users = User::with(['persona', 'roles'])->get();

        if ($users->isEmpty()) {
            $this->warn("⚠️  No hay usuarios registrados.");
            return;
        }

        $this->info("👥 LISTADO DE USUARIOS Y ROLES\n");

        $tableData = [];
        foreach ($users as $user) {
            $roles = $user->roles->pluck('name')->implode(', ') ?: 'Sin roles';
            $permissionsCount = $user->getAllPermissions()->count();
            
            $tableData[] = [
                $user->id,
                $user->persona->nombre_completo ?? 'Sin nombre',
                $user->email,
                $roles,
                $permissionsCount,
            ];
        }

        $this->table(
            ['ID', 'Nombre', 'Email', 'Roles', 'Permisos'],
            $tableData
        );

        $this->newLine();
        $this->info("💡 Usa: php artisan user:check-permissions {userId} para ver los permisos detallados de un usuario");
    }

    private function groupPermissions($permissions)
    {
        $groups = [
            'PARÁMETROS Y TEMAS' => collect(),
            'UBICACIÓN' => collect(),
            'INFRAESTRUCTURA' => collect(),
            'INSTRUCTORES' => collect(),
            'FICHAS' => collect(),
            'PERSONAS' => collect(),
            'INVENTARIO' => collect(),
            'APRENDICES' => collect(),
            'PROGRAMAS' => collect(),
            'COMPETENCIAS Y RAP' => collect(),
            'OTROS' => collect(),
        ];

        foreach ($permissions as $permission) {
            $name = strtoupper($permission->name);
            
            if (str_contains($name, 'PARAMETRO') || str_contains($name, 'TEMA')) {
                $groups['PARÁMETROS Y TEMAS']->push($permission);
            } elseif (str_contains($name, 'REGIONAL') || str_contains($name, 'MUNICIPIO')) {
                $groups['UBICACIÓN']->push($permission);
            } elseif (str_contains($name, 'CENTRO') || str_contains($name, 'SEDE') || 
                      str_contains($name, 'BLOQUE') || str_contains($name, 'PISO') || 
                      str_contains($name, 'AMBIENTE')) {
                $groups['INFRAESTRUCTURA']->push($permission);
            } elseif (str_contains($name, 'INSTRUCTOR') || str_contains($name, 'ESPECIALIDAD')) {
                $groups['INSTRUCTORES']->push($permission);
            } elseif (str_contains($name, 'FICHA')) {
                $groups['FICHAS']->push($permission);
            } elseif (str_contains($name, 'PERSONA')) {
                $groups['PERSONAS']->push($permission);
            } elseif (str_contains($name, 'PRODUCTO') || str_contains($name, 'CATALOGO') || 
                      str_contains($name, 'CARRITO') || str_contains($name, 'CATEGORIA') || 
                      str_contains($name, 'MARCA') || str_contains($name, 'PROVEEDOR') || 
                      str_contains($name, 'CONTRATO') || str_contains($name, 'ORDEN') || 
                      str_contains($name, 'PRESTAMO') || str_contains($name, 'DEVOLUCION') || 
                      str_contains($name, 'ENTRADA') || str_contains($name, 'SALIDA') || 
                      str_contains($name, 'INVENTARIO')) {
                $groups['INVENTARIO']->push($permission);
            } elseif (str_contains($name, 'APRENDIZ')) {
                $groups['APRENDICES']->push($permission);
            } elseif (str_contains($name, 'PROGRAMA') || $name === 'programa.index' || 
                      $name === 'programa.show' || $name === 'programa.create' || 
                      $name === 'programa.edit' || $name === 'programa.delete' || 
                      $name === 'programa.search') {
                $groups['PROGRAMAS']->push($permission);
            } elseif (str_contains($name, 'COMPETENCIA') || str_contains($name, 'RAP') || 
                      str_contains($name, 'RESULTADO')) {
                $groups['COMPETENCIAS Y RAP']->push($permission);
            } else {
                $groups['OTROS']->push($permission);
            }
        }

        // Filtrar grupos vacíos
        return collect($groups)->filter(function ($group) {
            return $group->isNotEmpty();
        });
    }
}
