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
    public function verAspirantes($curso)
    {

        return view('complementarios.ver_aspirantes', compact('curso'));
    }
    public function verPrograma($programa)
    { 
        return view('complementarios.ver_programa_complementario', compact('programa'));
    }
}
