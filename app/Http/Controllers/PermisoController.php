<?php

namespace App\Http\Controllers;

use App\Services\PermisoService;
use App\Models\User;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermisoController extends Controller
{
    protected PermisoService $permisoService;

    public function __construct(PermisoService $permisoService)
    {
        $this->permisoService = $permisoService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('permisos.index');
    }

    public function datatable(Request $request): JsonResponse
    {
        $baseQuery = User::query()->with(['persona', 'roles']);

        $recordsTotal = (clone $baseQuery)->count();

        $filteredQuery = clone $baseQuery;

        $searchValue = $request->input('search.value');
        if ($searchValue) {
            $filteredQuery->whereHas('persona', function ($query) use ($searchValue) {
                $query->where('primer_nombre', 'like', "%{$searchValue}%")
                    ->orWhere('segundo_nombre', 'like', "%{$searchValue}%")
                    ->orWhere('primer_apellido', 'like', "%{$searchValue}%")
                    ->orWhere('segundo_apellido', 'like', "%{$searchValue}%")
                    ->orWhere('numero_documento', 'like', "%{$searchValue}%")
                    ->orWhere('email', 'like', "%{$searchValue}%");
            });
        }

        $recordsFiltered = $searchValue ? (clone $filteredQuery)->count() : $recordsTotal;

        $columns = [
            0 => 'users.id',
            1 => 'personas.primer_nombre',
            2 => 'personas.numero_documento',
            3 => 'personas.email',
            4 => 'roles',
            5 => 'users.status',
        ];

        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'asc');
        $orderColumn = $columns[$orderColumnIndex] ?? 'users.id';

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $usersQuery = (clone $filteredQuery)->leftJoin('personas', 'users.persona_id', '=', 'personas.id')->orderBy($orderColumn, $orderDirection)->select('users.*');

        if ($length !== -1) {
            $usersQuery->skip($start)->take($length);
        }

        $users = $usersQuery->get()->load('persona');

        $data = $users->map(function (User $user, $index) use ($start) {
            $roles = $user->getRoleNames();
            $primaryRole = $roles->first() ?? 'Sin rol';
            $rolesHtml = '<span class="badge badge-primary">' . e($primaryRole) . '</span>';
            if ($roles->count() > 1) {
                $rolesHtml .= '<small class="text-muted d-block">(+' . ($roles->count() - 1) . ' más)</small>';
            }

            $statusBadge = $user->status === 1
                ? '<span class="badge badge-success">ACTIVO</span>'
                : '<span class="badge badge-danger">INACTIVO</span>';

            return [
                'index' => $start + $index + 1,
                'nombre' => $user->persona->nombre_completo ?? 'N/A',
                'numero_documento' => $user->persona->numero_documento ?? 'N/A',
                'email' => $user->persona->email ?? 'N/A',
                'roles' => $rolesHtml,
                'estado' => $statusBadge,
                'acciones' => '<div class="btn-group" role="group"><a class="btn btn-sm btn-light" href="' . route('permiso.show', $user->id) . '" title="Ver Permisos"><i class="fas fa-eye text-warning"></i></a></div>',
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $permisosUser = $request->input('permisos', []);
            $userId = $request->user_id;

            $this->permisoService->asignarPermisos($userId, $permisosUser);

            return redirect()->route('permiso.index')->with('success', 'Permisos asignados con éxito');
        } catch (\Exception $e) {
            Log::error('Error al asignar permisos: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        $permisos = Permission::all();
        $roles = Role::all();

        return view('permisos.show', compact('user', 'permisos', 'roles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
