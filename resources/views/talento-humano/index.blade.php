@extends('adminlte::page')

@section('plugins.Sweetalert2', true)

@section('title', 'Talento Humano')

@section('content_header')
    <x-page-header icon="fa-users" title="Talento Humano"
        subtitle="Consulta y gestiona información del talento humano en tiempo real" :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
            ['label' => 'Talento Humano', 'icon' => 'fa-users', 'active' => true],
        ]" />
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-10 mx-auto">
                    <div class="card card-outline card-primary shadow-sm">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-search mr-2"></i>Búsqueda de Persona
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8 mx-auto">
                                    <div class="form-group">
                                        <label for="numero_documento_buscar">
                                            Número de Documento
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fas fa-id-card"></i>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" id="numero_documento_buscar"
                                                placeholder="Ingrese el número de documento" autocomplete="off">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-secondary" id="btn-limpiar">
                                                    <i class="fas fa-eraser"></i> Limpiar
                                                </button>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            La búsqueda se realiza automáticamente mientras escribe
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para seleccionar sede -->
                    <div class="modal fade" id="modalSede" tabindex="-1" role="dialog" aria-labelledby="modalSedeLabel"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalSedeLabel">Seleccionar Sede</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="select_sede_modal">Sede <span class="text-danger">*</span></label>
                                        <select class="form-control" id="select_sede_modal" required>
                                            <option value="">Seleccione una sede...</option>
                                            @foreach (\App\Models\Sede::where('status', 1)->get() as $sede)
                                                <option value="{{ $sede->id }}">{{ $sede->sede }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="button" class="btn btn-primary"
                                        id="btn-confirmar-sede">Confirmar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="form-container" style="display: none;">
                        <div class="card card-outline card-success shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-edit text-success mr-2"></i>
                                    <h3 class="card-title mb-0" id="form-title">
                                        Información de la Persona
                                    </h3>
                                </div>
                                <div class="d-flex flex-wrap align-items-center">
                                    <button type="button" class="btn btn-warning mr-2 mb-2" id="btn-editar"
                                        style="display: none;">
                                        <i class="fas fa-edit mr-1"></i> Editar
                                    </button>
                                    <button type="button" class="btn btn-primary mb-2" id="btn-guardar"
                                        style="display: none;">
                                        <i class="fas fa-save mr-1"></i> Guardar
                                    </button>
                                    <button type="button" class="btn btn-success mr-2 mb-2" id="btn-registrar-entrada"
                                        style="display: none;">
                                        <i class="fas fa-sign-in-alt mr-1"></i> Registrar Entrada
                                    </button>
                                    <button type="button" class="btn btn-danger mb-2" id="btn-registrar-salida"
                                        style="display: none;">
                                        <i class="fas fa-sign-out-alt mr-1"></i> Registrar Salida
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="personaForm" autocomplete="off">
                                    @csrf
                                    <input type="hidden" id="persona_id" name="persona_id">
                                    <input type="hidden" id="action_mode" name="action_mode" value="create">
                                    @include('personas.partials.form', [
                                        'showCaracterizacion' => true,
                                        'cardinales' => $cardinales,
                                    ])
                                </form>
                            </div>
                            <div class="card-footer d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary" id="btn-cancelar">
                                    <i class="fas fa-times mr-1"></i> Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    @vite(['resources/css/parametros.css'])
    <style>
        /* Estilos personalizados para toasts más vistosos */
        .swal2-toast.swal2-icon-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
            border-left: 5px solid #1e7e34 !important;
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3) !important;
        }

        .swal2-toast.swal2-icon-error {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
            border-left: 5px solid #bd2130 !important;
            box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3) !important;
        }

        .swal2-toast.swal2-icon-warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%) !important;
            border-left: 5px solid #e0a800 !important;
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3) !important;
        }

        .swal2-toast.swal2-icon-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
            border-left: 5px solid #117a8b !important;
            box-shadow: 0 8px 20px rgba(23, 162, 184, 0.3) !important;
        }

        .swal2-toast {
            border-radius: 12px !important;
            padding: 20px 25px !important;
            min-width: 350px !important;
            backdrop-filter: blur(10px) !important;
            animation: slideInRight 0.5s ease-out !important;
        }

        .swal2-toast .swal2-title {
            color: #212529 !important;
            font-weight: 600 !important;
            font-size: 16px !important;
            margin: 0 0 8px 0 !important;
            text-align: left !important;
        }

        .swal2-toast .swal2-html-container {
            color: #212529 !important;
            font-size: 14px !important;
            margin: 0 !important;
            text-align: left !important;
            line-height: 1.5 !important;
        }

        .swal2-toast .swal2-icon {
            width: 48px !important;
            height: 48px !important;
            margin: 0 15px 0 0 !important;
            border: none !important;
        }

        .swal2-toast .swal2-icon.swal2-success {
            border-color: transparent !important;
        }

        .swal2-toast .swal2-icon .swal2-icon-content {
            font-size: 28px !important;
            color: #212529 !important;
        }

        .swal2-toast .swal2-timer-progress-bar {
            background: rgba(255, 255, 255, 0.4) !important;
            height: 4px !important;
            border-radius: 0 0 12px 12px !important;
        }

        .swal2-toast .swal2-actions {
            margin: 0 !important;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .swal2-toast:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.3s ease !important;
        }
    </style>
@endsection

@section('js')
    @vite(['resources/js/app.js', 'resources/js/pages/formularios-select-dinamico.js', 'resources/js/pages/talento-humano.js'])
    @stack('js')
@endsection
