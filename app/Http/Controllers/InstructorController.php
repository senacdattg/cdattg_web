<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Http\Requests\StoreInstructorRequest;
use App\Http\Requests\UpdateInstructorRequest;
use App\Http\Requests\InstructorRequest;
use App\Http\Requests\CreateInstructorRequest;
use App\Services\InstructorBusinessRulesService;
use App\Models\FichaCaracterizacion;
use App\Models\Persona;
use App\Models\Regional;
use App\Models\Tema;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class InstructorController extends Controller
{
    protected $businessRulesService;

    public function __construct(InstructorBusinessRulesService $businessRulesService)
    {
        $this->middleware('auth'); // Middleware de autenticación para todos los métodos del controlador
        $this->businessRulesService = $businessRulesService;

        // Middleware específico para métodos individuales usando permisos de Instructor
        $this->middleware('can:VER INSTRUCTOR')->only(['index', 'show']);
        $this->middleware('can:CREAR INSTRUCTOR')->only(['create', 'store']);
        $this->middleware('can:EDITAR INSTRUCTOR')->only(['edit', 'update']);
        $this->middleware('can:ELIMINAR INSTRUCTOR')->only('destroy');
        $this->middleware('can:GESTIONAR ESPECIALIDADES INSTRUCTOR')->only(['especialidades', 'asignarEspecialidad']);
        $this->middleware('can:VER FICHAS ASIGNADAS')->only('fichasAsignadas');
        $this->middleware('can:CAMBIAR ESTADO INSTRUCTOR')->only('cambiarEstado');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $filtroEstado = $request->input('estado', 'todos');
            $filtroEspecialidad = $request->input('especialidad');
            $filtroRegional = $request->input('regional');

            // Construir query base con relaciones
            $query = Instructor::with([
                'persona',
                'regional',
                'instructorFichas' => function($q) {
                    $q->with('ficha.programaFormacion');
                }
            ]);

            // Aplicar filtro de búsqueda por nombre o documento
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('persona', function($personaQuery) use ($search) {
                        $personaQuery->where('primer_nombre', 'like', "%{$search}%")
                            ->orWhere('segundo_nombre', 'like', "%{$search}%")
                            ->orWhere('primer_apellido', 'like', "%{$search}%")
                            ->orWhere('segundo_apellido', 'like', "%{$search}%")
                            ->orWhere('numero_documento', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                });
            }

            // Aplicar filtro por estado
            if ($filtroEstado !== 'todos') {
                if ($filtroEstado === 'activos') {
                    $query->where('status', true);
                } elseif ($filtroEstado === 'inactivos') {
                    $query->where('status', false);
                }
            }

            // Aplicar filtro por especialidad
            if ($filtroEspecialidad) {
                $query->whereJsonContains('especialidades', $filtroEspecialidad);
            }

            // Aplicar filtro por regional
            if ($filtroRegional) {
                $query->where('regional_id', $filtroRegional);
            }

            // Ordenar y paginar
            $instructores = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

            // Obtener datos para filtros
            $regionales = \App\Models\Regional::where('status', true)->orderBy('nombre')->get();
            $especialidades = \App\Models\RedConocimiento::where('status', true)->orderBy('nombre')->get();

            // Estadísticas
            $estadisticas = [
                'total' => Instructor::count(),
                'activos' => Instructor::where('status', true)->count(),
                'inactivos' => Instructor::where('status', false)->count(),
                'con_fichas' => Instructor::whereHas('instructorFichas')->count()
            ];

            // Verificar instructores sin usuario
            $instructoresSinUsuario = Instructor::whereDoesntHave('persona.user')
                ->with('persona:id,primer_nombre,primer_apellido,numero_documento,email')
                ->get();

            if ($instructoresSinUsuario->count() > 0) {
                return view('Instructores.error', compact('instructoresSinUsuario'))->with('error', 'Existen instructores sin usuario asociado. Por favor, cree un usuario para cada instructor.');
            }

            return view('Instructores.index', compact(
                'instructores', 
                'regionales', 
                'especialidades', 
                'estadisticas',
                'search',
                'filtroEstado',
                'filtroEspecialidad',
                'filtroRegional'
            ));
            
        } catch (Exception $e) {
            Log::error('Error al listar instructores', [
                'error' => $e->getMessage(),
                'search' => $request->input('search')
            ]);
            return redirect()->back()->with('error', 'Error al cargar la lista de instructores. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Búsqueda avanzada de instructores con AJAX
     */
    public function search(Request $request)
    {
        try {
            $search = $request->input('search');
            $filtroEstado = $request->input('estado', 'todos');
            $filtroEspecialidad = $request->input('especialidad');
            $filtroRegional = $request->input('regional');
            $page = $request->input('page', 1);

            // Construir query base con relaciones
            $query = Instructor::with([
                'persona',
                'regional',
                'instructorFichas' => function($q) {
                    $q->with('ficha.programaFormacion');
                }
            ]);

            // Aplicar filtros
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('persona', function($personaQuery) use ($search) {
                        $personaQuery->where('primer_nombre', 'like', "%{$search}%")
                            ->orWhere('segundo_nombre', 'like', "%{$search}%")
                            ->orWhere('primer_apellido', 'like', "%{$search}%")
                            ->orWhere('segundo_apellido', 'like', "%{$search}%")
                            ->orWhere('numero_documento', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                });
            }

            if ($filtroEstado !== 'todos') {
                if ($filtroEstado === 'activos') {
                    $query->where('status', true);
                } elseif ($filtroEstado === 'inactivos') {
                    $query->where('status', false);
                }
            }

            if ($filtroEspecialidad) {
                $query->whereJsonContains('especialidades', $filtroEspecialidad);
            }

            if ($filtroRegional) {
                $query->where('regional_id', $filtroRegional);
            }

            // Obtener resultados paginados
            $instructores = $query->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $page);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('Instructores.partials.instructores-table', compact('instructores'))->render(),
                    'pagination' => $instructores->links()->render()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Solicitud no válida'
            ]);

        } catch (Exception $e) {
            Log::error('Error en búsqueda de instructores', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error en la búsqueda'
                ], 500);
            }

            return redirect()->back()->with('error', 'Error en la búsqueda de instructores.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            // Obtener personas que no son instructores
            $personas = \App\Models\Persona::whereDoesntHave('instructor')
                ->whereHas('user') // Solo personas que tienen usuario
                ->with(['user', 'tipoDocumento'])
                ->get();
            
            $regionales = Regional::where('status', 1)->get();
            $especialidades = \App\Models\RedConocimiento::where('status', true)->orderBy('nombre')->get();

            return view('Instructores.create', compact('personas', 'regionales', 'especialidades'));
            
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de creación de instructor', [
                'error' => $e->getMessage()
            ]);
            return redirect()->route('instructor.index')->with('error', 'Error al cargar el formulario. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateInstructorRequest $request)
    {
        try {
            DB::beginTransaction();
            
            // Validar que la persona existe
            $persona = \App\Models\Persona::with(['instructor', 'user'])
                ->findOrFail($request->input('persona_id'));

            // Validaciones de negocio
            if ($persona->instructor) {
                return redirect()->back()->withInput()->with('error', 'Esta persona ya es instructor.');
            }

            if (!$persona->user) {
                return redirect()->back()->withInput()->with('error', 'Esta persona no tiene un usuario asociado.');
            }

            // Crear Instructor
            $instructor = new Instructor();
            $instructor->persona_id = $persona->id;
            $instructor->regional_id = $request->input('regional_id');
            $instructor->anos_experiencia = $request->input('anos_experiencia');
            $instructor->experiencia_laboral = $request->input('experiencia_laboral');
            $instructor->status = true;
            $instructor->save();

            // Asignar especialidades si se proporcionan
            if ($request->has('especialidades')) {
                $especialidades = $request->input('especialidades');
                
                // Log para debugging
                Log::info('Especialidades recibidas:', [
                    'especialidades_raw' => $especialidades,
                    'es_array' => is_array($especialidades),
                    'is_json' => is_string($especialidades),
                    'empty' => empty($especialidades)
                ]);
                
                // Si es string JSON, decodificar
                if (is_string($especialidades)) {
                    $especialidades = json_decode($especialidades, true);
                }
                
                // Si es array y no está vacío, guardar
                if (is_array($especialidades) && !empty($especialidades)) {
                    // Convertir array simple a formato esperado por la vista
                    $especialidadesFormateadas = [
                        'principal' => null,
                        'secundarias' => []
                    ];
                    
                    // Obtener nombres de especialidades desde la base de datos
                    $especialidadesDB = \App\Models\RedConocimiento::whereIn('id', $especialidades)->get();
                    $nombresEspecialidades = $especialidadesDB->pluck('nombre', 'id')->toArray();
                    
                    // Asignar la primera como principal y las demás como secundarias
                    if (count($especialidades) > 0) {
                        $primeraEspecialidad = $especialidades[0];
                        $especialidadesFormateadas['principal'] = $nombresEspecialidades[$primeraEspecialidad] ?? null;
                        
                        // Las demás como secundarias
                        for ($i = 1; $i < count($especialidades); $i++) {
                            if (isset($nombresEspecialidades[$especialidades[$i]])) {
                                $especialidadesFormateadas['secundarias'][] = $nombresEspecialidades[$especialidades[$i]];
                            }
                        }
                    }
                    
                    $instructor->especialidades = $especialidadesFormateadas;
                    $instructor->save();
                    
                    Log::info('Especialidades guardadas:', [
                        'instructor_id' => $instructor->id,
                        'especialidades_raw' => $especialidades,
                        'especialidades_formateadas' => $especialidadesFormateadas
                    ]);
                }
            }

            // Asignar rol de instructor al usuario existente
            $user = $persona->user;
            $user->assignRole('INSTRUCTOR');

            DB::commit();
            
            Log::info('Instructor creado exitosamente', [
                'instructor_id' => $instructor->id,
                'persona_id' => $persona->id,
                'user_id' => $user->id,
                'email' => $user->email,
                'especialidades' => $instructor->especialidades
            ]);
            
            return redirect()->route('instructor.index')->with('success', '¡Instructor asignado exitosamente!');
            
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al crear instructor - QueryException', [
                'error' => $e->getMessage(),
                'request_data' => $request->except(['password'])
            ]);
            return redirect()->back()->withInput()->with('error', 'Error de base de datos. Por favor, inténtelo de nuevo.');
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear instructor - Exception', [
                'error' => $e->getMessage(),
                'request_data' => $request->except(['password'])
            ]);
            return redirect()->back()->withInput()->with('error', 'Error inesperado. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Instructor $instructor)
    {
        try {
            $fichasCaracterizacion = FichaCaracterizacion::all();
            $instructor->persona->edad = Carbon::parse($instructor->persona->fecha_de_nacimiento)->age;
            $instructor->persona->fecha_de_nacimiento = Carbon::parse($instructor->persona->fecha_de_nacimiento)->format('d/m/Y');
            
            return view('Instructores.show', compact('instructor', 'fichasCaracterizacion'));
            
        } catch (Exception $e) {
            Log::error('Error al mostrar instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('instructor.index')->with('error', 'Error al cargar los datos del instructor. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Instructor $instructor)
    {
        try {
            // llamar los tipos de documentos
            $documentos = Tema::with(['parametros' => function ($query) {
                $query->wherePivot('status', 1);
            }])->findOrFail(2);
            
            // llamar los generos
            $generos = Tema::with(['parametros' => function ($query) {
                $query->wherePivot('status', 1);
            }])->findOrFail(3);
            
            $regionales = Regional::where('status', 1)->get();
            
            return view('Instructores.edit', ['instructor' => $instructor], compact('documentos', 'generos', 'regionales'));
            
        } catch (Exception $e) {
            Log::error('Error al cargar formulario de edición de instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('instructor.index')->with('error', 'Error al cargar el formulario de edición. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInstructorRequest $request, Instructor $instructor)
    {
        try {
            DB::beginTransaction();
            
            $persona = Persona::findOrFail($instructor->persona_id);
            $persona->update([
                'tipo_documento' => $request->input('tipo_documento'),
                'numero_documento' => $request->input('numero_documento'),
                'primer_nombre' => $request->input('primer_nombre'),
                'segundo_nombre' => $request->input('segundo_nombre'),
                'primer_apellido' => $request->input('primer_apellido'),
                'segundo_apellido' => $request->input('segundo_apellido'),
                'fecha_de_nacimiento' => $request->input('fecha_de_nacimiento'),
                'genero' => $request->input('genero'),
                'email' => $request->input('email'),
            ]);

            $instructor->update([
                'persona_id' => $persona->id,
                'regional_id' => $request->regional_id,
            ]);
            
            // Actualizar Usuario asociado a la Persona
            $user = User::where('persona_id', $persona->id)->first();
            if ($user) {
                $user->update([
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('numero_documento')),
                ]);
            }

            DB::commit();
            
            Log::info('Instructor actualizado exitosamente', [
                'instructor_id' => $instructor->id,
                'persona_id' => $persona->id,
                'user_id' => $user ? $user->id : null,
                'email' => $request->input('email')
            ]);
            
            return redirect()->route('instructor.index')->with('success', '¡Actualización Exitosa!');
            
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error al actualizar instructor - QueryException', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
                'request_data' => $request->except(['password'])
            ]);
            return redirect()->back()->withInput()->with('error', 'Error de base de datos. Por favor, inténtelo de nuevo.');
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar instructor - Exception', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
                'request_data' => $request->except(['password'])
            ]);
            return redirect()->back()->withInput()->with('error', 'Error inesperado. Por favor, inténtelo de nuevo.');
        }
    }
    public function ApiUpdate(Request $request)
    {
        try {
            DB::beginTransaction();
            $personaD = Persona::find($request->persona_id);
            $personaD->update([
                'tipo_documento' => $request->tipo_documento,
                'numero_documento' => $request->numero_documento,
                'primer_nombre' => $request->primer_nombre,
                'segundo_nombre' => $request->segundo_nombre,
                'primer_apellido' => $request->primer_apellido,
                'segundo_apellido' => $request->segundo_apellido,
                'fecha_de_nacimiento' => $request->fecha_de_nacimiento,
                'genero' => $request->genero,
                'email' => $request->email,
            ]);
            // actualizar Usuario asociado a la Persona
            // Actualizar Usuario asociado a la Persona
            $user = User::where('persona_id', $request->persona_id)->first();
            if ($user) {
                $user->update([
                    'email' => $request->email,
                    'password' => Hash::make($request->numero_documento),
                ]);
                // Refrescar el modelo del usuario
                $user = $user->fresh();
            }
            DB::commit();
            $user->refresh();
            $token = $user->createToken('Token Name')->plainTextToken; // Generar el token
            $personaD->refresh();
            $persona = [
                "id" => $personaD->id,
                "tipo_documento" => $personaD->tipoDocumento->name,
                "numero_documento" => $personaD->numero_documento,
                "primer_nombre" => $personaD->primer_nombre,
                "segundo_nombre" => $personaD->segundo_nombre,
                "primer_apellido" => $personaD->primer_apellido,
                "segundo_apellido" => $personaD->segundo_apellido,
                "fecha_de_nacimiento" => $personaD->fecha_de_nacimiento,
                "genero" => $personaD->tipoGenero->name,
                "email" => $personaD->email,
                "created_at" => $personaD->created_at,
                "updated_at" => $personaD->updated_at,
                "instructor_id" => $personaD->instructor->id,
                "regional_id" => $personaD->instructor->regional->id,
            ];
            // Retornar la respuesta JSON incluyendo el token
            return response()->json(['user' => $user, 'persona' => $persona, 'token' => $token], 200);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Instructor $instructor)
    {
        try {
            DB::beginTransaction();
            
            // Obtener información del instructor antes de eliminarlo
            $instructorId = $instructor->id;
            $personaId = $instructor->persona_id;
            
            // Obtener el usuario asociado para remover el rol
            $user = User::where('persona_id', $personaId)->first();
            
            // Remover el rol de instructor del usuario (si existe)
            if ($user && $user->hasRole('INSTRUCTOR')) {
                $user->removeRole('INSTRUCTOR');
            }
            
            // Eliminar solo el registro de instructor
            $instructor->delete();
            
            DB::commit();
            
            Log::info('Instructor eliminado exitosamente', [
                'instructor_id' => $instructorId,
                'persona_id' => $personaId,
                'user_id' => $user ? $user->id : null,
                'rol_removido' => $user ? 'INSTRUCTOR' : null
            ]);
            
            return redirect()->route('instructor.index')->with('success', 'Instructor eliminado exitosamente. La persona y usuario se mantienen intactos.');
            
        } catch (QueryException $e) {
            DB::rollBack();
            
            Log::error('Error al eliminar instructor - QueryException', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode()
            ]);
            
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'El instructor se encuentra en uso en estos momentos, no se puede eliminar');
            }
            
            return redirect()->back()->with('error', 'Error de base de datos al eliminar el instructor. Por favor, inténtelo de nuevo.');
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error al eliminar instructor - Exception', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Error inesperado al eliminar el instructor. Por favor, inténtelo de nuevo.');
        }
    }
    public function createImportarCSV()
    {
        return view('Instructores.createImportarCSV');
    }
    public function storeImportarCSV(Request $request)
    {
        try {
            // Validar que el archivo subido sea un archivo CSV o TXT
            $request->validate([
                'archivoCSV' => 'required|file|mimes:csv,txt',
            ]);

            // Obtener el archivo subido
            $archivo = $request->file('archivoCSV');
            $csvData = file_get_contents($archivo);

            // Eliminar el BOM (Byte Order Mark) si está presente al inicio del archivo
            if (substr($csvData, 0, 3) === "\u{FEFF}") {
                $csvData = substr($csvData, 3);
            }

            // Convertir el contenido del archivo CSV en un array de filas, usando punto y coma como delimitador
            $rows = array_map(function ($row) {
                return str_getcsv($row, ';');
            }, explode("\n", $csvData));

            // Extraer el encabezado (primera fila) del CSV
            $header = array_shift($rows);

            // Eliminar espacios en blanco alrededor de cada elemento del encabezado y convertirlo a mayúsculas
            $header = array_map('trim', $header);
            $header = array_map('strtoupper', $header);

            // Definir el encabezado esperado
            $expectedHeader = ['TITLE', 'ID_PERSONAL', 'CORREO INSTITUCIONAL'];

            // Comprobar si el encabezado del CSV coincide con el encabezado esperado
            if ($header !== $expectedHeader) {
                return redirect()->back()->with('error', 'El encabezado del archivo CSV no coincide con el formato esperado.');
            }

            // Iniciar una transacción de base de datos
            DB::beginTransaction();

            $errores = [];
            $procesados = 0;

            // Procesar cada fila de datos del CSV
            foreach ($rows as $row) {
                // Verificar que la cantidad de columnas en la fila coincida con la cantidad de columnas en el encabezado
                if (count($row) != count($header)) {
                    $errores[] = $row;
                    continue;
                }

                // Combinar el encabezado con los datos de la fila
                $data = array_combine($header, $row);

                try {
                    // Crear una nueva entrada en la tabla `Persona`
                    $persona = Persona::create([
                        'tipo_documento' => 8,
                        'numero_documento' => $data['ID_PERSONAL'],
                        'primer_nombre' => $data['TITLE'],
                        'genero' => 11,
                        'email' => $data['CORREO INSTITUCIONAL'],
                    ]);

                    // Crear una nueva entrada en la tabla `User`
                    $user = User::create([
                        'email' => $data['CORREO INSTITUCIONAL'],
                        'password' => Hash::make($data['ID_PERSONAL']),
                        'persona_id' => $persona->id,
                    ]);

                    // Asignar el rol de 'INSTRUCTOR' al usuario
                    $user->assignRole('INSTRUCTOR');

                    // Crear una nueva entrada en la tabla `Instructor`
                    Instructor::create([
                        'persona_id' => $persona->id,
                        'regional_id' => 1,
                    ]);

                    // Incrementar el contador de registros procesados con éxito
                    $procesados++;
                } catch (Exception $e) {
                    // Si ocurre un error, agregar la fila a la lista de errores y continuar con la siguiente
                    $errores[] = $data;
                    continue;
                }
            }

            // Confirmar la transacción de base de datos
            DB::commit();

            // Preparar el mensaje de éxito
            $mensaje = 'Instructores creados exitosamente: ' . $procesados;

            // Si hubo errores, agregar una nota al mensaje de éxito
            if (count($errores) > 0) {
                $mensaje .= '. Algunos registros no pudieron ser procesados.';
            }

            // Mostrar la vista con los errores y el mensaje de éxito
            return view('Instructores.errorImport', compact('errores'))->with('success', $mensaje);
        } catch (QueryException $e) {
            // Si ocurre un error en la base de datos, revertir la transacción
            DB::rollBack();
            return redirect()->back()->with('error', 'Error en la base de datos: ' . $e->getMessage());
        } catch (Exception $e) {
            // Si ocurre un error general, revertir la transacción
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }


    public function deleteWithoudUser($id)
    {
        try {
            DB::beginTransaction();
            
            // Buscar el instructor por persona_id
            $instructor = Instructor::where('persona_id', $id)->first();
            
            if (!$instructor) {
                return redirect()->back()->with('error', 'No se encontró el instructor.');
            }
            
            // Eliminar solo el instructor
            $instructor->delete();
            
            DB::commit();
            
            Log::info('Instructor sin usuario eliminado exitosamente', [
                'instructor_id' => $instructor->id,
                'persona_id' => $id
            ]);
            
            return redirect()->back()->with('success', 'Instructor eliminado exitosamente. La persona se mantiene intacta.');
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Error al eliminar instructor sin usuario', [
                'persona_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Error al eliminar el instructor.');
        }
    }

    /**
     * Mostrar especialidades del instructor
     */
    public function especialidades(Instructor $instructor)
    {
        $this->authorize('gestionarEspecialidades', $instructor);
        
        $especialidades = $instructor->especialidades;
        
        return view('instructores.especialidades', compact('instructor', 'especialidades'));
    }

    /**
     * Gestionar especialidades del instructor
     */
    public function gestionarEspecialidades(Instructor $instructor)
    {
        $this->authorize('gestionarEspecialidades', $instructor);
        
        // Obtener redes de conocimiento disponibles según la regional del instructor
        $redesConocimiento = \App\Models\RedConocimiento::where('regionals_id', $instructor->regional_id)
            ->where('status', true)
            ->orderBy('nombre')
            ->get();
        
        // Obtener especialidades actuales del instructor
        $especialidadesActuales = $instructor->especialidades ?? [];
        
        // Separar especialidades principales y secundarias
        $especialidadPrincipal = $especialidadesActuales['principal'] ?? null;
        $especialidadesSecundarias = $especialidadesActuales['secundarias'] ?? [];
        
        return view('instructores.gestionar-especialidades', compact(
            'instructor', 
            'redesConocimiento', 
            'especialidadPrincipal', 
            'especialidadesSecundarias'
        ));
    }

    /**
     * Asignar especialidad al instructor
     */
    public function asignarEspecialidad(Request $request, Instructor $instructor)
    {
        $this->authorize('gestionarEspecialidades', $instructor);
        
        $request->validate([
            'red_conocimiento_id' => 'required|exists:red_conocimientos,id',
            'tipo' => 'required|in:principal,secundaria'
        ]);

        try {
            DB::beginTransaction();
            
            // Validar que la red de conocimiento pertenezca a la regional del instructor
            $redConocimiento = \App\Models\RedConocimiento::where('id', $request->red_conocimiento_id)
                ->where('regionals_id', $instructor->regional_id)
                ->where('status', true)
                ->first();

            if (!$redConocimiento) {
                return redirect()->back()->with('error', 'La red de conocimiento no está disponible para esta regional');
            }

            $especialidadesActuales = $instructor->especialidades ?? [];

            if ($request->tipo === 'principal') {
                // Verificar que no esté ya asignada como secundaria
                $especialidadesSecundarias = $especialidadesActuales['secundarias'] ?? [];
                if (in_array($redConocimiento->nombre, $especialidadesSecundarias)) {
                    // Remover de secundarias antes de asignar como principal
                    $especialidadesSecundarias = array_filter($especialidadesSecundarias, function($esp) use ($redConocimiento) {
                        return $esp !== $redConocimiento->nombre;
                    });
                    $especialidadesActuales['secundarias'] = array_values($especialidadesSecundarias);
                }
                
                // Solo puede haber una especialidad principal
                $especialidadesActuales['principal'] = $redConocimiento->nombre;
                $mensaje = "Especialidad principal '{$redConocimiento->nombre}' asignada exitosamente";
            } else {
                // Agregar especialidad secundaria
                $especialidadesSecundarias = $especialidadesActuales['secundarias'] ?? [];
                
                // Verificar que no sea la misma especialidad principal
                if ($especialidadesActuales['principal'] === $redConocimiento->nombre) {
                    return redirect()->back()->with('warning', 'Esta especialidad ya está asignada como principal');
                }
                
                // Verificar que no esté ya en secundarias
                if (in_array($redConocimiento->nombre, $especialidadesSecundarias)) {
                    return redirect()->back()->with('warning', 'Esta especialidad ya está asignada como secundaria');
                }
                
                $especialidadesSecundarias[] = $redConocimiento->nombre;
                $especialidadesActuales['secundarias'] = $especialidadesSecundarias;
                $mensaje = "Especialidad secundaria '{$redConocimiento->nombre}' asignada exitosamente";
            }

            $instructor->especialidades = $especialidadesActuales;
            $instructor->save();

            DB::commit();

            Log::info('Especialidad asignada exitosamente', [
                'instructor_id' => $instructor->id,
                'especialidad' => $redConocimiento->nombre,
                'tipo' => $request->tipo,
                'especialidades_actuales' => $especialidadesActuales
            ]);

            return redirect()->back()->with('success', $mensaje);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al asignar especialidad', [
                'instructor_id' => $instructor->id,
                'red_conocimiento_id' => $request->red_conocimiento_id,
                'tipo' => $request->tipo,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Error al asignar la especialidad. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Remover especialidad del instructor
     */
    public function removerEspecialidad(Request $request, Instructor $instructor)
    {
        $this->authorize('gestionarEspecialidades', $instructor);
        
        $request->validate([
            'especialidad' => 'required|string',
            'tipo' => 'required|in:principal,secundaria'
        ]);

        try {
            DB::beginTransaction();
            
            $especialidadesActuales = $instructor->especialidades ?? [];
            $especialidadNombre = $request->especialidad;

            if ($request->tipo === 'principal') {
                // Verificar que la especialidad principal existe
                if ($especialidadesActuales['principal'] !== $especialidadNombre) {
                    return redirect()->back()->with('error', 'La especialidad principal especificada no coincide');
                }
                
                $especialidadesActuales['principal'] = null;
                $mensaje = "Especialidad principal '{$especialidadNombre}' removida exitosamente";
                
                Log::info('Especialidad principal removida', [
                    'instructor_id' => $instructor->id,
                    'especialidad_removida' => $especialidadNombre
                ]);
            } else {
                $especialidadesSecundarias = $especialidadesActuales['secundarias'] ?? [];
                
                // Verificar que la especialidad secundaria existe
                if (!in_array($especialidadNombre, $especialidadesSecundarias)) {
                    return redirect()->back()->with('error', 'La especialidad secundaria especificada no existe');
                }
                
                // Remover la especialidad de la lista
                $especialidadesSecundarias = array_filter($especialidadesSecundarias, function($esp) use ($especialidadNombre) {
                    return $esp !== $especialidadNombre;
                });
                $especialidadesActuales['secundarias'] = array_values($especialidadesSecundarias);
                $mensaje = "Especialidad secundaria '{$especialidadNombre}' removida exitosamente";
                
                Log::info('Especialidad secundaria removida', [
                    'instructor_id' => $instructor->id,
                    'especialidad_removida' => $especialidadNombre,
                    'especialidades_restantes' => $especialidadesSecundarias
                ]);
            }

            $instructor->especialidades = $especialidadesActuales;
            $instructor->save();

            DB::commit();

            return redirect()->back()->with('success', $mensaje);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al remover especialidad', [
                'instructor_id' => $instructor->id,
                'especialidad' => $request->especialidad,
                'tipo' => $request->tipo,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Error al remover la especialidad. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Dashboard específico para instructores
     */
    public function dashboard(Request $request)
    {
        try {
            $user = Auth::user();
            $instructor = $user->instructor;

            if (!$instructor) {
                return redirect()->back()->with('error', 'No se encontró información del instructor');
            }

            // Autorizar acceso
            $this->authorize('viewAny', Instructor::class);

            // Obtener fichas activas
            $fichasActivas = $instructor->instructorFichas()
                ->with(['ficha' => function($q) {
                    $q->with([
                        'programaFormacion.redConocimiento',
                        'modalidadFormacion',
                        'ambiente.sede',
                        'jornadaFormacion',
                        'diasFormacion'
                    ]);
                }])
                ->whereHas('ficha', function($q) {
                    $q->where('status', true)
                      ->where('fecha_fin', '>=', now()->toDateString());
                })
                ->orderBy('fecha_inicio')
                ->get();

            // Obtener fichas próximas (próximos 30 días)
            $fichasProximas = $instructor->instructorFichas()
                ->with(['ficha' => function($q) {
                    $q->with([
                        'programaFormacion.redConocimiento',
                        'modalidadFormacion',
                        'ambiente.sede',
                        'jornadaFormacion',
                        'diasFormacion'
                    ]);
                }])
                ->whereHas('ficha', function($q) {
                    $q->where('status', true)
                      ->where('fecha_inicio', '>=', now()->toDateString())
                      ->where('fecha_inicio', '<=', now()->addDays(30)->toDateString());
                })
                ->orderBy('fecha_inicio')
                ->get();

            // Obtener estadísticas de desempeño
            $estadisticas = $this->obtenerEstadisticasDesempeño($instructor);

            // Obtener eventos del calendario (clases)
            $eventosCalendario = $this->obtenerEventosCalendario($instructor);

            // Obtener notificaciones recientes
            $notificaciones = $this->obtenerNotificacionesRecientes($instructor);

            // Obtener resumen de actividades
            $actividadesRecientes = $this->obtenerActividadesRecientes($instructor);

            return view('instructores.dashboard', compact(
                'instructor',
                'fichasActivas',
                'fichasProximas',
                'estadisticas',
                'eventosCalendario',
                'notificaciones',
                'actividadesRecientes'
            ));

        } catch (\Exception $e) {
            Log::error('Error cargando dashboard del instructor', [
                'instructor_id' => Auth::user()->instructor?->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Error al cargar el dashboard del instructor');
        }
    }

    /**
     * Obtener estadísticas de desempeño del instructor
     */
    private function obtenerEstadisticasDesempeño(Instructor $instructor): array
    {
        $fichasActivas = $instructor->instructorFichas()
            ->whereHas('ficha', function($q) {
                $q->where('status', true)
                  ->where('fecha_fin', '>=', now()->toDateString());
            })
            ->get();

        $fichasFinalizadas = $instructor->instructorFichas()
            ->whereHas('ficha', function($q) {
                $q->where('fecha_fin', '<', now()->toDateString());
            })
            ->get();

        $totalHoras = $instructor->instructorFichas()->sum('total_horas_instructor');
        $horasEsteMes = $instructor->instructorFichas()
            ->whereHas('ficha', function($q) {
                $q->whereMonth('fecha_inicio', now()->month)
                  ->whereYear('fecha_inicio', now()->year);
            })
            ->sum('total_horas_instructor');

        return [
            'fichas_activas' => $fichasActivas->count(),
            'fichas_proximas' => $instructor->instructorFichas()
                ->whereHas('ficha', function($q) {
                    $q->where('status', true)
                      ->where('fecha_inicio', '>=', now()->toDateString())
                      ->where('fecha_inicio', '<=', now()->addDays(30)->toDateString());
                })
                ->count(),
            'fichas_finalizadas' => $fichasFinalizadas->count(),
            'total_horas' => $totalHoras,
            'horas_este_mes' => $horasEsteMes,
            'promedio_horas_mes' => $instructor->instructorFichas()
                ->whereHas('ficha', function($q) {
                    $q->where('fecha_inicio', '>=', now()->subMonths(6)->toDateString());
                })
                ->sum('total_horas_instructor') / 6,
            'anos_experiencia' => $instructor->anos_experiencia ?? 0,
            'especialidades' => count($instructor->especialidades['secundarias'] ?? []) + 
                               (empty($instructor->especialidades['principal']) ? 0 : 1)
        ];
    }

    /**
     * Obtener eventos del calendario para el instructor
     */
    private function obtenerEventosCalendario(Instructor $instructor): array
    {
        $eventos = [];

        $fichasActivas = $instructor->instructorFichas()
            ->with(['ficha.diasFormacion'])
            ->whereHas('ficha', function($q) {
                $q->where('status', true)
                  ->where('fecha_fin', '>=', now()->toDateString());
            })
            ->get();

        foreach ($fichasActivas as $instructorFicha) {
            $ficha = $instructorFicha->ficha;
            
            foreach ($ficha->diasFormacion as $diaFormacion) {
                $fechaInicio = Carbon::parse($ficha->fecha_inicio);
                $fechaFin = Carbon::parse($ficha->fecha_fin);
                
                // Generar eventos para cada día de formación en el rango
                $fechaActual = $fechaInicio->copy();
                while ($fechaActual->lte($fechaFin)) {
                    if ($this->esDiaFormacion($fechaActual, $diaFormacion->dia_nombre)) {
                        $eventos[] = [
                            'title' => $ficha->programaFormacion->nombre ?? 'Sin programa',
                            'start' => $fechaActual->format('Y-m-d') . 'T' . $diaFormacion->hora_inicio,
                            'end' => $fechaActual->format('Y-m-d') . 'T' . $diaFormacion->hora_fin,
                            'backgroundColor' => $this->obtenerColorPorEspecialidad($ficha->programaFormacion->redConocimiento->nombre ?? ''),
                            'borderColor' => $this->obtenerColorPorEspecialidad($ficha->programaFormacion->redConocimiento->nombre ?? ''),
                            'extendedProps' => [
                                'ficha_id' => $ficha->id,
                                'ambiente' => $ficha->ambiente->nombre ?? 'Sin ambiente',
                                'sede' => $ficha->ambiente->sede->nombre ?? 'Sin sede',
                                'modalidad' => $ficha->modalidadFormacion->nombre ?? 'Sin modalidad'
                            ]
                        ];
                    }
                    $fechaActual->addDay();
                }
            }
        }

        return $eventos;
    }

    /**
     * Verificar si una fecha corresponde a un día de formación
     */
    private function esDiaFormacion(Carbon $fecha, string $diaNombre): bool
    {
        $diasSemana = [
            'Lunes' => 1,
            'Martes' => 2,
            'Miércoles' => 3,
            'Jueves' => 4,
            'Viernes' => 5,
            'Sábado' => 6,
            'Domingo' => 0
        ];

        return $fecha->dayOfWeek === ($diasSemana[$diaNombre] ?? -1);
    }

    /**
     * Obtener color por especialidad para el calendario
     */
    private function obtenerColorPorEspecialidad(string $especialidad): string
    {
        $colores = [
            'Tecnologías de la Información y las Comunicaciones' => '#3498db',
            'Electrónica' => '#e74c3c',
            'Mecánica Industrial' => '#f39c12',
            'Construcción' => '#2ecc71',
            'Gastronomía' => '#9b59b6',
            'Agropecuaria' => '#27ae60',
            'Comercio' => '#34495e'
        ];

        return $colores[$especialidad] ?? '#95a5a6';
    }

    /**
     * Obtener notificaciones recientes para el instructor
     */
    private function obtenerNotificacionesRecientes(Instructor $instructor): array
    {
        // Simular notificaciones - en un sistema real vendrían de una tabla de notificaciones
        $notificaciones = [
            [
                'id' => 1,
                'titulo' => 'Nueva ficha asignada',
                'mensaje' => 'Se te ha asignado una nueva ficha de programación web',
                'tipo' => 'success',
                'fecha' => now()->subHours(2),
                'leida' => false
            ],
            [
                'id' => 2,
                'titulo' => 'Recordatorio de clase',
                'mensaje' => 'Tienes una clase programada mañana a las 8:00 AM',
                'tipo' => 'info',
                'fecha' => now()->subHours(5),
                'leida' => false
            ],
            [
                'id' => 3,
                'titulo' => 'Evaluación pendiente',
                'mensaje' => 'Debes completar la evaluación de la ficha 12345',
                'tipo' => 'warning',
                'fecha' => now()->subDays(1),
                'leida' => true
            ]
        ];

        return $notificaciones;
    }

    /**
     * Obtener actividades recientes del instructor
     */
    private function obtenerActividadesRecientes(Instructor $instructor): array
    {
        $actividades = [];

        // Obtener fichas recientes
        $fichasRecientes = $instructor->instructorFichas()
            ->with(['ficha.programaFormacion'])
            ->whereHas('ficha')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($fichasRecientes as $instructorFicha) {
            $actividades[] = [
                'tipo' => 'ficha_asignada',
                'titulo' => 'Ficha asignada',
                'descripcion' => 'Se asignó la ficha ' . $instructorFicha->ficha->ficha . ' - ' . 
                               ($instructorFicha->ficha->programaFormacion->nombre ?? 'Sin programa'),
                'fecha' => $instructorFicha->created_at,
                'icono' => 'fas fa-clipboard-list'
            ];
        }

        return $actividades;
    }

    /**
     * Ver fichas asignadas al instructor
     */
    public function fichasAsignadas(Request $request, Instructor $instructor = null)
    {
        $user = Auth::user();
        
        // Autorizar acceso usando la política
        if ($instructor) {
            $this->authorize('verFichasAsignadas', $instructor);
            $instructorActual = $instructor;
        } else {
            // Si no se especifica instructor, usar el del usuario autenticado
            $instructorActual = $user->instructor;
            if ($instructorActual) {
                $this->authorize('verFichasAsignadas', $instructorActual);
            }
        }
        
        if (!$instructorActual) {
            return redirect()->back()->with('error', 'No se encontró información del instructor');
        }

        // Obtener parámetros de filtro
        $filtroEstado = $request->get('estado', 'todas');
        $filtroFechaInicio = $request->get('fecha_inicio');
        $filtroFechaFin = $request->get('fecha_fin');
        $filtroPrograma = $request->get('programa');

        // Construir query base con relaciones
        $query = $instructorActual->instructorFichas()
            ->with([
                'ficha' => function($q) {
                    $q->with([
                        'programaFormacion.redConocimiento',
                        'modalidadFormacion',
                        'ambiente.sede',
                        'jornadaFormacion',
                        'diasFormacion'
                    ]);
                }
            ]);

        // Aplicar filtros
        if ($filtroEstado !== 'todas') {
            if ($filtroEstado === 'activas') {
                $query->whereHas('ficha', function($q) {
                    $q->where('status', true)
                      ->where('fecha_fin', '>=', now()->toDateString());
                });
            } elseif ($filtroEstado === 'finalizadas') {
                $query->whereHas('ficha', function($q) {
                    $q->where('fecha_fin', '<', now()->toDateString());
                });
            } elseif ($filtroEstado === 'inactivas') {
                $query->whereHas('ficha', function($q) {
                    $q->where('status', false);
                });
            }
        }

        if ($filtroFechaInicio) {
            $query->whereHas('ficha', function($q) use ($filtroFechaInicio) {
                $q->where('fecha_inicio', '>=', $filtroFechaInicio);
            });
        }

        if ($filtroFechaFin) {
            $query->whereHas('ficha', function($q) use ($filtroFechaFin) {
                $q->where('fecha_fin', '<=', $filtroFechaFin);
            });
        }

        if ($filtroPrograma) {
            $query->whereHas('ficha.programaFormacion', function($q) use ($filtroPrograma) {
                $q->where('nombre', 'like', "%{$filtroPrograma}%");
            });
        }

        // Ordenar por fecha de inicio descendente
        $query->orderBy('fecha_inicio', 'desc');

        // Paginar resultados
        $fichasAsignadas = $query->paginate(15)->withQueryString();

        // Obtener estadísticas
        $estadisticas = [
            'total' => $instructorActual->instructorFichas()->count(),
            'activas' => $instructorActual->instructorFichas()
                ->whereHas('ficha', function($q) {
                    $q->where('status', true)
                      ->where('fecha_fin', '>=', now()->toDateString());
                })->count(),
            'finalizadas' => $instructorActual->instructorFichas()
                ->whereHas('ficha', function($q) {
                    $q->where('fecha_fin', '<', now()->toDateString());
                })->count(),
            'total_horas' => $instructorActual->instructorFichas()->sum('total_horas_instructor')
        ];

        // Obtener programas únicos para el filtro
        $programas = $instructorActual->instructorFichas()
            ->with('ficha.programaFormacion')
            ->get()
            ->pluck('ficha.programaFormacion.nombre')
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('instructores.fichas-asignadas', compact(
            'instructorActual', 
            'fichasAsignadas', 
            'estadisticas',
            'programas',
            'filtroEstado',
            'filtroFechaInicio',
            'filtroFechaFin',
            'filtroPrograma'
        ));
    }

    /**
     * Cambiar estado del instructor
     */
    public function cambiarEstado(Request $request, Instructor $instructor)
    {
        $this->authorize('cambiarEstado', $instructor);
        
        $request->validate([
            'estado' => 'required|in:activo,inactivo'
        ]);

        try {
            $instructor->update(['estado' => $request->estado]);
            
            $mensaje = $request->estado === 'activo' 
                ? 'Instructor activado exitosamente' 
                : 'Instructor desactivado exitosamente';
                
            return redirect()->back()->with('success', $mensaje);
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del instructor: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cambiar el estado del instructor');
        }
    }

    /**
     * Verificar disponibilidad del instructor para una nueva ficha
     */
    public function verificarDisponibilidad(Request $request, Instructor $instructor)
    {
        try {
            $request->validate([
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'especialidad_requerida' => 'nullable|string',
                'horas_semanales' => 'nullable|integer|min:0|max:48'
            ]);

            $datosFicha = $request->only(['fecha_inicio', 'fecha_fin', 'especialidad_requerida', 'horas_semanales']);

            $disponibilidad = $this->businessRulesService->verificarDisponibilidad($instructor, $datosFicha);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'disponibilidad' => $disponibilidad
                ]);
            }

            return response()->json($disponibilidad);

        } catch (\Exception $e) {
            Log::error('Error verificando disponibilidad del instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al verificar disponibilidad'
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al verificar disponibilidad del instructor');
        }
    }

    /**
     * Obtener instructores disponibles para una ficha específica
     */
    public function instructoresDisponibles(Request $request)
    {
        try {
            $request->validate([
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'especialidad_requerida' => 'nullable|string',
                'regional_id' => 'nullable|integer|exists:regionals,id'
            ]);

            $criterios = $request->only(['fecha_inicio', 'fecha_fin', 'especialidad_requerida', 'regional_id']);
            $instructoresDisponibles = $this->businessRulesService->obtenerInstructoresDisponibles($criterios);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'instructores' => $instructoresDisponibles,
                    'total' => count($instructoresDisponibles)
                ]);
            }

            return view('instructores.disponibles', compact('instructoresDisponibles', 'criterios'));

        } catch (\Exception $e) {
            Log::error('Error obteniendo instructores disponibles', [
                'error' => $e->getMessage(),
                'criterios' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener instructores disponibles'
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al obtener instructores disponibles');
        }
    }

    /**
     * Validar reglas SENA para asignación de ficha
     */
    public function validarReglasSENA(Request $request, Instructor $instructor)
    {
        try {
            $request->validate([
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
                'especialidad_requerida' => 'nullable|string',
                'regional_id' => 'nullable|integer|exists:regionals,id'
            ]);

            $datosFicha = $request->only(['fecha_inicio', 'fecha_fin', 'especialidad_requerida', 'regional_id']);
            $validacion = $this->businessRulesService->validarReglasSENA($instructor, $datosFicha);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'validacion' => $validacion
                ]);
            }

            return response()->json($validacion);

        } catch (\Exception $e) {
            Log::error('Error validando reglas SENA', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al validar reglas de negocio'
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al validar reglas de negocio');
        }
    }

    /**
     * Obtener estadísticas de carga de trabajo
     */
    public function estadisticasCargaTrabajo(Request $request)
    {
        try {
            $estadisticas = $this->businessRulesService->obtenerEstadisticasCargaTrabajo();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'estadisticas' => $estadisticas
                ]);
            }

            return view('instructores.estadisticas-carga', compact('estadisticas'));

        } catch (\Exception $e) {
            Log::error('Error obteniendo estadísticas de carga', [
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al obtener estadísticas'
                ], 500);
            }

            return redirect()->back()->with('error', 'Error al obtener estadísticas de carga de trabajo');
        }
    }

    /**
     * Asignar ficha a instructor con validaciones de negocio
     */
    public function asignarFicha(Request $request, Instructor $instructor)
    {
        try {
            $request->validate([
                'ficha_id' => 'required|integer|exists:ficha_caracterizacions,id',
                'total_horas_instructor' => 'required|integer|min:1|max:1000',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
            ]);

            $ficha = FichaCaracterizacion::findOrFail($request->ficha_id);
            
            // Verificar que la ficha esté activa
            if (!$ficha->status) {
                return response()->json([
                    'success' => false,
                    'message' => 'La ficha seleccionada no está activa'
                ], 400);
            }

            // Validar disponibilidad del instructor
            $datosFicha = [
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'especialidad_requerida' => $ficha->programaFormacion->redConocimiento->nombre ?? null,
                'regional_id' => $ficha->regional_id,
                'horas_semanales' => $request->total_horas_instructor
            ];

            $disponibilidad = $this->businessRulesService->verificarDisponibilidad($instructor, $datosFicha);
            
            if (!$disponibilidad['disponible']) {
                return response()->json([
                    'success' => false,
                    'message' => 'El instructor no está disponible para esta ficha',
                    'razones' => $disponibilidad['razones'],
                    'conflictos' => $disponibilidad['conflictos'] ?? []
                ], 400);
            }

            // Validar reglas SENA
            $validacionSENA = $this->businessRulesService->validarReglasSENA($instructor, $datosFicha);
            
            if (!$validacionSENA['valido']) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se cumplen las reglas de negocio del SENA',
                    'errores' => $validacionSENA['errores']
                ], 400);
            }

            // Crear la asignación
            $instructorFicha = $instructor->instructorFichas()->create([
                'ficha_caracterizacion_id' => $ficha->id,
                'total_horas_instructor' => $request->total_horas_instructor,
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'status' => true,
                'user_create_id' => Auth::id()
            ]);

            Log::info('Ficha asignada al instructor', [
                'instructor_id' => $instructor->id,
                'ficha_id' => $ficha->id,
                'total_horas' => $request->total_horas_instructor
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ficha asignada exitosamente al instructor',
                'asignacion' => $instructorFicha,
                'advertencias' => $validacionSENA['advertencias'] ?? []
            ]);

        } catch (\Exception $e) {
            Log::error('Error asignando ficha al instructor', [
                'instructor_id' => $instructor->id,
                'ficha_id' => $request->ficha_id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al asignar la ficha al instructor'
            ], 500);
        }
    }

    /**
     * Desasignar ficha del instructor
     */
    public function desasignarFicha(Request $request, Instructor $instructor, $fichaId)
    {
        try {
            $instructorFicha = $instructor->instructorFichas()
                ->where('ficha_caracterizacion_id', $fichaId)
                ->first();

            if (!$instructorFicha) {
                return response()->json([
                    'success' => false,
                    'message' => 'La ficha no está asignada a este instructor'
                ], 404);
            }

            // Verificar que la ficha no haya comenzado aún
            if (Carbon::parse($instructorFicha->fecha_inicio)->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede desasignar una ficha que ya ha comenzado'
                ], 400);
            }

            $instructorFicha->update([
                'status' => false,
                'user_edit_id' => Auth::id()
            ]);

            Log::info('Ficha desasignada del instructor', [
                'instructor_id' => $instructor->id,
                'ficha_id' => $fichaId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ficha desasignada exitosamente del instructor'
            ]);

        } catch (\Exception $e) {
            Log::error('Error desasignando ficha del instructor', [
                'instructor_id' => $instructor->id,
                'ficha_id' => $fichaId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al desasignar la ficha del instructor'
            ], 500);
        }
    }
}
