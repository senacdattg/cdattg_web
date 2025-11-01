@extends('adminlte::page')

@section('title', 'Préstamo o Salida')

@section('content_header')
    <x-page-header
        icon="fas fa-exchange-alt"
        title="Préstamo o Salida"
        subtitle="Gestión de préstamos y salidas del inventario"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => '#'],
            ['label' => 'Inventario', 'active' => true],
            ['label' => 'Préstamos/Salidas', 'active' => true]
        ]"
    />
@endsection

@section('content')
    <div class="producto-form-container fade-in">
        {{-- Alertas --}}
        @include('components.session-alerts')

        <div class="row">
            <div class="col-12">
                <div class="producto-form-card slide-in">
                    <div class="form-header-gradient">
                        <h3>
                            <span class="header-icon">
                                <i class="fas fa-exchange-alt"></i>
                            </span>
                            Préstamo o Salida
                        </h3>
                        <p class="header-subtitle mt-2 mb-0">
                            <small class="text-muted">Completa los datos para registrar la solicitud</small>
                        </p>
                    </div>

                    @php($tipoInicial = old('tipo', request('tipo')))

                    <form action="{{ route('inventario.prestamos-salidas') }}" method="POST">
                        @csrf

                        <div class="form-content-container">
                            {{-- Resumen del Carrito --}}
                            @if(!empty($totalProductos) && $totalProductos > 0)
                                <div class="stats-grid">
                                    <div class="stat-card stat-info">
                                        <div class="stat-card-header">
                                            <div class="stat-card-icon">
                                                <i class="fas fa-boxes"></i>
                                            </div>
                                            <div>
                                                <div class="stat-card-label">Productos en la solicitud</div>
                                                <div class="stat-card-value">{{ $totalProductos ?? 0 }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stat-card stat-success">
                                        <div class="stat-card-header">
                                            <div class="stat-card-icon">
                                                <i class="fas fa-layer-group"></i>
                                            </div>
                                            <div>
                                                <div class="stat-card-label">Total de ítems</div>
                                                <div class="stat-card-value">{{ $totalItems ?? 0 }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="total_productos" value="{{ $totalProductos ?? 0 }}">
                                <input type="hidden" name="total_items" value="{{ $totalItems ?? 0 }}">
                            @else
                                {{-- Resumen dinámico desde carrito (se llenará con JavaScript) --}}
                                <div class="stats-grid" id="carrito-resumen-stats">
                                    <div class="stat-card stat-info">
                                        <div class="stat-card-header">
                                            <div class="stat-card-icon">
                                                <i class="fas fa-boxes"></i>
                                            </div>
                                            <div>
                                                <div class="stat-card-label">Productos en la solicitud</div>
                                                <div class="stat-card-value" id="carrito-total-productos">0</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="stat-card stat-success">
                                        <div class="stat-card-header">
                                            <div class="stat-card-icon">
                                                <i class="fas fa-layer-group"></i>
                                            </div>
                                            <div>
                                                <div class="stat-card-label">Total de ítems</div>
                                                <div class="stat-card-value" id="carrito-total-items">0</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Resumen de productos del carrito --}}
                            <div class="card mt-3 d-none" id="carrito-items-card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-shopping-cart"></i> Productos del Carrito
                                    </h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Producto</th>
                                                    <th width="100" class="text-center">Cantidad</th>
                                                </tr>
                                            </thead>
                                            <tbody id="carrito-items-tbody">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            {{-- Sección: Datos del Solicitante --}}
                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-user"></i>
                                    Datos del Solicitante
                                </h4>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label>
                                                <i class="fas fa-user"></i>
                                                Solicitante
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control-modern"
                                                value="{{ auth()->user()->name ?? 'Usuario' }}"
                                                readonly
                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label>
                                                <i class="fas fa-envelope"></i>
                                                Correo
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control-modern"
                                                value="{{ auth()->user()->email ?? '' }}"
                                                readonly
                                            >
                                        </div>
                                    </div>
                                    <input type="hidden" name="solicitante_email" value="{{ auth()->user()->email ?? '' }}">
                                    <input type="hidden" name="solicitante_id" value="{{ auth()->id() }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label>
                                                <i class="fas fa-user-tag"></i>
                                                Rol
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control-modern"
                                                value="{{ (auth()->check() && method_exists(auth()->user(), 'getRoleNames') && auth()->user()->getRoleNames()->first()) ? auth()->user()->getRoleNames()->first() : (auth()->user()->role->name ?? 'N/A') }}"
                                                readonly
                                            >
                                            <input
                                                type="hidden"
                                                name="rol"
                                                value="{{ (auth()->check() && method_exists(auth()->user(), 'getRoleNames') && auth()->user()->getRoleNames()->first()) ? auth()->user()->getRoleNames()->first() : (auth()->user()->role->name ?? '') }}"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="programa_formacion">
                                                <i class="fas fa-graduation-cap"></i>
                                                Nombre del programa formación
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                class="form-control-modern @error('programa_formacion') is-invalid @enderror"
                                                id="programa_formacion"
                                                name="programa_formacion"
                                                value="{{ old('programa_formacion') }}"
                                                placeholder="Ej: ADSI, Electricidad, etc."
                                                required
                                            >
                                            @error('programa_formacion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sección: Detalles y Fechas --}}
                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-clipboard-list"></i>
                                    Detalles y Fechas
                                </h4>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group-modern">
                                            <label for="tipo">
                                                <i class="fas fa-tags"></i>
                                                Tipo
                                                <span class="text-danger">*</span>
                                            </label>
                                            <select
                                                class="form-control-modern @error('tipo') is-invalid @enderror"
                                                id="tipo"
                                                name="tipo"
                                                required
                                            >
                                                <option value="">Seleccionar tipo...</option>
                                                <option value="prestamo" {{ ($tipoInicial ?? '') === 'prestamo' ? 'selected' : '' }}>Préstamo</option>
                                                <option value="salida" {{ ($tipoInicial ?? '') === 'salida' ? 'selected' : '' }}>Salida</option>
                                            </select>
                                            @error('tipo')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-none" id="grupo-fecha-devolucion">
                                        <div class="form-group-modern">
                                            <label for="fecha_devolucion">
                                                <i class="fas fa-calendar-check"></i>
                                                Fecha de Devolución
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input
                                                type="date"
                                                class="form-control-modern @error('fecha_devolucion') is-invalid @enderror"
                                                id="fecha_devolucion"
                                                name="fecha_devolucion"
                                                value="{{ old('fecha_devolucion') }}"
                                            >
                                            <small class="form-text text-muted d-block mt-1">
                                                <i class="fas fa-info-circle"></i>
                                                Fecha en la que se espera devolver los materiales
                                            </small>
                                            @error('fecha_devolucion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sección: Descripción --}}
                            <div class="form-section">
                                <h4 class="form-section-title">
                                    <i class="fas fa-comment-alt"></i>
                                    Motivo
                                </h4>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group-modern">
                                            <label for="descripcion">
                                                <i class="fas fa-comment-alt"></i>
                                                Motivo de la solicitud
                                                <span class="text-danger">*</span>
                                            </label>
                                            <textarea
                                                class="form-control-modern @error('descripcion') is-invalid @enderror"
                                                id="descripcion"
                                                name="descripcion"
                                                rows="4"
                                                placeholder="Describe el motivo del préstamo/salida, condiciones especiales, etc."
                                                required
                                            >{{ old('descripcion') }}</textarea>
                                            @error('descripcion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Botones de Acción --}}
                        <div class="form-actions-container">
                            <a href="{{ route('inventario.ordenes.index') }}" class="btn-modern btn-modern-secondary">
                                <i class="fas fa-times"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn-modern btn-modern-success">
                                <i class="fas fa-save"></i>
                                Crear Préstamo/Salida
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script para cargar datos del carrito y manejar campos dinámicos --}}
    <script src="{{ asset('js/inventario/solicitud.js') }}"></script>

    {{-- Alertas --}}
    @include('layout.alertas')

    {{-- Footer SENA --}}
    @include('layout.footer')
@endsection

@push('css')
    @vite(['public/css/inventario/shared/base.css'])
    <link href="{{ asset('css/inventario/inventario.css') }}" rel="stylesheet">
@endpush
