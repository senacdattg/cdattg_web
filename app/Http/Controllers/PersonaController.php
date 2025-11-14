<?php

namespace App\Http\Controllers;

use App\Services\PersonaService;
use App\Exceptions\PersonaException;
use App\Services\UbicacionService;
use App\Repositories\TemaRepository;
use App\Models\Persona;
use App\Models\Tema;
use App\Models\Pais;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdatePersonaRequest;
use App\Http\Requests\UpdatePersonaRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class PersonaController extends Controller
{
    private const PERMISSION_VIEW_PROFILE = 'VER PERFIL';
    private const PERMISSION_VIEW_PERSON = 'VER PERSONA';
    private const PERMISSION_ASSIGN_PERMISSIONS = 'ASIGNAR PERMISOS';
    private const ERROR_USER_NOT_RESOLVED = 'No se pudo determinar el usuario autenticado.';

    protected PersonaService $personaService;
    protected UbicacionService $ubicacionService;
    protected TemaRepository $temaRepo;

    public function __construct(
        PersonaService $personaService,
        UbicacionService $ubicacionService,
        TemaRepository $temaRepo
    ) {
        $this->middleware('auth');
        $this->personaService = $personaService;
        $this->ubicacionService = $ubicacionService;
        $this->temaRepo = $temaRepo;

        // Restringir acceso a aspirantes sin permisos específicos
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (!$user instanceof User) {
                abort(403, self::ERROR_USER_NOT_RESOLVED);
            }
            $routeName = $request->route()->getName();

            // Permitir acceso a mi-perfil si tiene VER PERFIL
            if ($routeName === 'personas.mi-perfil' && $user->can(self::PERMISSION_VIEW_PROFILE)) {
                return $next($request);
            }

            // Permitir acceso a show si tiene VER PERFIL o VER PERSONA
            if (
                $routeName === 'personas.show'
                && ($user->can(self::PERMISSION_VIEW_PROFILE) || $user->can(self::PERMISSION_VIEW_PERSON))
            ) {
                return $next($request);
            }

            // Bloquear aspirantes solo si carecen de permisos para el módulo
            if (
                $user->hasRole('ASPIRANTE')
                && !$user->can(self::PERMISSION_VIEW_PERSON)
                && !$user->can(self::PERMISSION_VIEW_PROFILE)
            ) {
                abort(403, 'No tienes permiso para acceder a este módulo.');
            }

            return $next($request);
        });

        $this->middleware('can:VER PERSONA')->only(['index']);
        $this->middleware('can:CREAR PERSONA')->only(['create', 'store']);
        $this->middleware('can:EDITAR PERSONA')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR PERSONA')->only('destroy');
        $this->middleware('can:CAMBIAR ESTADO USUARIO')->only('cambiarEstadoUser');
        $this->middleware('can:ASIGNAR PERMISOS')->only('updateRole');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('personas.index');
    }

    /**
     * Redirige al usuario a su propio perfil.
     * Solo accesible para usuarios con permiso 'VER PERFIL'.
     */
    public function miPerfil()
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user instanceof User) {
            abort(403, self::ERROR_USER_NOT_RESOLVED);
        }

        // Verificar permiso
        if (!$user->can(self::PERMISSION_VIEW_PROFILE)) {
            abort(403, 'No tienes permiso para ver tu perfil.');
        }

        // Obtener la persona del usuario autenticado
        if (!$user->persona_id) {
            return redirect()->route('verificarLogin')
                ->with('error', 'No se encontró información de persona para este usuario.');
        }

        // Redirigir al show de su propia persona
        return redirect()->route('personas.show', $user->persona_id);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $documentos = $this->temaRepo->obtenerTiposDocumento();
        $generos    = $this->temaRepo->obtenerGeneros();
        $paises     = Pais::where('status', 1)->get();
        $departamentos = Departamento::where('status', 1)->get();
        $municipios = Municipio::where('status', 1)->get();
        $vias       = $this->temaRepo->obtenerVias();
        $letras     = $this->temaRepo->obtenerLetras();
        $cardinales = $this->temaRepo->obtenerCardinales();

        // Cargar los tipos de caracterización
        $caracterizaciones = Tema::with(['parametros' => function ($query) {
            $query->wherePivot('status', 1);
        }])->findOrFail(16);

        return view('personas.create', [
            'documentos' => $documentos,
            'generos' => $generos,
            'paises' => $paises,
            'departamentos' => $departamentos,
            'municipios' => $municipios,
            'caracterizaciones' => $caracterizaciones,
            'vias' => $vias,
            'letras' => $letras,
            'cardinales' => $cardinales,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePersonaRequest $request)
    {
        try {
            $this->personaService->crear($request->validated());

            return redirect()->route('personas.index')->with(
                'success',
                'La persona y su usuario fueron creados exitosamente en el sistema.'
            );
        } catch (\Throwable $e) {
            Log::error('Error al registrar persona: ' . $e->getMessage());

            return redirect()->back()->withInput()->with(
                'error',
                'No se pudo registrar la persona. Por favor, verifique los datos e inténtelo nuevamente.'
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Persona $persona)
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (!$user instanceof User) {
            abort(403, self::ERROR_USER_NOT_RESOLVED);
        }

        // Verificar si el usuario tiene permiso 'VER PERSONA' (puede ver cualquier persona)
        if ($user->can(self::PERMISSION_VIEW_PERSON)) {
            // Usuario con permiso completo, puede ver cualquier persona
            $persona->loadMissing([
                'tipoDocumento',
                'tipoGenero',
                'pais',
                'departamento',
                'municipio',
                'caracterizacionesComplementarias',
                'caracterizacion',
                'user.roles',
                'userCreatedBy.persona',
                'userUpdatedBy.persona'
            ]);

            $rolesAsignados = $persona->user?->roles
                ? $persona->user->roles->pluck('name')->unique()->values()
                : collect();

            $rolesDisponibles = $user->can(self::PERMISSION_ASSIGN_PERMISSIONS)
                ? Role::orderBy('name')->get()
                : collect();

            return view('personas.show', [
                'persona' => $persona,
                'soloPerfil' => false,
                'rolesDisponibles' => $rolesDisponibles,
                'rolesAsignados' => $rolesAsignados,
            ]);
        }

        // Verificar si el usuario tiene permiso 'VER PERFIL' (solo puede ver su propio perfil)
        if ($user->can(self::PERMISSION_VIEW_PROFILE)) {
            // Verificar que el usuario solo pueda ver su propio perfil
            if ($user->persona_id !== $persona->id) {
                abort(403, 'No tienes permiso para ver este perfil. Solo puedes ver tu propio perfil.');
            }

            // Usuario solo puede ver su propio perfil
            $persona->loadMissing([
                'tipoDocumento',
                'tipoGenero',
                'pais',
                'departamento',
                'municipio',
                'caracterizacionesComplementarias',
                'caracterizacion',
                'user.roles',
            ]);

            $rolesAsignados = $persona->user?->roles
                ? $persona->user->roles->pluck('name')->unique()->values()
                : collect();

            return view('personas.show', [
                'persona' => $persona,
                'soloPerfil' => true,
                'rolesDisponibles' => collect(),
                'rolesAsignados' => $rolesAsignados,
            ]);
        }

        // Si no tiene ninguno de los permisos, denegar acceso
        abort(403, 'No tienes permiso para ver este perfil.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Persona $persona)
    {
        // llamar los tipos de documentos
        $documentos = Tema::with(['parametros' => function ($query) {
            $query->wherePivot('status', 1);
        }])->findOrFail(2);

        // llamar los generos
        $generos = Tema::with(['parametros' => function ($query) {
            $query->wherePivot('status', 1);
        }])->findOrFail(3);

        // llamar los paises
        $paises = Pais::where('status', 1)->get();

        // llamar los departamentos
        $departamentos = Departamento::where('status', 1)->get();

        // Los municipios se cargarán dinámicamente por JavaScript según el departamento
        $municipios = collect([]);

        // llamar los tipos de caracterizacion
        $caracterizaciones = Tema::with(['parametros' => function ($query) {
            $query->wherePivot('status', 1);
        }])->findOrFail(16);

        $vias = $this->temaRepo->obtenerVias();
        $cardinales = $this->temaRepo->obtenerCardinales();

        return view('personas.edit', [
            'persona' => $persona,
            'documentos' => $documentos,
            'generos' => $generos,
            'paises' => $paises,
            'departamentos' => $departamentos,
            'municipios' => $municipios,
            'caracterizaciones' => $caracterizaciones,
            'vias' => $vias,
            'letras' => $this->temaRepo->obtenerLetras(),
            'cardinales' => $cardinales,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePersonaRequest $request, Persona $persona)
    {
        $expectsJson = $request->expectsJson() || $request->wantsJson();
        $successPayload = null;
        $errorMessage = null;

        try {
            $this->personaService->actualizar($persona, $request->validated());

            $persona->loadMissing(['caracterizacionesComplementarias']);
            $successPayload = [
                'success' => true,
                'message' => 'Información actualizada exitosamente',
                'data' => [
                    'id' => $persona->id,
                    'tipo_documento' => $persona->tipo_documento,
                    'numero_documento' => $persona->numero_documento,
                    'primer_nombre' => $persona->primer_nombre,
                    'segundo_nombre' => $persona->segundo_nombre,
                    'primer_apellido' => $persona->primer_apellido,
                    'segundo_apellido' => $persona->segundo_apellido,
                    'fecha_nacimiento' => $persona->fecha_nacimiento,
                    'genero' => $persona->genero,
                    'telefono' => $persona->telefono,
                    'celular' => $persona->celular,
                    'email' => $persona->email,
                    'pais_id' => $persona->pais_id,
                    'departamento_id' => $persona->departamento_id,
                    'municipio_id' => $persona->municipio_id,
                    'direccion' => $persona->direccion,
                    'caracterizaciones' => $persona->caracterizacionesComplementarias->pluck('id')->toArray(),
                ],
            ];
        } catch (\Throwable $e) {
            Log::error("Error al actualizar la persona (ID: {$persona->id}): " . $e->getMessage());
            $errorMessage = 'Error al actualizar la información. Por favor, inténtelo de nuevo.';
        }

        if ($expectsJson) {
            $payload = $successPayload ?? [
                'success' => false,
                'message' => $errorMessage,
            ];

            return response()->json($payload, $successPayload ? 200 : 500);
        }

        if ($successPayload) {
            return redirect()->route('personas.show', $persona->id)
                ->with('success', $successPayload['message']);
        }

        return redirect()->back()->withErrors([
            'error' => $errorMessage . ' Comuníquese con el administrador si el problema persiste.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Persona $persona)
    {
        try {
            $this->personaService->eliminar($persona->id);

            return redirect()->route('personas.index')->with('success', 'Persona eliminada exitosamente');
        } catch (QueryException $e) {
            Log::error("Error al eliminar la persona (ID: {$persona->id}): " . $e->getMessage());

            $message = 'No se pudo eliminar la persona. Es posible que tenga un usuario asociado.';
        } catch (PersonaException $e) {
            Log::warning("No se pudo eliminar la persona (ID: {$persona->id}): " . $e->getMessage());

            $message = $e->getMessage();
        } catch (\Throwable $e) {
            Log::error("Error inesperado al eliminar la persona (ID: {$persona->id}): " . $e->getMessage());

            $message = 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo.';
        }

        return redirect()->back()->with('error', $message);
    }

    public function datatable(Request $request): JsonResponse
    {
        $this->authorize('VER PERSONA');

        $baseQuery = Persona::query()->with(['user']);

        $recordsTotal = (clone $baseQuery)->count();

        $filteredQuery = clone $baseQuery;

        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $filteredQuery->where(function ($query) use ($searchValue) {
                $query->where('primer_nombre', 'like', "%{$searchValue}%")
                    ->orWhere('segundo_nombre', 'like', "%{$searchValue}%")
                    ->orWhere('primer_apellido', 'like', "%{$searchValue}%")
                    ->orWhere('segundo_apellido', 'like', "%{$searchValue}%")
                    ->orWhere('numero_documento', 'like', "%{$searchValue}%")
                    ->orWhere('email', 'like', "%{$searchValue}%");
            });
        }

        $estado = $request->input('estado');
        $estadoAplicado = false;
        if ($estado && $estado !== 'todos') {
            $estadoAplicado = true;
            $filteredQuery->where('status', $estado === 'activos' ? 1 : 0);
        }

        $requiresFiltering = $searchValue || $estadoAplicado;
        $recordsFiltered = $requiresFiltering ? (clone $filteredQuery)->count() : $recordsTotal;

        $registradosSofiaTotal = (clone $baseQuery)->where('estado_sofia', 1)->count();
        $registradosSofiaFiltrados = (clone $filteredQuery)->where('estado_sofia', 1)->count();

        $columns = [
            0 => 'id',
            1 => 'primer_nombre',
            2 => 'numero_documento',
            3 => 'email',
            4 => 'telefono',
            5 => 'celular',
            6 => 'status',
            7 => 'estado_sofia',
        ];

        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'asc');
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $personasQuery = (clone $filteredQuery)->orderBy($orderColumn, $orderDirection);

        if ($length !== -1) {
            $personasQuery->skip($start)->take($length);
        }

        $personas = $personasQuery->get();

        $data = $personas->map(function (Persona $persona, $index) use ($start) {
            $badgeNoRegistrado = '<span class="badge bg-danger text-white px-2 py-1">No registrado</span>';
            $email = $persona->email ? e($persona->email) : $badgeNoRegistrado;
            $celular = $persona->celular
                ? '<a href="https://wa.me/' . e($persona->celular) . '" target="_blank" class="text-decoration-none">' .
                e($persona->celular) . ' <i class="fab fa-whatsapp text-success"></i></a>'
                : $badgeNoRegistrado;

            return [
                'index' => $start + $index + 1,
                'nombre' => $persona->nombre_completo,
                'numero_documento' => $persona->numero_documento,
                'email' => $email,
                'celular' => $celular,
                'estado' => view('personas.partials.estado', ['persona' => $persona])->render(),
                'estado_sofia' => view('personas.partials.estado-sofia', ['persona' => $persona])->render(),
                'acciones' => view('personas.partials.acciones', ['persona' => $persona])->render(),
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'sofia_registrados_total' => $registradosSofiaTotal,
            'sofia_registrados_filtrados' => $registradosSofiaFiltrados,
            'total_general' => $recordsTotal,
            'total_filtrado' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Cambia el estado de una persona.
     *
     * Este método alterna el estado de una persona entre activo (1) e inactivo (0).
     * Si el estado actual es 1, se cambiará a 0 y viceversa.
     *
     * @param int $id El ID de la persona cuyo estado se va a cambiar.
     * @return \Illuminate\Http\RedirectResponse Redirección de vuelta con un mensaje de éxito o error.
     */
    public function cambiarEstadoPersona($id)
    {
        $persona = Persona::findOrFail($id);
        $user = User::where('persona_id', $persona->id)->first();

        try {
            $persona->update(['status' => !$persona->status]);
            $user->update(['status' => !$user->status]);

            return redirect()->back()->with('success', 'Estado actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error("Error al cambiar estado de la persona (ID: {$id}): " . $e->getMessage());

            return redirect()->back()->with('error', 'No se pudo actualizar el estado.');
        }
    }

    public function updateRole(UpdatePersonaRoleRequest $request, Persona $persona)
    {
        if (!$persona->user) {
            return redirect()
                ->back()
                ->with('error', 'La persona no tiene un usuario asociado.');
        }

        $rolesSeleccionados = collect($request->validated()['roles'] ?? [])
            ->map(static fn(string $role) => trim($role))
            ->filter(static fn(string $role) => $role !== '')
            ->unique()
            ->values();

        try {
            DB::transaction(static function () use ($persona, $rolesSeleccionados) {
                $user = $persona->user;
                $user->syncRoles($rolesSeleccionados->all());
            });

            return redirect()
                ->route('personas.show', $persona->id)
                ->with('success', 'Roles actualizados correctamente.');
        } catch (\Throwable $exception) {
            Log::error('Error al actualizar el rol de la persona', [
                'persona_id' => $persona->id,
                'roles' => $rolesSeleccionados->all(),
                'error' => $exception->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'No se pudo actualizar el rol de la persona.');
        }
    }

    public function resetPassword(Persona $persona): RedirectResponse
    {
        if (!$persona->user) {
            return redirect()
                ->back()
                ->with('error', 'La persona no tiene un usuario asociado.');
        }

        if (empty($persona->numero_documento)) {
            return redirect()
                ->back()
                ->with('error', 'La persona no tiene número de documento registrado.');
        }

        try {
            DB::transaction(static function () use ($persona): void {
                $documento = (string) $persona->numero_documento;
                $persona->user->forceFill([
                    'password' => Hash::make($documento),
                ])->save();
            });

            return redirect()
                ->back()
                ->with('success', 'Contraseña restablecida correctamente.');
        } catch (\Throwable $exception) {
            Log::error('Error al restablecer contraseña de persona', [
                'persona_id' => $persona->id,
                'error' => $exception->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'No se pudo restablecer la contraseña.');
        }
    }

    /**
     * Consulta una persona por número de documento (usado por Talento Humano)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function consultarPorDocumento(Request $request): JsonResponse
    {
        $request->validate([
            'cedula' => 'required|string|max:20'
        ]);

        $persona = $this->personaService->buscarPorDocumento(trim($request->cedula));

        if (!$persona) {
            return response()->json([
                'success' => false,
                'message' => 'Persona no encontrada. Complete los datos para crear un nuevo registro.',
                'data' => null,
                'show_form' => true
            ]);
        }

        $persona->loadMissing(['caracterizacionesComplementarias']);

        return response()->json([
            'success' => true,
            'message' => 'Persona encontrada.',
            'data' => [
                'id' => $persona->id,
                'tipo_documento' => $persona->tipo_documento,
                'numero_documento' => $persona->numero_documento,
                'primer_nombre' => $persona->primer_nombre,
                'segundo_nombre' => $persona->segundo_nombre,
                'primer_apellido' => $persona->primer_apellido,
                'segundo_apellido' => $persona->segundo_apellido,
                'fecha_nacimiento' => $persona->fecha_nacimiento,
                'genero' => $persona->genero,
                'telefono' => $persona->telefono,
                'celular' => $persona->celular,
                'email' => $persona->email,
                'pais_id' => $persona->pais_id,
                'departamento_id' => $persona->departamento_id,
                'municipio_id' => $persona->municipio_id,
                'direccion' => $persona->direccion,
                'caracterizaciones' => $persona->caracterizacionesComplementarias->pluck('id')->toArray(),
            ],
            'show_form' => false
        ]);
    }

    /**
     * Crea una persona desde una petición JSON (usado por Talento Humano)
     *
     * @param StorePersonaRequest $request
     * @return JsonResponse
     */
    public function storeJson(StorePersonaRequest $request): JsonResponse
    {
        try {
            $persona = $this->personaService->crear($request->validated());

            Log::info('Persona creada desde Talento Humano', [
                'persona_id' => $persona->id,
                'numero_documento' => $persona->numero_documento,
                'user_id' => Auth::id()
            ]);

            $persona->loadMissing(['caracterizacionesComplementarias']);

            return response()->json([
                'success' => true,
                'message' => 'Persona creada exitosamente.',
                'data' => [
                    'tipo_documento' => $persona->tipo_documento,
                    'numero_documento' => $persona->numero_documento,
                    'primer_nombre' => $persona->primer_nombre,
                    'segundo_nombre' => $persona->segundo_nombre,
                    'primer_apellido' => $persona->primer_apellido,
                    'segundo_apellido' => $persona->segundo_apellido,
                    'fecha_nacimiento' => $persona->fecha_nacimiento,
                    'genero' => $persona->genero,
                    'telefono' => $persona->telefono,
                    'celular' => $persona->celular,
                    'email' => $persona->email,
                    'pais_id' => $persona->pais_id,
                    'departamento_id' => $persona->departamento_id,
                    'municipio_id' => $persona->municipio_id,
                    'direccion' => $persona->direccion,
                    'caracterizaciones' => $persona->caracterizacionesComplementarias->pluck('id')->toArray(),
                ]
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Error de base de datos al crear persona', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la información. Por favor, verifique los datos e intente nuevamente.'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Error inesperado al crear persona', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado. Por favor, contacte al administrador.'
            ], 500);
        }
    }
}
