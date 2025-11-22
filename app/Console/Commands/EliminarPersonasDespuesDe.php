<?php

namespace App\Console\Commands;

use App\Models\Persona;
use Illuminate\Console\Command;

class EliminarPersonasDespuesDe extends Command
{
    private const SEPARADOR = '========================================';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'personas:eliminar-despues-de '
        . '{id : ID límite (se eliminarán todos los registros con ID mayor a este)} '
        . '{--force : Ejecutar sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina registros de personas después de un ID específico';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $idLimite = (int) $this->argument('id');
        $force = $this->option('force');

        if (!$this->validarId($idLimite)) {
            return Command::FAILURE;
        }

        $totalAEliminar = Persona::where('id', '>', $idLimite)->count();
        $ultimoId = Persona::max('id');

        $this->mostrarInformacion($idLimite, $ultimoId, $totalAEliminar);

        if ($totalAEliminar === 0) {
            $this->info('No hay registros para eliminar.');
        } else {
            $this->mostrarEjemplos($idLimite);

            if ($this->confirmarEliminacion($force)) {
                $eliminados = $this->eliminarRegistros($idLimite, $totalAEliminar);
                $this->mostrarResultado($eliminados);
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Valida que el ID sea válido.
     */
    private function validarId(int $idLimite): bool
    {
        if ($idLimite <= 0) {
            $this->error('El ID debe ser un número positivo.');

            return false;
        }

        return true;
    }

    /**
     * Muestra la información de la eliminación.
     */
    private function mostrarInformacion(int $idLimite, ?int $ultimoId, int $totalAEliminar): void
    {
        $this->info(self::SEPARADOR);
        $this->info('ELIMINACIÓN DE PERSONAS');
        $this->info(self::SEPARADOR);
        $this->line("ID límite: {$idLimite}");
        $this->line("Último ID en la tabla: {$ultimoId}");
        $this->line("Total de registros a eliminar: {$totalAEliminar}");
        $this->info(self::SEPARADOR);
    }

    /**
     * Muestra ejemplos de registros a eliminar.
     */
    private function mostrarEjemplos(int $idLimite): void
    {
        $ejemplos = Persona::where('id', '>', $idLimite)
            ->orderBy('id', 'asc')
            ->limit(5)
            ->get(['id', 'numero_documento', 'primer_nombre', 'primer_apellido']);

        $this->newLine();
        $this->line('Ejemplos de registros a eliminar:');
        foreach ($ejemplos as $persona) {
            $nombreCompleto = "{$persona->primer_nombre} {$persona->primer_apellido}";
            $this->line(
                "  - ID: {$persona->id} | {$persona->numero_documento} | {$nombreCompleto}"
            );
        }
    }

    /**
     * Solicita confirmación para la eliminación.
     */
    private function confirmarEliminacion(bool $force): bool
    {
        if ($force) {
            return true;
        }

        if (!$this->confirm('¿Deseas continuar con la eliminación?', false)) {
            $this->warn('Operación cancelada.');

            return false;
        }

        return true;
    }

    /**
     * Muestra el resultado de la eliminación.
     */
    private function mostrarResultado(int $eliminados): void
    {
        $this->info(self::SEPARADOR);
        $this->info('¡Eliminación completada!');
        $this->info("Total eliminado: {$eliminados}");
        $this->info(self::SEPARADOR);
    }

    /**
     * Elimina los registros en lotes.
     */
    private function eliminarRegistros(int $idLimite, int $totalAEliminar): int
    {
        $this->newLine();
        $this->info('Eliminando registros...');

        $bar = $this->output->createProgressBar($totalAEliminar);
        $bar->start();

        $eliminados = 0;
        $lote = 100;

        do {
            $personas = Persona::where('id', '>', $idLimite)
                ->limit($lote)
                ->get();

            $count = $personas->count();

            if ($count > 0) {
                foreach ($personas as $persona) {
                    // Esto también eliminará el usuario asociado por el evento deleting
                    $persona->delete();
                    $eliminados++;
                    $bar->advance();
                }
            }
        } while ($count > 0);

        $bar->finish();
        $this->newLine(2);

        return $eliminados;
    }
}
