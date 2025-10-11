{{--
    Componente: Card de Estadística
    Uso:
    <x-cards.stat 
        title="Total Asignaciones" 
        value="2" 
        icon="fas fa-users" 
        color="primary" />
--}}

@props([
    'title' => '',
    'value' => '0',
    'icon' => 'fas fa-chart-bar',
    'color' => 'primary',
    'size' => 'col-md-3'
])

<div class="{{ $size }}">
    <div class="card border-left-{{ $color }} shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-{{ $color }} text-uppercase mb-1">
                        {{ $title }}
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $value }}
                    </div>
                </div>
                <div class="col-auto">
                    <i class="{{ $icon }} fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>
