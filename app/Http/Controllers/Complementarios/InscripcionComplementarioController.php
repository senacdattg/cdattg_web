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
use App\Services\ComplementarioService;
use App\Repositories\TemaRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InscripcionComplementarioController extends Controller
{
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
            'tipo_documento' => 'required|integer',
            'numero_documento' => 'required|string|max:191|unique:personas',
            'primer_nombre' => 'required|string|max:191',
            'segundo_nombre' => 'nullable|string|max:191',
            'primer_apellido' => 'required|string|max:191',
            'segundo_apellido' => 'nullable|string|max:191',
            'fecha_nacimiento' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $fechaNacimiento = Carbon::parse($value);
                    $edadMinima = Carbon::now()->subYears(14);

                    if ($fechaNacimiento->gt($edadMinima)) {
                        $fail('Debe tener al menos 14 años para registrarse.');
                    }
                },
            ],
            'genero' => 'required|integer',
            'telefono' => 'nullable|string|max:191',
            'celular' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:personas',
            'pais_id' => 'required|exists:pais,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'municipio_id' => 'required|exists:municipios,id',
            'direccion' => 'required|string|max:191',
            'observaciones' => 'nullable|string',
            'parametro_id' => 'nullable|exists:parametros,id',
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

        return redirect()->route('inscripcion.general')->with('success', '¡Registro exitoso! Sus datos han sido guardados correctamente.');
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
            'fecha_nacimiento' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $fechaNacimiento = Carbon::parse($value);
                    $edadMinima = Carbon::now()->subYears(14);

                    if ($fechaNacimiento->gt($edadMinima)) {
                        $fail('Debe tener al menos 14 años para registrarse.');
                    }
                },
            ],
            'genero' => 'required|integer',
            'telefono' => 'nullable|string|max:191',
            'celular' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'pais_id' => 'required|exists:pais,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'municipio_id' => 'required|exists:municipios,id',
            'direccion' => 'required|string|max:191',
            'observaciones' => 'nullable|string',
            'parametro_id' => 'nullable|exists:parametros,id',
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
