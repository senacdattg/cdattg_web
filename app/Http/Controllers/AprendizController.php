<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAprendizRequest;
use App\Http\Requests\UpdateAprendizRequest;
use App\Models\Aprendiz;
use App\Models\FichaCaracterizacion;
use App\Models\Persona;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AprendizController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

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
            $query = Aprendiz::with(['persona', 'fichaCaracterizacion']);

            // Filtro por búsqueda de nombre o documento
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('persona', function ($q) use ($search) {
                    $q->where('primer_nombre', 'LIKE', "%{$search}%")
                        ->orWhere('primer_apellido', 'LIKE', "%{$search}%")
                        ->orWhere('numero_documento', 'LIKE', "%{$search}%");
                });
            }

            // Filtro por ficha
            if ($request->filled('ficha_id')) {
                $query->where('ficha_caracterizacion_id', $request->ficha_id);
            }

            $aprendices = $query->paginate(10)->withQueryString();
            
            // Debug: Verificar que las personas estén cargadas
            Log::info('Aprendices cargados en index', [
                'total' => $aprendices->count(),
                'con_persona' => $aprendices->filter(fn($a) => !is_null($a->persona))->count(),
                'sin_persona' => $aprendices->filter(fn($a) => is_null($a->persona))->count(),
            ]);
            
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
            DB::transaction(function () use ($request) {
                $data = $request->validated();

                // Crear el aprendiz
                $aprendiz = Aprendiz::create($data);

                // Asignar rol de aprendiz al usuario asociado a la persona
                $persona = Persona::find($data['persona_id']);
                if ($persona->user) {
                    $persona->user->assignRole('APRENDIZ');
                }

                Log::info('Aprendiz creado exitosamente', [
                    'aprendiz_id' => $aprendiz->id,
                    'persona_id' => $data['persona_id'],
                    'ficha_id' => $data['ficha_caracterizacion_id'],
                    'user_id' => Auth::id()
                ]);
            });

            return redirect()->route('aprendices.index')
                ->with('success', '¡Aprendiz registrado exitosamente!');
        } catch (\Exception $e) {
            Log::error('Error al crear aprendiz: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar el aprendiz. Por favor, inténtelo de nuevo.');
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
            // Cargar el aprendiz con sus relaciones
            $aprendiz = Aprendiz::with([
                'persona',
                'fichaCaracterizacion.programaFormacion',
                'asistencias' => function ($query) {
                    $query->latest()->take(10);
                }
            ])->findOrFail($id);

            // Log de debug
            Log::info('Mostrando aprendiz', [
                'aprendiz_id' => $aprendiz->id,
                'persona_id' => $aprendiz->persona_id,
                'tiene_persona' => !is_null($aprendiz->persona),
            ]);

            // Verificar si el aprendiz tiene una persona asociada
            if (!$aprendiz->persona) {
                Log::warning('Aprendiz sin persona asociada', [
                    'aprendiz_id' => $aprendiz->id,
                    'persona_id' => $aprendiz->persona_id
                ]);
                
                return redirect()->route('aprendices.index')
                    ->with('warning', 'Este aprendiz no tiene información de persona asociada. Por favor, edite el registro para asignar una persona válida.');
            }

            return view('aprendices.show', compact('aprendiz'));
        } catch (\Exception $e) {
            Log::error('Error al mostrar aprendiz', [
                'id_recibido' => $id,
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine()
            ]);

            return redirect()->route('aprendices.index')
                ->with('error', 'Error al cargar la información del aprendiz: ' . $e->getMessage());
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
            // Cargar el aprendiz con sus relaciones
            $aprendiz = Aprendiz::with('fichaCaracterizacion')->findOrFail($id);
            
            // Obtener personas que no son aprendices, o que son este aprendiz
            $personas = Persona::where(function ($query) use ($aprendiz) {
                $query->whereDoesntHave('aprendiz')
                    ->orWhere('id', $aprendiz->persona_id);
            })->where('status', 1)->get();

            $fichas = FichaCaracterizacion::where('status', 1)->get();

            return view('aprendices.edit', compact('aprendiz', 'personas', 'fichas'));
        } catch (\Exception $e) {
            Log::error('Error al cargar formulario de edición de aprendiz: ' . $e->getMessage(), [
                'id_recibido' => $id
            ]);

            return redirect()->route('aprendices.index')->with('error', 'Error al cargar el formulario.');
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
            $aprendiz = Aprendiz::findOrFail($id);
            
            DB::transaction(function () use ($request, $aprendiz) {
                $data = $request->validated();

                // Actualizar el aprendiz
                $aprendiz->update($data);

                Log::info('Aprendiz actualizado exitosamente', [
                    'aprendiz_id' => $aprendiz->id,
                    'ficha_id' => $data['ficha_caracterizacion_id'],
                    'user_id' => Auth::id()
                ]);
            });

            return redirect()->route('aprendices.show', $aprendiz->id)
                ->with('success', 'Información del aprendiz actualizada exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar aprendiz: ' . $e->getMessage(), [
                'id_recibido' => $id,
                'request_data' => $request->all(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el aprendiz. Por favor, inténtelo de nuevo.');
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
            $aprendiz = Aprendiz::findOrFail($id);
            
            DB::transaction(function () use ($aprendiz) {
                // Eliminar el aprendiz (soft delete)
                $aprendiz->delete();

                Log::info('Aprendiz eliminado exitosamente', [
                    'aprendiz_id' => $aprendiz->id,
                    'user_id' => Auth::id()
                ]);
            });

            return redirect()->route('aprendices.index')
                ->with('success', 'Aprendiz eliminado exitosamente.');
        } catch (QueryException $e) {
            Log::error('Error de base de datos al eliminar aprendiz: ' . $e->getMessage(), [
                'id_recibido' => $id
            ]);

            return redirect()->back()
                ->with('error', 'No se puede eliminar el aprendiz porque tiene registros de asistencia asociados.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar aprendiz: ' . $e->getMessage(), [
                'id_recibido' => $id
            ]);

            return redirect()->back()
                ->with('error', 'Error al eliminar el aprendiz. Por favor, inténtelo de nuevo.');
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
            $ficha = FichaCaracterizacion::findOrFail($fichaId);

            $aprendices = $ficha->aprendices()
                ->with('aprendiz.persona')
                ->get()
                ->map(function ($aprendizFicha) {
                    return [
                        'id' => $aprendizFicha->aprendiz->id,
                        'persona_id' => $aprendizFicha->aprendiz->persona_id,
                        'nombre_completo' => $aprendizFicha->aprendiz->persona->nombre_completo,
                        'numero_documento' => $aprendizFicha->aprendiz->persona->numero_documento,
                        'email' => $aprendizFicha->aprendiz->persona->email,
                    ];
                });

            return response()->json([
                'success' => true,
                'aprendices' => $aprendices
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener aprendices por ficha: ' . $e->getMessage(), [
                'ficha_id' => $fichaId
            ]);

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
            $aprendices = Aprendiz::with('persona')->get();

            return response()->json([
                'success' => true,
                'aprendices' => $aprendices
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
            $search = $request->input('q', '');

            $aprendices = Aprendiz::with('persona')
                ->whereHas('persona', function ($query) use ($search) {
                    $query->where('primer_nombre', 'LIKE', "%{$search}%")
                        ->orWhere('primer_apellido', 'LIKE', "%{$search}%")
                        ->orWhere('numero_documento', 'LIKE', "%{$search}%");
                })
                ->limit(10)
                ->get()
                ->map(function ($aprendiz) {
                    return [
                        'id' => $aprendiz->id,
                        'nombre_completo' => $aprendiz->persona->nombre_completo,
                        'numero_documento' => $aprendiz->persona->numero_documento,
                        'email' => $aprendiz->persona->email,
                    ];
                });

            return response()->json([
                'success' => true,
                'aprendices' => $aprendices
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al buscar aprendices: ' . $e->getMessage(), [
                'search_term' => $request->input('q')
            ]);

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
            $aprendiz = Aprendiz::findOrFail($id);
            $nuevoEstado = !$aprendiz->estado;
            $aprendiz->update(['estado' => $nuevoEstado]);

            Log::info('Estado de aprendiz cambiado', [
                'aprendiz_id' => $aprendiz->id,
                'nuevo_estado' => $nuevoEstado,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()->with('success', 'Estado cambiado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del aprendiz: ' . $e->getMessage(), [
                'id_recibido' => $id
            ]);

            return redirect()->back()->with('error', 'Error al cambiar el estado del aprendiz.');
        }
    }

    /**
     * Método de test para verificar la carga de datos
     * TEMPORAL - ELIMINAR DESPUÉS
     */
    public function testData()
    {
        $query = Aprendiz::with(['persona', 'fichaCaracterizacion']);
        $aprendices = $query->paginate(10);
        
        $data = [];
        foreach ($aprendices as $aprendiz) {
            $data[] = [
                'id' => $aprendiz->id,
                'persona_id' => $aprendiz->persona_id,
                'persona_cargada' => !is_null($aprendiz->persona),
                'nombre' => $aprendiz->persona?->nombre_completo ?? 'NULL',
                'documento' => $aprendiz->persona?->numero_documento ?? 'NULL',
                'email' => $aprendiz->persona?->email ?? 'NULL',
                'relaciones_cargadas' => array_keys($aprendiz->getRelations()),
            ];
        }
        
        return response()->json([
            'success' => true,
            'total' => $aprendices->total(),
            'data' => $data,
        ]);
    }
}

