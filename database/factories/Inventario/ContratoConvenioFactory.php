<?php

namespace Database\Factories\Inventario;

use App\Models\Inventario\ContratoConvenio;
use App\Models\Inventario\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventario\ContratoConvenio>
 */
class ContratoConvenioFactory extends Factory
{
    protected $model = ContratoConvenio::class;

    public function definition(): array
    {
        static $usedNames = [];
        static $counter = 1;
        
        $mesesAtras = rand(0, 3);
        $fechaInicio = Carbon::now()->subMonths($mesesAtras);
        $fechaFin = (clone $fechaInicio)->addYear();

        $palabras = ['CONTRATO', 'CONVENIO', 'SUMINISTRO', 'SERVICIOS', 'EQUIPOS', 'ADQUISICIÓN', 'COMPRA', 'MANTENIMIENTO'];
        
        // Generar nombre único
        do {
            $name = strtoupper(
                $palabras[array_rand($palabras)] . ' ' . 
                $palabras[array_rand($palabras)] . ' ' . 
                rand(2024, 2025) . '-' . 
                str_pad($counter, 3, '0', STR_PAD_LEFT)
            );
            $counter++;
        } while (in_array($name, $usedNames));
        $usedNames[] = $name;
        
        $letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codigo = strtoupper(
            $letras[rand(0, 25)] . $letras[rand(0, 25)] . '-' . 
            rand(10, 99) . $letras[rand(0, 25)] . $letras[rand(0, 25)] . '-' . 
            rand(1000, 9999)
        );

        return [
            'name' => $name,
            'codigo' => $codigo,
            'proveedor_id' => Proveedor::factory(),
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'fecha_fin' => $fechaFin->format('Y-m-d'),
            'estado_id' => 1,
            'user_create_id' => 1,
            'user_update_id' => 1,
        ];
    }
}


