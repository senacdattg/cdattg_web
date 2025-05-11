<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\JornadaFormacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Events\TransactionBeginning;

class JornadaController extends Controller
{
    public function index()
    {
        $jornadas = JornadaFormacion::all();
        return view('jornada.index', compact('jornadas'));
    }

    public function create()
    {
        return view('jornada.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jornada' => 'required|string|max:255',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ]);
        try{
            DB::beginTransaction();
            JornadaFormacion::create([
                'jornada' => $request->jornada,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
            ]);
            DB::commit();
            return redirect()->route('jornada.index')->with('success', 'Jornada creada');
        }catch(Exception $e){
            DB::rollBack();
            return redirect()->back()->withErrors('Error al momento de crear la jornada');
        }



    }




    public function update(Request $request, $id)
    {

        $jornada = JornadaFormacion::find($id);
        $jornada->update([
            'jornada' => $request->jornada,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
        ]);

        return redirect()->route('jornada.index')->with('success', 'Jornada actualizada');
    }

    public function edit($id)
    {
        $jornada = JornadaFormacion::find($id);

        return view('jornada.edit', compact('jornada'));
    }

    public function destroy($id)
    {
        JornadaFormacion::destroy($id);

        return back()->with('error', 'Jornada eliminada');
    }
}
