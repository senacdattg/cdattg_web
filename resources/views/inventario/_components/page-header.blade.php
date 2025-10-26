{{-- 
    Componente: Header con búsqueda y acciones
    Props:
    - $title (string): Título de la página
    - $subtitle (string): Subtítulo opcional
    - $createRoute (string): Ruta para crear nuevo elemento
    - $createText (string): Texto del botón crear
    - $showSearch (bool): Mostrar barra de búsqueda
    - $showCart (bool): Mostrar icono del carrito
--}}
@props([
    'title',
    'subtitle' => null,
    'createRoute' => null,
    'createText' => 'Nuevo',
    'showSearch' => true,
    'showCart' => false,
    'searchPlaceholder' => 'Buscar...',
    'icon' => 'fas fa-list'
])

<div class="header-container">
    <div class="header-title">
        <h1><i class="{{ $icon }}"></i> {{ $title }}</h1>
        @if($subtitle)
            <p class="subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    
    @if($showSearch || $showCart)
        <div class="search-container">
            @if($showSearch)
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" 
                           id="searchInput" 
                           class="search-input" 
                           placeholder="{{ $searchPlaceholder }}" 
                           autocomplete="off">
                    <button type="button" class="search-clear" id="clearSearch" style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
            
            @if($showCart)
                <a href="{{ route('inventario.carrito.index') }}" 
                   class="btn-lg carrito-icon-btn" 
                   title="Ver Carrito">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge" id="cartCount" style="display: none;">0</span>
                </a>
            @endif
        </div>
    @endif
    
    @if($createRoute)
        <div class="header-buttons">
            <a href="{{ $createRoute }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus"></i> {{ $createText }}
            </a>
        </div>
    @endif
</div>
