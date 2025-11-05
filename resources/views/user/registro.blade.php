@extends('layout.master-layout-registro')
@section('content')
    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                {{-- Bienvenida al login --}}
                {{-- <a href="{{ route('login') }}" class="h1"><b>registro de asistencias SENA </b></a> --}}
            </div>
            <div class="card-body">
                <p class="login-box-msg">Registrarme</p>

                <form id="registroForm" action="{{ route('registrarme') }}" method="post">
                    @csrf

                    @include('complementarios.components.form-datos-personales', [
                        'context' => 'registro',
                        'userData' => [],
                        'paises' => \App\Models\Pais::all(),
                        'departamentos' => \App\Models\Departamento::all(),
                    ])

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-user-plus mr-2"></i>Registrarme
                            </button>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-arrow-left mr-2"></i>Volver
                            </a>
                        </div>
                    </div>

                    <script>
                        // Mostrar preloader al enviar el formulario
                        document.getElementById('registroForm').addEventListener('submit', function() {
                            $('body').addClass('preloader-active');
                        });
                    </script>

                    <script>
                        // Inicializar funcionalidades del formulario
                        document.addEventListener('DOMContentLoaded', function() {
                            // Configurar conversión a mayúsculas
                            setupUppercaseConversion();

                            // Configurar validación de números
                            setupNumberValidation();

                            // Configurar carga dinámica de municipios
                            setupMunicipioLoading();

                            // Configurar funcionalidad de dirección estructurada
                            setupAddressForm();

                            // Configurar validaciones de formulario
                            setupFormValidations();
                        });
                    </script>

                    
                </form>
                <hr>
                {{-- <a href="{{ route('login') }}" class="text-center">Ya tengo una cuenta</a> --}}
            </div>

        </div>
    </div>


@section('scripts')
    <script src="{{ asset('js/complementarios/formulario-inscripcion.js') }}"></script>
@endsection
@endsection
