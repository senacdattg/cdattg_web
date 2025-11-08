<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComplementarioOfertado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener programas complementarios activos (estado = 1)
        $programas = ComplementarioOfertado::with(['modalidad.parametro', 'jornada', 'diasFormacion'])
            ->where('estado', 1)
            ->get();

        // Asignar iconos a cada programa
        $programas->each(function($programa) {
            $programa->icono = $this->getIconoForPrograma($programa->nombre);
        });

        // Obtener programas en los que el usuario está inscrito
        $programasInscritos = collect();
        if (Auth::check() && Auth::user()->persona) {
            $personaId = Auth::user()->persona->id;
            
            // Debug: Verificar si hay datos en la tabla aspirantes_complementarios
            $aspirantesCount = \App\Models\AspiranteComplementario::where('persona_id', $personaId)
                ->where('estado', 1)
                ->count();
            
            Log::info("Debug HomeController - Persona ID: {$personaId}, Aspirantes encontrados: {$aspirantesCount}");
            
            $programasInscritos = ComplementarioOfertado::with(['modalidad.parametro', 'jornada', 'diasFormacion'])
                ->whereHas('aspirantes', function($query) use ($personaId) {
                    $query->where('persona_id', $personaId)
                          ->where('estado', 1); // Estado 1 = En proceso
                })
                ->get();

            Log::info("Debug HomeController - Programas inscritos encontrados: " . $programasInscritos->count());

            // Asignar iconos a cada programa inscrito
            $programasInscritos->each(function($programa) {
                $programa->icono = $this->getIconoForPrograma($programa->nombre);
            });
        } else {
            Log::info("Debug HomeController - Usuario no autenticado o sin persona asociada");
        }

        return view('home', compact('programas', 'programasInscritos'));
    }

    /**
     * Obtener icono para programa basado en su nombre
     */
    private function getIconoForPrograma($nombrePrograma)
    {
        $iconos = [
            'Auxiliar de Cocina' => 'fas fa-utensils',
            'Acabados en Madera' => 'fas fa-hammer',
            'Confección de Prendas' => 'fas fa-cut',
            'Mecánica Básica Automotriz' => 'fas fa-car',
            'Cultivos de Huertas Urbanas' => 'fas fa-spa',
            'Normatividad Laboral' => 'fas fa-gavel',
        ];

        return $iconos[$nombrePrograma] ?? 'fas fa-graduation-cap';
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
