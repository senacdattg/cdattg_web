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
                        'userData' => session('registro_data', []),
                        'paises' => \App\Models\Pais::all(),
                        'departamentos' => \App\Models\Departamento::all(),
                    ])

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

                    <div class="row justify-content-sm-center">
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-block">Registrarme</button>
                        </div>
                    </div>
                </form>
                <hr>
                {{-- <a href="{{ route('login') }}" class="text-center">Ya tengo una cuenta</a> --}}
            </div>

        </div>
    </div>


@section('scripts')
    @vite(['resources/js/complementarios/formulario-inscripcion.js'])
@endsection
@endsection
