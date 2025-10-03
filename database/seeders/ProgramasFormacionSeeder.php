<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProgramaFormacion;
use App\Models\RedConocimiento;
use App\Models\Parametro;
use App\Models\User;

class ProgramasFormacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener el primer usuario administrador para auditoría
        $adminUser = User::first();
        $userId = $adminUser ? $adminUser->id : 1;

        // Obtener red de conocimiento de Informática
        $redInformatica = RedConocimiento::where('nombre', 'INFORMÁTICA, DISEÑO Y DESARROLLO DE SOFTWARE')->first();
        $redInformaticaId = $redInformatica ? $redInformatica->id : null;

        // Obtener niveles de formación
        $nivelTecnico = Parametro::where('name', 'TÉCNICO')->first();
        $nivelTecnologo = Parametro::where('name', 'TECNÓLOGO')->first();
        $nivelAuxiliar = Parametro::where('name', 'AUXILIAR')->first();

        $programas_formacion = [
            [
                'codigo' => '228118',
                'nombre' => 'ANÁLISIS Y DESARROLLO DE SOFTWARE',
                'red_conocimiento_id' => $redInformaticaId,
                'nivel_formacion_id' => $nivelTecnologo ? $nivelTecnologo->id : 22,
                'status' => true,
            ],
            [
                'codigo' => '228106',
                'nombre' => 'TÉCNICO EN PROGRAMACIÓN DE SOFTWARE',
                'red_conocimiento_id' => $redInformaticaId,
                'nivel_formacion_id' => $nivelTecnico ? $nivelTecnico->id : 21,
                'status' => true,
            ],
            [
                'codigo' => '228107',
                'nombre' => 'TÉCNICO EN SISTEMAS',
                'red_conocimiento_id' => $redInformaticaId,
                'nivel_formacion_id' => $nivelTecnico ? $nivelTecnico->id : 21,
                'status' => true,
            ],
            [
                'codigo' => '228103',
                'nombre' => 'TECNÓLOGO EN ANÁLISIS Y DESARROLLO DE SISTEMAS DE INFORMACIÓN',
                'red_conocimiento_id' => $redInformaticaId,
                'nivel_formacion_id' => $nivelTecnologo ? $nivelTecnologo->id : 22,
                'status' => true,
            ],
            [
                'codigo' => '228101',
                'nombre' => 'AUXILIAR EN PROGRAMACIÓN DE COMPUTADORES',
                'red_conocimiento_id' => $redInformaticaId,
                'nivel_formacion_id' => $nivelAuxiliar ? $nivelAuxiliar->id : 23,
                'status' => true,
            ],
        ];

        foreach ($programas_formacion as $programa) {
            ProgramaFormacion::updateOrCreate(
                ['codigo' => $programa['codigo']], // Condición de búsqueda
                [
                    'nombre' => $programa['nombre'],
                    'red_conocimiento_id' => $programa['red_conocimiento_id'],
                    'nivel_formacion_id' => $programa['nivel_formacion_id'],
                    'user_create_id' => $userId,
                    'user_edit_id' => $userId,
                    'status' => $programa['status'],
                ]
            );
        }

        // Agregar programas de otras redes de conocimiento si existen
        $redComercial = RedConocimiento::where('nombre', 'COMERCIO Y VENTAS')->first();
        if ($redComercial) {
            ProgramaFormacion::updateOrCreate(
                ['codigo' => '233102'],
                [
                    'nombre' => 'TÉCNICO EN VENTA DE PRODUCTOS Y SERVICIOS',
                    'red_conocimiento_id' => $redComercial->id,
                    'nivel_formacion_id' => $nivelTecnico ? $nivelTecnico->id : 21,
                    'user_create_id' => $userId,
                    'user_edit_id' => $userId,
                    'status' => true,
                ]
            );
        }

        $redAdministrativa = RedConocimiento::where('nombre', 'GESTIÓN ADMINISTRATIVA Y FINANCIERA')->first();
        if ($redAdministrativa) {
            ProgramaFormacion::updateOrCreate(
                ['codigo' => '233103'],
                [
                    'nombre' => 'TÉCNICO EN CONTABILIDAD Y FINANZAS',
                    'red_conocimiento_id' => $redAdministrativa->id,
                    'nivel_formacion_id' => $nivelTecnico ? $nivelTecnico->id : 21,
                    'user_create_id' => $userId,
                    'user_edit_id' => $userId,
                    'status' => true,
                ]
            );
        }

        $redTurismo = RedConocimiento::where('nombre', 'HOTELERÍA Y TURISMO')->first();
        if ($redTurismo) {
            ProgramaFormacion::updateOrCreate(
                ['codigo' => '233104'],
                [
                    'nombre' => 'TÉCNICO EN GUÍA DE TURISMO',
                    'red_conocimiento_id' => $redTurismo->id,
                    'nivel_formacion_id' => $nivelTecnico ? $nivelTecnico->id : 21,
                    'user_create_id' => $userId,
                    'user_edit_id' => $userId,
                    'status' => true,
                ]
            );
        }
    }
}
