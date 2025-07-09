<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistroActividadesRequest;
use App\Http\Requests\UpdateRegistroActividadesRequest;
use App\Models\RegistroActividades;

class RegistroActividadesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('registro_actividades.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRegistroActividadesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(RegistroActividades $registroActividades)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RegistroActividades $registroActividades)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRegistroActividadesRequest $request, RegistroActividades $registroActividades)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegistroActividades $registroActividades)
    {
        //
    }
}
