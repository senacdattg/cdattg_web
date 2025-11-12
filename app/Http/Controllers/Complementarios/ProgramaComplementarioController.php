<?php

namespace App\Http\Controllers\Complementarios;

use App\Http\Controllers\Controller;
use App\Http\Requests\Complementarios\StoreProgramaComplementarioRequest;
use App\Http\Requests\Complementarios\UpdateProgramaComplementarioRequest;
use App\Models\ComplementarioOfertado;
use App\Services\ComplementarioService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ProgramaComplementarioController extends Controller
{
    public function __construct(
        private readonly ComplementarioService $complementarioService
    ) {}

    /**
     * Listar programas complementarios ofertados (Admin).
     */
    public function index(): View
    {
        $programas = $this->complementarioService
            ->obtenerProgramas(['modalidad.parametro', 'jornada', 'diasFormacion', 'ambiente']);

        $programas = $this->complementarioService->enriquecerProgramas($programas);

        return view(
            'complementarios.programas.admin.index',
            array_merge(
                ['programas' => $programas],
                $this->complementarioService->obtenerDatosFormulario()
            )
        );
    }

    /**
     * Mostrar formulario de creación de programa
     */
    public function create(): View
    {
        return view(
            'complementarios.programas.admin.create',
            $this->complementarioService->obtenerDatosFormulario()
        );
    }

    /**
     * Mostrar programas públicos (Vista pública)
     */
    public function programasPublicos(): View
    {
        $programas = $this->complementarioService
            ->obtenerProgramas(['modalidad.parametro', 'jornada', 'diasFormacion'], estado: 1);

        $programas = $this->complementarioService->enriquecerProgramas($programas);

        return view('complementarios.programas.public.index', [
            'programas' => $programas,
            'tiposDocumento' => $this->complementarioService->getTiposDocumento(),
            'generos' => $this->complementarioService->getGeneros(),
        ]);
    }

    /**
     * Mostrar todos los programas (Admin)
     */
    public function verProgramas(): View
    {
        $programas = $this->complementarioService
            ->obtenerProgramas(['modalidad.parametro', 'jornada', 'diasFormacion']);

        $programas = $this->complementarioService->enriquecerProgramas($programas);

        return view('complementarios.ver_programas', ['programas' => $programas]);
    }

    /**
     * Mostrar programa específico público
     */
    public function verPrograma(ComplementarioOfertado $programa): View
    {
        $programa->load(['modalidad.parametro', 'jornada', 'diasFormacion']);
        $programa = $this->complementarioService->enriquecerPrograma($programa);

        $programaData = [
            'id' => $programa->id,
            'nombre' => $programa->nombre,
            'descripcion' => $programa->descripcion,
            'duracion' => $programa->duracion . ' horas',
            'icono' => $programa->icono,
            'modalidad' => $programa->modalidad_nombre ?? 'N/A',
            'jornada' => $programa->jornada_nombre ?? 'N/A',
            'dias' => $programa->diasFormacion->map(static function ($dia) {
                return $dia->name . ' (' . $dia->pivot->hora_inicio . ' - ' . $dia->pivot->hora_fin . ')';
            })->implode(', '),
            'cupos' => $programa->cupos,
            'estado' => $programa->estado_label,
        ];

        return view('complementarios.programas.public.show', compact('programaData'));
    }

    /**
     * API: Obtener datos de programa para edición
     */
    public function edit(ComplementarioOfertado $programa): JsonResponse
    {
        $programa->load(['modalidad', 'jornada', 'diasFormacion', 'ambiente']);

        $dias = $programa->diasFormacion->map(static function ($dia) {
            return [
                'dia_id' => $dia->id,
                'hora_inicio' => $dia->pivot->hora_inicio,
                'hora_fin' => $dia->pivot->hora_fin,
            ];
        });

        return response()->json([
            'id' => $programa->id,
            'codigo' => $programa->codigo,
            'nombre' => $programa->nombre,
            'descripcion' => $programa->descripcion,
            'duracion' => $programa->duracion,
            'cupos' => $programa->cupos,
            'estado' => $programa->estado,
            'modalidad_id' => $programa->modalidad_id,
            'jornada_id' => $programa->jornada_id,
            'ambiente_id' => $programa->ambiente_id,
            'dias' => $dias,
        ]);
    }

    /**
     * Crear nuevo programa
     */
    public function store(StoreProgramaComplementarioRequest $request): RedirectResponse
    {
        $payload = $request->validated();

        DB::transaction(function () use ($payload) {
            $programa = ComplementarioOfertado::create($this->extractProgramaAtributos($payload));

            $this->complementarioService->sincronizarDiasFormacion($programa, $payload['dias'] ?? null);
        });

        return redirect()
            ->route('complementarios-ofertados.index')
            ->with('success', 'Programa creado exitosamente.');
    }

    /**
     * Actualizar programa
     */
    public function update(
        UpdateProgramaComplementarioRequest $request,
        ComplementarioOfertado $programa
    ): JsonResponse {
        $payload = $request->validated();

        DB::transaction(function () use ($programa, $payload) {
            $programa->update($this->extractProgramaAtributos($payload));

            $this->complementarioService->sincronizarDiasFormacion($programa, $payload['dias'] ?? null);
        });

        return response()->json([
            'success' => true,
            'message' => 'Programa actualizado exitosamente.',
        ]);
    }

    /**
     * Eliminar programa
     */
    public function destroy(ComplementarioOfertado $programa): JsonResponse
    {
        $programa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Programa eliminado exitosamente.',
        ]);
    }

    /**
     * Extrae los atributos permitidos para crear o actualizar un programa.
     */
    private function extractProgramaAtributos(array $payload): array
    {
        return collect($payload)->only([
            'codigo',
            'nombre',
            'descripcion',
            'duracion',
            'cupos',
            'estado',
            'modalidad_id',
            'jornada_id',
            'ambiente_id',
        ])->toArray();
    }
}
