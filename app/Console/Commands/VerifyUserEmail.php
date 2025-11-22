<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class VerifyUserEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:verify-email {email : El correo electrónico del usuario}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica manualmente el correo electrónico de un usuario (solo para desarrollo)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No se encontró un usuario con el correo: {$email}");
            return Command::FAILURE;
        }

        if ($user->hasVerifiedEmail()) {
            $this->info("El correo {$email} ya está verificado.");
            return Command::SUCCESS;
        }

        $user->markEmailAsVerified();

        $this->info("✓ Correo electrónico verificado exitosamente para: {$email}");
        $this->info("  Usuario ID: {$user->id}");
        $this->info("  Fecha de verificación: {$user->email_verified_at}");

        return Command::SUCCESS;
    }
}
