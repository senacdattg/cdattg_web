<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Instructor;
use App\Models\User;

class CheckInstructorRoles extends Command
{
    protected $signature = 'instructors:check-roles';
    protected $description = 'Verifica instructores que no tienen el rol INSTRUCTOR asignado';

    public function handle()
    {
        $this->info('🔍 Verificando instructores sin rol INSTRUCTOR...');
        $this->newLine();

        $instructores = Instructor::with(['persona.user'])->get();
        $sinRol = 0;
        $conRol = 0;

        foreach ($instructores as $instructor) {
            if ($instructor->persona && $instructor->persona->user) {
                $user = $instructor->persona->user;
                $nombre = trim($instructor->persona->primer_nombre . ' ' . $instructor->persona->primer_apellido);
                
                if ($user->hasRole('INSTRUCTOR')) {
                    $conRol++;
                    $this->line("✅ {$nombre}: Tiene rol INSTRUCTOR");
                } else {
                    $sinRol++;
                    $roles = $user->getRoleNames()->implode(', ');
                    $this->warn("❌ {$nombre}: Sin rol INSTRUCTOR (Roles: " . ($roles ?: 'Sin roles') . ")");
                }
            } else {
                $sinRol++;
                $this->error("⚠️  Instructor ID {$instructor->id}: Sin persona o usuario asociado");
            }
        }

        $this->newLine();
        $this->info("📊 RESUMEN:");
        $this->line("   - Con rol INSTRUCTOR: {$conRol}");
        $this->line("   - Sin rol INSTRUCTOR: {$sinRol}");
        $this->line("   - Total instructores: " . $instructores->count());

        if ($sinRol > 0) {
            $this->newLine();
            $this->warn("⚠️  Se encontraron {$sinRol} instructores sin el rol INSTRUCTOR asignado.");
            $this->info("💡 Ejecuta: php artisan roles:fix-instructors para corregir automáticamente.");
        }
    }
}

