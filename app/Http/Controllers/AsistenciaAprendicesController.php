<?php

namespace App\Http\Controllers;

use App\Services\AsistenciaService;
use App\Services\JornadaValidationService;
use App\Models\FichaCaracterizacion;
use App\Models\AsistenciaAprendiz;
use App\Models\JornadaFormacion;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AsistenciaAprendicesController extends Controller
{
    protected AsistenciaService $asistenciaService;
    protected JornadaValidationService $jornadaValidation;

    public function __construct(
        AsistenciaService $asistenciaService,
        JornadaValidationService $jornadaValidation
    ) {
        $this->asistenciaService = $asistenciaService;
        $this->jornadaValidation = $jornadaValidation;
    }

    /**
     * Muestra una lista de fichas de caracterización.
     *
     * Este método recupera todas las fichas de caracterización desde la base de datos
     * y las pasa a la vista 'asistencias.index' para su visualización.
     *
     * @return \Illuminate\View\View La vista 'asistencias.index' con las fichas de caracterización.
     */
    public function index (){
        $fichas = FichaCaracterizacion::select('id', 'ficha')->get();
        return view('asistencias.index', compact('fichas')); 
    }
   
    /**
     * Obtiene las asistencias de los aprendices por ficha.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene el ID de la ficha.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View Una respuesta JSON con un mensaje de error o una vista con las asistencias encontradas.
     *
     * @throws \Exception Si ocurre un error al obtener las asistencias.
     */
    public function getAttendanceByFicha (Request $request){
        try {
            $fichaId = $request->input('ficha');
            
            if (!$fichaId) {
                return response()->json(['message' => 'ID de ficha no proporcionado'], 400);
            }
            
            $asistencias = $this->asistenciaService->obtenerPorFicha($fichaId);

            if ($asistencias->isEmpty()) {
                return response()->json(['message' => 'No se encontraron asistencias para la ficha proporcionada'], 404);
            }
            
            return view('asistencias.asistencia_by_ficha', ['asistencias' => $asistencias]);
        } catch (Exception $e) {
            Log::error('Error obteniendo asistencias por ficha: ' . $e->getMessage());
            return response()->json(['message' => 'Error obteniendo asistencias', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Retrieve attendance records by date range and ficha.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * This method expects the following input parameters:
     * - 'ficha': The ID of the ficha (required).
     * - 'fecha_inicio': The start date of the attendance period (required).
     * - 'fecha_fin': The end date of the attendance period (required).
     *
     * The method performs the following actions:
     * 1. Validates that 'ficha', 'fecha_inicio', and 'fecha_fin' are provided.
     * 2. Queries the AsistenciaAprendiz model to find attendance records that match the provided ficha ID and fall within the specified date range.
     * 3. Returns a JSON response with a 400 status code if any of the required parameters are missing.
     * 4. Returns a JSON response with a 404 status code if no attendance records are found.
     * 5. Returns a view with the attendance records if found.
     */
    public function getAttendanceByDateAndFicha(Request $request){
       $ficha = $request->input('ficha');
       $fechaInicio = $request->input('fecha_inicio');
       $fechaFin = $request->input('fecha_fin');

        if (!$ficha || !$fechaInicio || !$fechaFin) {
            return response()->json(['message' => 'Datos incompletos'], 400);
        }

        try {
            $asistencias = $this->asistenciaService->obtenerPorFichaYFechas($ficha, $fechaInicio, $fechaFin);

            if ($asistencias->isEmpty()) {
                return response()->json(['message' => 'No se encontraron asistencias para la ficha y fechas proporcionadas'], 404);
            }

            return view('asistencias.asistencia_by_date', ['asistencias' => $asistencias]);
        } catch (Exception $e) {
            Log::error('Error obteniendo asistencias por fecha: ' . $e->getMessage());
            return response()->json(['message' => 'Error obteniendo asistencias', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene los documentos asociados a una ficha específica.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene el ID de la ficha.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View Una respuesta JSON con los documentos encontrados o un mensaje de error, 
     *         o una vista con los documentos si se encuentran.
     *
     * @throws \Exception Si ocurre un error al obtener los documentos.
     */
    public function getDocumentsByFicha(Request $request)
    {
        $fichaId = $request->input('ficha'); 
        
        try {
            if (!$fichaId) {
                return response()->json(['message' => 'ID de ficha no proporcionado'], 400);
            }

            $documentos = $this->asistenciaService->obtenerDocumentosPorFicha($fichaId);
            
            if ($documentos->isEmpty()) {
                return response()->json(['message' => 'No se encontraron documentos para la ficha proporcionada'], 404);
            }

            return view('asistencias.consulta_by_document', ['documentos' => $documentos]);
        } catch (Exception $e) {
            Log::error('Error obteniendo documentos por ficha: ' . $e->getMessage());
            return response()->json(['message' => 'Error obteniendo documentos', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene las asistencias de un aprendiz por su número de documento.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene el número de documento.
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View Una respuesta JSON con un mensaje de error o una vista con las asistencias encontradas.
     */
    public function getAttendanceByDocument(Request $request){
        $document = $request->input('documento'); 
       
        if (!$document) {
            return response()->json(['message' => 'Datos incompletos'], 400);
        }

        try {
            $asistencias = $this->asistenciaService->obtenerPorDocumento($document);
           
            if ($asistencias->isEmpty()) {
                return response()->json(['message' => 'No se encontraron asistencias para el documento proporcionado'], 404);
            }

            return view('asistencias.asistencia_by_document', ['asistencias' => $asistencias]);
        } catch (Exception $e) {
            Log::error('Error obteniendo asistencias por documento: ' . $e->getMessage());
            return response()->json(['message' => 'Error obteniendo asistencias', 'error' => $e->getMessage()], 500);
        }
    }

  
    /**
     * Almacena la asistencia de los aprendices.
     *
     * Este método recibe una solicitud HTTP con los datos de asistencia de los aprendices y los guarda en la base de datos.
     * Si se proporciona una lista de asistencias, se guarda cada una de ellas. Si se proporciona una sola asistencia, se guarda individualmente.
     * En caso de datos incompletos, se devuelve una respuesta con un mensaje de error.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos de asistencia.
     * @return \Illuminate\Http\JsonResponse La respuesta JSON con un mensaje de éxito o error.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();

            if (empty($data)) {
                return response()->json(['message' => 'Datos incompletos'], 400);
            }
      
            if (isset($data['attendance'])) {
                // Registro en lote
                $cantidad = $this->asistenciaService->registrarAsistenciaLote(
                    $data['attendance'], 
                    $data['caracterizacion_id']
                );
                
                return response()->json(['message' => "Lista de {$cantidad} asistencias guardada con éxito"], 200);
            } else {
                // Registro individual
                $this->asistenciaService->registrarAsistencia($data);
                
                return response()->json(['message' => 'Asistencia guardada con éxito'], 200);
            }
        } catch (Exception $e) {
            Log::error('Error guardando asistencia: ' . $e->getMessage());
            return response()->json(['message' => 'Error guardando asistencia', 'error' => $e->getMessage()], 500);
        }
    }

    
    /**
     * Actualiza la hora de salida de las asistencias de los aprendices.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos necesarios para la actualización.
     * 
     * @return \Illuminate\Http\JsonResponse La respuesta JSON con un mensaje de éxito o error.
     * 
     * @throws \Illuminate\Validation\ValidationException Si los datos proporcionados son incompletos.
     * 
     * Datos esperados en la solicitud:
     * - caracterizacion_id: ID de la caracterización del aprendiz.
     * - hora_salida: Hora de salida a actualizar.
     * - fecha: Fecha de la asistencia a actualizar.
     * 
     * Respuestas posibles:
     * - 200: Asistencias actualizadas con éxito.
     * - 400: Datos incompletos.
     * - 404: Asistencias no encontradas.
     */
    public function update(Request $request)
    {
        try {
            $data = $request->all();
            
            if (!isset($data['caracterizacion_id']) || !isset($data['hora_salida']) || !isset($data['fecha'])) {
                return response()->json(['message' => 'Datos incompletos'], 400);
            }

            $cantidad = $this->asistenciaService->actualizarHoraSalida(
                $data['caracterizacion_id'], 
                $data['fecha'], 
                $data['hora_salida']
            );

            if ($cantidad === 0) {
                return response()->json(['message' => 'No se encontraron asistencias para actualizar'], 404);
            }

            return response()->json(['message' => "Se actualizaron {$cantidad} asistencias con éxito"], 200);
        } catch (Exception $e) {
            Log::error('Error actualizando asistencias: ' . $e->getMessage());
            return response()->json(['message' => 'Error actualizando asistencias', 'error' => $e->getMessage()], 500);
        }
    }

    
    /**
     * Maneja la solicitud de novedad de asistencia de aprendices.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos de la novedad.
     * 
     * @return \Illuminate\Http\JsonResponse La respuesta JSON con el mensaje correspondiente y el código de estado HTTP.
     * 
     * @throws \Illuminate\Validation\ValidationException Si los datos de la solicitud están incompletos.
     * 
     * Este método verifica si la solicitud contiene los campos necesarios: 'caracterizacion_id', 'numero_identificacion', 'hora_entrada' y 'novedad'.
     * Si alguno de estos campos falta, devuelve una respuesta JSON con un mensaje de error y un código de estado 400.
     * 
     * Luego, intenta encontrar un registro de asistencia que coincida con 'caracterizacion_id', 'numero_identificacion' y 'hora_ingreso'.
     * Si se encuentra un registro, actualiza la 'hora_salida' y 'novedad_salida' con los datos actuales y guarda los cambios.
     * Devuelve una respuesta JSON con un mensaje de éxito y un código de estado 200.
     * 
     * Si no se encuentra un registro de asistencia, devuelve una respuesta JSON con un mensaje de error y un código de estado 404.
     */
    public function assistenceNovedad(Request $request)
    {
        if (!$request->has('caracterizacion_id') || !$request->has('numero_identificacion') || !$request->has('hora_entrada') || !$request->has('novedad')) {
            return response()->json(['message' => 'Datos incompletos'], 400);
        }
   
        $caracterizacion_id = $request->input('caracterizacion_id');
        $numero_identificacion = $request->input('numero_identificacion');
        $hora_ingreso_peticion = $request->input('hora_entrada');
        $novedad_salida = $request->input('novedad');
        
        $hora_ingreso = Carbon::parse($hora_ingreso_peticion)->format('H:i:s');
      
        $asistencia = AsistenciaAprendiz::where('caracterizacion_id', $caracterizacion_id)
            ->where('numero_identificacion', $numero_identificacion)
            ->where('hora_ingreso', $hora_ingreso)
            ->first();

        if (!$asistencia) {
            return response()->json(['message' => 'No se encontró asistencia'], 404);
        }

        $asistencia->hora_salida = Carbon::now();
        $asistencia->novedad_salida = $novedad_salida;
        $asistencia->save();

        return response()->json(['message' => 'Solicitud de respuesta aceptada'], 200);
    }

   
    /**
     * Obtiene la lista de asistencias para una ficha y jornada específicas.
     *
     * @param string $ficha El ID de la ficha.
     * @param string $jornada La jornada de formación.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con las asistencias encontradas o un mensaje de error.
     */
    public function getList(String $ficha, String $jornada)
    {
        // Obtiene la hora y fecha actual
        $horaEjecucion = Carbon::now()->format('H:i:s'); 
        $fechaActual = Carbon::now()->format('Y-m-d');

        // Obtiene la jornada de formación correspondiente
        $obJornada = JornadaFormacion::where('jornada', $jornada)->first();

        Log::info('Jornada: '.json_encode($obJornada));
        
        // Formatea las horas de inicio y fin de la jornada
        $h1Ini = Carbon::parse($obJornada->hora_inicio)->format('H'); 
        $m1Ini = Carbon::parse($obJornada->hora_inicio)->format('i');
        $h2Ini = Carbon::parse($obJornada->hora_fin)->format('H');
        $m2Fin = Carbon::parse($obJornada->hora_fin)->format('i');

        // Obtiene las asistencias para la ficha y jornada especificadas en la fecha actual
        $asistencias = AsistenciaAprendiz::whereHas('caracterizacion', function ($query) use ($ficha, $jornada) {
            $query->whereHas('ficha', function ($query) use ($ficha) {
                $query->where('ficha', $ficha);
            })->whereHas('jornada', function ($query) use ($jornada) {
                $query->where('jornada', $jornada);
            });
        })->whereDate('created_at', $fechaActual)->get();

        // Recorre las asistencias y verifica si la hora de ingreso está dentro del rango de la jornada
        foreach ($asistencias as $asistencia){
            $hourEnter = Carbon::parse($asistencia->hora_ingreso)->format('H:i:s');
            $dateEnter = Carbon::parse($asistencia->created_at)->format('Y-m-d'); 

            if($this->validateHour($horaEjecucion, $jornada, $h1Ini , $m1Ini , $h2Ini , $m2Fin) == true && $dateEnter == $fechaActual){
                return response()->json(['asistencias' => $asistencias], 200);
            }
        }

        // Si no se encontraron asistencias, devuelve un mensaje de error
        return response()->json(['message' => 'No se encontraron asistencias para la ficha y jornada proporcionadas'], 404);
    }

    /**
     * REFACTORIZADO: Los métodos morning, afternoon, night fueron eliminados.
     * Ahora se usa JornadaValidationService->validarHorarioJornada()
     * Configuración en config/jornadas.php
     */


    /***********Metodos para actulizar novedades de estrada y salida**************/ 

    /**
     * Actualiza la hora de salida y la novedad de salida de la asistencia de un aprendiz.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos necesarios para actualizar la asistencia.
     * 
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con un mensaje indicando el resultado de la operación.
     * 
     * Este método realiza las siguientes acciones:
     * - Obtiene la fecha y hora actual.
     * - Extrae los datos de la solicitud, incluyendo el número de identificación del aprendiz y la hora de ingreso.
     * - Busca la asistencia del aprendiz en la base de datos utilizando el número de identificación y la hora de ingreso.
     * - Verifica si la asistencia corresponde a la fecha actual y si la hora de ingreso corresponde a los turnos de mañana, tarde o noche.
     * - Si se cumplen las condiciones, actualiza la novedad de salida y la hora de salida de la asistencia y guarda los cambios.
     * - Si no se encuentra la asistencia, devuelve una respuesta JSON con un mensaje de error.
     * 
     * @throws \Exception Si ocurre un error al procesar la solicitud.
     */
    public function updateExitAsistence(Request $request){
        try {
            $data = $request->all();

            if (!isset($data['numero_identificacion']) || !isset($data['hora_ingreso']) || !isset($data['novedad_salida'])) {
                return response()->json(['message' => 'Datos incompletos'], 400);
            }

            // Obtener jornada a partir de la hora de ingreso
            $jornada = $this->jornadaValidation->obtenerJornadaPorHora($data['hora_ingreso']);

            if (!$jornada) {
                return response()->json(['message' => 'No se pudo determinar la jornada'], 400);
            }

            $actualizado = $this->asistenciaService->actualizarNovedadSalida(
                $data['caracterizacion_id'] ?? 0,
                $data['numero_identificacion'],
                $data['hora_ingreso'],
                $data['novedad_salida'],
                $jornada
            );

            if ($actualizado) {
                return response()->json(['message' => 'Novedad de salida actualizada'], 200);
            }

            return response()->json(['message' => 'No se pudo actualizar la novedad'], 400);
        } catch (Exception $e) {
            Log::error('Error actualizando novedad de salida: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Actualiza la novedad de entrada de la asistencia de un aprendiz.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP que contiene los datos de la asistencia.
     * @return \Illuminate\Http\JsonResponse Una respuesta JSON con un mensaje de éxito o error.
     *
     * Este método realiza las siguientes acciones:
     * - Obtiene la fecha y hora actual.
     * - Extrae los datos de la solicitud.
     * - Registra en el log los datos recibidos.
     * - Busca la asistencia del aprendiz por número de identificación y hora de ingreso.
     * - Registra en el log la asistencia encontrada.
     * - Verifica si la hora de ingreso corresponde a la mañana, tarde o noche y si la fecha de la asistencia es la actual.
     * - Si se cumplen las condiciones, actualiza la novedad de entrada y la hora de ingreso (si es en la mañana).
     * - Guarda los cambios en la base de datos.
     * - Devuelve una respuesta JSON con un mensaje de éxito.
     * - Si no se encuentra la asistencia, devuelve una respuesta JSON con un mensaje de error.
     *
     * @throws \Exception Si ocurre un error al procesar la solicitud.
     */
    public function updateEntraceAsistence (Request $request){
        try {
            $data = $request->all();

            if (!isset($data['numero_identificacion']) || !isset($data['hora_ingreso']) || !isset($data['novedad_entrada'])) {
                return response()->json(['message' => 'Datos incompletos'], 400);
            }

            // Obtener jornada a partir de la hora de ingreso
            $jornada = $this->jornadaValidation->obtenerJornadaPorHora($data['hora_ingreso']);

            if (!$jornada) {
                return response()->json(['message' => 'No se pudo determinar la jornada'], 400);
            }

            $actualizado = $this->asistenciaService->actualizarNovedadEntrada(
                $data['caracterizacion_id'] ?? 0,
                $data['numero_identificacion'],
                $data['hora_ingreso'],
                $data['novedad_entrada'],
                $jornada
            );

            if ($actualizado) {
                return response()->json(['message' => 'Novedad de entrada actualizada'], 200);
            }

            return response()->json(['message' => 'No se pudo actualizar la novedad'], 400);
        } catch (Exception $e) {
            Log::error('Error actualizando novedad de entrada: ' . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * REFACTORIZADO: Los métodos morningAsistence, affternoonAsistence, nightAsistence fueron eliminados.
     * Ahora se usa JornadaValidationService->validarAsistenciaEnJornada()
     * Configuración en config/jornadas.php
     */
}
