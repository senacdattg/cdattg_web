@extends('layout.master-layout-registro')

@push('styles')
    @vite('resources/css/pages/registro.css')
@endpush

@section('content')
    <div class="register-box">
        <div class="card sena-card shadow-lg">
            <div class="card-header sena-header text-center text-white">
                <div class="d-flex flex-column align-items-center">
                    <div class="rounded-circle sena-header-icon d-flex align-items-center justify-content-center mb-3">
                        <i class="fas fa-user-graduate fa-2x"></i>
                    </div>
                    <h1 class="h4 mb-1 font-weight-bold">Crea tu cuenta en Académica</h1>
                    <span class="small" style="color: rgba(255, 255, 255, 0.75);">
                        Conéctate con los programas complementarios del SENA.
                    </span>
                </div>
            </div>
            <div class="card-body sena-body">
                <div class="alert sena-alert border-0 d-flex align-items-start">
                    <i class="fas fa-info-circle fa-lg mr-3 mt-1 sena-alert__icon"></i>
                    <div>
                        <strong class="d-block mb-1 sena-alert__title">¿Por qué pedimos estos datos?</strong>
                        <p class="mb-0 small text-muted">
                            Validamos tu identidad y aseguramos la comunicación.
                            <br>
                            Personalizamos tu experiencia en el portal.
                        </p>
                    </div>
                </div>

                <form id="registroForm" action="{{ route('registrarme') }}" method="post" class="needs-validation"
                    novalidate>
                    @csrf

                    @include('personas.partials.form', [
                        'persona' => null,
                        'documentos' => $documentos,
                        'generos' => $generos,
                        'caracterizaciones' => $caracterizaciones,
                        'paises' => $paises,
                        'departamentos' => $departamentos,
                        'municipios' => $municipios,
                        'cardinales' => $cardinales,
                        'showCaracterizacion' => true,
                    ])

                    <div class="row mt-4">
                        <div class="col-md-6 mb-2">
                            <button type="submit"
                                class="btn btn-block py-2 text-uppercase font-weight-semibold sena-btn-primary">
                                <i class="fas fa-user-plus mr-2"></i>Registrarme ahora
                            </button>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ url('/') }}"
                                class="btn btn-block py-2 text-uppercase font-weight-semibold sena-btn-outline">
                                <i class="fas fa-arrow-left mr-2"></i>Volver al inicio
                            </a>
                        </div>
                    </div>
                    <p class="text-muted text-center small mb-0 mt-3">
                        Al continuar aceptas el tratamiento seguro de tus datos.
                        <br>
                        Consulta la política de privacidad del SENA para más información.
                    </p>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{-- Fix temporal para validación de registro - eliminar después de compilar assets --}}
    <script>
        console.log('🔧 [REGISTRO] Iniciando parche de validación...');

        document.addEventListener('DOMContentLoaded', function() {
            console.log('📄 [REGISTRO] DOM cargado');

            const form = document.getElementById('registroForm');
            if (!form) {
                console.error('❌ [REGISTRO] Formulario #registroForm no encontrado');
                return;
            }

            console.log('✅ [REGISTRO] Formulario encontrado');

            // CLONAR el formulario para eliminar todos los event listeners previos
            // Esto elimina la validación de formularios-select-dinamico.js
            const nuevoForm = form.cloneNode(true);
            form.parentNode.replaceChild(nuevoForm, form);
            console.log('✅ [REGISTRO] Listeners antiguos eliminados');

            nuevoForm.addEventListener('submit', function(e) {
                console.log('🔍 [REGISTRO] Validando antes de enviar...');

                const camposRequeridos = [{
                        id: 'tipo_documento',
                        nombre: 'Tipo de Documento'
                    },
                    {
                        id: 'numero_documento',
                        nombre: 'Número de Documento'
                    },
                    {
                        id: 'primer_nombre',
                        nombre: 'Primer Nombre'
                    },
                    {
                        id: 'primer_apellido',
                        nombre: 'Primer Apellido'
                    },
                    {
                        id: 'fecha_nacimiento',
                        nombre: 'Fecha de Nacimiento'
                    },
                    {
                        id: 'genero',
                        nombre: 'Género'
                    },
                    {
                        id: 'email',
                        nombre: 'Correo Electrónico'
                    },
                    {
                        id: 'pais_id',
                        nombre: 'País'
                    },
                    {
                        id: 'departamento_id',
                        nombre: 'Departamento'
                    },
                    {
                        id: 'municipio_id',
                        nombre: 'Municipio'
                    }
                ];

                let valido = true;
                let primerCampoInvalido = null;
                let camposFaltantes = [];

                camposRequeridos.forEach(campo => {
                    const elemento = document.getElementById(campo.id);
                    const valor = elemento ? elemento.value.trim() : '';

                    console.log(`  ${campo.nombre}: ${valor ? '✅ OK' : '❌ VACÍO'}`);

                    if (elemento && !valor) {
                        elemento.classList.add('is-invalid');
                        if (!primerCampoInvalido) {
                            primerCampoInvalido = elemento;
                        }
                        camposFaltantes.push(campo.nombre);
                        valido = false;
                    } else if (elemento) {
                        elemento.classList.remove('is-invalid');
                    }
                });

                if (!valido) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    console.error('❌ [REGISTRO] Validación fallida. Campos faltantes:', camposFaltantes);

                    Swal.fire({
                        icon: 'warning',
                        title: 'Campos requeridos',
                        html: '<strong>Complete todos los campos obligatorios marcados con *</strong><br><br>' +
                            '<div style="text-align: left; color: #666;">Faltan:<br>' +
                            camposFaltantes.map(c => '• ' + c).join('<br>') + '</div>',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#3085d6',
                        allowOutsideClick: false
                    });

                    if (primerCampoInvalido) {
                        setTimeout(() => {
                            primerCampoInvalido.focus();
                            primerCampoInvalido.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }, 100);
                    }
                } else {
                    console.log('✅ [REGISTRO] Validación exitosa, enviando formulario...');
                }
            });

            console.log('✅✅✅ [REGISTRO] Validación configurada correctamente');
        });
    </script>
@endpush
