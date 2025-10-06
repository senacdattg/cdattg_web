<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Municipio;

class ComplementarioController extends Controller
{
    /**
     * Display the gestion aspirantes view.
     *
     * @return \Illuminate\View\View
     */
    public function gestionAspirantes()
    {
        return view('complementarios.gestion_aspirantes');
    }

    /**
     * Display the procesamiento documentos view.
     *
     * @return \Illuminate\View\View
     */
    public function procesarDocumentos() 
    {
        return view('complementarios.procesamiento_documentos');
    }

    /**
     * Display the gestion programas complementarios view.
     *
     * @return \Illuminate\View\View
     */
    public function gestionProgramasComplementarios()
    {
        return view('complementarios.gestion_programas_complementarios');
    }
    public function estadisticas()
    {
        $departamentos = Departamento::select('id', 'departamento')->get();
        $municipios = Municipio::select('id', 'municipio')->get();

        return view('complementarios.estadisticas', compact('departamentos', 'municipios'));
    }

    public function getMunicipiosByDepartamento($departamento_id)
    {
        $municipios = Municipio::where('departamento_id', $departamento_id)
            ->select('id', 'municipio')
            ->get();

        return response()->json($municipios);
    }
    public function verAspirantes($curso)
    {

        return view('complementarios.ver_aspirantes', compact('curso'));
    }
    public function verPrograma($programa)
    {
        $programas = [
            'auxiliar-cocina' => [
                'nombre' => 'Auxiliar de Cocina',
                'descripcion' => 'Fundamentos de cocina, manipulación de alimentos y técnicas básicas de preparación.',
                'duracion' => '40 horas',
                'icono' => 'fas fa-utensils'
            ],
            'Acabados-en-Madera' => [
                'nombre' => 'Acabados en Madera',
                'descripcion' => 'Técnicas de acabado, barnizado y restauración de muebles de madera.',
                'duracion' => '60 horas',
                'icono' => 'fas fa-hammer'
            ],
            'Confección-de-Prendas' => [
                'nombre' => 'Confección de Prendas',
                'descripcion' => 'Técnicas básicas de corte, confección y terminado de prendas de vestir.',
                'duracion' => '50 horas',
                'icono' => 'fas fa-cut'
            ],
            'Mecánica-Básica-Automotriz' => [
                'nombre' => 'Mecánica Básica Automotriz',
                'descripcion' => 'Mantenimiento preventivo y diagnóstico básico de vehículos.',
                'duracion' => '90 horas',
                'icono' => 'fas fa-car'
            ],
            'Cultivos-de-Huertas-Urbanas' => [
                'nombre' => 'Cultivos de Huertas Urbanas',
                'descripcion' => 'Técnicas de cultivo y mantenimiento de huertas en espacios urbanos.',
                'duracion' => '120 horas',
                'icono' => 'fas fa-spa'
            ],
            'Normatividad-Laboral' => [
                'nombre' => 'Normatividad Laboral',
                'descripcion' => 'Actualización en normatividad laboral y seguridad social.',
                'duracion' => '60 horas',
                'icono' => 'fas fa-gavel'
            ]
        ];

        $programaData = $programas[$programa] ?? null;

        if (!$programaData) {
            abort(404);
        }

        return view('complementarios.ver_programa_publico', compact('programaData'));
    }

    public function programasPublicos()
    {
        return view('complementarios.programas_publicos');
    }
}
