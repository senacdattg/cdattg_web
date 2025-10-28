<?php

/**
 * Script para limpiar roles duplicados en el sistema
 * Este script identifica usuarios con múltiples roles y los corrige
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Persona;
use App\Models\Instructor;
use App\Models\Aprendiz;
use Spatie\Permission\Models\Role;

class RoleCleanupService
{
    public function __construct()
    {
        // Inicializar Laravel
        $app = require_once 'bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    }

    /**
     * Ejecuta la limpieza completa de roles
     */
    public function executeCleanup()
    {
        echo "🧹 Iniciando limpieza de roles duplicados...\n\n";

        $this->identifyDuplicateRoles();
        $this->cleanupInstructorRoles();
        $this->cleanupAprendizRoles();
        $this->cleanupOrphanedRoles();

        echo "\n✅ Limpieza completada exitosamente!\n";
    }

    /**
     * Identifica usuarios con roles duplicados
     */
    private function identifyDuplicateRoles()
    {
        echo "🔍 Identificando usuarios con roles duplicados...\n";

        $usersWithMultipleRoles = DB::table('users')
            ->join('personas', 'users.persona_id', '=', 'personas.id')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('users.id', 'personas.nombre_completo', 'personas.numero_documento')
            ->groupBy('users.id', 'personas.nombre_completo', 'personas.numero_documento')
            ->havingRaw('COUNT(roles.id) > 1')
            ->get();

        echo "📊 Encontrados " . $usersWithMultipleRoles->count() . " usuarios con roles duplicados:\n";
        
        foreach ($usersWithMultipleRoles as $user) {
            $roles = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_id', $user->id)
                ->pluck('roles.name')
                ->toArray();
            
            echo "   - {$user->nombre_completo} ({$user->numero_documento}): " . implode(', ', $roles) . "\n";
        }
        echo "\n";
    }

    /**
     * Limpia roles de instructores
     */
    private function cleanupInstructorRoles()
    {
        echo "👨‍🏫 Limpiando roles de instructores...\n";

        $instructores = Instructor::with(['persona.user'])->get();
        $cleaned = 0;

        foreach ($instructores as $instructor) {
            if ($instructor->persona && $instructor->persona->user) {
                $user = $instructor->persona->user;
                
                // Sincronizar solo el rol de INSTRUCTOR
                $user->syncRoles(['INSTRUCTOR']);
                $cleaned++;
                
                echo "   ✅ {$instructor->persona->nombre_completo}: Solo rol INSTRUCTOR\n";
            }
        }

        echo "📈 Instructores limpiados: {$cleaned}\n\n";
    }

    /**
     * Limpia roles de aprendices
     */
    private function cleanupAprendizRoles()
    {
        echo "👨‍🎓 Limpiando roles de aprendices...\n";

        $aprendices = Aprendiz::with(['persona.user'])->get();
        $cleaned = 0;

        foreach ($aprendices as $aprendiz) {
            if ($aprendiz->persona && $aprendiz->persona->user) {
                $user = $aprendiz->persona->user;
                
                // Sincronizar solo el rol de APRENDIZ
                $user->syncRoles(['APRENDIZ']);
                $cleaned++;
                
                echo "   ✅ {$aprendiz->persona->nombre_completo}: Solo rol APRENDIZ\n";
            }
        }

        echo "📈 Aprendices limpiados: {$cleaned}\n\n";
    }

    /**
     * Limpia roles huérfanos (usuarios sin relación específica)
     */
    private function cleanupOrphanedRoles()
    {
        echo "🧹 Limpiando roles huérfanos...\n";

        // Usuarios que no son instructores ni aprendices pero tienen roles
        $orphanedUsers = User::whereDoesntHave('persona.instructor')
            ->whereDoesntHave('persona.aprendiz')
            ->whereHas('roles')
            ->with('persona')
            ->get();

        $cleaned = 0;

        foreach ($orphanedUsers as $user) {
            if ($user->persona) {
                // Remover todos los roles específicos, mantener solo VISITANTE si existe
                $user->syncRoles(['VISITANTE']);
                $cleaned++;
                
                echo "   ✅ {$user->persona->nombre_completo}: Solo rol VISITANTE\n";
            }
        }

        echo "📈 Usuarios huérfanos limpiados: {$cleaned}\n\n";
    }

    /**
     * Genera reporte final
     */
    public function generateReport()
    {
        echo "📋 REPORTE FINAL DE ROLES:\n";
        echo str_repeat("=", 50) . "\n";

        $roleStats = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name', DB::raw('COUNT(*) as count'))
            ->groupBy('roles.name')
            ->orderBy('count', 'desc')
            ->get();

        foreach ($roleStats as $stat) {
            echo "   {$stat->name}: {$stat->count} usuarios\n";
        }

        echo "\n";
    }
}

// Ejecutar el script
try {
    $cleanup = new RoleCleanupService();
    $cleanup->executeCleanup();
    $cleanup->generateReport();
} catch (Exception $e) {
    echo "❌ Error durante la limpieza: " . $e->getMessage() . "\n";
    exit(1);
}

