<?php

namespace App\Console\Commands;

use App\Models\Inventario\Producto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillSenaBarcodes extends Command
{
    protected $signature = 'productos:backfill-sena-barcodes {--dry-run : Muestra qué haría sin guardar cambios} {--chunk=500 : Tamaño de lote para procesar}';

    protected $description = 'Genera y asigna códigos SENA de 11 dígitos incrementales para productos que no lo tengan';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $chunk = (int) $this->option('chunk');

        $this->info('Iniciando asignación de códigos SENA de 11 dígitos' . ($dryRun ? ' (dry-run)' : ''));

        // Asegurar formato uniforme: todos los existentes deben ser 11 dígitos zero-padded
        $normalizados = 0;
        Producto::whereNotNull('codigo_barras_sena')
            ->select('id', 'codigo_barras_sena')
            ->chunkById($chunk, function ($productos) use (&$normalizados, $dryRun) {
                foreach ($productos as $producto) {
                    $soloDigitos = preg_replace('/\D/', '', (string) $producto->codigo_barras_sena);
                    if (strlen($soloDigitos) > 0 && strlen($soloDigitos) !== 11) {
                        $nuevo = str_pad(substr($soloDigitos, -11), 11, '0', STR_PAD_LEFT);
                        if (!$dryRun) {
                            $producto->codigo_barras_sena = $nuevo;
                            $producto->saveQuietly();
                        }
                        $normalizados++;
                    }
                }
            });

        if ($normalizados > 0) {
            $this->info("Normalizados {$normalizados} códigos existentes a 11 dígitos");
        }

        $procesados = 0;
        $asignados = 0;

        Producto::whereNull('codigo_barras_sena')
            ->select('id')
            ->chunkById($chunk, function ($productos) use (&$procesados, &$asignados, $dryRun) {
                foreach ($productos as $producto) {
                    $procesados++;
                    $siguiente = $this->generarSiguienteCodigo();
                    if ($dryRun) {
                        $this->line("[dry-run] Producto {$producto->id} -> {$siguiente}");
                    } else {
                        // Reintentos simples ante colisión por índice único
                        $maxIntentos = 3;
                        for ($i = 0; $i < $maxIntentos; $i++) {
                            try {
                                $ok = DB::transaction(function () use ($producto, $siguiente) {
                                    $modelo = Producto::lockForUpdate()->find($producto->id);
                                    if (!$modelo) {
                                        return false;
                                    }
                                    if ($modelo->codigo_barras_sena) {
                                        return true; // ya asignado por otro proceso
                                    }
                                    $modelo->codigo_barras_sena = $siguiente;
                                    $modelo->save();
                                    return true;
                                });
                                if ($ok) {
                                    $asignados++;
                                    break;
                                }
                            } catch (\Throwable $e) {
                                // Si hubo colisión por único, recalcular siguiente y reintentar
                                if ($i === $maxIntentos - 1) {
                                    throw $e;
                                }
                            }
                        }
                    }
                }
            });

        $this->info("Procesados: {$procesados}, Asignados: {$asignados}");
        return self::SUCCESS;
    }

    private function generarSiguienteCodigo(): string
    {
        return DB::transaction(function () {
            // Al estar zero-padded a 11, MAX lexicográfico == MAX numérico
            $max = Producto::whereNotNull('codigo_barras_sena')
                ->select('codigo_barras_sena')
                ->max('codigo_barras_sena');

            $soloDigitos = preg_replace('/\D/', '', (string) $max);
            $num = $soloDigitos === '' ? 0 : (int) $soloDigitos;
            $next = $num + 1;
            return str_pad((string) $next, 11, '0', STR_PAD_LEFT);
        }, 3);
    }
}


