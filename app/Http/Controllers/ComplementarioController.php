<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    public function procesarDcoumentos() 
    {
        return view('complementarios.procesamiento_documentos');
    }
}
