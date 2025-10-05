<?php

namespace App\Http\Controllers;

use App\Models\Instructor;
use App\Http\Requests\StoreInstructorRequest;
use App\Http\Requests\UpdateInstructorRequest;
use App\Http\Requests\InstructorRequest;
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

            // Verificar personas sin usuario
            $personasSinUsuario = DB::table('personas')
                ->leftJoin('users', 'personas.id', '=', 'users.persona_id')
                ->whereNull('users.id')
                ->select('personas.id', 'personas.primer_nombre', 'personas.primer_apellido', 'personas.numero_documento', 'personas.email')
                ->get();

            if ($personasSinUsuario->count() > 0) {
                return view('Instructores.error', compact('personasSinUsuario'))->with('error', 'Existen personas sin usuario asociado. Por favor, cree un usuario para cada persona.');
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
            // llamar los tipos de documentos
            $documentos = Tema::with(['parametros' => function ($query) {
                $query->wherePivot('status', 1);
            }])->findOrFail(2);
            
            // llamar los generos
            $generos = Tema::with(['parametros' => function ($query) {
                $query->wherePivot('status', 1);
            }])->findOrFail(3);
            
            $regionales = Regional::where('status', 1)->get();

            return view('Instructores.create', compact('documentos', 'generos', 'regionales'));
            
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
    public function store(StoreInstructorRequest $request)
    {
        try {
            DB::beginTransaction();
            
            // Crear Persona
            $persona = new Persona();
            $persona->tipo_documento = $request->input('tipo_documento');
            $persona->numero_documento = $request->input('numero_documento');
            $persona->primer_nombre = $request->input('primer_nombre');
            $persona->segundo_nombre = $request->input('segundo_nombre');
            $persona->primer_apellido = $request->input('primer_apellido');
            $persona->segundo_apellido = $request->input('segundo_apellido');
            $persona->fecha_de_nacimiento = $request->input('fecha_de_nacimiento');
            $persona->genero = $request->input('genero');
            $persona->email = $request->input('email');
            $persona->save();

            // Crear Instructor
            $instructor = new Instructor();
            $instructor->persona_id = $persona->id;
            $instructor->regional_id = $request->input('regional_id');
            $instructor->save();

            // Crear Usuario asociado a la Persona
            $user = new User();
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('numero_documento'));
            $user->persona_id = $persona->id;
            $user->save();
            $user->assignRole('INSTRUCTOR');

            DB::commit();
            
            Log::info('Instructor creado exitosamente', [
                'instructor_id' => $instructor->id,
                'persona_id' => $persona->id,
                'user_id' => $user->id,
                'email' => $request->input('email')
            ]);
            
            return redirect()->route('instructor.index')->with('success', '¡Registro Exitoso!');
            
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
            
            // Verificar si el instructor tiene relaciones que impidan su eliminación
            $persona = Persona::find($instructor->persona_id);
            $user = User::where('persona_id', $instructor->persona_id)->first();
            
            // Eliminar el instructor
            $instructor->delete();
            
            // Si existe usuario asociado, eliminarlo también
            if ($user) {
                $user->delete();
            }
            
            // Si existe persona asociada, eliminarla también
            if ($persona) {
                $persona->delete();
            }
            
            DB::commit();
            
            Log::info('Instructor eliminado exitosamente', [
                'instructor_id' => $instructor->id,
                'persona_id' => $persona ? $persona->id : null,
                'user_id' => $user ? $user->id : null
            ]);
            
            return redirect()->route('instructor.index')->with('success', 'Instructor eliminado exitosamente');
            
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
        $instructor = Instructor::where('persona_id', $id)->delete();
        $persona = Persona::where('id', $id)->delete();

        if ($instructor && $persona) {
            return redirect()->back()->with('success', '¡Registro eliminado exitosamente!');
        } else {
            return redirect()->back()->with('error', '¡Error al eliminar el registro!');
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
                // Solo puede haber una especialidad principal
                $especialidadesActuales['principal'] = $redConocimiento->nombre;
                $mensaje = 'Especialidad principal asignada exitosamente';
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
                $mensaje = 'Especialidad secundaria asignada exitosamente';
            }

            $instructor->especialidades = $especialidadesActuales;
            $instructor->save();

            return redirect()->back()->with('success', $mensaje);
        } catch (\Exception $e) {
            Log::error('Error al asignar especialidad: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al asignar la especialidad');
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
            $especialidadesActuales = $instructor->especialidades ?? [];

            if ($request->tipo === 'principal') {
                $especialidadesActuales['principal'] = null;
                $mensaje = 'Especialidad principal removida exitosamente';
            } else {
                $especialidadesSecundarias = $especialidadesActuales['secundarias'] ?? [];
                $especialidadesSecundarias = array_filter($especialidadesSecundarias, function($esp) use ($request) {
                    return $esp !== $request->especialidad;
                });
                $especialidadesActuales['secundarias'] = array_values($especialidadesSecundarias);
                $mensaje = 'Especialidad secundaria removida exitosamente';
            }

            $instructor->especialidades = $especialidadesActuales;
            $instructor->save();

            return redirect()->back()->with('success', $mensaje);
        } catch (\Exception $e) {
            Log::error('Error al remover especialidad: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al remover la especialidad');
        }
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
