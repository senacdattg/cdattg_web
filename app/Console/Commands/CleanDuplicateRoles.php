<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Persona;
use App\Models\Instructor;
use App\Models\Aprendiz;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class CleanDuplicateRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:cleanup {--dry-run : Solo mostrar qué se haría sin ejecutar cambios}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia roles duplicados y asigna roles correctos según las relaciones del sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Iniciando limpieza de roles duplicados...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('🔍 MODO DRY-RUN: Solo se mostrarán los cambios que se harían');
            $this->newLine();
        }

        // Identificar usuarios con roles duplicados
        $this->identifyDuplicateRoles();
        
        // Limpiar roles de instructores
        $this->cleanupInstructorRoles($dryRun);
        
        // Limpiar roles de aprendices
        $this->cleanupAprendizRoles($dryRun);
        
        // Limpiar roles huérfanos
        $this->cleanupOrphanedRoles($dryRun);
        
        // Generar reporte final
        $this->generateReport();

        $this->newLine();
        $this->info('✅ Limpieza completada exitosamente!');
    }

    /**
     * Identifica usuarios con roles duplicados
     */
    private function identifyDuplicateRoles()
    {
        $this->info('🔍 Identificando usuarios con roles duplicados...');

        $usersWithMultipleRoles = DB::table('users')
            ->join('personas', 'users.persona_id', '=', 'personas.id')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('users.id', 'personas.primer_nombre', 'personas.segundo_nombre', 'personas.primer_apellido', 'personas.segundo_apellido', 'personas.numero_documento')
            ->groupBy('users.id', 'personas.primer_nombre', 'personas.segundo_nombre', 'personas.primer_apellido', 'personas.segundo_apellido', 'personas.numero_documento')
            ->havingRaw('COUNT(roles.id) > 1')
            ->get();

        $this->info("📊 Encontrados {$usersWithMultipleRoles->count()} usuarios con roles duplicados:");
        
        foreach ($usersWithMultipleRoles as $user) {
            $roles = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_id', $user->id)
                ->pluck('roles.name')
                ->toArray();
            
            $nombreCompleto = trim($user->primer_nombre . ' ' . $user->segundo_nombre . ' ' . $user->primer_apellido . ' ' . $user->segundo_apellido);
            $this->line("   - {$nombreCompleto} ({$user->numero_documento}): " . implode(', ', $roles));
        }
        $this->newLine();
    }

    /**
     * Limpia roles de instructores
     */
    private function cleanupInstructorRoles($dryRun = false)
    {
        $this->info('👨‍🏫 Limpiando roles de instructores...');

        $instructores = Instructor::with(['persona.user'])->get();
        $cleaned = 0;

        foreach ($instructores as $instructor) {
            if ($instructor->persona && $instructor->persona->user) {
                $user = $instructor->persona->user;
                $currentRoles = $user->getRoleNames()->toArray();
                
                if (!$dryRun) {
                    // Sincronizar solo el rol de INSTRUCTOR
                    $user->syncRoles(['INSTRUCTOR']);
                }
                
                $cleaned++;
                $this->line("   ✅ {$instructor->persona->nombre_completo}: " . 
                    ($dryRun ? "Se asignaría solo INSTRUCTOR" : "Solo rol INSTRUCTOR"));
            }
        }

        $this->info("📈 Instructores procesados: {$cleaned}");
        $this->newLine();
    }

    /**
     * Limpia roles de aprendices
     */
    private function cleanupAprendizRoles($dryRun = false)
    {
        $this->info('👨‍🎓 Limpiando roles de aprendices...');

        $aprendices = Aprendiz::with(['persona.user'])->get();
        $cleaned = 0;

        foreach ($aprendices as $aprendiz) {
            if ($aprendiz->persona && $aprendiz->persona->user) {
                $user = $aprendiz->persona->user;
                
                if (!$dryRun) {
                    // Sincronizar solo el rol de APRENDIZ
                    $user->syncRoles(['APRENDIZ']);
                }
                
                $cleaned++;
                $this->line("   ✅ {$aprendiz->persona->nombre_completo}: " . 
                    ($dryRun ? "Se asignaría solo APRENDIZ" : "Solo rol APRENDIZ"));
            }
        }

        $this->info("📈 Aprendices procesados: {$cleaned}");
        $this->newLine();
    }

    /**
     * Limpia roles huérfanos (usuarios sin relación específica)
     */
    private function cleanupOrphanedRoles($dryRun = false)
    {
        $this->info('🧹 Limpiando roles huérfanos...');

        // Usuarios que no son instructores ni aprendices pero tienen roles
        $orphanedUsers = User::whereDoesntHave('persona.instructor')
            ->whereDoesntHave('persona.aprendiz')
            ->whereHas('roles')
            ->with('persona')
            ->get();

        $cleaned = 0;

        foreach ($orphanedUsers as $user) {
            if ($user->persona) {
                if (!$dryRun) {
                    // Remover todos los roles específicos, mantener solo VISITANTE si existe
                    $user->syncRoles(['VISITANTE']);
                }
                
                $cleaned++;
                $this->line("   ✅ {$user->persona->nombre_completo}: " . 
                    ($dryRun ? "Se asignaría solo VISITANTE" : "Solo rol VISITANTE"));
            }
        }

        $this->info("📈 Usuarios huérfanos procesados: {$cleaned}");
        $this->newLine();
    }

    /**
     * Genera reporte final
     */
    private function generateReport()
    {
        $this->info('📋 REPORTE FINAL DE ROLES:');
        $this->line(str_repeat('=', 50));

        $roleStats = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name', DB::raw('COUNT(*) as count'))
            ->groupBy('roles.name')
            ->orderBy('count', 'desc')
            ->get();

        foreach ($roleStats as $stat) {
            $this->line("   {$stat->name}: {$stat->count} usuarios");
        }

        $this->newLine();
    }
}