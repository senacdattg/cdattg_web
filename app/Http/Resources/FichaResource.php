<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FichaResource extends JsonResource
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
            'numero' => $this->ficha,
            'programa' => [
                'id' => $this->programaFormacion->id ?? null,
                'nombre' => $this->programaFormacion->nombre ?? 'N/A',
                'codigo' => $this->programaFormacion->codigo ?? null,
                'red_conocimiento' => $this->programaFormacion->redConocimiento->nombre ?? 'N/A',
            ],
            'jornada' => [
                'id' => $this->jornadaFormacion->id ?? null,
                'nombre' => $this->jornadaFormacion->jornada ?? 'N/A',
                'hora_inicio' => $this->jornadaFormacion->hora_inicio ?? null,
                'hora_fin' => $this->jornadaFormacion->hora_fin ?? null,
            ],
            'modalidad' => [
                'id' => $this->modalidadFormacion->id ?? null,
                'nombre' => $this->modalidadFormacion->nombre ?? 'N/A',
            ],
            'ambiente' => [
                'id' => $this->ambiente->id ?? null,
                'nombre' => $this->ambiente->nombre ?? 'N/A',
                'sede' => $this->ambiente->sede->nombre ?? 'N/A',
            ],
            'fechas' => [
                'inicio' => $this->fecha_inicio,
                'fin' => $this->fecha_fin,
                'duracion_meses' => $this->duracion_meses ?? null,
            ],
            'status' => $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

