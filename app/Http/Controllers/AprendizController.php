<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAprendizRequest;
use App\Http\Requests\UpdateAprendizRequest;
use App\Services\AprendizService;
use App\Models\FichaCaracterizacion;
use App\Models\Persona;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AprendizController extends Controller
{
    protected AprendizService $aprendizService;

    public function __construct(AprendizService $aprendizService)
    {
        $this->middleware('auth');
        $this->aprendizService = $aprendizService;

        $this->middleware('can:VER APRENDIZ')->only(['index', 'show']);
        $this->middleware('can:CREAR APRENDIZ')->only(['create', 'store']);
        $this->middleware('can:EDITAR APRENDIZ')->only(['edit', 'update', 'cambiarEstado']);
        $this->middleware('can:ELIMINAR APRENDIZ')->only('destroy');
    }

    /**
     * Muestra un listado de aprendices con búsqueda y filtros.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $filtros = $request->only(['search', 'ficha_id']);
            $filtros['per_page'] = 15;

            $aprendices = $this->aprendizService->listarConFiltros($filtros);
            $fichas = FichaCaracterizacion::where('status', 1)->get();

            return view('aprendices.index', compact('aprendices', 'fichas'));
        } catch (\Exception $e) {
            Log::error('Error al listar aprendices: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar el listado de aprendices.');
        }
    }

    /**
     * Muestra el formulario para crear un nuevo aprendiz.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        try {
            // Obtener solo personas que NO son aprendices aún
            $personas = Persona::whereDoesntHave('aprendiz')
                ->where('status', 1)
                ->get();

            $fichas = FichaCaracterizacion::where('status', 1)->get();

            return view('aprendices.create', compact('personas', 'fichas'));
        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de creación de aprendiz: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cargar el formulario.');
        }
    }

    /**
     * Almacena un nuevo aprendiz en la base de datos.
     *
     * @param StoreAprendizRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreAprendizRequest $request)
    {
        try {
            $this->aprendizService->crear($request->validated());

            return redirect()->route('aprendices.index')
                ->with('success', '¡Aprendiz registrado exitosamente!');
        } catch (\Exception $e) {
            Log::error('Error al crear aprendiz', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Muestra la información de un aprendiz específico.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            $aprendiz = $this->aprendizService->obtener($id);

            if (!$aprendiz) {
                return redirect()->route('aprendices.index')
                    ->with('error', 'Aprendiz no encontrado.');
            }

            if (!$aprendiz->persona) {
                return redirect()->route('aprendices.index')
                    ->with('warning', 'Este aprendiz no tiene información de persona asociada.');
            }

            return view('aprendices.show', compact('aprendiz'));
        } catch (\Exception $e) {
            Log::error('Error al mostrar aprendiz: ' . $e->getMessage());

            return redirect()->route('aprendices.index')
                ->with('error', 'Error al cargar la información del aprendiz.');
        }
    }

    /**
     * Muestra el formulario para editar un aprendiz.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $aprendiz = $this->aprendizService->obtener($id);
            
            if (!$aprendiz) {
                return redirect()->route('aprendices.index')
                    ->with('error', 'Aprendiz no encontrado.');
            }

            $fichas = FichaCaracterizacion::where('status', 1)->get();

            return view('aprendices.edit', compact('aprendiz', 'fichas'));
        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de edición: ' . $e->getMessage());

            return redirect()->route('aprendices.index')
                ->with('error', 'Error al cargar el formulario.');
        }
    }

    /**
     * Actualiza la información de un aprendiz en la base de datos.
     *
     * @param UpdateAprendizRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateAprendizRequest $request, $id)
    {
        try {
            $this->aprendizService->actualizar($id, $request->validated());

            return redirect()->route('aprendices.show', $id)
                ->with('success', 'Información del aprendiz actualizada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar aprendiz: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Elimina un aprendiz de la base de datos.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            $this->aprendizService->eliminar($id);

            return redirect()->route('aprendices.index')
                ->with('success', 'Aprendiz eliminado exitosamente.');
        } catch (QueryException $e) {
            Log::error('Error de base de datos al eliminar aprendiz: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'No se puede eliminar el aprendiz porque tiene registros asociados.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar aprendiz: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Obtiene los aprendices asociados a una ficha específica.
     *
     * @param int $fichaId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAprendicesByFicha($fichaId)
    {
        try {
            $aprendices = $this->aprendizService->obtenerPorFicha($fichaId);

            $datos = $aprendices->map(function ($aprendiz) {
                return $this->aprendizService->formatearParaApi($aprendiz);
            });

            return response()->json([
                'success' => true,
                'aprendices' => $datos
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener aprendices por ficha: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los aprendices de la ficha.'
            ], 500);
        }
    }

    /**
     * API endpoint para listar todos los aprendices.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiIndex()
    {
        try {
            $aprendices = $this->aprendizService->listarConFiltros(['per_page' => 1000]);

            $datos = $aprendices->map(function ($aprendiz) {
                return $this->aprendizService->formatearParaApi($aprendiz);
            });

            return response()->json([
                'success' => true,
                'aprendices' => $datos
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error en API de aprendices: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los aprendices.'
            ], 500);
        }
    }

    /**
     * Busca aprendices por término de búsqueda (nombre o documento).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $termino = $request->input('q', '');
            $aprendices = $this->aprendizService->buscar($termino, 10);

            $datos = $aprendices->map(function ($aprendiz) {
                return $this->aprendizService->formatearParaApi($aprendiz);
            });

            return response()->json([
                'success' => true,
                'aprendices' => $datos
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al buscar aprendices: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al buscar aprendices.'
            ], 500);
        }
    }

    /**
     * Cambia el estado de un aprendiz (activo/inactivo).
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cambiarEstado($id)
    {
        try {
            $this->aprendizService->cambiarEstado($id);

            return redirect()->back()->with('success', 'Estado cambiado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del aprendiz: ' . $e->getMessage());

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * DataTable server-side para aprendices
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable(Request $request)
    {
        try {
            $query = \App\Models\Aprendiz::with(['persona.tipoDocumento', 'fichaCaracterizacion.programaFormacion'])
                ->whereIn('id', function($subquery) {
                    $subquery->select(\DB::raw('MAX(id)'))
                        ->from('aprendices')
                        ->groupBy('persona_id');
                });

            // Búsqueda global
            if ($search = $request->input('search.value')) {
                $query->whereHas('persona', function ($q) use ($search) {
                    $q->where('primer_nombre', 'LIKE', "%{$search}%")
                        ->orWhere('segundo_nombre', 'LIKE', "%{$search}%")
                        ->orWhere('primer_apellido', 'LIKE', "%{$search}%")
                        ->orWhere('segundo_apellido', 'LIKE', "%{$search}%")
                        ->orWhere('numero_documento', 'LIKE', "%{$search}%");
                });
            }

            $totalRecords = $query->count();
            
            // Ordenamiento
            if ($request->has('order')) {
                $orderColumn = $request->input('order.0.column');
                $orderDir = $request->input('order.0.dir');
                
                $columns = ['id', 'persona.numero_documento', 'persona.primer_nombre', 'fichaCaracterizacion.ficha', 'estado'];
                if (isset($columns[$orderColumn])) {
                    if (str_contains($columns[$orderColumn], '.')) {
                        [$relation, $column] = explode('.', $columns[$orderColumn]);
                        $query->join($relation === 'persona' ? 'personas' : 'ficha_caracterizacions', 
                            'aprendices.' . $relation . '_id', '=', $relation === 'persona' ? 'personas.id' : 'ficha_caracterizacions.id')
                            ->orderBy($column, $orderDir);
                    } else {
                        $query->orderBy($columns[$orderColumn], $orderDir);
                    }
                }
            }

            // Paginación
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            
            $aprendices = $query->skip($start)->take($length)->get();

            $data = $aprendices->map(function ($aprendiz) {
                return [
                    'id' => $aprendiz->id,
                    'documento' => $aprendiz->persona->numero_documento ?? 'N/A',
                    'nombre' => $aprendiz->persona->nombre_completo ?? 'N/A',
                    'ficha' => $aprendiz->fichaCaracterizacion->ficha ?? 'N/A',
                    'programa' => $aprendiz->fichaCaracterizacion->programaFormacion->nombre ?? 'N/A',
                    'estado' => $aprendiz->estado ? 'Activo' : 'Inactivo',
                    'acciones' => $aprendiz->id,
                ];
            });

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error en DataTable de aprendices: ' . $e->getMessage());
            
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Error al cargar los datos'
            ], 500);
        }
    }
}

