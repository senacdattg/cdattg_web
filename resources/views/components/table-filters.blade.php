@props([
    'action' => '',
    'method' => 'GET',
    'collapsible' => true,
    'collapsed' => false,
    'title' => 'Filtros de Búsqueda',
    'icon' => 'fa-filter'
])

<div class="card shadow-sm mb-4 no-hover">
    <div class="card-header bg-white py-3 d-flex align-items-center">
        <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
            <i class="fas {{ $icon }} mr-2"></i> {{ $title }}
        </h5>
        @if($collapsible)
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="collapse"
                data-target="#filtrosForm" aria-expanded="{{ $collapsed ? 'false' : 'true' }}">
                <i class="fas fa-chevron-{{ $collapsed ? 'down' : 'up' }}"></i>
            </button>
        @endif
    </div>

    <div class="collapse {{ $collapsed ? '' : 'show' }}" id="filtrosForm">
        <div class="card-body">
            <form action="{{ $action }}" method="{{ $method }}">
                {{ $slot }}
            </form>
        </div>
    </div>
</div>
