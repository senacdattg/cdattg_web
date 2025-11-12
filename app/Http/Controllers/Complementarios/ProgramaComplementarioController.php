<?php

namespace App\Http\Controllers\Complementarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplementarioOfertado;
use App\Models\Parametro;
use App\Models\JornadaFormacion;
use App\Models\Ambiente;
use App\Services\ComplementarioService;
use Illuminate\Support\Facades\Auth;

class ProgramaComplementarioController extends Controller
{
    const VALIDATION_RULE_POSITIVE_INTEGER = 'required|integer|min:1';
    const VALIDATION_RULE_TIME = 'nullable|date_format:H:i';

    protected $complementarioService;

    public function __construct(ComplementarioService $complementarioService)
    {
        $this->complementarioService = $complementarioService;
    }

    /**
     * Mostrar gestión de programas complementarios (Admin)
     */
    public function gestionProgramasComplementarios()
    {
        $programas = ComplementarioOfertado::with([
            'modalidad.parametro',
            'jornada',
            'diasFormacion',
            'ambiente'
        ])->get();
        $modalidades = \App\Models\ParametroTema::where('tema_id', 5)->with('parametro')->get();
        $jornadas = \App\Models\JornadaFormacion::all();
        $ambientes = Ambiente::with('piso')->where('status', 1)->orderBy('piso_id')->orderBy('title')->get();
        return view(
            'complementarios.gestion_complementarios.index',
            compact(
                'programas',
                'modalidades',
                'jornadas',
                'ambientes'
            )
        );
    }

    /**
     * Mostrar formulario de creación de programa
     */
    public function create()
    {
        $modalidades = \App\Models\ParametroTema::where('tema_id', 5)->with('parametro')->get();
        $jornadas = \App\Models\JornadaFormacion::all();
        $ambientes = Ambiente::with('piso')->where('status', 1)->orderBy('piso_id')->orderBy('title')->get();
        return view('complementarios.gestion_complementarios.create', compact('modalidades', 'jornadas', 'ambientes'));
    }

    /**
     * Mostrar programas públicos (Vista pública)
     */
    public function programasPublicos()
    {
        $programas = ComplementarioOfertado::with(['modalidad.parametro', 'jornada', 'diasFormacion'])
            ->where('estado', 1)
            ->get();

        $programas->each(function ($programa) {
            $programa->icono = $this->complementarioService->getIconoForPrograma($programa->nombre);
            $programa->badge_class = $this->complementarioService->getBadgeClassForEstado($programa->estado);
            $programa->estado_label = $this->complementarioService->getEstadoLabel($programa->estado);
            $programa->modalidad_nombre = $programa->modalidad->parametro->name ?? null;
            $programa->jornada_nombre = $programa->jornada->jornada ?? null;
        });

        // Obtener programas en los que el usuario está inscrito
        $programasInscritosIds = collect();
        if (Auth::check() && Auth::user()->persona) {
            $personaId = Auth::user()->persona->id;

            $programasInscritosIds = \App\Models\AspiranteComplementario::where('persona_id', $personaId)
                ->where('estado', 1)
                ->pluck('complementario_id');
        }

        // Obtener tipos de documento y géneros dinámicamente
        $tiposDocumento = $this->complementarioService->getTiposDocumento();
        $generos = $this->complementarioService->getGeneros();

        return view(
            'complementarios.programas_publicos',
            compact(
                'programas',
                'tiposDocumento',
                'generos',
                'programasInscritosIds'
            )
        );
    }

    /**
     * Mostrar todos los programas (Admin)
     */
    public function verProgramas()
    {
        $programas = ComplementarioOfertado::with(['modalidad.parametro', 'jornada', 'diasFormacion'])->get();
        $programas->each(function ($programa) {
            $programa->icono = $this->complementarioService->getIconoForPrograma($programa->nombre);
            $programa->badge_class = $this->complementarioService->getBadgeClassForEstado($programa->estado);
            $programa->estado_label = $this->complementarioService->getEstadoLabel($programa->estado);
        });
        return view('complementarios.ver_programas', compact('programas'));
    }

    /**
     * Mostrar programa específico público
     */
    public function verPrograma($id)
    {
        $programa = ComplementarioOfertado::with(['modalidad', 'jornada', 'diasFormacion'])->findOrFail($id);

        $programaData = [
            'id' => $programa->id,
            'nombre' => $programa->nombre,
            'descripcion' => $programa->descripcion,
            'duracion' => $programa->duracion . ' horas',
            'icono' => $this->complementarioService->getIconoForPrograma($programa->nombre),
            'modalidad' => $programa->modalidad->parametro->name ?? 'N/A',
            'jornada' => $programa->jornada->jornada ?? 'N/A',
            'dias' => $programa->diasFormacion->map(function ($dia) {
                return $dia->name . ' (' . $dia->pivot->hora_inicio . ' - ' . $dia->pivot->hora_fin . ')';
            })->implode(', '),
            'cupos' => $programa->cupos,
            'estado' => $programa->estado_label,
        ];

        return view('complementarios.ver_programa_publico', compact('programaData'));
    }

    /**
     * API: Obtener datos de programa para edición
     */
    public function edit($id)
    {
        $programa = ComplementarioOfertado::with(
            ['modalidad', 'jornada', 'diasFormacion', 'ambiente']
        )->findOrFail($id);

        $dias = $programa->diasFormacion->map(function ($dia) {
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
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|unique:complementarios_ofertados',
            'nombre' => 'required',
            'descripcion' => 'nullable',
            'duracion' => self::VALIDATION_RULE_POSITIVE_INTEGER,
            'cupos' => self::VALIDATION_RULE_POSITIVE_INTEGER,
            'estado' => 'required|integer|in:0,1,2',
            'modalidad_id' => 'required|exists:parametros_temas,id',
            'jornada_id' => 'required|exists:jornadas_formacion,id',
            'ambiente_id' => 'required|exists:ambientes,id',
            'dias' => 'nullable|array',
            'dias.*.dia_id' => 'exists:parametros_temas,id',
            'dias.*.hora_inicio' => self::VALIDATION_RULE_TIME,
            'dias.*.hora_fin' => self::VALIDATION_RULE_TIME,
        ]);

        $programa = ComplementarioOfertado::create($request->only([
            'codigo',
            'nombre',
            'descripcion',
            'duracion',
            'cupos',
            'estado',
            'modalidad_id',
            'jornada_id',
            'ambiente_id'
        ]));

        if ($request->dias) {
            foreach ($request->dias as $dia) {
                $programa->diasFormacion()->attach($dia['dia_id'], [
                    'hora_inicio' => $dia['hora_inicio'],
                    'hora_fin' => $dia['hora_fin'],
                ]);
            }
        }

        return redirect()->route('gestion-programas-complementarios')
            ->with('success', 'Programa creado exitosamente.');
    }

    /**
     * Actualizar programa
     */
    public function update(Request $request, $id)
    {
        $programa = ComplementarioOfertado::findOrFail($id);

        $request->validate([
            'codigo' => 'required|unique:complementarios_ofertados,codigo,' . $id,
            'nombre' => 'required',
            'descripcion' => 'nullable',
            'duracion' => self::VALIDATION_RULE_POSITIVE_INTEGER,
            'cupos' => self::VALIDATION_RULE_POSITIVE_INTEGER,
            'estado' => 'required|integer|in:0,1,2',
            'modalidad_id' => 'required|exists:parametros_temas,id',
            'jornada_id' => 'required|exists:jornadas_formacion,id',
            'ambiente_id' => 'required|exists:ambientes,id',
            'dias' => 'nullable|array',
            'dias.*.dia_id' => 'exists:parametros_temas,id',
            'dias.*.hora_inicio' => self::VALIDATION_RULE_TIME,
            'dias.*.hora_fin' => self::VALIDATION_RULE_TIME,
        ]);

        $programa->update($request->only([
            'codigo',
            'nombre',
            'descripcion',
            'duracion',
            'cupos',
            'estado',
            'modalidad_id',
            'jornada_id',
            'ambiente_id'
        ]));

        $programa->diasFormacion()->detach();
        if ($request->dias) {
            foreach ($request->dias as $dia) {
                $programa->diasFormacion()->attach($dia['dia_id'], [
                    'hora_inicio' => $dia['hora_inicio'],
                    'hora_fin' => $dia['hora_fin'],
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Programa actualizado exitosamente.']);
    }

    /**
     * Eliminar programa
     */
    public function destroy($id)
    {
        $programa = ComplementarioOfertado::findOrFail($id);
        $programa->delete();

        return response()->json(['success' => true, 'message' => 'Programa eliminado exitosamente.']);
    }
}
