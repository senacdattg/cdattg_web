<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAsignacionInstructorRequest;
use App\Models\AsignacionInstructor;
use App\Models\Competencia;
use App\Models\FichaCaracterizacion;
use App\Models\Instructor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AsignacionInstructorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $asignaciones = AsignacionInstructor::with([
            'ficha.programaFormacion',
            'instructor.persona',
            'competencia',
            'resultadosAprendizaje',
        ])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('asignaciones.index', compact('asignaciones'));
    }

    public function create(): View
    {
        $fichas = FichaCaracterizacion::with('programaFormacion')
            ->orderBy('ficha')
            ->get();

        $instructores = Instructor::with('persona')
            ->orderBy('nombre_completo_cache')
            ->get();

        return view('asignaciones.create', compact('fichas', 'instructores'));
    }

    public function store(StoreAsignacionInstructorRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $existe = AsignacionInstructor::where('ficha_id', $validated['ficha_id'])
            ->where('instructor_id', $validated['instructor_id'])
            ->where('competencia_id', $validated['competencia_id'])
            ->exists();

        if ($existe) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'La asignación ya existe para esta ficha, instructor y competencia.');
        }

        try {
            DB::transaction(function () use ($validated) {
                $asignacion = AsignacionInstructor::create([
                    'ficha_id' => $validated['ficha_id'],
                    'instructor_id' => $validated['instructor_id'],
                    'competencia_id' => $validated['competencia_id'],
                ]);

                $asignacion->resultadosAprendizaje()->sync($validated['resultados']);
            });
        } catch (\Throwable $e) {
            Log::error('Error al guardar asignación de instructor', [
                'payload' => $validated,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ocurrió un error al guardar la asignación. Intente nuevamente.');
        }

        return redirect()
            ->route('asignaciones.instructores.index')
            ->with('success', 'Asignación registrada correctamente.');
    }

    public function competenciasPorFicha(FichaCaracterizacion $ficha): JsonResponse
    {
        $competencias = $ficha->programaFormacion
            ? $ficha->programaFormacion->competencias()
                ->select('competencias.id', 'competencias.codigo', 'competencias.nombre')
                ->orderBy('competencias.nombre')
                ->get()
            : collect();

        return response()->json([
            'data' => $competencias,
        ]);
    }

    public function resultadosPorCompetencia(Competencia $competencia): JsonResponse
    {
        $resultados = $competencia->resultadosAprendizaje()
            ->select('resultados_aprendizajes.id', 'resultados_aprendizajes.codigo', 'resultados_aprendizajes.nombre', 'resultados_aprendizajes.duracion')
            ->orderBy('resultados_aprendizajes.codigo')
            ->get();

        return response()->json([
            'data' => $resultados,
        ]);
    }
}

