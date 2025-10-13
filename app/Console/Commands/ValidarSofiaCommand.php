<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AspiranteComplementario;
use App\Models\Persona;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ValidarSofiaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sofia:validar {complementario_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validar estado de registro en SenaSofiaPlus para aspirantes de un programa complementario';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $complementarioId = $this->argument('complementario_id');

        // Obtener aspirantes que necesitan validación (estado_sofia = 0 o 2)
        $aspirantes = AspiranteComplementario::with('persona')
            ->where('complementario_id', $complementarioId)
            ->whereHas('persona', function($query) {
                $query->whereIn('estado_sofia', [0, 2]);
            })
            ->get();

        if ($aspirantes->isEmpty()) {
            $this->info('No hay aspirantes que necesiten validación.');
            return;
        }

        $this->info("Validando {$aspirantes->count()} aspirantes...");

        $bar = $this->output->createProgressBar($aspirantes->count());
        $bar->start();

        $exitosos = 0;
        $errores = 0;

        foreach ($aspirantes as $aspirante) {
            try {
                $resultado = $this->validarAspirante($aspirante->persona->numero_documento);

                // Actualizar estado basado en resultado
                $nuevoEstado = $this->determinarEstadoSofia($resultado);
                $aspirante->persona->update(['estado_sofia' => $nuevoEstado]);

                if ($nuevoEstado === 1) {
                    $exitosos++;
                }

                $this->info("Cédula {$aspirante->persona->numero_documento}: {$resultado}");

            } catch (\Exception $e) {
                $this->error("Error con cédula {$aspirante->persona->numero_documento}: {$e->getMessage()}");
                $errores++;
            }

            $bar->advance();

            // Delay para evitar rate limiting
            sleep(2);
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Validación completada:");
        $this->info("✅ Registrados: {$exitosos}");
        $this->info("❌ Errores: {$errores}");
    }

    private function validarAspirante($cedula)
    {
        // Ejecutar script de Node.js
        $scriptPath = base_path('resources/js/sofia-validator.js');

        $process = new Process(['node', $scriptPath, $cedula]);
        $process->setTimeout(30000); // 30 segundos timeout
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return trim($process->getOutput());
    }

    private function determinarEstadoSofia($resultado)
    {
        $resultadoLower = strtolower($resultado);

        if (str_contains($resultadoLower, 'ya existe') ||
            str_contains($resultadoLower, 'ya cuentas con un registro')) {
            return 1; // Registrado
        } elseif (str_contains($resultadoLower, 'actualizar tu documento') ||
                  str_contains($resultadoLower, 'requiere_cambio')) {
            return 2; // Requiere cambio de cédula
        } elseif (str_contains($resultadoLower, 'creado') ||
                  str_contains($resultadoLower, 'cuenta_creada')) {
            return 0; // No registrado (pudo crear cuenta)
        } else {
            return 2; // Error o desconocido -> requiere cambio
        }
    }
}
