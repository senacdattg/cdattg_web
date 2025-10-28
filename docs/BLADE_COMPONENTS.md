# Componentes Blade Reutilizables

Este documento describe los componentes Blade creados para eliminar la duplicación de código en las vistas y mejorar la mantenibilidad del sistema.

## Componentes Disponibles

### 1. Page Header (`x-page-header`)

Componente para headers de página con icono, título, subtítulo y breadcrumb.

**Uso:**
```blade
<x-page-header 
    icon="fa-chalkboard-teacher" 
    title="Instructores"
    subtitle="Gestión de instructores del sistema"
    :breadcrumb="[
        ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
        ['label' => 'Instructores', 'active' => true, 'icon' => 'fa-chalkboard-teacher']
    ]"
/>
```

**Parámetros:**
- `icon`: Icono de FontAwesome (default: 'fa-home')
- `title`: Título de la página (default: 'Página')
- `subtitle`: Subtítulo descriptivo (default: 'Descripción de la página')
- `breadcrumb`: Array de elementos del breadcrumb

### 2. Breadcrumb (`x-breadcrumb`)

Componente para navegación breadcrumb.

**Uso:**
```blade
<x-breadcrumb :items="[
    ['label' => 'Inicio', 'url' => route('home'), 'icon' => 'fa-home'],
    ['label' => 'Página Actual', 'active' => true, 'icon' => 'fa-page']
]" />
```

### 3. Data Table (`x-data-table`)

Componente para tablas de datos con búsqueda y paginación.

**Uso:**
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
    <!-- Contenido de las filas de la tabla -->
</x-data-table>
```

### 4. Session Alerts (`x-session-alerts`)

Componente para mostrar alertas de sesión (success, error, warning, info).

**Uso:**
```blade
<x-session-alerts />
```

**Parámetros opcionales:**
- `successKey`: Clave de sesión para success (default: 'success')
- `errorKey`: Clave de sesión para error (default: 'error')
- `warningKey`: Clave de sesión para warning (default: 'warning')
- `infoKey`: Clave de sesión para info (default: 'info')

### 5. Action Buttons (`x-action-buttons`)

Componente para botones de acción comunes (ver, editar, eliminar).

**Uso:**
```blade
<x-action-buttons 
    :show="true"
    :edit="true"
    :delete="true"
    showUrl="{{ route('registro.show', $id) }}"
    editUrl="{{ route('registro.edit', $id) }}"
    deleteUrl="{{ route('registro.destroy', $id) }}"
    showPermission="VER REGISTRO"
    editPermission="EDITAR REGISTRO"
    deletePermission="ELIMINAR REGISTRO"
    :custom="[
        [
            'url' => route('registro.accion', $id),
            'title' => 'Acción personalizada',
            'icon' => 'fas fa-cog',
            'color' => 'text-primary',
            'permission' => 'PERMISO PERSONALIZADO'
        ]
    ]"
/>
```

### 6. Status Badge (`x-status-badge`)

Componente para mostrar el estado de un registro.

**Uso:**
```blade
<x-status-badge :status="$registro->activo" />
```

**Parámetros:**
- `status`: Boolean que indica si está activo
- `activeText`: Texto para estado activo (default: 'Activo')
- `inactiveText`: Texto para estado inactivo (default: 'Inactivo')
- `activeClass`: Clases CSS para estado activo
- `inactiveClass`: Clases CSS para estado inactivo
- `showIcon`: Mostrar icono (default: true)

### 7. Back Button (`x-back-button`)

Componente para botón de navegación "Volver".

**Uso:**
```blade
<x-back-button 
    url="{{ route('registros.index') }}"
    text="Volver a la lista"
    icon="fa-arrow-left"
    class="btn-outline-secondary btn-sm mb-3"
/>
```

### 8. Create Card (`x-create-card`)

Componente para tarjeta de creación de registros.

**Uso:**
```blade
<x-create-card 
    url="{{ route('registro.create') }}"
    title="Crear Registro"
    icon="fa-plus-circle"
    permission="CREAR REGISTRO"
/>
```

### 9. Empty State (`x-empty-state`)

Componente para mostrar estado vacío en tablas.

**Uso:**
```blade
<x-empty-state 
    message="No hay registros disponibles"
    icon="fa-inbox"
    image="img/no-data.svg"
    :colspan="6"
/>
```

## Beneficios de Usar Componentes

1. **Reducción de duplicación**: El mismo código HTML no se repite en múltiples vistas
2. **Mantenibilidad**: Cambios en un componente se reflejan en todas las vistas que lo usan
3. **Consistencia**: Todas las vistas tienen el mismo aspecto y comportamiento
4. **Reutilización**: Los componentes pueden usarse en diferentes contextos
5. **Legibilidad**: Las vistas son más limpias y fáciles de entender

## Ejemplo de Refactorización

**Antes:**
```blade
@section('content_header')
<section class="content-header dashboard-header py-4">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-12 col-md-6 d-flex align-items-center">
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center mr-3"
                    style="width: 48px; height: 48px;">
                    <i class="fas fa-chalkboard-teacher text-white fa-lg"></i>
                </div>
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Instructores</h1>
                    <p class="text-muted mb-0 font-weight-light">Gestión de instructores del sistema</p>
                </div>
            </div>
            <div class="col-sm-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 justify-content-end">
                        <li class="breadcrumb-item">
                            <a href="{{ route('verificarLogin') }}" class="link_right_header">
                                <i class="fas fa-home"></i> Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <i class="fas fa-chalkboard-teacher"></i> Instructores
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>
@endsection
```

**Después:**
```blade
@section('content_header')
    <x-page-header 
        icon="fa-chalkboard-teacher" 
        title="Instructores"
        subtitle="Gestión de instructores del sistema"
        :breadcrumb="[
            ['label' => 'Inicio', 'url' => route('verificarLogin'), 'icon' => 'fa-home'],
            ['label' => 'Instructores', 'active' => true, 'icon' => 'fa-chalkboard-teacher']
        ]"
    />
@endsection
```

## Migración de Vistas Existentes

Para migrar una vista existente:

1. Identificar código HTML repetido
2. Reemplazar con el componente correspondiente
3. Pasar los parámetros necesarios
4. Probar la funcionalidad
5. Verificar que el diseño se mantenga consistente

## Consideraciones

- Los componentes están diseñados para ser flexibles y reutilizables
- Mantienen la compatibilidad con el sistema de permisos existente
- Siguen las convenciones de diseño del sistema
- Son fáciles de personalizar mediante parámetros
