# Guía de Refactorización de Tablas

Esta guía explica cómo refactorizar las vistas que aún no usan el componente `x-data-table` para eliminar la duplicación de código en las tablas.

## Componentes Disponibles

### 1. Data Table (`x-data-table`)
Componente principal para tablas con búsqueda y paginación.

### 2. Table Filters (`x-table-filters`)
Componente para filtros de búsqueda colapsibles.

### 3. Bulk Actions (`x-bulk-actions`)
Componente para acciones masivas en tablas.

## Patrón de Refactorización

### Antes (Código Repetido)
```blade
<div class="card shadow-sm mb-4 no-hover">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lista de Registros</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-borderless table-striped mb-0">
                <thead class="thead-light">
                    <tr>
                        <th class="px-4 py-3" style="width: 5%">#</th>
                        <th class="px-4 py-3" style="width: 25%">Nombre</th>
                        <!-- ... más columnas ... -->
                    </tr>
                </thead>
                <tbody>
                    @forelse ($registros as $registro)
                        <tr>
                            <td class="px-4">{{ $loop->iteration }}</td>
                            <td class="px-4">{{ $registro->nombre }}</td>
                            <!-- ... más celdas ... -->
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="{{ asset('img/no-data.svg') }}" alt="No data" style="width: 120px" class="mb-3">
                                <p class="text-muted">No hay registros</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        <div class="float-right">
            {{ $registros->links() }}
        </div>
    </div>
</div>
```

### Después (Con Componente)
```blade
<x-data-table 
    title="Lista de Registros"
    searchable="true"
    searchAction="{{ route('registros.index') }}"
    searchPlaceholder="Buscar..."
    searchValue="{{ request('search') }}"
    :columns="[
        ['label' => '#', 'width' => '5%'],
        ['label' => 'Nombre', 'width' => '25%'],
        ['label' => 'Acciones', 'width' => '20%', 'class' => 'text-center']
    ]"
    :pagination="$registros->links()"
>
    @forelse ($registros as $registro)
        <tr>
            <td class="px-4">{{ $loop->iteration }}</td>
            <td class="px-4">{{ $registro->nombre }}</td>
            <!-- ... más celdas ... -->
        </tr>
    @empty
        <x-empty-state 
            message="No hay registros disponibles"
            image="img/no-data.svg"
            :colspan="3"
        />
    @endforelse
</x-data-table>
```

## Vistas Refactorizadas

### ✅ Completadas
- `resources/views/Instructores/index.blade.php`
- `resources/views/fichas/index.blade.php`
- `resources/views/aprendices/index.blade.php`
- `resources/views/competencias/index.blade.php`
- `resources/views/guias_aprendizaje/index.blade.php`

### 🔄 Pendientes de Refactorización
- `resources/views/programas/index.blade.php`
- `resources/views/resultados_aprendizaje/index.blade.php`
- `resources/views/red_conocimiento/index.blade.php`
- `resources/views/parametros/index.blade.php`
- `resources/views/municipios/index.blade.php`
- `resources/views/temas/index.blade.php`
- `resources/views/regional/index.blade.php`
- `resources/views/personas/index.blade.php`
- `resources/views/permisos/index.blade.php`
- `resources/views/sede/index.blade.php`
- `resources/views/piso/index.blade.php`
- `resources/views/entradaSalidas/index.blade.php`
- `resources/views/caracterizacion/index.blade.php`
- `resources/views/bloque/index.blade.php`
- `resources/views/ambiente/index.blade.php`

## Pasos para Refactorizar una Vista

### 1. Identificar la Estructura de la Tabla
- Buscar `<div class="table-responsive">`
- Identificar las columnas del `<thead>`
- Localizar el `@forelse` y `@endforelse`

### 2. Extraer Configuración de Columnas
```php
:columns="[
    ['label' => '#', 'width' => '5%'],
    ['label' => 'Nombre', 'width' => '25%'],
    ['label' => 'Estado', 'width' => '15%'],
    ['label' => 'Acciones', 'width' => '20%', 'class' => 'text-center']
]"
```

### 3. Configurar Búsqueda (si aplica)
```php
searchable="true"
searchAction="{{ route('registros.index') }}"
searchPlaceholder="Buscar..."
searchValue="{{ request('search') }}"
```

### 4. Configurar Paginación
```php
:pagination="$registros->links()"
```

### 5. Reemplazar la Estructura HTML
- Reemplazar desde `<div class="card shadow-sm mb-4 no-hover">` hasta el cierre
- Mantener solo el contenido del `@forelse`
- Usar `<x-empty-state>` para el estado vacío

### 6. Agregar Filtros (si aplica)
```blade
<x-table-filters 
    action="{{ route('registros.index') }}"
    method="GET"
    title="Filtros de Búsqueda"
    icon="fa-filter"
>
    <!-- Contenido de los filtros -->
</x-table-filters>
```

## Beneficios de la Refactorización

1. **Reducción de código**: ~70% menos líneas de HTML repetido
2. **Consistencia**: Todas las tablas tienen el mismo comportamiento
3. **Mantenibilidad**: Cambios centralizados en componentes
4. **Funcionalidades**: Búsqueda, paginación y filtros automáticos
5. **Accesibilidad**: Estructura HTML semántica consistente

## Consideraciones Especiales

### Tablas con Filtros Complejos
Si la vista tiene filtros complejos, usar el componente `x-table-filters` antes de la tabla.

### Tablas con Acciones Masivas
Para tablas que necesiten selección múltiple, usar el componente `x-bulk-actions`.

### Tablas con JavaScript Personalizado
Mantener el JavaScript específico de la vista en la sección `@section('js')`.

### Estados Vacíos Personalizados
Usar el componente `x-empty-state` con mensajes e iconos específicos.

## Script de Automatización

Para acelerar el proceso, se puede crear un script que:
1. Identifique automáticamente las tablas
2. Extraiga la configuración de columnas
3. Genere el código del componente
4. Reemplace la estructura HTML

## Verificación Post-Refactorización

1. ✅ La tabla se renderiza correctamente
2. ✅ La búsqueda funciona (si está habilitada)
3. ✅ La paginación funciona
4. ✅ Los filtros funcionan (si están presentes)
5. ✅ El estado vacío se muestra correctamente
6. ✅ Los botones de acción funcionan
7. ✅ El diseño es consistente con otras vistas
