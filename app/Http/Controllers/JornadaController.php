<?php

namespace App\Http\Controllers;

use App\Services\JornadaFormacionService;
use App\Models\JornadaFormacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JornadaController extends Controller
{
    protected JornadaFormacionService $jornadaService;

    public function __construct(JornadaFormacionService $jornadaService)
    {
        $this->jornadaService = $jornadaService;
    }

    public function index()
    {
        $jornadas = $this->jornadaService->listarTodas();
        return view('jornada.index', compact('jornadas')); 
    }

    public function create()
    {
        return view('jornada.create');
    }

    public function store(Request $request)
    {
        try {
            $datos = $request->validate([
                'jornada' => 'required|string|max:255',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            ]);

            $this->jornadaService->crear($datos);

            return redirect()->route('jornada.index')->with('success', 'Jornada creada exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al crear jornada: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al crear jornada.');
        }
    }

    public function edit($id)
    {
        $jornada = JornadaFormacion::findOrFail($id);
        return view('jornada.edit', compact('jornada'));
    }

    public function update(Request $request, $id)
    {
        try {
            $datos = $request->validate([
                'jornada' => 'required|string|max:255',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            ]);

            $this->jornadaService->actualizar($id, $datos);

            return redirect()->route('jornada.index')->with('success', 'Jornada actualizada exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar jornada: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error al actualizar jornada.');
        }
    }

    public function destroy($id)
    {
        JornadaFormacion::destroy($id);

        return back()->with('error', 'Jornada eliminada');
    }
}
