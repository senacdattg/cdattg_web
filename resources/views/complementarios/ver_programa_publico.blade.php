@extends('layout.master-layout-registro')
@extends('layout.alertas')
@section('css')
    @vite(['resources/css/formulario_inscripcion.css'])
@endsection
@section('content')
    @include('complementarios.components.header-programas-publicos')

    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="text-center mb-4">
                    <h2 class="text-success">Información del Programa</h2>
                    <p class="text-muted">Detalles del programa seleccionado</p>
                </div>
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-graduation-cap mr-2"></i>{{ $programaData['nombre'] }}
                        </h3>
                        <div class="card-tools">
                            <a href="{{ route('programas-complementarios.publicos') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a Programas
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="text-center mb-5">
                                    <i class="{{ $programaData['icono'] }} fa-5x text-success mb-4"></i>
                                    <h2 class="mb-3">{{ $programaData['nombre'] }}</h2>
                                    <span class="badge badge-success">Con Oferta</span>
                                </div>

                    <h5 class="mb-3">Descripción</h5>
                    <p class="text-muted mb-5">{{ $programaData['descripcion'] }}</p>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-light">
                                <div class="info-box-content py-3">
                                    <span class="info-box-text">Duración</span>
                                    <span class="info-box-number">{{ $programaData['duracion'] }} horas</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                            <div class="col-md-4">
                                <div class="card card-widget widget-user">
                                    <div class="widget-user-header bg-success">
                                        <h3 class="widget-user-username">Inscripción</h3>
                                        <h5 class="widget-user-desc">Programa Disponible</h5>
                                    </div>
                                    <div class="card-footer">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="description-block">
                                                    <span class="description-text">¿ESTÁS INTERESADO?</span>
                                                    <p class="text-muted mb-3">Realiza tu inscripción ahora mismo</p>
                                                    <button type="button" class="btn btn-success btn-block"
                                                            onclick="openInscripcionModal({{ $programaData['id'] }}, '{{ $programaData['nombre'] }}')">
                                                        <i class="fas fa-user-plus mr-1"></i> Inscribirse
                                                    </button>
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

    <!-- Modal de Inscripción -->
    <div class="modal fade" id="inscripcionModal" tabindex="-1" role="dialog" aria-labelledby="inscripcionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="inscripcionModalLabel">
                        <i class="fas fa-user-plus mr-2"></i>Inscripción al Programa
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <h6 class="mb-4">¿Ya tienes una cuenta en el sistema?</h6>
                    <div class="row">
                        <div class="col-6">
                            <button type="button" class="btn btn-primary btn-block" onclick="redirectToLogin()">
                                <i class="fas fa-sign-in-alt fa-2x mb-2"></i><br>
                                <strong>Iniciar Sesión</strong><br>
                                <small>Ya tengo cuenta</small>
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-success btn-block" onclick="redirectToRegistro()">
                                <i class="fas fa-user-plus fa-2x mb-2"></i><br>
                                <strong>Registrarme</strong><br>
                                <small>Soy nuevo</small>
                            </button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            Si ya tienes cuenta, inicia sesión para continuar con tu inscripción.<br>
                            Si eres nuevo, regístrate primero para crear tu cuenta.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

@include('layout.footer')
@endsection

<script>
let selectedProgramaId = null;

function openInscripcionModal(programaId, programaNombre) {
   selectedProgramaId = programaId;
   document.getElementById('inscripcionModalLabel').innerHTML =
       '<i class="fas fa-user-plus mr-2"></i>Inscripción: ' + programaNombre;
   $('#inscripcionModal').modal('show');
}

function redirectToLogin() {
   if (selectedProgramaId) {
       // Redirigir al login con parámetro para recordar el programa
       window.location.href = '/login?redirect=programas-complementarios/' + selectedProgramaId + '/inscripcion';
   } else {
       window.location.href = '/login';
   }
}

function redirectToRegistro() {
   if (selectedProgramaId) {
       // Redirigir al registro con parámetro para recordar el programa
       window.location.href = '/registro?redirect=programas-complementarios/' + selectedProgramaId + '/inscripcion';
   } else {
       window.location.href = '/registro';
   }
}
</script>
