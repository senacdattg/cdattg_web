<?php

namespace App\Http\Controllers;

use App\Models\AsistenciaAprendiz;
use App\Models\FichaCaracterizacion;
use App\Models\JornadaFormacion;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Exists;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use PhpParser\Node\Expr\Cast\String_;
use PHPUnit\Framework\Constraint\Count;

class AsistenciaAprendicesController extends Controller
{

    /**
     * Muestra una lista de fichas de caracterización.
     *
     * Este método recupera todas las fichas de caracterización desde la base de datos
     * y las pasa a la vista 'asistencias.index' para su visualización.
     *
     * @return \Illuminate\View\View La vista 'asistencias.index' con las fichas de caracterización.
     */
    public function index (){
        // $fichas = FichaCaracterizacion::select('id', 'ficha')->get();
        $fichas = FichaCaracterizacion::all();
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

            $asistencias = AsistenciaAprendiz::whereHas('caracterizacion', function ($query) use ($fichaId) {
                $query->where('ficha_id', $fichaId);
            })->orderBy('id', 'desc')->get();

            if ($asistencias->isEmpty()) {
                return response()->json(['message' => 'No se encontraron asistencias para la ficha proporcionada'], 404);
            }
            return view('asistencias.asistencia_by_ficha', ['asistencias' => $asistencias]);
        } catch (Exception $e) {
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
       $fecha_inicio = $request->input('fecha_inicio');
       $fecha_fin = $request->input('fecha_fin');

         if (!$ficha || !$fecha_inicio || !$fecha_fin) {
              return response()->json(['message' => 'Datos incompletos'], 400);
         }
            $asistencias = AsistenciaAprendiz::whereHas('caracterizacion', function ($query) use ($ficha) {
                $query->where('ficha_id', $ficha);
            })->whereBetween('created_at', [$fecha_inicio, $fecha_fin])->orderBy('created_at', 'desc')
            ->get();
        if ($asistencias->isEmpty()) {
            return response()->json(['message' => 'No se encontraron asistencias para la ficha y fechas proporcionadas'], 404);
        }

        return view('asistencias.asistencia_by_date', ['asistencias' => $asistencias]);
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
        $ficha_id = $request->input('ficha');

        try {
            if (!$ficha_id) {
                return response()->json(['message' => 'ID de ficha no proporcionado'], 400);
            }

            $documentos = AsistenciaAprendiz::select('numero_identificacion')
            ->whereHas('caracterizacion', function ($query) use ($ficha_id) {
                $query->where('ficha_id', $ficha_id);
            })
            ->get();

            if ($documentos->isEmpty()) {
                return response()->json(['message' => 'No se encontraron documentos para la ficha proporcionada'], 404);
            }

            return view('asistencias.consulta_by_document', ['documentos' => $documentos]);


            return response()->json(['documentos' => $documentos], 200);
        } catch (Exception $e) {
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

            if ( !$document) {
                return response()->json(['message' => 'Datos incompletos'], 400);
            }

            $asistencias = AsistenciaAprendiz::where('numero_identificacion', $document)->get();

            if ($asistencias->isEmpty()) {
                return response()->json(['message' => 'No se encontraron asistencias para la ficha y documento proporcionados'], 404);
            }

            return view('asistencias.asistencia_by_document', ['asistencias' => $asistencias]);
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
            $data = $request->all();

            if (isset($data['attendance']) ) {

                foreach ($data['attendance'] as $attendance) {
                    $horaIngreso = Carbon::parse($attendance['hora_ingreso'])->format('Y-m-d H:i:s');

                    AsistenciaAprendiz::create([
                        'caracterizacion_id' => $data['caracterizacion_id'],
                        'nombres' => $attendance['nombres'],
                        'apellidos' => $attendance['apellidos'],
                        'numero_identificacion' => $attendance['numero_identificacion'],
                        'hora_ingreso' => $horaIngreso,
                    ]);
                }
                return response()->json(['message' => 'Lista de asistencia guardada con éxito'], 200);
            }else {
                if($data != null){
                    Log::info('Data: '.json_encode($data));
                    $horaIngreso = Carbon::parse($data['hora_ingreso'])->format('Y-m-d H:i:s');
                    AsistenciaAprendiz::create([
                        'caracterizacion_id' => $data['caracterizacion_id'],
                        'nombres' => $data['nombres'],
                        'apellidos' => $data['apellidos'],
                        'numero_identificacion' => $data['numero_identificacion'],
                        'hora_ingreso' => $horaIngreso,
                    ]);
                    return response()->json(['message' => 'Asistencia guardada con éxito'], 200);
                }else {
                    return response()->json(['message' => 'Datos incompletos'], 400);
                }
            }
            return response()->json(['message' => 'Error saving attendance'], 500);
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
            $data = $request->all();
            if (!isset($data['caracterizacion_id']) || !isset($data['hora_salida']) || !isset($data['fecha'])) {
                return response()->json(['message' => 'Datos incompletos'], 400);
            }
            $horaSalida = Carbon::parse($data['hora_salida'])->format('Y-m-d H:i:s');
            $asistencias = AsistenciaAprendiz::where('caracterizacion_id', $data['caracterizacion_id'])
                ->whereDate('created_at', $data['fecha'])
                ->select('id', 'hora_salida')
                ->get();
            if (!$asistencias) {
                return response()->json(['message' => 'Asistencias no encontradas'], 404);
            }

            foreach ($asistencias as $asistencia) {

                if($asistencia->hora_salida == null){
                    $asistencia->hora_salida = $horaSalida;
                }
                $asistencia->save();
            }

            return response()->json(['message' => 'Asistencias actualizadas con éxito'], 200);
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

        if($asistencia){
            $asistencia->hora_salida = Carbon::now();
            $asistencia->novedad_salida = $novedad_salida;
            $asistencia->save();

            return response()->json(['message' => 'Solicitud de respuesta acepta'], 200);
        }

        if (!$asistencia) {
            return response()->json(['message' => 'No se encontró asistencia'], 404);
        }

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

    public function validateHour($ingreso, $jornada, $hora1, $min1, $hora2, $min2)
    {
        $horaInicio = Carbon::createFromTime($hora1, $min1 , 0);
        $horaFin = Carbon::createFromTime($hora2, $min2, 0);


        $horaIngreso = Carbon::parse($ingreso);

        if ($horaIngreso->between($horaInicio, $horaFin)) {
            return true;
        }

         // if ($horaIngreso->between($horaInicio, $horaFin) && $jornada === $morning ) {
        //     return true;
        // }


        return false;
    }


    /**
     * Verifica si la hora de ingreso está dentro del rango de la mañana y si la jornada es "Mañana".
     *
     * @param string $ingreso La hora de ingreso en formato de cadena.
     * @param string $jornada La jornada del aprendiz.
     * @return bool Retorna true si la hora de ingreso está entre las 06:00 y las 13:10 y la jornada es "Mañana", de lo contrario retorna false.
     */
    public function morning($ingreso, $jornada)
    {
        $horaInicio = Carbon::createFromTime(06, 00, 0);
        $horaFin = Carbon::createFromTime(13, 10, 0);
        $morning = 'Mañana';

        $horaIngreso = Carbon::parse($ingreso);

        if ($horaIngreso->between($horaInicio, $horaFin) && $jornada === $morning ) {
            return true;
        }


        return false;
    }

    /**
     * Verifica si la hora de ingreso está dentro del rango de la tarde.
     *
     * @param string $ingreso La hora de ingreso en formato de cadena.
     * @param string $jornada La jornada a verificar, debe ser 'Tarde'.
     * @return bool Retorna true si la hora de ingreso está entre las 13:00 y las 18:10 y la jornada es 'Tarde', de lo contrario retorna false.
     */
    public function afternoon ($ingreso, $jornada){
        $horaInicio = Carbon::createFromTime(13, 00, 0);
        $horaFin = Carbon::createFromTime(18, 10, 0);
        $morning = 'Tarde';

        $horaIngreso = Carbon::parse($ingreso);

        if ($horaIngreso->between($horaInicio, $horaFin) && $morning === $jornada) {
            return true;
        }

        return false;
    }

    /**
     * Verifica si la hora de ingreso está dentro del rango de la noche y si la jornada es nocturna.
     *
     * @param string $ingreso La hora de ingreso en formato de cadena.
     * @param string $jornada La jornada del aprendiz.
     * @return bool Retorna true si la hora de ingreso está entre las 17:50 y las 23:10 y la jornada es nocturna, de lo contrario retorna false.
     */
    public function night($ingreso, $jornada)
    {
        $horaInicio = Carbon::createFromTime(17, 50, 0);
        $horaFin = Carbon::createFromTime(23, 10, 0);
        $night = 'Noche';

        $horaIngreso = Carbon::parse($ingreso);

        if ($horaIngreso->between($horaInicio, $horaFin) && $jornada === $night) {
            return true;
        }

        return false;
    }


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

        $actualDate = Carbon::now()->format('Y-m-d');
        $actualHour = Carbon::now()->format('H:i:s');

        $data = $request->all();

        $numeroIdentificacion = $data['numero_identificacion'];
        $horaIngreso = Carbon::parse($data['hora_ingreso'])->format('H:i:s');

        Log::info('Hora de ingreso: ' . $horaIngreso);

        $asistencia = AsistenciaAprendiz::where('numero_identificacion', $numeroIdentificacion)
            ->where('hora_ingreso', $horaIngreso)
            ->first();

        Log::info('Asistencia: ' . $asistencia);

        $dateAsistence = $asistencia->created_at->format('Y-m-d');
        $actualHourCarbon = Carbon::parse($actualHour);

        if($this->morningAsistence($horaIngreso, $actualHourCarbon) == true && $dateAsistence == $actualDate){
            $asistencia->novedad_salida = $data['novedad_salida'];
            $asistencia->hora_salida = $actualHour;
            $asistencia->save();
            return response()->json(['message' => 'Novedad de salidad Actualizada'], 200);
        }

        if($this->affternoonAsistence($horaIngreso, $actualHourCarbon) == true && $dateAsistence == $actualDate){
            $asistencia->novedad_salida = $data['novedad_salida'];
            $asistencia->hora_salida = $actualHour;
            $asistencia->save();
            return response()->json(['message' => 'Novedad de salidad Actualizada'], 200);
        }

        if($this->nightAsistence($horaIngreso, $actualHourCarbon) == true && $dateAsistence == $actualDate){
            $asistencia->novedad_salida = $data['novedad_salida'];
            $asistencia->hora_salida = $actualHour;
            $asistencia->save();
            return response()->json(['message' => 'Novedad de salidad Actualizada'], 200);
        }



        if (!$asistencia) {
            return response()->json(['message' => 'Asistencia no encontrada'], 404);
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
        $actualDate = Carbon::now()->format('Y-m-d');
        $actualHour = Carbon::now()->format('H:i:s');

        $data = $request->all();

        log::info('data: '.json_encode($data));

        $numeroIdentificacion = $data['numero_identificacion'];
        $horaIngreso = Carbon::parse($data['hora_ingreso'])->format('H:i:s');

        Log::info('Hora de ingreso: ' . $horaIngreso);

        $asistencia = AsistenciaAprendiz::where('numero_identificacion', $numeroIdentificacion)
            ->where('hora_ingreso', $horaIngreso)
            ->first();

        Log::info('Asistencia: ' . $asistencia);

        $dateAsistence = $asistencia->created_at->format('Y-m-d');
        $actualHourCarbon = Carbon::parse($actualHour);

        if($this->morningAsistence($horaIngreso, $actualHourCarbon) == true && $dateAsistence == $actualDate){
            $asistencia->novedad_entrada = $data['novedad_entrada'];
            $asistencia->hora_ingreso = carbon::now()->format('H:i:s');
            $asistencia->save();
            return response()->json(['message' => 'Novedad de entrada Actualizada'], 200);
        }

        if($this->affternoonAsistence($horaIngreso, $actualHourCarbon) == true && $dateAsistence == $actualDate){
            $asistencia->novedad_entrada = $data['novedad_entrada'];
            $asistencia->save();
            return response()->json(['message' => 'Novedad de entrada Actualizada'], 200);
        }

        if($this->nightAsistence($horaIngreso, $actualHourCarbon) == true && $dateAsistence == $actualDate){
            $asistencia->novedad_entrada = $data['novedad_entrada'];
            $asistencia->save();
            return response()->json(['message' => 'Novedad de entrada Actualizada'], 200);
        }

        if (!$asistencia) {
            return response()->json(['message' => 'Asistencia no encontrada'], 404);
        }
    }

    /**
     * Verifica si la hora de entrada dada y la hora actual caen dentro del período de asistencia matutina.
     *
     * Este método verifica si la hora de entrada proporcionada ($horaIngreso) y la hora actual ($actualHour)
     * están dentro del período de asistencia matutina definido, que comienza a las 06:00 AM y termina a la 01:10 PM.
     *
     * @param string $horaIngreso La hora de entrada a verificar, en un formato que pueda ser interpretado por Carbon.
     * @param \Carbon\Carbon $actualHour La hora actual a verificar.
     * @return bool Devuelve true si ambas horas están dentro del período de asistencia matutina, de lo contrario false.
     */

    private function morningAsistence($horaIngreso, $actualHour){
        $horaInicio = Carbon::createFromTime(06, 00, 0);
        $horaFin = Carbon::createFromTime(13, 10, 0);

        $horaIngreso = Carbon::parse($horaIngreso);

        if ($horaIngreso->between($horaInicio, $horaFin) && $actualHour->between($horaInicio, $horaFin)) {
            return true;
        }

        return false;
    }

    /**
     * Verifica si la hora de ingreso y la hora actual están dentro del rango de asistencia de la tarde.
     *
     * @param string $horaIngreso La hora de ingreso en formato de cadena.
     * @param \Carbon\Carbon $actualHour La hora actual como una instancia de Carbon.
     * @return bool Retorna true si ambas horas están dentro del rango de 13:00 a 18:10, de lo contrario, retorna false.
     */
    private function affternoonAsistence($horaIngreso, $actualHour){
        $horaInicio = Carbon::createFromTime(13, 00, 0);
        $horaFin = Carbon::createFromTime(18, 10, 0);

        $horaIngreso = Carbon::parse($horaIngreso);

        if ($horaIngreso->between($horaInicio, $horaFin) && $actualHour->between($horaInicio, $horaFin)) {
            return true;
        }

        return false;

    }

    /**
     * Verifica si la hora de ingreso y la hora actual están dentro del rango de asistencia nocturna.
     *
     * @param string $horaIngreso La hora de ingreso en formato de cadena.
     * @param \Carbon\Carbon $actualHour La hora actual como instancia de Carbon.
     * @return bool Retorna true si ambas horas están dentro del rango de asistencia nocturna, de lo contrario retorna false.
     */
    private function nightAsistence($horaIngreso, $actualHour){
        $horaInicio = Carbon::createFromTime(17, 50, 0);
        $horaFin = Carbon::createFromTime(23, 10, 0);
        $horaIngreso = Carbon::parse($horaIngreso);

        if ($horaIngreso->between($horaInicio, $horaFin) && $actualHour->between($horaInicio, $horaFin)) {
            return true;
        }

        return false;
    }


}
