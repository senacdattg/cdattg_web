<?php

namespace App\Services;

use App\Models\ComplementarioOfertado;
use App\Models\AspiranteComplementario;
use App\Models\Parametro;
use App\Models\Tema;
use Illuminate\Support\Facades\Log;

class ComplementarioService
{
    /**
     * Obtener icono para un programa complementario
     */
    public function getIconoForPrograma($nombre)
    {
        $iconos = [
            'Auxiliar de Cocina' => 'fas fa-utensils',
            'Acabados en Madera' => 'fas fa-hammer',
            'Confección de Prendas' => 'fas fa-cut',
            'Mecánica Básica Automotriz' => 'fas fa-car',
            'Cultivos de Huertas Urbanas' => 'fas fa-spa',
            'Normatividad Laboral' => 'fas fa-gavel',
        ];

        return $iconos[$nombre] ?? 'fas fa-graduation-cap';
    }

    /**
     * Obtener clase CSS para el badge según el estado del programa
     */
    public function getBadgeClassForEstado($estado)
    {
        $badgeClasses = [
            0 => 'bg-secondary', // Sin Oferta
            1 => 'bg-success',   // Con Oferta
            2 => 'bg-warning',   // Cupos Llenos
        ];

        return $badgeClasses[$estado] ?? 'bg-secondary';
    }

    /**
     * Obtener label del estado del programa
     */
    public function getEstadoLabel($estado)
    {
        $estados = [
            0 => 'Sin Oferta',
            1 => 'Con Oferta',
            2 => 'Cupos Llenos',
        ];

        return $estados[$estado] ?? 'Desconocido';
    }

    /**
     * Obtener tipos de documento dinámicamente desde el tema-parametro
     */
    public function getTiposDocumento()
    {
        // Buscar el tema "TIPO DE DOCUMENTO"
        $temaTipoDocumento = Tema::where('name', 'TIPO DE DOCUMENTO')->first();

        if (!$temaTipoDocumento) {
            // Fallback: devolver valores hardcodeados si no se encuentra el tema
            return collect([
                ['id' => 3, 'name' => 'CEDULA DE CIUDADANIA'],
                ['id' => 4, 'name' => 'CEDULA DE EXTRANJERIA'],
                ['id' => 5, 'name' => 'PASAPORTE'],
                ['id' => 6, 'name' => 'TARJETA DE IDENTIDAD'],
                ['id' => 7, 'name' => 'REGISTRO CIVIL'],
                ['id' => 8, 'name' => 'SIN IDENTIFICACION'],
            ]);
        }

        // Obtener parámetros activos del tema
        return $temaTipoDocumento->parametros()
            ->where('parametros_temas.status', 1)
            ->orderBy('parametros.name')
            ->get(['parametros.id', 'parametros.name']);
    }

    /**
     * Obtener géneros dinámicamente desde el tema-parametro
     */
    public function getGeneros()
    {
        // Buscar el tema "GENERO"
        $temaGenero = Tema::where('name', 'GENERO')->first();

        if (!$temaGenero) {
            // Fallback: devolver valores hardcodeados si no se encuentra el tema
            return collect([
                ['id' => 9, 'name' => 'MASCULINO'],
                ['id' => 10, 'name' => 'FEMENINO'],
                ['id' => 11, 'name' => 'NO DEFINE'],
            ]);
        }

        // Obtener parámetros activos del tema
        return $temaGenero->parametros()
            ->where('parametros_temas.status', 1)
            ->orderBy('parametros.name')
            ->get(['parametros.id', 'parametros.name']);
    }

    /**
     * Verificar si un usuario ya está inscrito en un programa
     */
    public function verificarInscripcionExistente($personaId, $programaId)
    {
        return AspiranteComplementario::where('persona_id', $personaId)
            ->where('complementario_id', $programaId)
            ->exists();
    }

    /**
     * Crear aspirante complementario
     */
    public function crearAspirante($personaId, $programaId, $observaciones = null)
    {
        return AspiranteComplementario::create([
            'persona_id' => $personaId,
            'complementario_id' => $programaId,
            'observaciones' => $observaciones,
            'estado' => 1, // Estado "En proceso"
        ]);
    }

    /**
     * Actualizar estado del aspirante
     */
    public function actualizarEstadoAspirante($aspiranteId, $estado)
    {
        $aspirante = AspiranteComplementario::findOrFail($aspiranteId);
        $aspirante->update(['estado' => $estado]);
        return $aspirante;
    }

    /**
     * Obtener estadísticas básicas de un programa
     */
    public function obtenerEstadisticasPrograma($programaId)
    {
        $programa = ComplementarioOfertado::findOrFail($programaId);

        return [
            'total_aspirantes' => AspiranteComplementario::where('complementario_id', $programaId)->count(),
            'aspirantes_activos' => AspiranteComplementario::where('complementario_id', $programaId)
                ->where('estado', 1)->count(),
            'aspirantes_aceptados' => AspiranteComplementario::where('complementario_id', $programaId)
                ->where('estado', 3)->count(),
            'cupos_disponibles' => $programa->cupos - AspiranteComplementario::where('complementario_id', $programaId)->count(),
        ];
    }
}