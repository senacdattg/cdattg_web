@extends('complementarios.layout.master-layout-complementarios')
@section('title', 'Formulario de Inscripción | SENA')
@section('css')
    @vite(['resources/css/formulario_inscripcion.css'])
@endsection
@section('scripts')
    @vite(['resources/js/complementarios/formulario-inscripcion.js'])
@endsection
@section('content')

     <div class="container-fluid mt-4" style="background-color: #ebf1f4; min-height: 100vh;">
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
                    <h2 class="text-dark">Formulario de Inscripción</h2>
                    <p class="text-muted">Complete sus datos para inscribirse</p>
                </div>
                <div class="card" style="background-color: #ffffff; border-color: #dee2e6;">
                    <div class="card-header" style="background-color: #ffffff; color: #343a40; border-left: 4px solid #007bff;">
                        <h3 class="card-title">
                            <i class="fas fa-user-plus mr-2"></i>Formulario de Inscripción
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('programas-complementarios.publicos') }}" class="btn btn-outline-primary btn-sm">
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
                    <div class="card" style="background-color: #ffffff; border-color: #dee2e6;">
                        <div class="card-header" style="background-color: #ffffff; color: #343a40; border-left: 4px solid #007bff;">
                            <h5 class="mb-0"><i class="fas fa-user mr-2"></i>Información Personal</h5>
                        </div>
                        <div class="card-body">
                            <form id="formInscripcion" method="POST"
                                action="{{ route('programas-complementarios.procesar-inscripcion', $programa->id) }}">
                                @csrf
                                <input type="hidden" name="programa_id" value="{{ $programa->id }}">

                               @include('complementarios.components.form-datos-personales', [
                                   'context' => 'inscripcion',
                                   'userData' => $userData ?? []
                               ])

                                <hr class="my-4" style="border-color: #dee2e6;">

                                <div class="card mb-4" style="background-color: #ffffff; border-color: #dee2e6;">
                                    <div class="card-header" style="background-color: #ffffff; color: #343a40; border-left: 4px solid #007bff;">
                                        <h5 class="mb-0"><i class="fas fa-tags mr-2"></i>Caracterización</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-3">Seleccione una categoría que corresponda a su situación:</p>

                                        @foreach ($categoriasConHijos as $categoria)
                                            <div class="card card-outline mb-3" style="border-color: #dee2e6;">
                                                <div class="card-header" style="background-color: #f8f9fa; color: #343a40;">
                                                    <h6 class="mb-0">{{ $categoria['nombre'] }}</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        @foreach ($categoria['hijos'] as $hijo)
                                                            <div class="col-12 mb-2">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="caracterizacion_id" value="{{ $hijo->id }}"
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

                                <div class="card mb-4" style="background-color: #ffffff; border-color: #dee2e6;">
                                    <div class="card-header" style="background-color: #ffffff; color: #343a40; border-left: 4px solid #007bff;">
                                        <h5 class="mb-0"><i class="fas fa-sticky-note mr-2"></i>Observaciones y Términos</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="observaciones" class="form-label" style="color: #343a40;">Observaciones</label>
                                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"
                                                placeholder="Información adicional que considere relevante..."></textarea>
                                        </div>

                                        <div class="form-check mb-4">
                                            <input class="form-check-input" type="checkbox" id="acepto_terminos" name="acepto_terminos" required>
                                            <label class="form-check-label" for="acepto_terminos" style="color: #343a40;">
                                                Acepto los <a href="#" data-toggle="modal" data-target="#modalTerminos" style="color: #007bff;">términos y condiciones</a> del proceso de inscripción *
                                            </label>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="reset" class="btn btn-outline-secondary me-md-2 mr-3">Limpiar</button>
                                            <button type="submit" class="btn btn-primary">Enviar Inscripción</button>
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
                        <div class="widget-user-header" style="background-color: #ffffff; color: #343a40; border-left: 4px solid #007bff;">
                            <h3 class="widget-user-username">Información del Programa</h3>
                            <h5 class="widget-user-desc">{{ $programa->nombre }}</h5>
                        </div>
                        <div class="card-footer" style="background-color: #ffffff;">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="description-block">
                                        <span class="description-text" style="color: #343a40;">DESCRIPCIÓN</span>
                                        <p class="text-muted mb-3">{{ $programa->descripcion }}</p>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="description-block">
                                                    <span class="description-text" style="color: #343a40;">DURACIÓN</span>
                                                    <h5 class="description-header" style="color: #007bff;">{{ formatear_horas($programa->duracion) }} horas</h5>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="description-block">
                                                    <span class="description-text" style="color: #343a40;">MODALIDAD</span>
                                                    <h5 class="description-header" style="color: #007bff;">
                                                        {{ $programa->modalidad->parametro->name ?? 'N/A' }}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="description-block">
                                                    <span class="description-text" style="color: #343a40;">JORNADA</span>
                                                    <h5 class="description-header" style="color: #007bff;">
                                                        {{ $programa->jornada->jornada ?? 'N/A' }}</h5>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="description-block">
                                                    <span class="description-text" style="color: #343a40;">CUPO</span>
                                                    <h5 class="description-header" style="color: #007bff;">{{ $programa->cupos }}</h5>
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

@include('complementarios.layout.footer-complementarios')
@include('components.modal-terminos')
@endsection
