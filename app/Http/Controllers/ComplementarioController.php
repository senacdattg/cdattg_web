<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\ComplementarioOfertado;
use App\Models\Parametro;
use App\Models\JornadaFormacion;

class ComplementarioController extends Controller
{
    /**
     * Display the gestion aspirantes view.
     *
     * @return \Illuminate\View\View
     */
    public function gestionAspirantes()
    {
        return view('complementarios.gestion_aspirantes');
    }

    /**
     * Display the procesamiento documentos view.
     *
     * @return \Illuminate\View\View
     */
    public function procesarDocumentos() 
    {
        return view('complementarios.procesamiento_documentos');
    }

    /**
     * Display the gestion programas complementarios view.
     *
     * @return \Illuminate\View\View
     */
    public function gestionProgramasComplementarios()
    {

        $programas = ComplementarioOfertado::with(['modalidad.parametro', 'jornada', 'diasFormacion'])->get();
        $modalidades = \App\Models\ParametroTema::where('tema_id', 5)->with('parametro')->get();
        $jornadas = \App\Models\JornadaFormacion::all();
        return view('complementarios.gestion_programas_complementarios', compact('programas', 'modalidades', 'jornadas'));
    }
    public function estadisticas()
    {
        $departamentos = Departamento::select('id', 'departamento')->get();
        $municipios = Municipio::select('id', 'municipio')->get();

        return view('complementarios.estadisticas', compact('departamentos', 'municipios'));
    }
    public function verAspirantes($curso)
    {

        return view('complementarios.ver_aspirantes', compact('curso'));
    }
    public function verPrograma($id)
    {
        $programa = ComplementarioOfertado::with(['modalidad', 'jornada', 'diasFormacion'])->findOrFail($id);

        $programaData = [
            'nombre' => $programa->nombre,
            'descripcion' => $programa->descripcion,
            'duracion' => $programa->duracion . ' horas',
            'icono' => $this->getIconoForPrograma($programa->nombre),
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

    public function getIconoForPrograma($nombre)
    {
        $iconos = [
            'Auxiliar de Cocina' => 'fas fa-utensils',
            'Acabados en Madera' => 'fas fa-hammer',
            'Confección de Prendas' => 'fas fa-cut',
            'Mecánica Básica Automotriz' => 'fas fa-car',
            'Cultivos de Huertas Urbanas' => 'fas fa-spa',
            'Normatividad Laboral' => 'fas fa-gavel',
        ];

        return $iconos[$nombre] ?? 'fas fa-graduation-cap';
    }

    public function programasPublicos()
    {
        $programas = ComplementarioOfertado::with(['modalidad.parametro', 'jornada', 'diasFormacion'])->where('estado', 1)->get();
        $programas->each(function($programa) {
            $programa->icono = $this->getIconoForPrograma($programa->nombre);
        });
        return view('complementarios.programas_publicos', compact('programas'));
    }

    public function edit($id)
    {
        $programa = ComplementarioOfertado::with(['modalidad', 'jornada', 'diasFormacion'])->findOrFail($id);

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
            'dias' => $dias,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|unique:complementarios_ofertados',
            'nombre' => 'required',
            'descripcion' => 'nullable',
            'duracion' => 'required|integer|min:1',
            'cupos' => 'required|integer|min:1',
            'estado' => 'required|integer|in:0,1,2',
            'modalidad_id' => 'required|exists:parametros_temas,id',
            'jornada_id' => 'required|exists:jornadas_formacion,id',
            'dias' => 'nullable|array',
            'dias.*.dia_id' => 'exists:parametros_temas,id',
            'dias.*.hora_inicio' => 'nullable|date_format:H:i',
            'dias.*.hora_fin' => 'nullable|date_format:H:i',
        ]);

        $programa = ComplementarioOfertado::create($request->only([
            'codigo', 'nombre', 'descripcion', 'duracion', 'cupos', 'estado', 'modalidad_id', 'jornada_id'
        ]));

        if ($request->dias) {
            foreach ($request->dias as $dia) {
                $programa->diasFormacion()->attach($dia['dia_id'], [
                    'hora_inicio' => $dia['hora_inicio'],
                    'hora_fin' => $dia['hora_fin'],
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Programa creado exitosamente.']);
    }

    public function update(Request $request, $id)
    {
        $programa = ComplementarioOfertado::findOrFail($id);

        $request->validate([
            'codigo' => 'required|unique:complementarios_ofertados,codigo,' . $id,
            'nombre' => 'required',
            'descripcion' => 'nullable',
            'duracion' => 'required|integer|min:1',
            'cupos' => 'required|integer|min:1',
            'estado' => 'required|integer|in:0,1,2',
            'modalidad_id' => 'required|exists:parametros_temas,id',
            'jornada_id' => 'required|exists:jornadas_formacion,id',
            'dias' => 'nullable|array',
            'dias.*.dia_id' => 'exists:parametros_temas,id',
            'dias.*.hora_inicio' => 'nullable|date_format:H:i',
            'dias.*.hora_fin' => 'nullable|date_format:H:i',
        ]);

        $programa->update($request->only([
            'codigo', 'nombre', 'descripcion', 'duracion', 'cupos', 'estado', 'modalidad_id', 'jornada_id'
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

    public function destroy($id)
    {
        $programa = ComplementarioOfertado::findOrFail($id);
        $programa->delete();

        return response()->json(['success' => true, 'message' => 'Programa eliminado exitosamente.']);
    }
}
