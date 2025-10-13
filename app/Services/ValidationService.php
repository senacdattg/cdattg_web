<?php

namespace App\Services;

use App\Traits\ValidacionesSena;
use Illuminate\Support\Facades\Validator;

class ValidationService
{
    use ValidacionesSena;

    /**
     * Valida datos de aprendiz
     *
     * @param array $datos
     * @return array
     */
    public function validarAprendiz(array $datos): array
    {
        $validator = Validator::make($datos, [
            'persona_id' => 'required|integer|exists:personas,id',
            'ficha_caracterizacion_id' => 'required|integer|exists:ficha_caracterizacions,id',
            'estado' => 'boolean',
        ]);

        if ($validator->fails()) {
            return [
                'valido' => false,
                'errores' => $validator->errors()->toArray(),
            ];
        }

        return [
            'valido' => true,
            'datos' => $validator->validated(),
        ];
    }

    /**
     * Valida datos de instructor
     *
     * @param array $datos
     * @return array
     */
    public function validarInstructor(array $datos): array
    {
        $validator = Validator::make($datos, [
            'persona_id' => 'required|integer|exists:personas,id',
            'regional_id' => 'required|integer|exists:regionals,id',
            'anos_experiencia' => 'nullable|integer|min:0',
            'experiencia_laboral' => 'nullable|string',
            'especialidades' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return [
                'valido' => false,
                'errores' => $validator->errors()->toArray(),
            ];
        }

        return [
            'valido' => true,
            'datos' => $validator->validated(),
        ];
    }

    /**
     * Valida datos de ficha
     *
     * @param array $datos
     * @return array
     */
    public function validarFicha(array $datos): array
    {
        $validator = Validator::make($datos, [
            'ficha' => 'required|string|unique:ficha_caracterizacions,ficha',
            'programa_formacion_id' => 'required|integer|exists:programa_formacions,id',
            'jornada_formacion_id' => 'required|integer|exists:jornada_formacions,id',
            'modalidad_formacion_id' => 'required|integer|exists:modalidad_formacions,id',
            'ambiente_id' => 'required|integer|exists:ambientes,id',
            'regional_id' => 'required|integer|exists:regionals,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);

        if ($validator->fails()) {
            return [
                'valido' => false,
                'errores' => $validator->errors()->toArray(),
            ];
        }

        return [
            'valido' => true,
            'datos' => $validator->validated(),
        ];
    }

    /**
     * Valida número de documento según tipo
     *
     * @param string $numeroDocumento
     * @param int $tipoDocumento
     * @return array
     */
    public function validarDocumento(string $numeroDocumento, int $tipoDocumento): array
    {
        $reglas = [
            1 => '/^\d{6,10}$/', // CC
            2 => '/^\d{6,10}$/', // CE
            3 => '/^\d{10}$/',   // TI
            8 => '/^\d{6,12}$/', // Otro
        ];

        $patron = $reglas[$tipoDocumento] ?? $reglas[8];

        if (!preg_match($patron, $numeroDocumento)) {
            return [
                'valido' => false,
                'mensaje' => 'El formato del documento no es válido para el tipo seleccionado',
            ];
        }

        return [
            'valido' => true,
            'mensaje' => 'Documento válido',
        ];
    }

    /**
     * Valida email institucional SENA
     *
     * @param string $email
     * @return array
     */
    public function validarEmailSena(string $email): array
    {
        $dominiosPermitidos = ['@sena.edu.co', '@misena.edu.co'];
        
        $esValido = false;
        foreach ($dominiosPermitidos as $dominio) {
            if (str_ends_with($email, $dominio)) {
                $esValido = true;
                break;
            }
        }

        return [
            'valido' => $esValido,
            'mensaje' => $esValido 
                ? 'Email institucional válido'
                : 'Debe usar un email institucional del SENA',
        ];
    }
}

