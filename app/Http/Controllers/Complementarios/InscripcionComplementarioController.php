<?php

namespace App\Http\Controllers\Complementarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplementarioOfertado;
use App\Models\Persona;
use App\Models\AspiranteComplementario;
use App\Models\User;
use App\Models\Pais;
use App\Models\Departamento;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\ComplementarioService;
use App\Repositories\TemaRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InscripcionComplementarioController extends Controller
{
    private const REQUIRED_INTEGER = 'required|integer';
    private const REQUIRED_STRING_MAX_191 = 'required|string|max:191';
    private const NULLABLE_STRING_MAX_191 = 'nullable|string|max:191';
    private const REQUIRED_EMAIL_MAX_191 = 'required|email|max:191';

    protected $complementarioService;
    protected $temaRepository;

    public function __construct(ComplementarioService $complementarioService, TemaRepository $temaRepository)
    {
        $this->complementarioService = $complementarioService;
        $this->temaRepository = $temaRepository;
    }

    /**
     * Mostrar formulario general de inscripción a eventos del SENA
     */
    public function inscripcionGeneral()
    {
        // Obtener categorías de caracterización principales con sus hijos
        $categoriasConHijos = $this->obtenerCaracterizacionesAgrupadas();

        $paises = Pais::all();
        $departamentos = Departamento::all();

        // Obtener tipos de documento y géneros dinámicamente
        $tiposDocumento = $this->complementarioService->getTiposDocumento();
        $generos = $this->complementarioService->getGeneros();

        return view('complementarios.inscripciones.general', compact('categoriasConHijos', 'paises', 'departamentos', 'tiposDocumento', 'generos'));
    }

    /**
     * Procesar la inscripción general (solo datos de persona y caracterización)
     */
    public function procesarInscripcionGeneral(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'tipo_documento' => self::REQUIRED_INTEGER,
            'numero_documento' => 'required|string|max:191|unique:personas',
            'primer_nombre' => self::REQUIRED_STRING_MAX_191,
            'segundo_nombre' => self::NULLABLE_STRING_MAX_191,
            'primer_apellido' => self::REQUIRED_STRING_MAX_191,
            'segundo_apellido' => self::NULLABLE_STRING_MAX_191,
            'fecha_nacimiento' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        return;
                    }

                    try {
                        $fechaNacimiento = Carbon::parse($value);
                        $edadMinima = Carbon::now()->subYears(14);

                        if ($fechaNacimiento->gt($edadMinima)) {
                            $fail('Debe tener al menos 14 años para registrarse.');
                        }
                    } catch (\Exception $e) {
                        $fail('La fecha de nacimiento no es válida.');
                    }
                },
            ],
            'genero' => self::REQUIRED_INTEGER,
            'telefono' => self::NULLABLE_STRING_MAX_191,
            'celular' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:personas',
            'pais_id' => self::REQUIRED_INTEGER,
            'departamento_id' => self::REQUIRED_INTEGER,
            'municipio_id' => self::REQUIRED_INTEGER,
            'direccion' => self::NULLABLE_STRING_MAX_191,
            'observaciones' => 'nullable|string',
            'parametro_id' => 'nullable|exists:parametros,id',
        ]);

        // Verificar si ya existe una persona con el mismo documento o email
        $personaExistente = Persona::where('numero_documento', $request->numero_documento)
            ->orWhere('email', $request->email)
            ->first();

        if ($personaExistente) {
            return back()
                ->withInput()
                ->with('error', 'Ya existe una persona registrada con este número de documento o correo electrónico.');
        }

        // Crear nueva persona
        $persona = Persona::create($request->only([
            'tipo_documento',
            'numero_documento',
            'primer_nombre',
            'segundo_nombre',
            'primer_apellido',
            'segundo_apellido',
            'fecha_nacimiento',
            'genero',
            'telefono',
            'celular',
            'email',
            'pais_id',
            'departamento_id',
            'municipio_id',
            'direccion',
            'caracterizacion_id'
        ]));

        return redirect()
            ->route('inscripcion.general')
            ->with('success', '¡Registro exitoso! Sus datos han sido guardados correctamente.');
    }

    /**
     * Mostrar formulario de inscripción a programa específico
     */
    public function formularioInscripcion($id)
    {
        // Permitir acceso a usuarios no autenticados - el formulario crea la cuenta automáticamente
        $programa = ComplementarioOfertado::with(['modalidad.parametro', 'jornada'])->findOrFail($id);

        $documentos = $this->buildTemaPayload(
            $this->temaRepository->obtenerTiposDocumento(),
            $this->complementarioService->getTiposDocumento()
        );
        $generos = $this->buildTemaPayload(
            $this->temaRepository->obtenerGeneros(),
            $this->complementarioService->getGeneros()
        );
        $caracterizaciones = $this->buildTemaPayload(
            $this->temaRepository->obtenerCaracterizacionesComplementarias()
        );
        $vias = $this->buildTemaPayload($this->temaRepository->obtenerVias());
        $letras = $this->buildTemaPayload($this->temaRepository->obtenerLetras());
        $cardinales = $this->buildTemaPayload($this->temaRepository->obtenerCardinales());

        $paises = Pais::all();
        $departamentos = Departamento::all();
        $municipios = collect();

        $categoriasConHijos = $this->obtenerCaracterizacionesAgrupadas($caracterizaciones);

        $personaAutenticada = Auth::check() ? Auth::user()->persona : null;
        $this->prefillOldInput($personaAutenticada);

        return view('complementarios.inscripciones.create', [
            'programa' => $programa,
            'categoriasConHijos' => $categoriasConHijos,
            'paises' => $paises,
            'departamentos' => $departamentos,
            'municipios' => $municipios,
            'documentos' => $documentos,
            'generos' => $generos,
            'caracterizaciones' => $caracterizaciones,
            'vias' => $vias,
            'letras' => $letras,
            'cardinales' => $cardinales,
        ]);
    }

    private function obtenerCaracterizacionesAgrupadas(?object $caracterizacionesPayload = null): Collection
    {
        $payload = $caracterizacionesPayload ?? $this->buildTemaPayload(
            $this->temaRepository->obtenerCaracterizacionesComplementarias()
        );

        $parametros = collect($payload->parametros ?? []);

        if ($parametros->isEmpty()) {
            return collect();
        }

        $hijos = $parametros->map(function ($parametro) {
            $id = data_get($parametro, 'id');
            $nombre = data_get($parametro, 'name', data_get($parametro, 'nombre', ''));

            if (!$id) {
                return null;
            }

            $formatted = (string) Str::of($nombre ?? '')
                ->replace('_', ' ')
                ->lower()
                ->title();

            return (object) [
                'id' => $id,
                'nombre' => $formatted,
            ];
        })->filter()->values();

        if ($hijos->isEmpty()) {
            return collect();
        }

        return collect([
            [
                'id' => $payload->id ?? null,
                'nombre' => 'Opciones disponibles',
                'hijos' => $hijos,
            ],
        ]);
    }

    /**
     * Procesar la inscripción del aspirante
     */
    public function procesarInscripcion(Request $request, $id)
    {
        try {
            // Verificar si el usuario ya está inscrito
            $existingInscription = $this->checkExistingInscription($id);
            if ($existingInscription) {
                return redirect()->back()->with('error', 'Ya estás inscrito en este programa complementario.');
            }

            // Validar los datos del formulario
            $validatedData = $this->validateInscripcionData($request);

            // Procesar persona y usuario
            $persona = $this->processPersona($validatedData);
            $this->processUser($validatedData, $persona);

            // Crear aspirante
            $aspirante = $this->createAspirante($persona, $id, $validatedData);

            // Procesar documento
            return $this->processDocumento($request, $aspirante, $persona);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error al procesar inscripción', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'programa_id' => $id,
                'request_data' => $request->except(['documento_identidad', '_token'])
            ]);

            return redirect()->back()
                ->with('error', 'Ocurrió un error al procesar la inscripción. Por favor, inténtalo nuevamente.')
                ->withInput();
        }
    }

    /**
     * Verificar si el usuario ya está inscrito
     */
    private function checkExistingInscription($programaId)
    {
        if (!Auth::check()) {
            return false;
        }

        return AspiranteComplementario::where('persona_id', Auth::user()->persona_id)
            ->where('complementario_id', $programaId)
            ->first();
    }

    /**
     * Validar los datos del formulario
     */
    private function validateInscripcionData(Request $request)
    {
        return $request->validate([
            'tipo_documento' => self::REQUIRED_INTEGER,
            'numero_documento' => 'required|string|max:191',
            'primer_nombre' => self::REQUIRED_STRING_MAX_191,
            'segundo_nombre' => self::NULLABLE_STRING_MAX_191,
            'primer_apellido' => self::REQUIRED_STRING_MAX_191,
            'segundo_apellido' => self::NULLABLE_STRING_MAX_191,
            'fecha_nacimiento' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        return;
                    }

                    try {
                        $fechaNacimiento = Carbon::parse($value);
                        $edadMinima = Carbon::now()->subYears(14);

                        if ($fechaNacimiento->gt($edadMinima)) {
                            $fail('Debe tener al menos 14 años para registrarse.');
                        }
                    } catch (\Exception $e) {
                        $fail('La fecha de nacimiento no es válida.');
                    }
                },
            ],
            'genero' => self::REQUIRED_INTEGER,
            'telefono' => self::NULLABLE_STRING_MAX_191,
            'celular' => 'required|string|max:191',
            'email' => self::REQUIRED_EMAIL_MAX_191,
            'pais_id' => self::REQUIRED_INTEGER,
            'departamento_id' => self::REQUIRED_INTEGER,
            'municipio_id' => self::REQUIRED_INTEGER,
            'direccion' => self::NULLABLE_STRING_MAX_191,
            'observaciones' => 'nullable|string',
            'parametro_id' => 'nullable|exists:parametros,id',
            'documento_identidad' => 'required|file|mimes:pdf,application/pdf|max:5120',
            'acepto_privacidad' => 'required',
            'acepto_terminos' => 'required',
        ]);
    }

    /**
     * Procesar la persona (crear o actualizar)
     */
    private function processPersona(array $validatedData)
    {
        $personaExistente = Persona::where('numero_documento', $validatedData['numero_documento'])
            ->orWhere('email', $validatedData['email'])
            ->first();

        if ($personaExistente) {
            // Si ya existe, usar esa persona
            $persona = $personaExistente;

            // Actualizar datos si es necesario
            $persona->update([
                'tipo_documento' => $validatedData['tipo_documento'] ?? $persona->tipo_documento,
                'numero_documento' => $validatedData['numero_documento'] ?? $persona->numero_documento,
                'primer_nombre' => $validatedData['primer_nombre'] ?? $persona->primer_nombre,
                'segundo_nombre' => $validatedData['segundo_nombre'] ?? $persona->segundo_nombre,
                'primer_apellido' => $validatedData['primer_apellido'] ?? $persona->primer_apellido,
                'segundo_apellido' => $validatedData['segundo_apellido'] ?? $persona->segundo_apellido,
                'fecha_nacimiento' => $validatedData['fecha_nacimiento'] ?? $persona->fecha_nacimiento,
                'genero' => $validatedData['genero'] ?? $persona->genero,
                'telefono' => $validatedData['telefono'] ?? $persona->telefono,
                'celular' => $validatedData['celular'] ?? $persona->celular,
                'email' => $validatedData['email'] ?? $persona->email,
                'pais_id' => $validatedData['pais_id'] ?? $persona->pais_id,
                'departamento_id' => $validatedData['departamento_id'] ?? $persona->departamento_id,
                'municipio_id' => $validatedData['municipio_id'] ?? $persona->municipio_id,
                'direccion' => $validatedData['direccion'] ?? $persona->direccion,
                'caracterizacion_id' => $validatedData['parametro_id'] ?? $persona->caracterizacion_id,
            ]);
        } else {
            // Crear nueva persona
            $persona = Persona::create($request->only([
                'tipo_documento',
                'numero_documento',
                'primer_nombre',
                'segundo_nombre',
                'primer_apellido',
                'segundo_apellido',
                'fecha_nacimiento',
                'genero',
                'telefono',
                'celular',
                'email',
                'pais_id',
                'departamento_id',
                'municipio_id',
                'direccion',
                'caracterizacion_id',
                'status'
            ]) + ['user_create_id' => 1, 'user_edit_id' => 1]);
        }

        return Persona::create($validatedData + ['user_create_id' => 1, 'user_edit_id' => 1]);
    }

    /**
     * Procesar el usuario (crear o actualizar rol)
     */
    private function processUser(array $validatedData, Persona $persona)
    {
        $existingUser = User::where('email', $validatedData['email'])->first();

        if (!$existingUser) {
            $user = User::create([
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['numero_documento']),
                'status' => 1,
                'persona_id' => $persona->id,
            ]);
            $user->assignRole('ASPIRANTE');
            
            // Enviar email de verificación automáticamente
            $user->sendEmailVerificationNotification();
        } elseif ($existingUser->hasRole('VISITANTE')) {
            $existingUser->removeRole('VISITANTE');
            $existingUser->assignRole('ASPIRANTE');
        }
    }

    /**
     * Crear el registro del aspirante
     */
    private function createAspirante(Persona $persona, $programaId, array $validatedData)
    {
        return AspiranteComplementario::create([
            'persona_id' => $persona->id,
            'complementario_id' => $programaId,
            'observaciones' => $validatedData['observaciones'] ?? null,
            'estado' => 1, // Estado "En proceso"
        ]);
    }

    /**
     * Procesar el documento de identidad
     */
    private function processDocumento(Request $request, AspiranteComplementario $aspirante, Persona $persona)
    {
        try {
            Log::info('Procesando documento de identidad', [
                'aspirante_id' => $aspirante->id,
                'persona_id' => $persona->id,
                'file_name' => $request->file('documento_identidad')->getClientOriginalName()
            ]);

            if ($request->hasFile('documento_identidad')) {
                $file = $request->file('documento_identidad');

                $tipoDocumento = $persona->tipoDocumento->name ?? 'DOC';
                $numeroDocumento = $persona->numero_documento;
                $primerNombre = $persona->primer_nombre;
                $primerApellido = $persona->primer_apellido;
                $timestamp = now()->format('d-m-y-H-i-s');

                $fileName = "{$tipoDocumento}_{$numeroDocumento}_{$primerNombre}_" .
                           "{$primerApellido}_{$timestamp}.{$file->getClientOriginalExtension()}";

                Log::info('Subiendo archivo a Google Drive', [
                    'file_name' => $fileName,
                    'file_size' => $file->getSize()
                ]);

                $path = Storage::disk('google')->putFileAs('documentos_aspirantes', $file, $fileName);

                Log::info('Archivo subido exitosamente', ['path' => $path]);

                $aspirante->update([
                    'documento_identidad_path' => $path,
                    'documento_identidad_nombre' => $fileName,
                    'estado' => 2, // Estado "Completo"
                ]);

                Log::info('Aspirante actualizado con documento', [
                    'aspirante_id' => $aspirante->id,
                    'estado' => $aspirante->estado
                ]);
            }

            return redirect()->route('login.index')->with(
                'success',
                '¡Inscripción completada exitosamente! Su cuenta de usuario ha sido creada. ' .
                'Puede iniciar sesión con su correo electrónico y número de documento como contraseña.'
            );

        } catch (\Exception $e) {
            Log::error('Error al procesar documento: ' . $e->getMessage(), [
                'exception' => $e,
                'aspirante_id' => $aspirante->id,
                'trace' => $e->getTraceAsString()
            ]);

            $aspirante->update(['estado' => 1]);

            return back()->withInput()->with(
                'error',
                'Los datos personales fueron registrados correctamente, ' .
                'pero hubo un error al procesar el documento. Por favor contacte al administrador.'
            );
        }
    }

    private function buildTemaPayload($tema = null, $fallback = null): object
    {
        if ($tema && $tema->parametros?->count()) {
            return $tema;
        }

        $parametros = $this->normalizeParametrosCollection($fallback);

        return (object) [
            'parametros' => $parametros,
        ];
    }

    private function normalizeParametrosCollection($items): Collection
    {
        return collect($items)->map(function ($item) {
            $id = data_get($item, 'id');
            $name = data_get($item, 'name', data_get($item, 'nombre', ''));

            if ($id === null) {
                return null;
            }

            return (object) [
                'id' => $id,
                'name' => strtoupper((string) $name),
            ];
        })->filter()->values();
    }

    private function prefillOldInput($persona): void
    {
        if (!$persona instanceof Persona || session()->hasOldInput()) {
            return;
        }

        $fechaNacimiento = $persona->fecha_nacimiento;
        if ($fechaNacimiento && !$fechaNacimiento instanceof Carbon) {
            try {
                $fechaNacimiento = Carbon::parse($fechaNacimiento);
            } catch (\Throwable $th) {
                $fechaNacimiento = null;
            }
        }

        session()->flashInput([
            'tipo_documento' => $persona->tipo_documento,
            'numero_documento' => $persona->numero_documento,
            'primer_nombre' => $persona->primer_nombre,
            'segundo_nombre' => $persona->segundo_nombre,
            'primer_apellido' => $persona->primer_apellido,
            'segundo_apellido' => $persona->segundo_apellido,
            'email' => $persona->email,
            'fecha_nacimiento' => $fechaNacimiento ? $fechaNacimiento->format('Y-m-d') : null,
            'genero' => $persona->genero,
            'telefono' => $persona->telefono,
            'celular' => $persona->celular,
            'pais_id' => $persona->pais_id,
            'departamento_id' => $persona->departamento_id,
            'municipio_id' => $persona->municipio_id,
            'direccion' => $persona->direccion,
            'caracterizacion_id' => $persona->caracterizacion_id,
        ]);
    }
}
