<?php

namespace App\Http\Controllers\Inventario;

use App\Models\Inventario\ContratoConvenio;
use App\Models\Inventario\Proveedor;
use App\Models\ParametroTema;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Inventario\ContratoConvenioRequest;
use Illuminate\Support\Facades\Storage;

class ContratoConvenioController extends InventarioController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:VER CONTRATO')->only('index', 'show');
        $this->middleware('can:CREAR CONTRATO')->only('create', 'store');
        $this->middleware('can:EDITAR CONTRATO')->only('edit', 'update');
        $this->middleware('can:ELIMINAR CONTRATO')->only('destroy');
    }

    public function index(Request $request) : View
    {
        $search = $request->input('search');

        $contratosConveniosQuery = ContratoConvenio::with([
            'proveedor',
            'estado.parametro',
            'userCreate.persona',
            'userUpdate.persona'
        ])->latest();

        if (!empty($search)) {
            $contratosConveniosQuery->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('codigo', 'LIKE', "%{$search}%")
                    ->orWhereHas('proveedor', function ($proveedorQuery) use ($search) {
                        $proveedorQuery->where('proveedor', 'LIKE', "%{$search}%");
                    });
            });
        }

        $contratosConvenios = $contratosConveniosQuery
            ->paginate(10)
            ->appends($request->only('search'));

        $contratosConvenios->withPath(route('inventario.contratos-convenios.index'));

        $estados = ParametroTema::with(['parametro', 'tema'])
            ->whereHas('tema', fn($q) => $q->where('name', 'ESTADOS'))
            ->where('parametros_temas.status', 1)
            ->get();

        return view('inventario.contratos_convenios.index', compact('contratosConvenios', 'estados'));
    }

    public function create() : View
    {
        $proveedores = Proveedor::orderBy('proveedor')->get();
        return view('inventario.contratos_convenios.create', compact('proveedores'));
    }

    public function show(ContratoConvenio $contratoConvenio) : View
    {
        $contratoConvenio->load([
            'proveedor',
            'productos',
            'estado.parametro',
            'userCreate.persona',
            'userUpdate.persona'
        ]);
        return view('inventario.contratos_convenios.show', compact('contratoConvenio'));
    }

    public function edit(ContratoConvenio $contratoConvenio) : View
    {
        $proveedores = Proveedor::orderBy('proveedor')->get();
        return view('inventario.contratos_convenios.edit', compact('contratoConvenio', 'proveedores'));
    }

    public function store(ContratoConvenioRequest $request) : RedirectResponse
    {
        $validated = $request->validated();

        $contrato = new ContratoConvenio($validated);
        $this->setUserIds($contrato);
        $contrato->save();

        return redirect()->route('inventario.contratos-convenios.index')
            ->with('success', 'Contrato/Convenio creado exitosamente.');
    }

    public function update(ContratoConvenioRequest $request, ContratoConvenio $contratoConvenio) : RedirectResponse
    {
        $validated = $request->validated();

        $contratoConvenio->fill($validated);
        $this->setUserIds($contratoConvenio, true);
        $contratoConvenio->save();

        return redirect()->route('inventario.contratos-convenios.index')
            ->with('success', 'Contrato/Convenio actualizado exitosamente.');
    }

    public function destroy(ContratoConvenio $contratoConvenio) : RedirectResponse
    {
        try {
            $contratoConvenio->delete();
            return redirect()->route('inventario.contratos-convenios.index')
                ->with('success', 'Contrato/Convenio eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar el Contrato/Convenio porque está en uso.');
        }
    }
}
