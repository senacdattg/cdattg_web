<?php

namespace App\Http\Controllers\Complementarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplementarioOfertado;
use App\Models\Persona;
use App\Models\AspiranteComplementario;
use App\Models\User;
use App\Models\Tema;
use App\Models\Pais;
use App\Models\Departamento;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use App\Services\ComplementarioService;
use Carbon\Carbon;

class InscripcionComplementarioController extends Controller
{
    protected $complementarioService;

    private const REQUIRED_INTEGER = 'required|integer';
    private const REQUIRED_STRING_MAX_191 = 'required|string|max:191';
    private const NULLABLE_STRING_MAX_191 = 'nullable|string|max:191';
    private const REQUIRED_EMAIL_MAX_191 = 'required|email|max:191';

    public function __construct(ComplementarioService $complementarioService)
    {
        $this->complementarioService = $complementarioService;
    }

    /**
     * Mostrar formulario general de inscripción a eventos del SENA
     */
    public function inscripcionGeneral()
    {
        // Obtener temas de caracterización con sus parámetros
        $temasCaracterizacion = Tema::where('name', 'PERSONA CARACTERIZACION')
            ->with(['parametros' => function ($query) {
                $query->where('parametros_temas.status', 1);
            }])
            ->where('status', 1)
            ->get();

        $paises = Pais::all();
        $departamentos = Departamento::all();

        // Obtener tipos de documento y géneros dinámicamente
        $tiposDocumento = $this->complementarioService->getTiposDocumento();
        $generos = $this->complementarioService->getGeneros();

        return view(
            'complementarios.inscripcion_general',
            compact('temasCaracterizacion', 'paises', 'departamentos', 'tiposDocumento', 'generos'));
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
                function ($value, $fail) {
                    $fechaNacimiento = Carbon::parse($value);
                    $edadMinima = Carbon::now()->subYears(14);

                    if ($fechaNacimiento->gt($edadMinima)) {
                        $fail('Debe tener al menos 14 años para registrarse.');
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
            'direccion' => self::REQUIRED_STRING_MAX_191,
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
        Persona::create($request->only([
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
            'parametro_id'
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

        // Obtener temas de caracterización con sus parámetros
        $temasCaracterizacion = Tema::where('name', 'PERSONA CARACTERIZACION')
            ->with([
                'parametros' => function ($query) {
                    $query->where('parametros_temas.status', 1);
                }
            ])
            ->where('status', 1)
            ->get();

        $paises = Pais::all();
        $departamentos = Departamento::all();

        // Obtener tipos de documento y géneros dinámicamente
        $tiposDocumento = $this->complementarioService->getTiposDocumento();
        $generos = $this->complementarioService->getGeneros();

        // Inicializar datos del usuario
        $userData = [];

        // Si el usuario está autenticado, cargar sus datos existentes
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->persona) {
                $persona = $user->persona;
                $userData = [
                    'tipo_documento' => $persona->tipo_documento,
                    'numero_documento' => $persona->numero_documento,
                    'primer_nombre' => $persona->primer_nombre,
                    'segundo_nombre' => $persona->segundo_nombre,
                    'primer_apellido' => $persona->primer_apellido,
                    'segundo_apellido' => $persona->segundo_apellido,
                    'email' => $persona->email,
                    'fecha_nacimiento' => $persona->fecha_nacimiento,
                    'genero' => $persona->genero,
                    'telefono' => $persona->telefono,
                    'celular' => $persona->celular,
                    'pais_id' => $persona->pais_id,
                    'departamento_id' => $persona->departamento_id,
                    'municipio_id' => $persona->municipio_id,
                    'direccion' => $persona->direccion,
                ];
            }
        }

        return view(
            'complementarios.formulario_inscripcion',
            compact(
                'programa',
                'temasCaracterizacion',
                'paises',
                'departamentos',
                'tiposDocumento',
                'generos',
                'userData'
            )
        );
    }

    /**
     * Procesar la inscripción del aspirante
     */
    public function procesarInscripcion(Request $request, $id)
    {
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
                function ($value, $fail) {
                    $fechaNacimiento = Carbon::parse($value);
                    $edadMinima = Carbon::now()->subYears(14);

                    if ($fechaNacimiento->gt($edadMinima)) {
                        $fail('Debe tener al menos 14 años para registrarse.');
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
            'direccion' => self::REQUIRED_STRING_MAX_191,
            'observaciones' => 'nullable|string',
            'parametro_id' => 'nullable|exists:parametros,id',
            'documento_identidad' => 'required|file|mimes:pdf|max:5120',
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
            $personaExistente->update($validatedData);
            return $personaExistente;
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
}
