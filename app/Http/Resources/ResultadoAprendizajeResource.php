<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultadoAprendizajeResource extends JsonResource
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
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'competencia' => [
                'id' => $this->competencia->id ?? null,
                'nombre' => $this->competencia->nombre ?? 'N/A',
            ],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}

