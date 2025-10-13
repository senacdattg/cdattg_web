<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AprendizResource extends JsonResource
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
            'persona' => [
                'id' => $this->persona->id ?? null,
                'nombre_completo' => $this->persona->nombre_completo ?? 'N/A',
                'numero_documento' => $this->persona->numero_documento ?? 'N/A',
                'tipo_documento' => $this->persona->tipoDocumento->name ?? 'N/A',
                'email' => $this->persona->email ?? 'N/A',
                'telefono' => $this->persona->telefono ?? null,
            ],
            'ficha' => [
                'id' => $this->fichaCaracterizacion->id ?? null,
                'numero' => $this->fichaCaracterizacion->ficha ?? 'N/A',
                'programa' => $this->fichaCaracterizacion->programaFormacion->nombre ?? 'N/A',
                'jornada' => $this->fichaCaracterizacion->jornadaFormacion->jornada ?? 'N/A',
                'fecha_inicio' => $this->fichaCaracterizacion->fecha_inicio ?? null,
                'fecha_fin' => $this->fichaCaracterizacion->fecha_fin ?? null,
            ],
            'estado' => $this->estado,
            'estado_texto' => $this->estado ? 'Activo' : 'Inactivo',
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

