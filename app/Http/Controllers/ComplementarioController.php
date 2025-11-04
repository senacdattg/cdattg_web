<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @deprecated Esta clase ha sido refactorizada y dividida en controladores especializados.
 * Los métodos han sido movidos a:
 * - ProgramaComplementarioController: Gestión de programas
 * - AspiranteComplementarioController: Gestión de aspirantes
 * - InscripcionComplementarioController: Procesos de inscripción
 * - EstadisticaComplementarioController: Estadísticas
 * - ValidacionSofiaController: Validación SOFIA
 * - DocumentoComplementarioController: Gestión de documentos
 * - PerfilComplementarioController: Perfiles de usuario
 *
 * Esta clase se mantiene temporalmente para compatibilidad, pero será eliminada en futuras versiones.
 */
class ComplementarioController extends Controller
{
    /**
     * @deprecated Esta clase ha sido refactorizada. Use AspiranteComplementarioController::gestionAspirantes()
     */
    public function gestionAspirantes()
    {
        return app(\App\Http\Controllers\Complementarios\AspiranteComplementarioController::class)->gestionAspirantes();
    }

    /**
     * @deprecated Esta clase ha sido refactorizada. Use DocumentoComplementarioController::procesarDocumentos()
     */
    public function procesarDcoumentos()
    {
        return app(\App\Http\Controllers\Complementarios\DocumentoComplementarioController::class)->procesarDocumentos();
    }

    /**
     * @deprecated Esta clase ha sido refactorizada. Use ProgramaComplementarioController::gestionProgramasComplementarios()
     */
    public function gestionProgramasComplementarios()
    {
        return app(\App\Http\Controllers\Complementarios\ProgramaComplementarioController::class)->gestionProgramasComplementarios();
    }

    /**
     * @deprecated Esta clase ha sido refactorizada. Use ProgramaComplementarioController::create()
     */
    public function create()
    {
        return app(\App\Http\Controllers\Complementarios\ProgramaComplementarioController::class)->create();
    }
    /**
     * @deprecated Esta clase ha sido refactorizada. Use EstadisticaComplementarioController::estadisticas()
     */
    public function estadisticas()
    {
        return app(\App\Http\Controllers\Complementarios\EstadisticaComplementarioController::class)->estadisticas();
    }

    /**
     * @deprecated Esta clase ha sido refactorizada. Use EstadisticaComplementarioController::obtenerEstadisticasReales()
     */
    public function obtenerEstadisticasReales($filtros = [])
    {
        return app(\App\Services\EstadisticaComplementarioService::class)->obtenerEstadisticasReales($filtros);
    }

    /**
     * @deprecated Esta clase ha sido refactorizada. Use EstadisticaComplementarioController::apiEstadisticas()
     */
    public function apiEstadisticas(Request $request)
    {
        return app(\App\Http\Controllers\Complementarios\EstadisticaComplementarioController::class)->apiEstadisticas($request);
    }
    /**
     * @deprecated Esta clase ha sido refactorizada. Use AspiranteComplementarioController::verAspirantes()
     */
    public function verAspirantes($curso)
    {
        return app(\App\Http\Controllers\Complementarios\AspiranteComplementarioController::class)->verAspirantes($curso);
    }
    /**
     * @deprecated Esta clase ha sido refactorizada. Use ProgramaComplementarioController::verPrograma()
     */
    public function verPrograma($id)
    {
        return app(\App\Http\Controllers\Complementarios\ProgramaComplementarioController::class)->verPrograma($id);
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
     * @deprecated Esta clase ha sido refactorizada. Use InscripcionComplementarioController::inscripcionGeneral()
     */
    public function inscripcionGeneral()
    {
        return app(\App\Http\Controllers\Complementarios\InscripcionComplementarioController::class)->inscripcionGeneral();
    }

    /**
     * @deprecated Esta clase ha sido refactorizada. Use InscripcionComplementarioController::procesarInscripcionGeneral()
     */
    public function procesarInscripcionGeneral(Request $request)
    {
        return app(\App\Http\Controllers\Complementarios\InscripcionComplementarioController::class)->procesarInscripcionGeneral($request);
    }

    /**
     * @deprecated Esta clase ha sido refactorizada. Use ProgramaComplementarioController::programasPublicos()
     */
    public function programasPublicos()
    {
        return app(\App\Http\Controllers\Complementarios\ProgramaComplementarioController::class)->programasPublicos();
    }

    /**
     * @deprecated Esta clase ha sido refactorizada. Use ProgramaComplementarioController::verProgramas()
     */
    public function verProgramas()
    {
        return app(\App\Http\Controllers\Complementarios\ProgramaComplementarioController::class)->verProgramas();
    }

    /**
     * Obtener la clase CSS para el badge según el estado del programa
     */
    private function getBadgeClassForEstado($estado)
    {
        $badgeClasses = [
            0 => 'bg-secondary', // Sin Oferta
            1 => 'bg-success',   // Con Oferta
            2 => 'bg-warning',   // Cupos Llenos
        ];

        return $badgeClasses[$estado] ?? 'bg-secondary';
    }

    /**
     * Obtener el label del estado del programa
     */
    private function getEstadoLabel($estado)
    {
        $estados = [
            0 => 'Sin Oferta',
            1 => 'Con Oferta',
            2 => 'Cupos Llenos',
        ];

        return $estados[$estado] ?? 'Desconocido';
    }

    /**
     * @deprecated Esta clase ha sido refactorizada. Use InscripcionComplementarioController::formularioInscripcion()
     */
    public function formularioInscripcion($id)
    {
        return app(\App\Http\Controllers\Complementarios\InscripcionComplementarioController::class)->formularioInscripcion($id);
    }

    /**
     * @deprecated Esta clase ha sido refactorizada. Use ProgramaComplementarioController::edit()
     */
    public function edit($id)
    {
        return app(\App\Http\Controllers\Complementarios\ProgramaComplementarioController::class)->edit($id);
    }

    /**
     * @deprecated Esta clase ha sido refactorizada. Use ProgramaComplementarioController::store()
     */
    public function store(Request $request)
    {
        return app(\App\Http\Controllers\Complementarios\ProgramaComplementarioController::class)->store($request);
    }

    /**
     * @deprecated Esta clase ha sido refactorizada. Use ProgramaComplementarioController::update()
     */
    public function update(Request $request, $id)
    {
        return app(\App\Http\Controllers\Complementarios\ProgramaComplementarioController::class)->update($request, $id);
    }

    /**
     * @deprecated Esta clase ha sido refactorizada. Use ProgramaComplementarioController::destroy()
     */
    public function destroy($id)
    {
        return app(\App\Http\Controllers\Complementarios\ProgramaComplementarioController::class)->destroy($id);
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
            'caracterizacion_id' => 'nullable|exists:categorias_caracterizacion_complementarios,id',
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
                'municipio_id', 'direccion', 'caracterizacion_id'
            ]));
        } else {
            // Crear nueva persona
            $persona = Persona::create($request->only([
                'tipo_documento', 'numero_documento', 'primer_nombre', 'segundo_nombre',
                'primer_apellido', 'segundo_apellido', 'fecha_nacimiento', 'genero',
                'telefono', 'celular', 'email', 'pais_id', 'departamento_id',
                'municipio_id', 'direccion', 'caracterizacion_id', 'status'
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
        } else {
            // Si el usuario ya existe, verificar si tiene rol VISITANTE y cambiarlo a ASPIRANTE
            if ($existingUser->hasRole('VISITANTE')) {
                $existingUser->removeRole('VISITANTE');
                $existingUser->assignRole('ASPIRANTE');
            }
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
     * @deprecated Esta clase ha sido refactorizada. Use AspiranteComplementarioController::agregarAspirante()
     */
    public function agregarAspirante(Request $request, $complementarioId)
    {
        return app(\App\Http\Controllers\Complementarios\AspiranteComplementarioController::class)->agregarAspirante($request, $complementarioId);
    }

    /**
     * @deprecated Esta clase ha sido refactorizada. Use PerfilComplementarioController::Perfil()
     */
    public function Perfil()
    {
        return app(\App\Http\Controllers\Complementarios\PerfilComplementarioController::class)->Perfil();
    }

    /**
     * @deprecated Esta clase ha sido refactorizada. Use AspiranteComplementarioController::eliminarAspirante()
     */
    public function eliminarAspirante($complementarioId, $aspiranteId)
    {
        return app(\App\Http\Controllers\Complementarios\AspiranteComplementarioController::class)->eliminarAspirante($complementarioId, $aspiranteId);
    }

    /**
     * @deprecated Esta clase ha sido refactorizada. Use AspiranteComplementarioController::exportarAspirantesExcel()
     */
    public function exportarAspirantesExcel($complementarioId)
    {
        return app(\App\Http\Controllers\Complementarios\AspiranteComplementarioController::class)->exportarAspirantesExcel($complementarioId);
    }
}
