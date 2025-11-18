@extends('adminlte::page')

{{-- SweetAlert2 activado globalmente en config/adminlte.php --}}

@section('title', 'Registrar Ingreso y Salida')

@section('content_header')
    <x-page-header icon="fa-sign-in-alt" title="Registrar Ingreso y Salida"
        subtitle="Registra las entradas y salidas del personal" :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
            ['label' => 'Control y Seguimiento', 'icon' => 'fa-clipboard-check'],
            [
                'label' => 'Ingreso y Salida',
                'url' => route('control-seguimiento.ingreso-salida.index'),
                'icon' => 'fa-sign-in-alt',
            ],
            ['label' => 'Registrar', 'icon' => 'fa-plus-circle', 'active' => true],
        ]" />
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-lg-10 mx-auto">
                    @livewire('ingreso-salida-component')
                </div>
            </div>
        </div>
    </section>
@endsection

@section('css')
    @vite(['resources/css/parametros.css'])
    <style>
        /* Asegurar que el contenido se muestre completamente */
        html, body {
            overflow-x: hidden !important;
            overflow-y: auto !important;
        }

        .content {
            min-height: auto !important;
            overflow: visible !important;
        }

        .content-wrapper {
            overflow: visible !important;
        }

        .container-fluid {
            overflow: visible !important;
        }

        section.content {
            padding-bottom: 2rem !important;
        }

        /* Asegurar que el componente Livewire se muestre completamente */
        [wire\:id] {
            overflow: visible !important;
        }

        .col-12.col-lg-10 {
            overflow: visible !important;
        }

        /* Asegurar que las cards se muestren completamente */
        .card {
            overflow: visible !important;
        }

        .card-body {
            overflow: visible !important;
        }

        /* Asegurar que el modal se muestre correctamente */
        .modal.show {
            z-index: 1050 !important;
            display: block !important;
        }

        .modal-backdrop {
            z-index: 1040 !important;
        }

        /* Estilos mejorados para la UI de ingreso-salida */
        .bg-success-light {
            background-color: rgba(40, 167, 69, 0.1) !important;
        }

        /* Botones de acción principales - Diseño profesional */
        .btn-action-principal {
            padding: 0.875rem 2rem;
            font-size: 1.0625rem;
            font-weight: 600;
            border-radius: 0.5rem;
            min-width: 220px;
            white-space: nowrap;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: none;
            letter-spacing: 0.3px;
        }

        .btn-action-principal:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-action-principal:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-action-principal:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        }

        .btn-action-principal.btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-action-principal.btn-success:hover {
            background: linear-gradient(135deg, #218838 0%, #1aa179 100%);
        }

        .btn-action-principal.btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .btn-action-principal.btn-danger:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
        }

        .btn-action-principal:disabled,
        .btn-action-principal[disabled] {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none !important;
        }

        @media (max-width: 767.98px) {
            .btn-action-principal {
                width: 100%;
                min-width: auto;
                padding: 1rem 1.5rem;
                font-size: 1rem;
            }

            .card-header .row {
                margin: 0;
            }

            .card-header .col-12 {
                padding-left: 0;
                padding-right: 0;
            }

            .card-header .badge {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }

            .card-header h4 {
                font-size: 1rem;
            }
        }

        @media (min-width: 768px) and (max-width: 991.98px) {
            .btn-action-principal {
                min-width: 200px;
                padding: 0.75rem 1.75rem;
                font-size: 1rem;
            }
        }

        @media (min-width: 992px) {
            .btn-action-principal {
                min-width: 240px;
                padding: 1rem 2.25rem;
                font-size: 1.125rem;
            }
        }

        .form-control-plaintext.bg-light {
            background-color: #f8f9fa !important;
            border: 1px solid #dee2e6 !important;
        }

        /* Ajustes responsive para el input de búsqueda */
        .input-group-md {
            font-size: 1rem;
        }

        .input-group-md .form-control {
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        .input-group-md .input-group-text {
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
        }

        .input-group-md .btn {
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
        }

        @media (min-width: 768px) {
            .input-group-lg-md {
                font-size: 1.25rem;
            }

            .input-group-lg-md .form-control {
                padding: 0.5rem 1rem;
                font-size: 1.25rem;
                line-height: 1.5;
            }

            .input-group-lg-md .input-group-text {
                padding: 0.5rem 1rem;
                font-size: 1.25rem;
            }

            .input-group-lg-md .btn {
                padding: 0.5rem 1rem;
                font-size: 1.25rem;
            }
        }

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
    @vite(['resources/js/app.js'])
    @stack('js')
@endsection

