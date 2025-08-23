<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DetalleOrdenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Aquí implementarás la lógica para mostrar todos los préstamos y salidas
        return view('inventario.ordenes.prestamos_salidas_index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('inventario.ordenes.prestamos_salidas');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validación de los datos
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'required|string|max:20',
            'rol' => 'required|in:estudiante,instructor,coordinador,administrativo',
            'programa_formacion' => 'required|string|max:255',
            'ficha' => 'required|string|max:50',
            'tipo' => 'required|in:prestamo,salida',
            'fecha_adquirido' => 'required|date',
            'fecha_devolucion' => 'required|date|after:fecha_adquirido',
            'estado_articulo' => 'required|in:excelente,bueno,regular,malo,dañado',
        ]);

        // Aquí implementarás la lógica para guardar los datos
        // Ejemplo:
        // PrestamoSalida::create($validatedData);

        return redirect()->route('prestamos_salidas.index')
            ->with('success', 'Préstamo/Salida creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): View
    {
        // Aquí implementarás la lógica para mostrar un préstamo/salida específico
        return view('inventario.ordenes.prestamos_salidas_show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): View
    {
        // Aquí implementarás la lógica para mostrar el formulario de edición
        return view('inventario.ordenes.prestamos_salidas_edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        // Validación de los datos
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'required|string|max:20',
            'rol' => 'required|in:estudiante,instructor,coordinador,administrativo',
            'programa_formacion' => 'required|string|max:255',
            'ficha' => 'required|string|max:50',
            'tipo' => 'required|in:prestamo,salida',
            'fecha_adquirido' => 'required|date',
            'fecha_devolucion' => 'required|date|after:fecha_adquirido',
            'estado_articulo' => 'required|in:excelente,bueno,regular,malo,dañado',
        ]);

        // Aquí implementarás la lógica para actualizar los datos
        // Ejemplo:
        // $prestamoSalida = PrestamoSalida::findOrFail($id);
        // $prestamoSalida->update($validatedData);

        return redirect()->route('prestamos_salidas.index')
            ->with('success', 'Préstamo/Salida actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        // Aquí implementarás la lógica para eliminar un préstamo/salida
        // Ejemplo:
        // $prestamoSalida = PrestamoSalida::findOrFail($id);
        // $prestamoSalida->delete();

        return redirect()->route('prestamos_salidas.index')
            ->with('success', 'Préstamo/Salida eliminado exitosamente.');
    }
}
