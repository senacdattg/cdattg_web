<?php

namespace App\Http\Controllers;

use App\Services\EntradaSalidaService;
use App\Services\ExportService;
use App\Models\EntradaSalida;
use App\Models\FichaCaracterizacion;
use App\Models\Ambiente;
use App\Http\Requests\StoreEntradaSalidaRequest;
// use App\Http\Requests\UpdateEntradaSalidaRequest; // Request no existe
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EntradaSalidaController extends Controller
{
    protected EntradaSalidaService $entradaSalidaService;
    protected ExportService $exportService;

    public function __construct(
        EntradaSalidaService $entradaSalidaService,
        ExportService $exportService
    ) {
        $this->entradaSalidaService = $entradaSalidaService;
        $this->exportService = $exportService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $fichaCaracterizacion)
    {
        $ficha = FichaCaracterizacion::where('id', $fichaCaracterizacion);

        $registros = EntradaSalida::where('instructor_user_id', Auth::user()->id)
            ->where('fecha', Carbon::now()->toDateString())
            ->where('listado', null)->get();

        // Pasa los registros a la vista
        return view('entradaSalidas.index', compact('registros', 'ficha'));
    }
    public function apiIndex(Request $request)
    {
        $fichaCaracterizacion = $request->ficha_id;
        $instructor = $request->instructor_id;
        // Obtén todos los registros de entrada/salida del usuario actual
        $registros = EntradaSalida::where('instructor_user_id', $instructor)
            ->where('fecha', Carbon::now()->toDateString())
            ->where('ficha_caracterizacion_id', $fichaCaracterizacion)
            ->where('listado', null)->get();

        return response()->json($registros, 200);
    }
    public function registros(Request $request)
    {
        $fichaCaracterizacion = $request->ficha_id;
        $ambiente_id = $request->ambiente_id;

        $ambiente = Ambiente::where('id', $ambiente_id)->first();
        $descripcion = $request->descripcion;
        $fecha = Carbon::now()->toDateString();
        // @dd($ficha);
        $ficha = FichaCaracterizacion::where('id', $fichaCaracterizacion)->first();
        // Obtén todos los registros de entrada/salida del usuario actual
        $registros = EntradaSalida::where('instructor_user_id', Auth::user()->id)
            ->where('fecha', Carbon::now()->toDateString())
            ->where('ficha_caracterizacion_id', $fichaCaracterizacion)
            ->where('listado', null)->get();
        // @dd($registros);
        // Pasa los registros a la vista
        return view('entradaSalidas.index', compact('registros', 'ficha', 'fecha', 'ambiente', 'descripcion'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('entradaSalidas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function apiStoreEntradaSalida(Request $request)
    {
        try {
            $datos = [
                'fecha' => Carbon::now()->toDateString(),
                'instructor_user_id' => $request->instructor_user_id,
                'aprendiz' => $request->aprendiz,
                'entrada' => Carbon::now(),
                'ficha_caracterizacion_id' => $request->ficha_caracterizacion_id,
                'ambiente_id' => $request->ambiente_id,
            ];

            $this->entradaSalidaService->registrarEntrada($datos);

            return response()->json(["message" => "Entrada registrada con éxito"], 200);
        } catch (\Exception $e) {
            Log::error('Error al registrar entrada: ' . $e->getMessage());
            return response()->json(["error" => "Error al registrar entrada"], 500);
        }
    }
    public function apiUpdateEntradaSalida(Request $request)
    {
        try {
            $this->entradaSalidaService->registrarSalida($request->aprendiz);

            return response()->json(["message" => "Salida registrada con éxito"], 200);
        } catch (\Exception $e) {
            Log::error('Error al registrar salida: ' . $e->getMessage());
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }
    public function storeEntradaSalida($ficha_id, $aprendiz, $ambiente_id, $descripcion)
    {

        // @dd('holis');
        try {
            // crear aprendiz
            $entradaSalida = EntradaSalida::create([
                'fecha' => Carbon::now()->toDateString(),
                'instructor_user_id' => Auth::user()->id,
                'aprendiz' => $aprendiz,
                'entrada' => Carbon::now(),
                'ficha_caracterizacion_id' => $ficha_id,
            ]);


            return redirect()->route('entradaSalida.registros', compact('ficha_id','ambiente_id', 'descripcion'))->with('success', '¡Registro Exitoso!');
        } catch (QueryException $e) {
            // Manejar excepciones de la base de datos
            @dd($e);
            return redirect()->back()->withErrors(['error' => 'Error de base de datos. Por favor, inténtelo de nuevo.']);
        } catch (\Exception $e) {
            // Manejar otras excepciones
            @dd($e);
            return redirect()->back()->withErrors(['error' => 'Se produjo un error. Por favor, inténtelo de nuevo.']);
        }
    }
    public function apiListarEntradaSalida(Request $request)
    {
        // Obtener la fecha actual
        $fechaHoy = Carbon::now()->toDateString();

        // Realizar la consulta inicial
        $entradaSalidas = EntradaSalida::where('fecha', $fechaHoy)
            ->where('instructor_user_id', $request->instructor_user_id)
            ->where('ficha_caracterizacion_id', $request->ficha_caracterizacion_id)
            ->where('ambiente_id', $request->ambiente_id)
            ->where('listado', null)
            ->get();

        try {
            DB::beginTransaction();

            // Verificar si hay resultados en la consulta inicial
            if ($entradaSalidas->isNotEmpty()) {
                foreach ($entradaSalidas as $entradaSalida) {
                    $entradaSalida->update([
                        'listado' => 1,
                    ]);
                }
            }

            DB::commit();

            // Realizar una nueva consulta para verificar las actualizaciones
            $entradaSalidasNew = EntradaSalida::where('fecha', $fechaHoy)
                ->where('instructor_user_id', $request->instructor_user_id)
                ->where('ficha_caracterizacion_id', $request->ficha_caracterizacion_id)
                ->where('ambiente_id', $request->ambiente_id)
                ->where('listado', 1)
                ->get();

            // Verificar si la nueva consulta tiene resultados
            if ($entradaSalidasNew->isNotEmpty()) {
                return response()->json('Listado exitosamente', 200);
            } else {
                return response()->json('No se encontraron registros actualizados', 404);
            }
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al listar: ' . $e->getMessage()], 500);
        }
    }

    public function store(StoreEntradaSalidaRequest $request)
    {
        @dd('holis');
        try {

            $validator = validator::make($request->all(), [
                // 'user_id' => Auth::user()->id,
                'aprendiz' => 'required|string',
                'ficha_caracterizacion_id' => 'required',
            ]);
            // @dd($request->ficha_caracterizacion_id);

            if ($validator->fails()) {
                // @dd('holis');
                @dd($validator);
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            // @dd('parela ahí');

            // Crear Persona
            $entradaSalida = EntradaSalida::create([
                'fecha' => Carbon::now()->toDateString(),
                'instructor_user_id' => Auth::user()->id,
                'aprendiz' => $request->input('aprendiz'),
                'entrada' => Carbon::now(),
                'ficha_caracterizacion_id' => $request->input('ficha_caracterizacion_id'),
            ]);


            return redirect()->route('entradaSalida.registros', compact(''))->with('success', '¡Registro Exitoso!');
        } catch (QueryException $e) {
            // Manejar excepciones de la base de datos
            @dd($e);
            return redirect()->back()->withErrors(['error' => 'Error de base de datos. Por favor, inténtelo de nuevo.']);
        } catch (\Exception $e) {
            // Manejar otras excepciones
            @dd($e);
            return redirect()->back()->withErrors(['error' => 'Se produjo un error. Por favor, inténtelo de nuevo.']);
        }
    }
    public function updateSalida(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'aprendiz' => 'required|string',
            ]);
            if ($validator->fails()) {
                @dd('holis');
                @dd($validator);
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $entradaSalida = EntradaSalida::whereExists(function ($query) use ($request) {
                $query->where('aprendiz', $request->input('aprendiz'))
                    ->where('salida', null);
            })->first();
            if ($entradaSalida) {

                $entradaSalida->update([
                    'salida' => Carbon::now(),
                ]);
                return redirect()->route('entradaSalida.registros', ['fichaCaracterizacion' => $entradaSalida->ficha_caracterizacion_id])->with('success', 'Salida Exitosa');
            } else {
                return redirect()->back()->withErrors(['error' => 'No ha tomado asistencia a este aprendiz.']);
            }
        } catch (QueryException $e) {
            // Manejar excepciones de la base de datos
            @dd($e);
            return redirect()->back()->withErrors(['error' => 'Error de base de datos. Por favor, inténtelo de nuevo.']);
        } catch (\Exception $e) {
            // Manejar otras excepciones
            @dd($e);
            return redirect()->back()->withErrors(['error' => 'Se produjo un error. Por favor, inténtelo de nuevo.']);
        }
    }
    public function updateEntradaSalida($aprendiz)
    {
        try {
            $entradaSalida = EntradaSalida::where('aprendiz', $aprendiz)
                ->where('salida', null)->first();

            if ($entradaSalida) {

                $entradaSalida->update([
                    'salida' => Carbon::now(),
                ]);
                return redirect()->route('entradaSalida.registros', ['fichaCaracterizacion' => $entradaSalida->ficha_caracterizacion_id])->with('success', 'Salida Exitosa');
            } else {
                return redirect()->back()->withErrors(['error' => 'No ha tomado asistencia a este aprendiz.']);
            }
        } catch (QueryException $e) {
            // Manejar excepciones de la base de datos
            @dd($e);
            return redirect()->back()->withErrors(['error' => 'Error de base de datos. Por favor, inténtelo de nuevo.']);
        } catch (\Exception $e) {
            // Manejar otras excepciones
            @dd($e);
            return redirect()->back()->withErrors(['error' => 'Se produjo un error. Por favor, inténtelo de nuevo.']);
        }
    }
    public function crearCarpetaUser()
    {
        $user_id = Auth::id(); // Obtener el ID del usuario autenticado

        $carpeta_csv = public_path('csv');
        $carpeta_usuario = public_path('csv/' . $user_id);

        if (!file_exists($carpeta_csv)) {
            mkdir($carpeta_csv, 0777, true);
        }

        if (!file_exists($carpeta_usuario)) {
            mkdir($carpeta_usuario, 0777, true);
            // echo "Carpeta del usuario creada correctamente.";
        } else {
            // echo "La carpeta del usuario ya existe.";
        }
    }
    public function generarCSV($ficha)
    {
        try {
            $datos = [
                'instructor_user_id' => Auth::id(),
                'ficha_caracterizacion_id' => $ficha,
                'fecha' => Carbon::now()->toDateString(),
            ];

            $response = $this->exportService->exportarEntradaSalidasCSV($datos);

            $this->entradaSalidaService->marcarComoListadas($datos);

            return $response;
        } catch (\Exception $e) {
            Log::error('Error al generar CSV: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al generar CSV.');
        }
    }
    /**
     * Display the specified resource.
     */
    public  function marcarListado($ficha)
    {
        DB::table('entrada_salidas')
            ->where('instructor_user_id', Auth::user()->id)
            ->where('fecha', Carbon::now()->toDateString())
            ->where('ficha_caracterizacion_id', $ficha)
            ->update(['listado' => 1]);
    }
    public function destroyFichaCaractrizacion()
    {
        FichaCaracterizacion::where('user_id', Auth::user()->id)->delete();
    }
    public function show(EntradaSalida $entradaSalida)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EntradaSalida $entradaSalida)
    {
        return view('entradaSalidas.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EntradaSalida $entradaSalida)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntradaSalida $entradaSalida)
    {
        try {
            $this->entradaSalidaService->eliminar($entradaSalida->id);

            return redirect()->back()->with('success', '¡Registro eliminado exitosamente!');
        } catch (\Exception $e) {
            Log::error('Error al eliminar entrada/salida: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al eliminar registro.');
        }
    }

    public function cargarDatos(Request $request)
    {
        $data = $request->validate([
            'evento' => 'required',
            'ficha_id',
        ]);
        // @dd($request->ficha_caracterizacion_id);
        $ficha_id = $request->ficha_id;
        $evento = $request->evento;
        $ambiente_id = $request->ambiente_id;
        $descripcion = $request->descripcion;
        if ($request->evento == 1) {
            // @dd('se supone que aqui vamos bien' . $request->evento);
                return view('entradaSalidas.create', compact('ficha_id', 'evento', 'ambiente_id', 'descripcion'));
        } else {
            return view('entradaSalidas.edit', compact('ficha_id','evento', 'ambiente_id', 'descripcion'));
        }
    }
}
