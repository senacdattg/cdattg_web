@props([
    'url' => '',
    'title' => 'Crear',
    'icon' => 'fa-plus-circle',
    'permission' => '',
    'class' => 'text-decoration-none'
])

@if($permission)
    @can($permission)
        <a href="{{ $url }}" class="{{ $class }}">
            <div class="card shadow-sm mb-4 hover-card">
                <div class="card-header bg-white py-3 d-flex align-items-center">
                    <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
                        <i class="fas {{ $icon }} mr-2"></i> {{ $title }}
                    </h5>
                </div>
            </div>
        </a>
    @endcan
@else
    <a href="{{ $url }}" class="{{ $class }}">
        <div class="card shadow-sm mb-4 hover-card">
            <div class="card-header bg-white py-3 d-flex align-items-center">
                <h5 class="card-title m-0 font-weight-bold text-primary d-flex align-items-center flex-grow-1">
                    <i class="fas {{ $icon }} mr-2"></i> {{ $title }}
                </h5>
            </div>
        </div>
    </a>
@endif
