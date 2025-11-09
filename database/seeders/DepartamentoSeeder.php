<?php

namespace Database\Seeders;

use App\Models\Departamento;
use Illuminate\Database\Seeder;
use Database\Seeders\Concerns\TruncatesTables;

class DepartamentoSeeder extends Seeder
{
    use TruncatesTables;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateModel(Departamento::class);

        $departamentos = [
            ['id' => 5, 'departamento' => 'ANTIOQUIA', 'pais_id' => 1],
            ['id' => 8, 'departamento' => 'ATLÁNTICO', 'pais_id' => 1],
            ['id' => 11, 'departamento' => 'BOGOTÁ, D.C.', 'pais_id' => 1],
            ['id' => 13, 'departamento' => 'BOLÍVAR', 'pais_id' => 1],
            ['id' => 15, 'departamento' => 'BOYACÁ', 'pais_id' => 1],
            ['id' => 17, 'departamento' => 'CALDAS', 'pais_id' => 1],
            ['id' => 18, 'departamento' => 'CAQUETÁ', 'pais_id' => 1],
            ['id' => 19, 'departamento' => 'CAUCA', 'pais_id' => 1],
            ['id' => 20, 'departamento' => 'CESAR', 'pais_id' => 1],
            ['id' => 23, 'departamento' => 'CÓRDOBA', 'pais_id' => 1],
            ['id' => 25, 'departamento' => 'CUNDINAMARCA', 'pais_id' => 1],
            ['id' => 27, 'departamento' => 'CHOCÓ', 'pais_id' => 1],
            ['id' => 41, 'departamento' => 'HUILA', 'pais_id' => 1],
            ['id' => 44, 'departamento' => 'LA GUAJIRA', 'pais_id' => 1],
            ['id' => 47, 'departamento' => 'MAGDALENA', 'pais_id' => 1],
            ['id' => 50, 'departamento' => 'META', 'pais_id' => 1, 'status' => 1],
            ['id' => 52, 'departamento' => 'NARIÑO', 'pais_id' => 1],
            ['id' => 54, 'departamento' => 'NORTE DE SANTANDER', 'pais_id' => 1],
            ['id' => 63, 'departamento' => 'QUINDIO', 'pais_id' => 1],
            ['id' => 66, 'departamento' => 'RISARALDA', 'pais_id' => 1],
            ['id' => 68, 'departamento' => 'SANTANDER', 'pais_id' => 1],
            ['id' => 70, 'departamento' => 'SUCRE', 'pais_id' => 1],
            ['id' => 73, 'departamento' => 'TOLIMA', 'pais_id' => 1],
            ['id' => 76, 'departamento' => 'VALLE DEL CAUCA', 'pais_id' => 1],
            ['id' => 81, 'departamento' => 'ARAUCA', 'pais_id' => 1],
            ['id' => 85, 'departamento' => 'CASANARE', 'pais_id' => 1],
            ['id' => 86, 'departamento' => 'PUTUMAYO', 'pais_id' => 1],
            ['id' => 88, 'departamento' => 'ARCHIPIÉLAGO DE SAN ANDRÉS, PROVIDENCIA Y SANTA CATALINA', 'pais_id' => 1],
            ['id' => 91, 'departamento' => 'AMAZONAS', 'pais_id' => 1],
            ['id' => 94, 'departamento' => 'GUAINÍA', 'pais_id' => 1],
            ['id' => 95, 'departamento' => 'GUAVIARE', 'pais_id' => 1, 'status' => 1],
            ['id' => 97, 'departamento' => 'VAUPÉS', 'pais_id' => 1],
            ['id' => 99, 'departamento' => 'VICHADA', 'pais_id' => 1],
        ];

        foreach ($departamentos as $departamento) {
            Departamento::updateOrCreate(
                ['id' => $departamento['id']],
                [
                    'departamento' => $departamento['departamento'],
                    'pais_id'      => $departamento['pais_id'],
                    'status'       => $departamento['status'] ?? 1,
                ]
            );
        }
    }
}
