<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use App\Models\Departamento;
use App\Repositories\TemaRepository;
use Illuminate\View\View;

/**
 * Controlador para el módulo de Talento Humano
 *
 * Este controlador solo muestra la vista de consulta de talento humano.
 * Toda la lógica de negocio (consulta y creación de personas) se maneja
 * a través de PersonaController usando las mismas rutas y servicios.
 */
class TalentoHumanoController extends Controller
{
    /**
     * @var TemaRepository
     */
    protected TemaRepository $temaRepository;

    /**
     * Constructor del controlador
     */
    public function __construct(TemaRepository $temaRepository)
    {
        $this->middleware('auth');
        $this->temaRepository = $temaRepository;
    }

    /**
     * Muestra el formulario principal de talento humano
     *
     * Esta vista permite consultar y crear personas usando los mismos
     * endpoints que el módulo de Personas (PersonaController).
     *
     * @return View
     */
    public function index(): View
    {
        // Obtener datos para los formularios (igual que PersonaController::create)
        $documentos = $this->temaRepository->obtenerTiposDocumento();
        $generos = $this->temaRepository->obtenerGeneros();
        $paises = Pais::where('status', 1)->orderBy('pais')->get();
        $departamentos = Departamento::where('status', 1)->orderBy('departamento')->get();
        $municipios = collect([]); // Se cargan dinámicamente con JavaScript
        $caracterizaciones = $this->temaRepository->obtenerCaracterizacionesComplementarias();
        $vias = $this->temaRepository->obtenerVias();
        $letras = $this->temaRepository->obtenerLetras();
        $cardinales = $this->temaRepository->obtenerCardinales();

        return view(
            'talento-humano.index',
            compact(
                'documentos',
                'generos',
                'paises',
                'departamentos',
                'municipios',
                'caracterizaciones',
                'vias',
                'letras',
                'cardinales'
            )
        );
    }
}
