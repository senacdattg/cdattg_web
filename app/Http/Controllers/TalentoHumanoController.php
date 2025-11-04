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
                'message' => 'La persona no está registrada en la base de datos.',
                'data' => null
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
            'data' => $userData
        ]);
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