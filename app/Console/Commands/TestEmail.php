<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmail extends Command
{
    protected $signature = 'test:email {email : Email destino para la prueba}';
    protected $description = 'Prueba el envío de correos SMTP';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('Probando envío de correo a: ' . $email);
        $this->info('Configuración SMTP:');
        $this->info('  MAILER: ' . config('mail.default'));
        $this->info('  HOST: ' . config('mail.mailers.smtp.host'));
        $this->info('  PORT: ' . config('mail.mailers.smtp.port'));
        $this->info('  ENCRYPTION: ' . config('mail.mailers.smtp.encryption'));
        $this->info('  FROM: ' . config('mail.from.address'));
        $this->info('  USERNAME: ' . (config('mail.mailers.smtp.username') ? 'Configurado' : 'NO CONFIGURADO'));
        
        try {
            Mail::raw('Este es un correo de prueba desde Laravel. Si recibes esto, el SMTP está funcionando correctamente.', function ($message) use ($email) {
                $message->to($email)
                    ->subject('Prueba de Correo - Laravel');
            });
            
            $this->info('✅ Correo enviado exitosamente!');
            Log::info('Correo de prueba enviado exitosamente', ['email' => $email]);
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error al enviar correo: ' . $e->getMessage());
            Log::error('Error en prueba de correo', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return 1;
        }
    }
}
