<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use App\Models\Parametro;
use App\Models\Pais;
use App\Models\Departamento;
use App\Models\Municipio;
use App\Models\CategoriaCaracterizacionComplementario;

class TalentoHumanoController extends Controller
{
    public function index()
    {
        // Obtener datos necesarios para el formulario
        $tiposDocumento = $this->getTiposDocumento();
        $generos = $this->getGeneros();
        $paises = Pais::all();
        $departamentos = Departamento::all();
        $categoriasConHijos = CategoriaCaracterizacionComplementario::with('children')->whereNull('parent_id')->get();

        return view('talento-humano.index', compact(
            'tiposDocumento',
            'generos',
            'paises',
            'departamentos',
            'categoriasConHijos'
        ));
    }

    public function consultar(Request $request)
    {
        // Verificar si es una consulta o creación
        if ($request->has('action_type') && $request->action_type === 'crear') {
            return $this->crearPersona($request);
        }

        $request->validate([
            'cedula' => 'required|string|max:20'
        ]);

        $cedula = trim($request->cedula);

        // Buscar persona por número de documento
        $persona = Persona::with([
            'tipoDocumento',
            'tipoGenero',
            'pais',
            'departamento',
            'municipio',
            'caracterizacion'
        ])->where('numero_documento', $cedula)->first();

        if (!$persona) {
            return response()->json([
                'success' => false,
                'message' => 'La persona no está registrada en la base de datos. Complete el formulario para crearla.',
                'data' => null,
                'show_form' => true
            ]);
        }

        // Preparar datos para el formulario
        $userData = [
            'tipo_documento' => $persona->tipo_documento,
            'numero_documento' => $persona->numero_documento,
            'primer_nombre' => $persona->primer_nombre,
            'segundo_nombre' => $persona->segundo_nombre,
            'primer_apellido' => $persona->primer_apellido,
            'segundo_apellido' => $persona->segundo_apellido,
            'fecha_nacimiento' => $persona->fecha_nacimiento,
            'genero' => $persona->genero,
            'telefono' => $persona->telefono,
            'celular' => $persona->celular,
            'email' => $persona->email,
            'pais_id' => $persona->pais_id,
            'departamento_id' => $persona->departamento_id,
            'municipio_id' => $persona->municipio_id,
            'direccion' => $persona->direccion,
            'caracterizacion_id' => $persona->caracterizacion_id,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Persona encontrada.',
            'data' => $userData,
            'show_form' => false
        ]);
    }

    public function crearPersona(Request $request)
    {
        try {
            // Log para debugging
            \Log::info('Creando persona - Request data:', $request->all());

            // Cambiar el action_type para identificar que es creación
            $request->merge(['action_type' => 'crear']);

            // Validar los datos del formulario
            $request->validate([
                'tipo_documento' => 'required|integer',
                'numero_documento' => 'required|string|max:191|unique:personas',
                'primer_nombre' => 'required|string|max:191',
                'segundo_nombre' => 'nullable|string|max:191',
                'primer_apellido' => 'required|string|max:191',
                'segundo_apellido' => 'nullable|string|max:191',
                'fecha_nacimiento' => 'required|date',
                'genero' => 'required|integer',
                'telefono' => 'nullable|string|max:191',
                'celular' => 'required|string|max:191',
                'email' => 'required|email|max:191|unique:personas',
                'pais_id' => 'required|exists:pais,id',
                'departamento_id' => 'required|exists:departamentos,id',
                'municipio_id' => 'required|exists:municipios,id',
                'direccion' => 'required|string|max:191',
                'observaciones' => 'nullable|string',
                'caracterizacion_id' => 'nullable|exists:categorias_caracterizacion_complementarios,id',
            ]);

            // Verificar si ya existe una persona con el mismo documento o email
            $personaExistente = Persona::where('numero_documento', $request->numero_documento)
                ->orWhere('email', $request->email)
                ->first();

            if ($personaExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una persona registrada con este número de documento o correo electrónico.'
                ]);
            }

            // Crear nueva persona
            $persona = Persona::create($request->only([
                'tipo_documento', 'numero_documento', 'primer_nombre', 'segundo_nombre',
                'primer_apellido', 'segundo_apellido', 'fecha_nacimiento', 'genero',
                'telefono', 'celular', 'email', 'pais_id', 'departamento_id',
                'municipio_id', 'direccion', 'caracterizacion_id'
            ]) + [
                'user_create_id' => auth()->id() ?? 1,
                'user_edit_id' => auth()->id() ?? 1,
                'status' => 1
            ]);

            \Log::info('Persona creada exitosamente:', $persona->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Persona creada exitosamente.',
                'data' => [
                    'tipo_documento' => $persona->tipo_documento,
                    'numero_documento' => $persona->numero_documento,
                    'primer_nombre' => $persona->primer_nombre,
                    'segundo_nombre' => $persona->segundo_nombre,
                    'primer_apellido' => $persona->primer_apellido,
                    'segundo_apellido' => $persona->segundo_apellido,
                    'fecha_nacimiento' => $persona->fecha_nacimiento,
                    'genero' => $persona->genero,
                    'telefono' => $persona->telefono,
                    'celular' => $persona->celular,
                    'email' => $persona->email,
                    'pais_id' => $persona->pais_id,
                    'departamento_id' => $persona->departamento_id,
                    'municipio_id' => $persona->municipio_id,
                    'direccion' => $persona->direccion,
                    'caracterizacion_id' => $persona->caracterizacion_id,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creando persona:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método auxiliar para obtener tipos de documento dinámicamente desde el tema-parametro
     */
    private function getTiposDocumento()
    {
        // Buscar el tema "TIPO DE DOCUMENTO"
        $temaTipoDocumento = \App\Models\Tema::where('name', 'TIPO DE DOCUMENTO')->first();

        if (!$temaTipoDocumento) {
            // Fallback: devolver valores hardcodeados si no se encuentra el tema
            return collect([
                ['id' => 3, 'name' => 'CEDULA DE CIUDADANIA'],
                ['id' => 4, 'name' => 'CEDULA DE EXTRANJERIA'],
                ['id' => 5, 'name' => 'PASAPORTE'],
                ['id' => 6, 'name' => 'TARJETA DE IDENTIDAD'],
                ['id' => 7, 'name' => 'REGISTRO CIVIL'],
                ['id' => 8, 'name' => 'SIN IDENTIFICACION'],
            ]);
        }

        // Obtener parámetros activos del tema
        return $temaTipoDocumento->parametros()
            ->where('parametros_temas.status', 1)
            ->orderBy('parametros.name')
            ->get(['parametros.id', 'parametros.name']);
    }

    /**
     * Método auxiliar para obtener géneros dinámicamente desde el tema-parametro
     */
    private function getGeneros()
    {
        // Buscar el tema "GENERO"
        $temaGenero = \App\Models\Tema::where('name', 'GENERO')->first();

        if (!$temaGenero) {
            // Fallback: devolver valores hardcodeados si no se encuentra el tema
            return collect([
                ['id' => 9, 'name' => 'MASCULINO'],
                ['id' => 10, 'name' => 'FEMENINO'],
                ['id' => 11, 'name' => 'NO DEFINE'],
            ]);
        }

        // Obtener parámetros activos del tema
        return $temaGenero->parametros()
            ->where('parametros_temas.status', 1)
            ->orderBy('parametros.name')
            ->get(['parametros.id', 'parametros.name']);
    }
}