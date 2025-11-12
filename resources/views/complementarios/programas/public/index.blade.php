@extends('complementarios.layout.master')
@section('title', 'Programas Complementarios | SENA')

@php
    $modalidades = $programas->pluck('modalidad_nombre')->filter()->unique()->sort()->values();
    $jornadas = $programas->pluck('jornada_nombre')->filter()->unique()->sort()->values();
    $estados = $programas->pluck('estado_label')->filter()->unique()->sort()->values();
@endphp

@push('css')
    <style>
        .hero-banner {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #0f9d58 0%, #0c7b43 50%, #0a6537 100%);
            color: #ffffff;
        }

        .hero-copy {
            position: relative;
            z-index: 1;
        }

        .hero-figure {
            max-height: 260px;
            filter: drop-shadow(0 12px 28px rgba(0, 0, 0, 0.25));
            position: relative;
            z-index: 1;
        }

        .filters-card .select2-selection--single {
            height: 38px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid mt-3 px-2 px-md-4" style="background-color: #f1f5f9; min-height: 100vh;">
        <section class="hero-banner rounded-lg shadow mb-4">
            <div class="container py-5 hero-copy">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-6 mb-4 mb-lg-0 text-center text-lg-left">
                        <span class="badge badge-light text-success text-uppercase px-3 py-2 mb-3 font-weight-bold">
                            Formación complementaria
                        </span>
                        <h1 class="display-4 font-weight-bold mb-3">
                            Programas Complementarios SENA
                        </h1>
                        <p class="lead mb-0">
                            Encuentra oportunidades de aprendizaje flexibles, con certificación y enfoque en habilidades
                            aplicadas al sector productivo regional.
                        </p>
                    </div>
                    <div class="col-12 col-lg-6 text-center">
                        <img src="{{ asset('img/flor_guaviare.png') }}" class="img-fluid hero-figure"
                            alt="Imagen representativa SENA">
                    </div>
                </div>
            </div>
        </section>

        <!-- Alertas de sesión -->
        <div class="container-fluid px-2 px-md-4">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-10">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>
                            <strong>¡Éxito!</strong> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <strong>Error:</strong> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Advertencia:</strong> {{ session('warning') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Información:</strong> {{ session('info') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-10">
                <div class="card filters-card border border-light shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pb-1">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <h3 class="card-title mb-0 text-gray-800">
                                    <i class="fas fa-graduation-cap text-primary mr-2"></i>
                                    Programas Disponibles
                                </h3>
                                <small class="text-muted d-block mt-1">
                                    <span id="program-count">{{ $programas->count() }}</span> resultados encontrados
                                </small>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary" type="button" id="reset-filters">
                                <i class="fas fa-undo mr-1"></i> Limpiar filtros
                            </button>
                        </div>
                    </div>
                    <div class="card-body pt-2">
                        <div class="row">
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label for="filter-search" class="text-muted text-uppercase small font-weight-bold">
                                    Buscar
                                </label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white text-muted">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                    <input type="search" id="filter-search" class="form-control"
                                        placeholder="Nombre, temática o descripción">
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label for="filter-modalidad" class="text-muted text-uppercase small font-weight-bold">
                                    Modalidad
                                </label>
                                <select id="filter-modalidad" class="form-control form-control-sm" data-widget="select2"
                                    data-placeholder="Todas">
                                    <option value="">Todas</option>
                                    @foreach ($modalidades as $modalidad)
                                        <option value="{{ \Illuminate\Support\Str::slug($modalidad) }}">
                                            {{ $modalidad }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label for="filter-jornada" class="text-muted text-uppercase small font-weight-bold">
                                    Jornada
                                </label>
                                <select id="filter-jornada" class="form-control form-control-sm" data-widget="select2"
                                    data-placeholder="Todas">
                                    <option value="">Todas</option>
                                    @foreach ($jornadas as $jornada)
                                        <option value="{{ \Illuminate\Support\Str::slug($jornada) }}">
                                            {{ $jornada }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label for="filter-estado" class="text-muted text-uppercase small font-weight-bold">
                                    Estado
                                </label>
                                <select id="filter-estado" class="form-control form-control-sm" data-widget="select2"
                                    data-placeholder="Todos">
                                    <option value="">Todos</option>
                                    @foreach ($estados as $estado)
                                        <option value="{{ \Illuminate\Support\Str::slug($estado) }}">
                                            {{ $estado }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card programs-card border-0 shadow-sm">
                    <div class="card-body pb-4 pt-3">
                        <div class="row justify-content-center program-results g-3">
                            @foreach ($programas as $programa)
                                @include('complementarios.components.card-programas', [
                                    'programa' => $programa,
                                ])
                            @endforeach
                        </div>
                        <div class="text-center py-5 {{ $programas->isEmpty() ? '' : 'd-none' }}" id="empty-state"
                            data-empty="{{ $programas->isEmpty() ? 'true' : 'false' }}">
                            <img src="{{ asset('img/ui/empty-state-search.svg') }}" alt="Sin resultados" class="mb-4"
                                style="max-width: 220px;">
                            <h5 class="text-muted font-weight-bold" data-state-message>
                                {{ $programas->isEmpty() ? 'Aún no hay programas disponibles para este periodo.' : 'No encontramos programas con los filtros aplicados.' }}
                            </h5>
                            <p class="text-muted mb-3" data-state-description>
                                {{ $programas->isEmpty() ? 'Nuestro equipo actualizará la oferta próximamente. Vuelve más tarde o comunícate con tu centro de formación.' : 'Prueba ajustando los criterios de búsqueda o selecciona otra modalidad/jornada.' }}
                            </p>
                            <button class="btn btn-outline-primary btn-sm {{ $programas->isEmpty() ? 'd-none' : '' }}"
                                id="empty-reset">
                                <i class="fas fa-filter mr-1"></i> Restablecer filtros
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = Array.from(document.querySelectorAll('.program-card'));
            const searchInput = document.getElementById('filter-search');
            const modalidadSelect = document.getElementById('filter-modalidad');
            const jornadaSelect = document.getElementById('filter-jornada');
            const estadoSelect = document.getElementById('filter-estado');
            const countBadge = document.getElementById('program-count');
            const emptyState = document.getElementById('empty-state');
            const emptyMessage = emptyState?.querySelector('[data-state-message]');
            const emptyDescription = emptyState?.querySelector('[data-state-description]');
            const emptyResetButton = document.getElementById('empty-reset');
            let isInitialEmpty = emptyState?.dataset.empty === 'true';
            const resetButtons = document.querySelectorAll('#reset-filters, #empty-reset');

            const filterPrograms = () => {
                const searchTerm = (searchInput.value || '').trim().toLowerCase();
                const modalidad = modalidadSelect.value;
                const jornada = jornadaSelect.value;
                const estado = estadoSelect.value;

                let visible = 0;

                cards.forEach((card) => {
                    const matchesSearch = !searchTerm ||
                        card.dataset.search.includes(searchTerm) ||
                        card.dataset.name.includes(searchTerm);

                    const matchesModalidad = !modalidad || card.dataset.modalidad === modalidad;
                    const matchesJornada = !jornada || card.dataset.jornada === jornada;
                    const matchesEstado = !estado || card.dataset.estado === estado;

                    const shouldShow = matchesSearch && matchesModalidad && matchesJornada &&
                        matchesEstado;

                    card.classList.toggle('d-none', !shouldShow);

                    if (shouldShow) {
                        visible += 1;
                    }
                });

                countBadge.textContent = visible;

                if (!emptyState) {
                    return;
                }

                const showEmpty = visible === 0;
                emptyState.classList.toggle('d-none', !showEmpty);

                if (showEmpty && !isInitialEmpty) {
                    emptyMessage.textContent = 'No encontramos programas con los filtros aplicados.';
                    emptyDescription.textContent =
                        'Prueba ajustando los criterios de búsqueda o selecciona otra modalidad/jornada.';
                    emptyResetButton?.classList.remove('d-none');
                }

                if (showEmpty && isInitialEmpty) {
                    emptyResetButton?.classList.add('d-none');
                }

                if (!showEmpty && isInitialEmpty) {
                    emptyState.dataset.empty = 'false';
                    emptyResetButton?.classList.remove('d-none');
                    isInitialEmpty = false;
                }
            };

            resetButtons.forEach((button) => {
                button.addEventListener('click', function() {
                    searchInput.value = '';
                    modalidadSelect.value = '';
                    jornadaSelect.value = '';
                    estadoSelect.value = '';

                    if (window.$ && $.fn.select2) {
                        $(modalidadSelect).val('').trigger('change');
                        $(jornadaSelect).val('').trigger('change');
                        $(estadoSelect).val('').trigger('change');
                    }

                    filterPrograms();
                });
            });

            [searchInput, modalidadSelect, jornadaSelect, estadoSelect].forEach((element) => {
                element.addEventListener('input', filterPrograms);
                element.addEventListener('change', filterPrograms);
            });

            filterPrograms();
        });
    </script>
@endpush
