<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsistenciaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'aprendiz' => [
                'nombres' => $this->nombres,
                'apellidos' => $this->apellidos,
                'nombre_completo' => "{$this->nombres} {$this->apellidos}",
                'numero_identificacion' => $this->numero_identificacion,
            ],
            'caracterizacion' => [
                'id' => $this->caracterizacion->id ?? null,
                'ficha' => $this->caracterizacion->ficha->ficha ?? 'N/A',
                'jornada' => $this->caracterizacion->jornada->jornada ?? 'N/A',
            ],
            'horarios' => [
                'ingreso' => $this->hora_ingreso?->format('H:i:s'),
                'salida' => $this->hora_salida?->format('H:i:s'),
                'duracion_minutos' => $this->hora_salida 
                    ? $this->hora_ingreso->diffInMinutes($this->hora_salida)
                    : null,
            ],
            'novedades' => [
                'entrada' => $this->novedad_entrada,
                'salida' => $this->novedad_salida,
            ],
            'fecha_registro' => $this->created_at?->format('Y-m-d'),
            'hora_registro' => $this->created_at?->format('H:i:s'),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

