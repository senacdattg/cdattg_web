<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\ComplementarioOfertado;
use App\Models\Parametro;
use App\Models\JornadaFormacion;
use App\Models\CategoriaCaracterizacionComplementario;
use App\Models\Pais;
use App\Models\Persona;
use App\Models\AspiranteComplementario;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ComplementarioController extends Controller
{
    /**
     * Display the gestion aspirantes view.
     *
     * @return \Illuminate\View\View
     */
    public function gestionAspirantes()
    {
        $programas = ComplementarioOfertado::with(['modalidad.parametro', 'jornada', 'diasFormacion'])->get();

        // Add aspirantes count for each program
        $programas->each(function($programa) {
            $programa->aspirantes_count = AspiranteComplementario::where('complementario_id', $programa->id)->count();
        });

        return view('complementarios.gestion_aspirantes', compact('programas'));
    }

    public function procesarDcoumentos() 
    {
        return view('complementarios.procesamiento_documentos');
    }
    
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
        // Find program by name (assuming curso is the program name)
        $programa = ComplementarioOfertado::where('nombre', str_replace('-', ' ', $curso))->firstOrFail();

        // Get aspirantes for this program
        $aspirantes = AspiranteComplementario::with(['persona', 'complementario'])
            ->where('complementario_id', $programa->id)
            ->get();

        return view('complementarios.ver_aspirantes', compact('programa', 'aspirantes'));
    }
    public function verPrograma($id)
    {
        $programa = ComplementarioOfertado::with(['modalidad', 'jornada', 'diasFormacion'])->findOrFail($id);

        $programaData = [
            'id' => $programa->id,
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

    public function formularioInscripcion($id)
    {
        $programa = ComplementarioOfertado::with(['modalidad.parametro', 'jornada'])->findOrFail($id);
        
        // Obtener categorías de caracterización principales con sus hijos
        $categorias = CategoriaCaracterizacionComplementario::getMainCategories();
        $categoriasConHijos = $categorias->map(function($categoria) {
            return [
                'id' => $categoria->id,
                'nombre' => $categoria->nombre,
                'hijos' => $categoria->getActiveChildren()
            ];
        });

        $paises = Pais::all();
        $departamentos = Departamento::all();

        return view('complementarios.formulario_inscripcion', compact('programa', 'categoriasConHijos', 'paises', 'departamentos'));
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

    /**
     * Procesar la inscripción del aspirante
     */
    public function procesarInscripcion(Request $request, $id)
    {
        // Validar los datos del formulario
        $request->validate([
            'tipo_documento' => 'required|integer',
            'numero_documento' => 'required|string|max:191',
            'primer_nombre' => 'required|string|max:191',
            'segundo_nombre' => 'nullable|string|max:191',
            'primer_apellido' => 'required|string|max:191',
            'segundo_apellido' => 'nullable|string|max:191',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|integer',
            'telefono' => 'nullable|string|max:191',
            'celular' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'pais_id' => 'required|exists:pais,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'municipio_id' => 'required|exists:municipios,id',
            'direccion' => 'required|string|max:191',
            'observaciones' => 'nullable|string',
            'categorias' => 'nullable|array',
            'categorias.*' => 'exists:categorias_caracterizacion_complementarios,id',
        ]);

        // Verificar si ya existe una persona con el mismo documento o email
        $personaExistente = Persona::where('numero_documento', $request->numero_documento)
            ->orWhere('email', $request->email)
            ->first();

        if ($personaExistente) {
            // Si ya existe, usar esa persona
            $persona = $personaExistente;
            
            // Actualizar datos si es necesario
            $persona->update($request->only([
                'tipo_documento', 'numero_documento', 'primer_nombre', 'segundo_nombre',
                'primer_apellido', 'segundo_apellido', 'fecha_nacimiento', 'genero',
                'telefono', 'celular', 'email', 'pais_id', 'departamento_id', 
                'municipio_id', 'direccion'
            ]));
        } else {
            // Crear nueva persona
            $persona = Persona::create($request->only([
                'tipo_documento', 'numero_documento', 'primer_nombre', 'segundo_nombre',
                'primer_apellido', 'segundo_apellido', 'fecha_nacimiento', 'genero',
                'telefono', 'celular', 'email', 'pais_id', 'departamento_id', 
                'municipio_id', 'direccion', 'status'
            ]));
        }

        // Crear o actualizar el registro del aspirante
        $aspirante = AspiranteComplementario::updateOrCreate(
            [
                'persona_id' => $persona->id,
                'complementario_id' => $id
            ],
            [
                'observaciones' => $request->observaciones,
                'estado' => 1, // Estado "En proceso"
            ]
        );

        // Redirigir a la segunda fase (subida de documentos) con el aspirante_id como parรกmetro
        return redirect()->route('programas-complementarios.documentos', ['id' => $id, 'aspirante_id' => $aspirante->id])
            ->with('success', 'Datos personales registrados correctamente. Ahora debe subir su documento de identidad.');

        // Verificar si ya existe un usuario con este email
        $existingUser = User::where('email', $request->email)->first();

        if (!$existingUser) {
            // Crear cuenta de usuario automáticamente
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->numero_documento), // Usar documento como contraseña
                'status' => 1,
                'persona_id' => $persona->id,
            ]);

            // Asignar rol de aspirante
            $user->assignRole('ASPIRANTE');
        }

        // Redirigir a la segunda fase (subida de documentos)
        return redirect()->route('programas-complementarios.documentos', $id)
            ->with('success', 'Datos personales registrados correctamente. Ahora debe subir su documento de identidad.')
            ->with('aspirante_id', $aspirante->id);

    }

    /**
     * Mostrar formulario para subir documentos
     */
    public function formularioDocumentos($id)
    {
        $programa = ComplementarioOfertado::findOrFail($id);

        // Obtener aspirante_id de la URL
        $aspirante_id = $request->query('aspirante_id');
        
        return view('complementarios.formulario_documentos', compact('programa', 'aspirante_id'));
    }


    /**
     * Procesar la subida de documentos
     */
    public function subirDocumento(Request $request, $id)
    {
        Log::info('subirDocumento method reached', [
            'request_data' => $request->all(),
            'files' => $request->files->all()
        ]);

        // Validar el archivo
        $request->validate([
            'documento_identidad' => 'required|file|mimes:pdf|max:5120', // 5MB mรกximo
            'aspirante_id' => 'required|exists:aspirantes_complementarios,id',
            'acepto_privacidad' => 'required|accepted',
        ]);

        try {
            // Obtener el aspirante
            $aspirante = AspiranteComplementario::findOrFail($request->aspirante_id);
            
            // Procesar el archivo y subirlo a Google Drive
            if ($request->hasFile('documento_identidad')) {
                $file = $request->file('documento_identidad');
                $fileName = 'documento_identidad_' . $aspirante->persona->numero_documento . '_' . time() . '.' . $file->getClientOriginalExtension();

                Log::info('Attempting to upload file to Google Drive', [
                    'file_name' => $fileName,
                    'file_size' => $file->getSize(),
                    'disk_config' => config('filesystems.disks.google')
                ]);

                // Subir a Google Drive
                $path = Storage::disk('google')->putFileAs('documentos_aspirantes', $file, $fileName);

                Log::info('File uploaded successfully', ['path' => $path]);

                // Actualizar el registro del aspirante con la informaciรณn del documento
                $aspirante->update([
                    'documento_identidad_path' => $path,
                    'documento_identidad_nombre' => $fileName,
                    'estado' => 2, // Estado "Documento subido"
                ]);
            }

            return redirect()->route('programas-complementarios.perfil-aspirante', ['id' => $aspirante->id])
                ->with('success', 'Documento subido exitosamente. Su inscripción está en proceso de revisión.');

        } catch (\Exception $e) {
            Log::error('Error al subir documento: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error al subir el documento. Por favor intente nuevamente.');
        }

        return view('complementarios.formulario_documentos', compact('programa'));

    }


    /**
      * Mostrar perfil del aspirante
      */
    public function perfilAspirante($id)
    {
        $aspirante = AspiranteComplementario::with('persona')->findOrFail($id);

        return view('complementarios.perfil_aspirante', compact('aspirante'));
    }

    public function validarSofia($complementarioId)
    {
        try {
            // Ejecutar el comando Artisan
            $exitCode = \Artisan::call('sofia:validar', [
                'complementario_id' => $complementarioId
            ]);

            $output = \Artisan::output();

            if ($exitCode === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Validación completada exitosamente',
                    'output' => $output
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error durante la validación',
                    'output' => $output
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar perfil propio del aspirante autenticado
     */
    public function miPerfil()
    {
        $user = Auth::user();
        $aspirante = AspiranteComplementario::with(['persona', 'complementario'])
            ->where('persona_id', $user->persona_id)
            ->first();

        if (!$aspirante) {
            return redirect()->route('home')->with('error', 'No se encontró información de aspirante para este usuario.');
        }

        return view('complementarios.mi_perfil', compact('aspirante'));
    }
}
