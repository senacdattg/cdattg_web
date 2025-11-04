@extends('complementarios.layout.master-layout-complementarios')
@section('title', 'Información del Programa | SENA')
@section('css')
    @vite(['resources/css/formulario_inscripcion.css'])
<<<<<<< HEAD
    <style>
    .btn-register {
        background-color: #3f474e;
        border-color: #3f474e;
        color: #fff;
    }
    .btn-register:hover, .btn-register:focus {
        background-color: #343a40;
        border-color: #343a40;
        color: #fff;
    }
    </style>
=======
>>>>>>> develop
@endsection
@section('content')
    

<<<<<<< HEAD
    <div class="container-fluid mt-4 px-2 px-md-4" style="background-color: #ebf1f4; min-height: 100vh;">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="text-center mb-4">
                    <h2 class="text-dark">Información del Programa</h2>
=======
    <div class="container-fluid mt-4 px-2 px-md-4">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="text-center mb-4">
                    <h2 class="text-success">Información del Programa</h2>
>>>>>>> develop
                    <p class="text-muted">Detalles del programa seleccionado</p>
                </div>
                <div class="d-flex justify-content-center">
                    <div class="col-lg-10 col-xl-8">
                        @include('complementarios.components.card-programa', ['programaData' => $programaData])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Inscripción -->
    <div class="modal fade" id="inscripcionModal" tabindex="-1" role="dialog" aria-labelledby="inscripcionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
<<<<<<< HEAD
                <div class="modal-header" style="background-color: #ffffff; color: #343a40; border-left: 4px solid #007bff;">
                    <h5 class="modal-title" id="inscripcionModalLabel">
                        <i class="fas fa-user-plus mr-2"></i>Inscripción al Programa
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
=======
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="inscripcionModalLabel">
                        <i class="fas fa-user-plus mr-2"></i>Inscripción al Programa
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
>>>>>>> develop
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
<<<<<<< HEAD
                            <button type="button" class="btn btn-register btn-block" onclick="redirectToRegistro()">
=======
                            <button type="button" class="btn btn-success btn-block" onclick="redirectToRegistro()">
>>>>>>> develop
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

<<<<<<< HEAD
@include('complementarios.layout.footer-complementarios')
=======
@include('layout.footer')
>>>>>>> develop
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
        window.location.href = '/login?redirect=' + selectedProgramaId;
    } else {
        window.location.href = '/login';
    }
}

function redirectToRegistro() {
    if (selectedProgramaId) {
        // Redirigir directamente al formulario de inscripción del programa complementario
        // El formulario creará automáticamente el perfil de usuario
        window.location.href = '/programas-complementarios/' + selectedProgramaId + '/inscripcion';
    } else {
        // Si no hay programa seleccionado, redirigir a programas públicos
        window.location.href = '/programas-complementarios';
    }
}
</script>
