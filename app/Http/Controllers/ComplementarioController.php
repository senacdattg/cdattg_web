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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ValidarSofiaJob;
use App\Models\SofiaValidationProgress;
use Spatie\Permission\Models\Role;

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

        // Obtener datos reales para las estadísticas
        $estadisticas = $this->obtenerEstadisticasReales();

        return view('complementarios.estadisticas', compact('departamentos', 'municipios', 'estadisticas'));
    }

    /**
     * Obtener estadísticas reales de la base de datos
     */
    public function obtenerEstadisticasReales($filtros = [])
    {
        // Total de aspirantes
        $totalAspirantes = AspiranteComplementario::count();

        // Aspirantes aceptados (estado 3 = Aceptado)
        $aspirantesAceptados = AspiranteComplementario::where('estado', 3)->count();

        // Aspirantes pendientes (estado 1 = En proceso, 2 = Documento subido)
        $aspirantesPendientes = AspiranteComplementario::whereIn('estado', [1, 2])->count();

        // Programas activos
        $programasActivos = ComplementarioOfertado::where('estado', 1)->count();

        // Tendencia de inscripciones por mes (últimos 6 meses)
        $tendenciaInscripciones = AspiranteComplementario::selectRaw('
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                COUNT(*) as total
            ')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Distribución por programas
        $distribucionProgramas = AspiranteComplementario::selectRaw('
                complementarios_ofertados.nombre as programa,
                COUNT(*) as total
            ')
            ->join('complementarios_ofertados', 'aspirantes_complementarios.complementario_id', '=', 'complementarios_ofertados.id')
            ->groupBy('complementarios_ofertados.nombre')
            ->orderBy('total', 'desc')
            ->get();

        // Programas con mayor demanda
        $programasDemanda = AspiranteComplementario::selectRaw('
                complementarios_ofertados.nombre as programa,
                COUNT(*) as total_aspirantes,
                SUM(CASE WHEN aspirantes_complementarios.estado = 3 THEN 1 ELSE 0 END) as aceptados,
                SUM(CASE WHEN aspirantes_complementarios.estado IN (1, 2) THEN 1 ELSE 0 END) as pendientes
            ')
            ->join('complementarios_ofertados', 'aspirantes_complementarios.complementario_id', '=', 'complementarios_ofertados.id')
            ->groupBy('complementarios_ofertados.nombre', 'complementarios_ofertados.id')
            ->orderBy('total_aspirantes', 'desc')
            ->limit(10)
            ->get()
            ->map(function($programa) {
                $tasaAceptacion = $programa->total_aspirantes > 0 
                    ? round(($programa->aceptados / $programa->total_aspirantes) * 100, 1)
                    : 0;
                
                return [
                    'programa' => $programa->programa,
                    'total_aspirantes' => $programa->total_aspirantes,
                    'aceptados' => $programa->aceptados,
                    'pendientes' => $programa->pendientes,
                    'tasa_aceptacion' => $tasaAceptacion
                ];
            });

        return [
            'total_aspirantes' => $totalAspirantes,
            'aspirantes_aceptados' => $aspirantesAceptados,
            'aspirantes_pendientes' => $aspirantesPendientes,
            'programas_activos' => $programasActivos,
            'tendencia_inscripciones' => $tendenciaInscripciones,
            'distribucion_programas' => $distribucionProgramas,
            'programas_demanda' => $programasDemanda
        ];
    }

    /**
     * API para obtener estadísticas con filtros
     */
    public function apiEstadisticas(Request $request)
    {
        $filtros = $request->only(['fecha_inicio', 'fecha_fin', 'departamento_id', 'municipio_id', 'programa_id']);
        
        $estadisticas = $this->obtenerEstadisticasReales($filtros);
        
        return response()->json($estadisticas);
    }
    public function verAspirantes($curso)
    {
        // Find program by name (assuming curso is the program name)
        $programa = ComplementarioOfertado::where('nombre', str_replace('-', ' ', $curso))->firstOrFail();

        // Get aspirantes for this program
        $aspirantes = AspiranteComplementario::with(['persona', 'complementario'])
            ->where('complementario_id', $programa->id)
            ->get();

        // Check for existing validation progress for this program
        $existingProgress = SofiaValidationProgress::where('complementario_id', $programa->id)
            ->whereIn('status', ['pending', 'processing'])
            ->first();

        return view('complementarios.ver_aspirantes', compact('programa', 'aspirantes', 'existingProgress'));
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

    /**
     * Mostrar formulario general de inscripción a eventos del SENA
     */
    public function inscripcionGeneral()
    {
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

        // Obtener tipos de documento y géneros dinámicamente
        $tiposDocumento = $this->getTiposDocumento();
        $generos = $this->getGeneros();

        return view('complementarios.inscripcion_general', compact('categoriasConHijos', 'paises', 'departamentos', 'tiposDocumento', 'generos'));
    }

    /**
     * Procesar la inscripción general (solo datos de persona y caracterización)
     */
    public function procesarInscripcionGeneral(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'tipo_documento' => 'required|integer',
            'numero_documento' => 'required|string|max:191|unique:personas',
            'primer_nombre' => 'required|string|max:191',
            'segundo_nombre' => 'nullable|string|max:191',
            'primer_apellido' => 'required|string|max:191',
            'segundo_apellido' => 'nullable|string|max:191',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|integer',
            'telefono' => 'nullable|string|max:191',
            'celular' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:personas',
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
            return back()->withInput()->with('error', 'Ya existe una persona registrada con este número de documento o correo electrónico.');
        }

        // Crear nueva persona
        $persona = Persona::create($request->only([
            'tipo_documento', 'numero_documento', 'primer_nombre', 'segundo_nombre',
            'primer_apellido', 'segundo_apellido', 'fecha_nacimiento', 'genero',
            'telefono', 'celular', 'email', 'pais_id', 'departamento_id',
            'municipio_id', 'direccion'
        ]));

        // Guardar caracterización si se proporcionó (solo la primera categoría seleccionada)
        if ($request->categorias && is_array($request->categorias) && count($request->categorias) > 0) {
            $persona->caracterizacion_id = $request->categorias[0];
            $persona->save();
        }

        return redirect()->route('inscripcion.general')->with('success', '¡Registro exitoso! Sus datos han sido guardados correctamente.');
    }

    public function programasPublicos()
    {
        $programas = ComplementarioOfertado::with(['modalidad.parametro', 'jornada', 'diasFormacion'])->where('estado', 1)->get();
        $programas->each(function($programa) {
            $programa->icono = $this->getIconoForPrograma($programa->nombre);
        });

        // Obtener tipos de documento y géneros dinámicamente
        $tiposDocumento = $this->getTiposDocumento();
        $generos = $this->getGeneros();

        return view('complementarios.programas_publicos', compact('programas', 'tiposDocumento', 'generos'));
    }

    public function formularioInscripcion($id)
    {
        // Permitir acceso a usuarios no autenticados - el formulario crea la cuenta automáticamente
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

        // Obtener tipos de documento y géneros dinámicamente
        $tiposDocumento = $this->getTiposDocumento();
        $generos = $this->getGeneros();

        return view('complementarios.formulario_inscripcion', compact('programa', 'categoriasConHijos', 'paises', 'departamentos', 'tiposDocumento', 'generos'));
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
        // Si el usuario está autenticado, verificar si ya está inscrito en este programa
        if (Auth::check()) {
            $existingInscription = AspiranteComplementario::where('persona_id', Auth::user()->persona_id)
                ->where('complementario_id', $id)
                ->first();

            if ($existingInscription) {
                return redirect()->back()->with('error', 'Ya estás inscrito en este programa complementario.');
            }
        }

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
            ]) + ['user_create_id' => 1, 'user_edit_id' => 1]);
        }

        // Verificar si ya existe una inscripción para esta persona en este programa
        $existingInscription = AspiranteComplementario::where('persona_id', $persona->id)
            ->where('complementario_id', $id)
            ->first();

        if ($existingInscription) {
            return redirect()->back()->with('error', 'Ya estás inscrito en este programa complementario.');
        }

        // Crear el registro del aspirante
        $aspirante = AspiranteComplementario::create([
            'persona_id' => $persona->id,
            'complementario_id' => $id,
            'observaciones' => $request->observaciones,
            'estado' => 1, // Estado "En proceso"
        ]);

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
        return redirect()->route('programas-complementarios.documentos', ['id' => $id, 'aspirante_id' => $aspirante->id])
            ->with('success', 'Datos personales registrados correctamente. Ahora debe subir su documento de identidad.');

    }

    /**
     * Mostrar formulario para subir documentos
     */
    public function formularioDocumentos(Request $request, $id)
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

        Log::info('=== subirDocumento method reached ===', [
            'request_data' => $request->all(),
            'files' => $request->files->all(),
            'aspirante_id' => $request->aspirante_id,
            'has_file' => $request->hasFile('documento_identidad'),
            'file_info' => $request->hasFile('documento_identidad') ? [
                'name' => $request->file('documento_identidad')->getClientOriginalName(),
                'size' => $request->file('documento_identidad')->getSize(),
                'mime' => $request->file('documento_identidad')->getMimeType()
            ] : null
        ]);

        // Validar el archivo
        Log::info('Antes de validación - campos recibidos:', $request->all());
        $request->validate([
            'documento_identidad' => 'required|file|mimes:pdf|max:5120', // 5MB máximo
            'aspirante_id' => 'required|exists:aspirantes_complementarios,id',
            'acepto_privacidad' => 'required',
        ]);
        Log::info('Después de validación - validación pasó');

        try {
            // Obtener el aspirante
            Log::info('Buscando aspirante con ID: ' . $request->aspirante_id);
            $aspirante = AspiranteComplementario::findOrFail($request->aspirante_id);


            Log::info('Aspirante found', [
                'aspirante_id' => $aspirante->id,
                'persona_id' => $aspirante->persona_id,
                'numero_documento' => $aspirante->persona->numero_documento
            ]);

            // Procesar el archivo y subirlo a Google Drive
            if ($request->hasFile('documento_identidad')) {
                $file = $request->file('documento_identidad');
                
                // Crear nombre de archivo con formato: tipo_documento_NumeroDocumento_PrimerNombre_PrimerApellido_timestamp.pdf
                $tipoDocumento = $aspirante->persona->tipo_documento;
                $numeroDocumento = $aspirante->persona->numero_documento;
                $primerNombre = $aspirante->persona->primer_nombre;
                $primerApellido = $aspirante->persona->primer_apellido;
                $timestamp = now()->format('d-m-y-H-i-s');
                
                $fileName = "{$tipoDocumento}_{$numeroDocumento}_{$primerNombre}_{$primerApellido}_{$timestamp}.{$file->getClientOriginalExtension()}";

                Log::info('Attempting to upload file to Google Drive', [
                    'file_name' => $fileName,
                    'file_size' => $file->getSize(),
                    'disk_config' => config('filesystems.disks.google'),
                    'google_credentials_path' => storage_path('app/google-credentials.json'),
                    'credentials_exist' => file_exists(storage_path('app/google-credentials.json'))
                ]);

                // Verificar configuración de Google Drive
                Log::info('Google Drive config check', [
                    'client_id' => env('GOOGLE_DRIVE_CLIENT_ID') ? 'SET' : 'NOT SET',
                    'client_secret' => env('GOOGLE_DRIVE_CLIENT_SECRET') ? 'SET' : 'NOT SET',
                    'refresh_token' => env('GOOGLE_DRIVE_REFRESH_TOKEN') ? 'SET' : 'NOT SET',
                    'folder_id' => env('GOOGLE_DRIVE_FOLDER_ID') ? 'SET' : 'NOT SET'
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

            return redirect()->route('login.index')
                ->with('success', 'Documento subido exitosamente. Su cuenta de usuario ha sido creada. Puede iniciar sesión con su correo electrónico y número de documento como contraseña.');

        } catch (\Exception $e) {
            Log::error('Error al subir documento: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error al subir el documento. Por favor intente nuevamente.');
        }

        return view('complementarios.formulario_documentos', compact('programa'));

    }



    public function validarSofia($complementarioId)
    {
        try {
            \Log::info("Iniciando solicitud de validación SenaSofiaPlus", [
                'complementario_id' => $complementarioId,
                'user_id' => auth()->id(),
                'timestamp' => now()
            ]);

            // Verificar que el programa existe
            $programa = ComplementarioOfertado::findOrFail($complementarioId);
            \Log::info("Programa encontrado: {$programa->nombre}");

            // Contar aspirantes que necesitan validación
            $aspirantesCount = AspiranteComplementario::with('persona')
                ->where('complementario_id', $complementarioId)
                ->whereHas('persona', function($query) {
                    $query->whereIn('estado_sofia', [0, 2]);
                })
                ->count();

            \Log::info("Aspirantes que necesitan validación: {$aspirantesCount}");

            if ($aspirantesCount === 0) {
                \Log::warning("No hay aspirantes que necesiten validación para programa {$complementarioId}");
                return response()->json([
                    'success' => false,
                    'message' => 'No hay aspirantes que necesiten validación en este programa.'
                ]);
            }

            // Verificar si ya hay una validación en progreso para este programa
            $existingProgress = SofiaValidationProgress::where('complementario_id', $complementarioId)
                ->whereIn('status', ['pending', 'processing'])
                ->first();

            if ($existingProgress) {
                \Log::warning("Ya existe una validación en progreso para programa {$complementarioId}", [
                    'progress_id' => $existingProgress->id,
                    'status' => $existingProgress->status
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Ya hay una validación en progreso para este programa. Espere a que termine.'
                ]);
            }

            // Crear registro de progreso
            $progress = SofiaValidationProgress::create([
                'complementario_id' => $complementarioId,
                'user_id' => auth()->id(),
                'status' => 'pending',
                'total_aspirantes' => $aspirantesCount,
                'processed_aspirantes' => 0,
                'successful_validations' => 0,
                'failed_validations' => 0,
            ]);

            \Log::info("Registro de progreso creado", [
                'progress_id' => $progress->id,
                'total_aspirantes' => $aspirantesCount
            ]);

            // Dispatch el job a la queue con configuración optimizada
            ValidarSofiaJob::dispatch($complementarioId, auth()->id(), $progress->id)
                ->onQueue('sofia-validation') // Usar cola específica para validaciones Sofia
                ->delay(now()->addSeconds(2)); // Pequeño delay para asegurar que el registro esté guardado

            \Log::info("Job despachado a la cola", [
                'job_class' => ValidarSofiaJob::class,
                'queue' => 'sofia-validation',
                'delay' => 2
            ]);

            return response()->json([
                'success' => true,
                'message' => "Validación iniciada para {$aspirantesCount} aspirantes. El proceso se ejecutará en segundo plano.",
                'aspirantes_count' => $aspirantesCount,
                'progress_id' => $progress->id
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error("Programa no encontrado: {$complementarioId}", ['exception' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Programa no encontrado.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error("Error iniciando validación SenaSofiaPlus", [
                'complementario_id' => $complementarioId,
                'user_id' => auth()->id(),
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener el progreso de una validación
     */
    public function getValidationProgress($progressId)
    {
        try {
            $progress = SofiaValidationProgress::with('complementario')->findOrFail($progressId);

            return response()->json([
                'success' => true,
                'progress' => [
                    'id' => $progress->id,
                    'status' => $progress->status,
                    'status_label' => $progress->status_label,
                    'total_aspirantes' => $progress->total_aspirantes,
                    'processed_aspirantes' => $progress->processed_aspirantes,
                    'successful_validations' => $progress->successful_validations,
                    'failed_validations' => $progress->failed_validations,
                    'progress_percentage' => $progress->progress_percentage,
                    'started_at' => $progress->started_at?->format('d/m/Y H:i:s'),
                    'completed_at' => $progress->completed_at?->format('d/m/Y H:i:s'),
                    'errors' => $progress->errors,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el progreso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Agregar aspirante existente a un programa complementario
     */
    public function agregarAspirante(Request $request, $complementarioId)
    {
        $request->validate([
            'numero_documento' => 'required|string|max:191',
        ]);

        try {
            // Verificar que el programa existe
            $programa = ComplementarioOfertado::findOrFail($complementarioId);

            // Buscar persona por número de documento
            $persona = Persona::where('numero_documento', $request->numero_documento)->first();

            if (!$persona) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ninguna persona registrada con el número de documento "' . $request->numero_documento . '".'
                ]);
            }

            // Verificar si ya está inscrita en este programa
            $aspiranteExistente = AspiranteComplementario::where('persona_id', $persona->id)
                ->where('complementario_id', $complementarioId)
                ->first();

            if ($aspiranteExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'La persona con documento "' . $request->numero_documento . '" ya se encuentra inscrita en este programa complementario.'
                ]);
            }

            // Crear nuevo aspirante - ahora permite múltiples programas por persona
            AspiranteComplementario::create([
                'persona_id' => $persona->id,
                'complementario_id' => $complementarioId,
                'estado' => 1, // Estado "En proceso"
                'observaciones' => 'Agregado manualmente desde gestión de aspirantes'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Aspirante agregado exitosamente. ' . $persona->primer_nombre . ' ' . $persona->primer_apellido . ' ha sido inscrito en el programa.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error agregando aspirante: ' . $e->getMessage(), [
                'complementario_id' => $complementarioId,
                'numero_documento' => $request->numero_documento,
                'exception' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor. Por favor intente nuevamente.'
            ], 500);
        }
    }

    /**
     * Mostrar perfil propio del usuario autenticado
     */
    public function Perfil()
    {
        $user = Auth::user();

        // Verificar que el usuario esté autenticado
        if (!$user) {
            return redirect('/login')->with('error', 'Debe iniciar sesión para acceder a su perfil.');
        }

        // Verificar que el usuario tenga permisos para ver personas
        if (!$user->can('VER PERSONA')) {
            return redirect()->route('home')->with('error', 'Acceso no autorizado.');
        }

        // Obtener la persona del usuario
        $persona = $user->persona;

        if (!$persona) {
            return redirect()->route('home')->with('error', 'No se encontró información de persona para este usuario.');
        }

        // Si es aspirante, también obtener sus programas complementarios
        $aspirantes = [];
        if ($user->hasRole('ASPIRANTE')) {
            $aspirantes = AspiranteComplementario::with(['persona', 'complementario'])
                ->where('persona_id', $user->persona_id)
                ->get();
        }

        // Obtener tipos de documento y géneros dinámicamente
        $tiposDocumento = $this->getTiposDocumento();
        $generos = $this->getGeneros();

        return view('personas.show', compact('persona', 'aspirantes', 'user', 'tiposDocumento', 'generos'));
    }

    /**
     * Método auxiliar para obtener tipos de documento dinámicamente desde el tema-parametro
     */
    private function getTiposDocumento()
    {
        // Buscar el tema "TIPO DE DOCUMENTO"
        $temaTipoDocumento = \App\Models\Tema::where('name', 'TIPO DE DOCUMENTO')->first();

        if (!$temaTipoDocumento) {
            // Fallback: devolver valores hardcodeados si no se encuentra el tema
            return collect([
                ['id' => 3, 'name' => 'CEDULA DE CIUDADANIA'],
                ['id' => 4, 'name' => 'CEDULA DE EXTRANJERIA'],
                ['id' => 5, 'name' => 'PASAPORTE'],
                ['id' => 6, 'name' => 'TARJETA DE IDENTIDAD'],
                ['id' => 7, 'name' => 'REGISTRO CIVIL'],
                ['id' => 8, 'name' => 'SIN IDENTIFICACION'],
            ]);
        }

        // Obtener parámetros activos del tema
        return $temaTipoDocumento->parametros()
            ->where('parametros_temas.status', 1)
            ->orderBy('parametros.name')
            ->get(['parametros.id', 'parametros.name']);
    }

    /**
     * Método auxiliar para obtener géneros dinámicamente desde el tema-parametro
     */
    private function getGeneros()
    {
        // Buscar el tema "GENERO"
        $temaGenero = \App\Models\Tema::where('name', 'GENERO')->first();

        if (!$temaGenero) {
            // Fallback: devolver valores hardcodeados si no se encuentra el tema
            return collect([
                ['id' => 9, 'name' => 'MASCULINO'],
                ['id' => 10, 'name' => 'FEMENINO'],
                ['id' => 11, 'name' => 'NO DEFINE'],
            ]);
        }

        // Obtener parámetros activos del tema
        return $temaGenero->parametros()
            ->where('parametros_temas.status', 1)
            ->orderBy('parametros.name')
            ->get(['parametros.id', 'parametros.name']);
    }
    /**
     * Eliminar aspirante de un programa complementario
     */
    public function eliminarAspirante($complementarioId, $aspiranteId)
    {
        try {
            // Verificar que el programa existe
            $programa = ComplementarioOfertado::findOrFail($complementarioId);

            // Verificar que el aspirante existe y pertenece al programa
            $aspirante = AspiranteComplementario::where('id', $aspiranteId)
                ->where('complementario_id', $complementarioId)
                ->with('persona')
                ->firstOrFail();

            // Verificar permisos del usuario (solo administradores pueden eliminar)
            if (!auth()->user()->can('ELIMINAR ASPIRANTE COMPLEMENTARIO')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para eliminar aspirantes.'
                ], 403);
            }

            // Guardar información del aspirante antes de eliminar para el mensaje
            $personaNombre = $aspirante->persona->primer_nombre . ' ' . $aspirante->persona->primer_apellido;
            $numeroDocumento = $aspirante->persona->numero_documento;

            // Eliminar el aspirante
            $aspirante->delete();

            \Log::info('Aspirante eliminado exitosamente', [
                'aspirante_id' => $aspiranteId,
                'complementario_id' => $complementarioId,
                'persona_id' => $aspirante->persona_id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Aspirante eliminado exitosamente. ' . $personaNombre . ' (' . $numeroDocumento . ') ya no está inscrito en el programa.'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aspirante o programa no encontrado.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error eliminando aspirante: ' . $e->getMessage(), [
                'complementario_id' => $complementarioId,
                'aspirante_id' => $aspiranteId,
                'user_id' => auth()->id(),
                'exception' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor. Por favor intente nuevamente.'
            ], 500);
        }
    }
}
