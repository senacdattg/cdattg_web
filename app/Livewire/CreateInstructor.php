<?php

namespace App\Livewire;

use App\Models\CentroFormacion;
use App\Models\JornadaFormacion;
use App\Models\ParametroTema;
use App\Models\Persona;
use App\Models\RedConocimiento;
use App\Models\Regional;
use App\Services\InstructorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CreateInstructor extends Component
{
    // Selección de persona
    public $persona_id = null;
    
    // Información laboral
    public $regional_id = null;
    public $centro_formacion_id = null;
    public $tipo_vinculacion_id = null;
    public $jornadas = [];
    public $fecha_ingreso_sena = null;
    public $anos_experiencia = null;
    public $experiencia_instructor_meses = null;
    public $experiencia_laboral = null;
    
    // Formación académica
    public $nivel_academico_id = null;
    public $formacion_pedagogia = null;
    public $titulos_obtenidos = [''];
    public $instituciones_educativas = [''];
    public $certificaciones_tecnicas = [''];
    public $cursos_complementarios = [''];
    
    // Competencias y habilidades
    public $areas_experticia = null;
    public $competencias_tic = null;
    public $idiomas = [['idioma' => '', 'nivel' => '']];
    public $habilidades_pedagogicas = [];
    public $especialidades = [];
    
    // Información administrativa
    public $numero_contrato = null;
    public $fecha_inicio_contrato = null;
    public $fecha_fin_contrato = null;
    public $supervisor_contrato = null;
    public $eps = null;
    public $arl = null;
    
    // Datos para selects
    public $centrosFormacion = [];
    
    protected InstructorService $instructorService;
    
    public function boot(InstructorService $instructorService): void
    {
        $this->instructorService = $instructorService;
    }
    
    public function mount(): void
    {
        // Inicializar con valores por defecto si hay old input
        if (old('titulos_obtenidos')) {
            $this->titulos_obtenidos = old('titulos_obtenidos');
        }
        if (old('instituciones_educativas')) {
            $this->instituciones_educativas = old('instituciones_educativas');
        }
        if (old('certificaciones_tecnicas')) {
            $this->certificaciones_tecnicas = old('certificaciones_tecnicas');
        }
        if (old('cursos_complementarios')) {
            $this->cursos_complementarios = old('cursos_complementarios');
        }
        if (old('idiomas')) {
            $this->idiomas = old('idiomas');
        }
        if (old('regional_id')) {
            $this->regional_id = old('regional_id');
            $this->updatedRegionalId();
        }
    }
    
    /**
     * Hook que se ejecuta cuando cambia la regional
     */
    public function updatedRegionalId(): void
    {
        $this->centro_formacion_id = null;
        
        if ($this->regional_id) {
            $this->centrosFormacion = CentroFormacion::where('regional_id', $this->regional_id)
                ->where('status', true)
                ->orderBy('nombre')
                ->get(['id', 'nombre'])
                ->toArray();
        } else {
            $this->centrosFormacion = [];
        }
    }
    
    /**
     * Agregar campo dinámico
     */
    public function agregarTitulo(): void
    {
        $this->titulos_obtenidos[] = '';
    }
    
    public function eliminarTitulo(int $index): void
    {
        unset($this->titulos_obtenidos[$index]);
        $this->titulos_obtenidos = array_values($this->titulos_obtenidos);
    }
    
    public function agregarInstitucion(): void
    {
        $this->instituciones_educativas[] = '';
    }
    
    public function eliminarInstitucion(int $index): void
    {
        unset($this->instituciones_educativas[$index]);
        $this->instituciones_educativas = array_values($this->instituciones_educativas);
    }
    
    public function agregarCertificacion(): void
    {
        $this->certificaciones_tecnicas[] = '';
    }
    
    public function eliminarCertificacion(int $index): void
    {
        unset($this->certificaciones_tecnicas[$index]);
        $this->certificaciones_tecnicas = array_values($this->certificaciones_tecnicas);
    }
    
    public function agregarCurso(): void
    {
        $this->cursos_complementarios[] = '';
    }
    
    public function eliminarCurso(int $index): void
    {
        unset($this->cursos_complementarios[$index]);
        $this->cursos_complementarios = array_values($this->cursos_complementarios);
    }
    
    public function agregarIdioma(): void
    {
        $this->idiomas[] = ['idioma' => '', 'nivel' => ''];
    }
    
    public function eliminarIdioma(int $index): void
    {
        unset($this->idiomas[$index]);
        $this->idiomas = array_values($this->idiomas);
    }
    
    /**
     * Guardar instructor
     */
    public function store(): void
    {
        try {
            $datos = $this->validate();
            
            // Preparar especialidades
            if (!empty($this->especialidades)) {
                $datos['especialidades'] = $this->especialidades;
            }
            
            // Preparar jornadas (array de IDs) - mapear desde parametro_temas a jornadas_formacion
            $jornadasIds = [];
            if (!empty($this->jornadas)) {
                // Obtener los parametro_temas seleccionados
                $parametrosTemas = ParametroTema::whereIn('id', $this->jornadas)
                    ->with('parametro')
                    ->get();
                
                // Mapear nombres de parámetros a IDs de jornadas_formacion
                foreach ($parametrosTemas as $parametroTema) {
                    $nombreJornada = $parametroTema->parametro->name ?? null;
                    if ($nombreJornada) {
                        $jornadaFormacion = JornadaFormacion::where('jornada', $nombreJornada)->first();
                        if ($jornadaFormacion) {
                            $jornadasIds[] = $jornadaFormacion->id;
                        }
                    }
                }
            }
            
            // Preparar arrays JSON - campos dinámicos que vienen como arrays
            $camposJsonArray = [
                'titulos_obtenidos',
                'instituciones_educativas',
                'certificaciones_tecnicas',
                'cursos_complementarios',
                'areas_experticia',
                'competencias_tic',
                'idiomas',
                'habilidades_pedagogicas',
            ];
            
            foreach ($camposJsonArray as $campo) {
                if (isset($datos[$campo]) && is_array($datos[$campo])) {
                    // Filtrar valores vacíos y trim
                    $valores = array_filter(array_map('trim', $datos[$campo]));
                    $datos[$campo] = !empty($valores) ? array_values($valores) : null;
                } else {
                    $datos[$campo] = null;
                }
            }
            
            // Convertir áreas de experticia y competencias TIC de string a array si vienen como texto
            if (isset($datos['areas_experticia']) && is_string($datos['areas_experticia'])) {
                $datos['areas_experticia'] = array_filter(array_map('trim', explode("\n", $datos['areas_experticia'])));
                $datos['areas_experticia'] = !empty($datos['areas_experticia']) ? array_values($datos['areas_experticia']) : null;
            }
            
            if (isset($datos['competencias_tic']) && is_string($datos['competencias_tic'])) {
                $datos['competencias_tic'] = array_filter(array_map('trim', explode("\n", $datos['competencias_tic'])));
                $datos['competencias_tic'] = !empty($datos['competencias_tic']) ? array_values($datos['competencias_tic']) : null;
            }
            
            // Agregar usuario creador
            $datos['user_create_id'] = Auth::id();
            
            $instructor = $this->instructorService->crear($datos, $jornadasIds);
            
            session()->flash('success', '¡Instructor asignado exitosamente!');
            
            $this->redirect(route('instructor.index'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Las validaciones se manejan automáticamente por Livewire
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error al crear instructor desde Livewire: ' . $e->getMessage());
            
            session()->flash('error', $e->getMessage());
            
            // Mostrar mensaje de error sin hacer redirect
            $this->addError('general', $e->getMessage());
        }
    }
    
    protected function rules(): array
    {
        return [
            'persona_id' => 'required|exists:personas,id',
            'regional_id' => 'required|exists:regionals,id',
            'centro_formacion_id' => 'nullable|exists:centro_formacions,id',
            'tipo_vinculacion_id' => 'nullable|exists:parametros_temas,id',
            'jornadas' => 'nullable|array',
            'jornadas.*' => 'exists:parametros_temas,id',
            'fecha_ingreso_sena' => 'nullable|date|before_or_equal:today',
            'anos_experiencia' => 'nullable|integer|min:0|max:50',
            'experiencia_instructor_meses' => 'nullable|integer|min:0',
            'experiencia_laboral' => 'nullable|string|max:1000',
            'nivel_academico_id' => 'nullable|exists:parametros_temas,id',
            'formacion_pedagogia' => 'nullable|string|max:500',
            'titulos_obtenidos' => 'nullable|array',
            'titulos_obtenidos.*' => 'nullable|string|max:255',
            'instituciones_educativas' => 'nullable|array',
            'instituciones_educativas.*' => 'nullable|string|max:255',
            'certificaciones_tecnicas' => 'nullable|array',
            'certificaciones_tecnicas.*' => 'nullable|string|max:255',
            'cursos_complementarios' => 'nullable|array',
            'cursos_complementarios.*' => 'nullable|string|max:255',
            'areas_experticia' => 'nullable',
            'competencias_tic' => 'nullable',
            'idiomas' => 'nullable|array',
            'idiomas.*.idioma' => 'nullable|string|max:100',
            'idiomas.*.nivel' => 'nullable|string|in:básico,intermedio,avanzado,nativo',
            'habilidades_pedagogicas' => 'nullable|array',
            'habilidades_pedagogicas.*' => 'in:virtual,presencial,dual',
            'especialidades' => 'nullable|array',
            'especialidades.*' => 'exists:red_conocimientos,id',
            'numero_contrato' => 'nullable|string|max:100',
            'fecha_inicio_contrato' => 'nullable|date',
            'fecha_fin_contrato' => 'nullable|date|after_or_equal:fecha_inicio_contrato',
            'supervisor_contrato' => 'nullable|string|max:255',
            'eps' => 'nullable|string|max:100',
            'arl' => 'nullable|string|max:100',
        ];
    }
    
    public function render()
    {
        $personasDisponibles = Persona::query()
            ->whereDoesntHave('instructor')
            ->orderBy('primer_nombre')
            ->orderBy('primer_apellido')
            ->get();
        
        $regionales = Regional::where('status', 1)->get();
        $especialidades = RedConocimiento::where('status', true)->orderBy('nombre')->get();
        
        // Jornadas de trabajo desde parametros_temas (tema: JORNADAS)
        $jornadasTrabajo = ParametroTema::whereHas('tema', function($q) {
            $q->where('name', 'LIKE', '%JORNADAS%');
        })->whereHas('parametro', function($query) {
            $query->where('status', true);
        })->where('status', true)
          ->with('parametro')
          ->get()
          ->sortBy(function($pt) {
              return $pt->parametro->name;
          })
          ->values();
        
        // Tipos de vinculación desde parametros_temas (tema: TIPOS DE VINCULACION)
        $tiposVinculacion = ParametroTema::whereHas('tema', function($q) {
            $q->where('name', 'LIKE', '%TIPOS DE VINCULACION%');
        })->whereHas('parametro', function($query) {
            $query->where('status', true);
        })->where('status', true)
          ->with('parametro')
          ->get()
          ->sortBy(function($pt) {
              return $pt->parametro->name;
          })
          ->values();
        
        // Log para depuración
        Log::info('Tipos de vinculación cargados en CreateInstructor', [
            'cantidad' => $tiposVinculacion->count(),
            'tipos' => $tiposVinculacion->pluck('parametro.name', 'id')->toArray()
        ]);
        
        // Niveles académicos desde parametros_temas (tema: NIVELES ACADEMICOS)
        $nivelesAcademicos = ParametroTema::whereHas('tema', function($q) {
            $q->where('name', 'LIKE', '%NIVELES ACADEMICOS%');
        })->whereHas('parametro', function($query) {
            $query->where('status', true);
        })->where('status', true)
          ->with('parametro')
          ->get()
          ->sortBy(function($pt) {
              return $pt->parametro->name;
          })
          ->values();
        
        // Log para depuración
        Log::info('Niveles académicos cargados en CreateInstructor', [
            'cantidad' => $nivelesAcademicos->count(),
            'niveles' => $nivelesAcademicos->pluck('parametro.name', 'id')->toArray()
        ]);
        
        return view('livewire.create-instructor', [
            'personasDisponibles' => $personasDisponibles,
            'regionales' => $regionales,
            'especialidadesList' => $especialidades,
            'jornadasTrabajo' => $jornadasTrabajo,
            'tiposVinculacion' => $tiposVinculacion,
            'nivelesAcademicos' => $nivelesAcademicos
        ]);
    }
}

