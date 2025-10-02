<?php

namespace App\Http\Controllers;

use App\Models\ProgramaFormacion;
use App\Models\RedConocimiento;
use App\Models\Parametro;
use App\Http\Requests\StoreProgramaFormacionRequest;
use App\Http\Requests\UpdateProgramaFormacionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProgramaFormacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:programa.index')->only('index');
        $this->middleware('permission:programa.create')->only('create', 'store');
        $this->middleware('permission:programa.edit')->only('edit', 'update');
        $this->middleware('permission:programa.delete')->only('destroy');
        $this->middleware('permission:programa.search')->only('search');
    }

    /**
     * Muestra una lista paginada de programas de formación.
     *
     * Este método recupera una lista de programas de formación desde la base de datos,
     * incluyendo las relaciones con 'sede' y 'tipoPrograma', y los pagina en grupos de 7.
     * Luego, pasa esta lista a la vista 'programas.index'.
     *
     * @return \Illuminate\View\View La vista que muestra la lista de programas de formación.
     */
    public function index()
    {
        $programas = ProgramaFormacion::with(['redConocimiento', 'nivelFormacion'])->orderBy('id', 'desc')->paginate(6);

        return view('programas.index', compact('programas'));
    }


    /**
     * Muestra el formulario para crear un nuevo programa de formación.
     *
     * Este método obtiene todas las sedes y tipos de programas disponibles.
     * Si no hay sedes o tipos de programas, se asigna null a las variables correspondientes.
     *
     * @return \Illuminate\View\View La vista del formulario de creación de programas de formación.
     */
    public function create()
    {
        $redesConocimiento = RedConocimiento::all();
        $nivelesFormacion = Parametro::where('tema_id', 1)->get(); // Asumiendo que tema_id 1 corresponde a niveles de formación

        return view('programas.create', compact('redesConocimiento', 'nivelesFormacion'));
    }


    /**
     * Almacena un nuevo programa de formación en la base de datos.
     *
     * Valida los datos de entrada del formulario y crea un nuevo registro
     * en la tabla 'programas_formacion' con los datos proporcionados.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos del formulario.
     * @return \Illuminate\Http\RedirectResponse Redirige a la página de índice de programas con un mensaje de éxito.
     */
    public function store(StoreProgramaFormacionRequest $request)
    {
        try {
            $programaFormacion = new ProgramaFormacion();
            $programaFormacion->codigo = $request->input('codigo');
            $programaFormacion->nombre = $request->input('nombre');
            $programaFormacion->red_conocimiento_id = $request->input('red_conocimiento_id');
            $programaFormacion->nivel_formacion_id = $request->input('nivel_formacion_id');

            if ($programaFormacion->save()) {
                Log::info('Programa de formación creado exitosamente', [
                    'programa_id' => $programaFormacion->id,
                    'codigo' => $programaFormacion->codigo,
                    'nombre' => $programaFormacion->nombre,
                    'usuario_id' => Auth::id()
                ]);
                return redirect()->route('programa.index')->with('success', 'Programa de formación creado exitosamente.');
            } else {
                return redirect()->back()->with('error', 'Error al crear el programa de formación.');
            }
        } catch (\Exception $e) {
            Log::error('Error al crear programa de formación', [
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Error interno al crear el programa de formación.');
        }
    }


    /**
     * Muestra el formulario de edición para un programa de formación específico.
     *
     * @param string $id El ID del programa de formación a editar.
     * @return \Illuminate\View\View La vista del formulario de edición con los datos del programa, sedes y tipos de programa.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si no se encuentra el programa de formación con el ID proporcionado.
     */
    public function edit(string $id)
    {
        $programa = ProgramaFormacion::findOrFail($id);
        $redesConocimiento = RedConocimiento::all();
        $nivelesFormacion = Parametro::where('tema_id', 1)->get(); // Asumiendo que tema_id 1 corresponde a niveles de formación

        return view('programas.edit', compact('programa', 'redesConocimiento', 'nivelesFormacion'));
    }


    /**
     * Actualiza un programa de formación existente.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos del formulario.
     * @param string $id El ID del programa de formación que se va a actualizar.
     * @return \Illuminate\Http\RedirectResponse Redirige a la página de índice de programas con un mensaje de éxito.
     *
     * @throws \Illuminate\Validation\ValidationException Si la validación de los datos falla.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Si no se encuentra el programa de formación con el ID proporcionado.
     */
    public function update(UpdateProgramaFormacionRequest $request, string $id)
    {
        try {
            $programaFormacion = ProgramaFormacion::findOrFail($id);
            $programaFormacion->codigo = $request->input('codigo');
            $programaFormacion->nombre = $request->input('nombre');
            $programaFormacion->red_conocimiento_id = $request->input('red_conocimiento_id');
            $programaFormacion->nivel_formacion_id = $request->input('nivel_formacion_id');
            
            if ($programaFormacion->save()) {
                Log::info('Programa de formación actualizado exitosamente', [
                    'programa_id' => $programaFormacion->id,
                    'codigo' => $programaFormacion->codigo,
                    'nombre' => $programaFormacion->nombre,
                    'usuario_id' => Auth::id()
                ]);
                return redirect()->route('programa.index')->with('success', 'Programa de formación actualizado exitosamente.');
            } else {
                return redirect()->back()->with('error', 'Error al actualizar el programa de formación.');
            }
        } catch (\Exception $e) {
            Log::error('Error al actualizar programa de formación', [
                'programa_id' => $id,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Error interno al actualizar el programa de formación.');
        }
    }


    /**
     * Elimina un programa de formación especificado por su ID.
     *
     * @param string $id El ID del programa de formación a eliminar.
     * @return \Illuminate\Http\RedirectResponse Redirige a la página de índice de programas con un mensaje de éxito.
     */
    public function destroy(string $id)
    {
        try {
            $programaFormacion = ProgramaFormacion::findOrFail($id);
            $nombrePrograma = $programaFormacion->nombre;
            
            if ($programaFormacion->delete()) {
                Log::info('Programa de formación eliminado exitosamente', [
                    'programa_id' => $id,
                    'nombre' => $nombrePrograma,
                    'usuario_id' => Auth::id()
                ]);
                return redirect()->route('programa.index')->with('success', 'Programa de formación eliminado exitosamente.');
            } else {
                return redirect()->back()->with('error', 'Error al eliminar el programa de formación.');
            }
        } catch (\Exception $e) {
            Log::error('Error al eliminar programa de formación', [
                'programa_id' => $id,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', 'Error interno al eliminar el programa de formación.');
        }
    }


    /**
     * Busca programas de formación basados en el término de búsqueda proporcionado.
     *
     * Este método toma una solicitud HTTP que contiene un término de búsqueda y busca
     * programas de formación cuyo nombre coincida con el término de búsqueda. También
     * busca programas de formación que estén asociados con sedes o tipos de programas
     * cuyo nombre coincida con el término de búsqueda.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene el término de búsqueda.
     * @return \Illuminate\View\View La vista que muestra los programas de formación encontrados.
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('search');
            $redConocimientoId = $request->input('red_conocimiento_id');
            $nivelFormacionId = $request->input('nivel_formacion_id');
            $status = $request->input('status');
            $perPage = $request->input('per_page', 6);
            
            // Construir consulta base
            $programasQuery = ProgramaFormacion::with(['redConocimiento', 'nivelFormacion', 'userCreated', 'userEdited']);

            // Aplicar filtros
            if (!empty($query)) {
                $programasQuery->where(function ($q) use ($query) {
                    $q->where('codigo', 'LIKE', "%{$query}%")
                      ->orWhere('nombre', 'LIKE', "%{$query}%")
                      ->orWhereHas('redConocimiento', function ($subQuery) use ($query) {
                          $subQuery->where('nombre', 'LIKE', "%{$query}%");
                      })
                      ->orWhereHas('nivelFormacion', function ($subQuery) use ($query) {
                          $subQuery->where('name', 'LIKE', "%{$query}%");
                      });
                });
            }

            if (!empty($redConocimientoId)) {
                $programasQuery->where('red_conocimiento_id', $redConocimientoId);
            }

            if (!empty($nivelFormacionId)) {
                $programasQuery->where('nivel_formacion_id', $nivelFormacionId);
            }

            if ($status !== null && $status !== '') {
                $programasQuery->where('status', $status);
            }

            // Ordenar y paginar
            $programas = $programasQuery->orderBy('nombre', 'asc')->paginate($perPage);

            // Si es una petición AJAX, devolver JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'programas' => $programas->items(),
                        'pagination' => [
                            'current_page' => $programas->currentPage(),
                            'last_page' => $programas->lastPage(),
                            'per_page' => $programas->perPage(),
                            'total' => $programas->total(),
                            'has_more_pages' => $programas->hasMorePages(),
                        ],
                        'filters' => [
                            'search' => $query,
                            'red_conocimiento_id' => $redConocimientoId,
                            'nivel_formacion_id' => $nivelFormacionId,
                            'status' => $status,
                        ]
                    ]
                ]);
            }

            // Si no hay resultados y hay búsqueda, mostrar mensaje
            if ($programas->count() == 0 && !empty($query)) {
                Log::info('Búsqueda de programas sin resultados', [
                    'query' => $query,
                    'filters' => $request->all(),
                    'usuario_id' => Auth::id()
                ]);
                return redirect()->route('programa.index')->with('error', 'No se encontraron programas de formación con los criterios especificados.');
            }

            Log::info('Búsqueda de programas realizada', [
                'query' => $query,
                'filters' => $request->all(),
                'resultados' => $programas->count(),
                'usuario_id' => Auth::id()
            ]);

            return view('programas.index', compact('programas'));
        } catch (\Exception $e) {
            Log::error('Error en búsqueda de programas', [
                'filters' => $request->all(),
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error interno en la búsqueda.'
                ], 500);
            }
            
            return redirect()->route('programa.index')->with('error', 'Error interno en la búsqueda.');
        }
    }

    /**
     * Cambiar el estado de un programa de formación.
     *
     * @param string $id El ID del programa de formación.
     * @return \Illuminate\Http\JsonResponse Respuesta JSON con el resultado.
     */
    public function cambiarEstado(string $id)
    {
        try {
            $programa = ProgramaFormacion::findOrFail($id);
            $programa->status = !$programa->status;
            
            if ($programa->save()) {
                Log::info('Estado del programa cambiado', [
                    'programa_id' => $id,
                    'nuevo_estado' => $programa->status ? 'activo' : 'inactivo',
                    'usuario_id' => Auth::id()
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Estado del programa actualizado exitosamente.',
                    'status' => $programa->status
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cambiar el estado del programa.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del programa', [
                'programa_id' => $id,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno al cambiar el estado del programa.'
            ], 500);
        }
    }

    /**
     * Obtener programas por red de conocimiento.
     *
     * @param string $redConocimientoId ID de la red de conocimiento.
     * @return \Illuminate\Http\JsonResponse Lista de programas.
     */
    public function getByRedConocimiento(string $redConocimientoId)
    {
        try {
            $programas = ProgramaFormacion::where('red_conocimiento_id', $redConocimientoId)
                ->where('status', true)
                ->orderBy('nombre')
                ->get(['id', 'codigo', 'nombre']);

            return response()->json([
                'success' => true,
                'data' => $programas
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener programas por red de conocimiento', [
                'red_conocimiento_id' => $redConocimientoId,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los programas.'
            ], 500);
        }
    }

    /**
     * Obtener programas por nivel de formación.
     *
     * @param string $nivelFormacionId ID del nivel de formación.
     * @return \Illuminate\Http\JsonResponse Lista de programas.
     */
    public function getByNivelFormacion(string $nivelFormacionId)
    {
        try {
            $programas = ProgramaFormacion::where('nivel_formacion_id', $nivelFormacionId)
                ->where('status', true)
                ->orderBy('nombre')
                ->get(['id', 'codigo', 'nombre']);

            return response()->json([
                'success' => true,
                'data' => $programas
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener programas por nivel de formación', [
                'nivel_formacion_id' => $nivelFormacionId,
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los programas.'
            ], 500);
        }
    }

    /**
     * Obtener todos los programas activos.
     *
     * @return \Illuminate\Http\JsonResponse Lista de programas activos.
     */
    public function getActivos()
    {
        try {
            $programas = ProgramaFormacion::where('status', true)
                ->with(['redConocimiento', 'nivelFormacion'])
                ->orderBy('nombre')
                ->get(['id', 'codigo', 'nombre', 'red_conocimiento_id', 'nivel_formacion_id']);

            return response()->json([
                'success' => true,
                'data' => $programas
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener programas activos', [
                'error' => $e->getMessage(),
                'usuario_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los programas activos.'
            ], 500);
        }
    }
}
