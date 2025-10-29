<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstructorResource extends JsonResource
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
                'email' => $this->persona->email ?? 'N/A',
            ],
            'regional' => [
                'id' => $this->regional->id ?? null,
                'nombre' => $this->regional->nombre ?? 'N/A',
            ],
            'especialidades' => $this->especialidades ?? [],
            'especialidad_principal' => $this->especialidades['principal'] ?? null,
            'especialidades_secundarias' => $this->especialidades['secundarias'] ?? [],
            'anos_experiencia' => $this->anos_experiencia ?? 0,
            'experiencia_laboral' => $this->experiencia_laboral,
            'status' => $this->status,
            'status_texto' => $this->status ? 'Activo' : 'Inactivo',
            'fichas_activas' => $this->whenLoaded('instructorFichas', function () {
                return $this->instructorFichas->where('status', true)->count();
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}

