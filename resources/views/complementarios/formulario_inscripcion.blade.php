@extends('complementarios.layout.master-layout-complementarios')
@section('title', 'Formulario de Inscripción | SENA')
@section('css')
    @vite(['resources/css/formulario_inscripcion.css'])
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar conversión a mayúsculas
    setupUppercaseConversion();

    // Configurar validación de números
    setupNumberValidation();

    // Configurar carga dinámica de municipios
    setupMunicipioLoading();
});

function setupUppercaseConversion() {
    const camposTexto = [
        'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido'
    ];

    camposTexto.forEach(campoId => {
        const campo = document.getElementById(campoId);
        if (campo) {
            campo.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        }
    });
}

function setupNumberValidation() {
    const camposNumericos = ['numero_documento', 'telefono', 'celular'];

    camposNumericos.forEach(campoId => {
        const campo = document.getElementById(campoId);
        if (campo) {
            campo.addEventListener('keypress', soloNumeros);
        }
    });
}

function soloNumeros(event) {
    const key = event.key;
    if (event.ctrlKey || event.altKey || event.metaKey) {
        return true;
    }
    if (!/^\d$/.test(key)) {
        event.preventDefault();
        return false;
    }
    return true;
}

function setupMunicipioLoading() {
    const departamentoSelect = document.getElementById('departamento_id');
    if (departamentoSelect) {
        departamentoSelect.addEventListener('change', function() {
            loadMunicipiosForDepartamento(this.value);
        });
    }
}

function loadMunicipiosForDepartamento(departamentoId) {
    const municipioSelect = document.getElementById('municipio_id');
    if (!municipioSelect) return;

    if (departamentoId) {
        fetch(`/municipios/${departamentoId}`)
            .then(response => response.json())
            .then(data => {
                municipioSelect.innerHTML = '<option value="">Seleccione...</option>';
                data.forEach(municipio => {
                    const option = document.createElement('option');
                    option.value = municipio.id;
                    option.textContent = municipio.municipio;
                    municipioSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error cargando municipios:', error);
                municipioSelect.innerHTML = '<option value="">Error cargando municipios</option>';
            });
    } else {
        municipioSelect.innerHTML = '<option value="">Seleccione...</option>';
    }
}
</script>
@endsection
@section('content')

     <div class="container-fluid mt-4">
         @if(session('user_data'))
             <div class="alert alert-info alert-dismissible">
                 <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                 <h5><i class="icon fas fa-info"></i> Información Pre-llenada</h5>
                 Hemos completado algunos campos con la información de su cuenta. Por favor, complete los campos faltantes y revise que toda la información sea correcta.
             </div>
         @endif
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="text-center mb-4">
                    <h2 class="text-success">Formulario de Inscripción</h2>
                    <p class="text-muted">Complete sus datos para inscribirse</p>
                </div>
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-plus mr-2"></i>Formulario de Inscripción
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('programas-complementarios.publicos') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a Programas
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h4 class="text-muted">{{ $programa->nombre }}</h4>
                        </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card card-success">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-user mr-2"></i>Información Personales</h5>
                        </div>
                        <div class="card-body">
                            <form id="formInscripcion" method="POST"
                                action="{{ route('programas-complementarios.procesar-inscripcion', $programa->id) }}">
                                @csrf
                                <input type="hidden" name="programa_id" value="{{ $programa->id }}">

                               @include('complementarios.components.form-datos-personales', [
                                   'context' => 'inscripcion',
                                   'userData' => session('user_data', [])
                               ])

                                <hr class="my-4">

                                <div class="card card-success mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-tags mr-2"></i>Caracterización</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-3">Seleccione las categorías que correspondan a su situación:</p>

                                        @foreach ($categoriasConHijos as $categoria)
                                            <div class="card card-outline card-success mb-3">
                                                <div class="card-header">
                                                    <h6 class="mb-0">{{ $categoria['nombre'] }}</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        @foreach ($categoria['hijos'] as $hijo)
                                                            <div class="col-12 mb-2">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="categorias[]" value="{{ $hijo->id }}"
                                                                        id="categoria_{{ $hijo->id }}">
                                                                    <label class="form-check-label"
                                                                        for="categoria_{{ $hijo->id }}">
                                                                        {{ ucwords(str_replace('_', ' ', $hijo->nombre)) }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="card card-success mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-sticky-note mr-2"></i>Observaciones y Términos</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="observaciones" class="form-label">Observaciones</label>
                                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"
                                                placeholder="Información adicional que considere relevante..."></textarea>
                                        </div>

                                        <div class="form-check mb-4">
                                            <input class="form-check-input" type="checkbox" id="acepto_terminos" required>
                                            <label class="form-check-label" for="acepto_terminos">
                                                Acepto los términos y condiciones del proceso de inscripción *
                                            </label>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="reset" class="btn btn-outline-secondary me-md-2">Limpiar</button>
                                            <button type="submit" class="btn btn-success">Enviar Inscripción</button>
                                        </div>

                                        <script>
                                            // Mostrar preloader al enviar el formulario
                                            document.getElementById('formInscripcion').addEventListener('submit', function() {
                                                $('body').addClass('preloader-active');
                                            });
                                        </script>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-widget widget-user">
                        <div class="widget-user-header bg-success">
                            <h3 class="widget-user-username">Información del Programa</h3>
                            <h5 class="widget-user-desc">{{ $programa->nombre }}</h5>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="description-block">
                                        <span class="description-text">DESCRIPCIÓN</span>
                                        <p class="text-muted mb-3">{{ $programa->descripcion }}</p>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="description-block">
                                                    <span class="description-text">DURACIÓN</span>
                                                    <h5 class="description-header">{{ $programa->duracion }} horas</h5>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="description-block">
                                                    <span class="description-text">MODALIDAD</span>
                                                    <h5 class="description-header">
                                                        {{ $programa->modalidad->parametro->name ?? 'N/A' }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="description-block">
                                                    <span class="description-text">JORNADA</span>
                                                    <h5 class="description-header">
                                                        {{ $programa->jornada->jornada ?? 'N/A' }}</h5>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="description-block">
                                                    <span class="description-text">CUPO</span>
                                                    <h5 class="description-header">{{ $programa->cupos }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@include('layout.footer')
@endsection

