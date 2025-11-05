{{-- 
    Layout base para páginas del inventario usando @yield
    Props:
    - $title (string): Título de la página
    - $icon (string): Icono del título
    - $subtitle (string): Subtítulo opcional
    - $showSearch (bool): Mostrar barra de búsqueda
    - $searchPlaceholder (string): Placeholder de búsqueda
    - $createRoute (string): Ruta para crear nuevo elemento
    - $createText (string): Texto del botón crear
--}}
@extends('inventario.layouts.base')

@push('styles')
    @vite([
        'resources/css/inventario/shared/base.css',
        'resources/css/inventario/inventario_listas.css'
    ])
@endpush

@section('title', $title ?? 'Inventario')

@section('content_header')
    <div class="header-container">
        <div class="header-title">
            <h1><i class="{{ $icon ?? 'fas fa-list' }}"></i> {{ $title }}</h1>
            @if(isset($subtitle))
                <p class="subtitle">{{ $subtitle }}</p>
            @endif
        </div>
        
        @if(isset($showSearch) && $showSearch)
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           id="searchInput" 
                           class="search-input" 
                           placeholder="{{ $searchPlaceholder ?? 'Buscar...' }}" 
                           autocomplete="off">
                    <button type="button" class="search-clear" id="clearSearch" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif
        
        @if(isset($createRoute))
            <div class="header-buttons">
                <a href="{{ $createRoute }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>
                    {{ $createText ?? 'Nuevo' }}
                </a>
            </div>
        @endif
    </div>
@endsection

@section('main-content')
    {{-- Contenido principal usando @yield --}}
    @yield('page-content')
    
    {{-- Scripts específicos de la página --}}
    @yield('page-scripts')
@endsection

@push('scripts')
    @vite([
        'resources/js/inventario/inventario_listas.js',
        'resources/js/inventario/paginacion.js'
    ])
    @yield('additional-scripts')
@endpush

