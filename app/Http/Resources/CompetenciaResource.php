<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetenciaResource extends JsonResource
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
            'duracion_horas' => $this->duracion_horas,
            'resultados_aprendizaje' => $this->whenLoaded('resultadosAprendizaje', function () {
                return ResultadoAprendizajeResource::collection($this->resultadosAprendizaje);
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}

