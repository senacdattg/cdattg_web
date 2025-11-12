<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UbicacionService;
use Illuminate\Http\JsonResponse;

class UbicacionPublicApiController extends Controller
{
    public function __construct(private readonly UbicacionService $ubicacionService)
    {
    }

    public function paises(): JsonResponse
    {
        $paises = $this->ubicacionService->obtenerPaisesActivos();

        return response()->json([
            'success' => true,
            'paises' => $paises,
        ]);
    }
}
