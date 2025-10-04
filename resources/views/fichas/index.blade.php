@extends('adminlte::page')

@section('title', 'Fichas de Caracterización')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1><i class="fas fa-file-alt"></i> Fichas de Caracterización</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                    <a href="{{ route('home') }}">Inicio</a>
                </li>
                <li class="breadcrumb-item active">Fichas de Caracterización</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <!-- Alertas -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Botón de crear nueva ficha -->
    @can('CREAR PROGRAMA DE CARACTERIZACION')
        <div class="row mb-3">
            <div class="col-12">
                <a href="{{ route('fichaCaracterizacion.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Ficha de Caracterización
                </a>
            </div>
        </div>
    @endcan

    <!-- Card principal -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Lista de Fichas de Caracterización
            </h3>
            
            <!-- Filtros avanzados -->
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>

        <div class="card-body">
            <!-- Filtros básicos -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <form method="GET" action="{{ route('fichaCaracterizacion.index') }}" id="searchForm" class="form-inline">
                        <div class="form-group mr-3">
                            <label for="search" class="sr-only">Buscar</label>
                            <input type="text" name="search" id="searchInput" class="form-control" placeholder="Buscar por ficha, programa, instructor..." 
                                   value="{{ request()->get('search') }}">
                        </div>
                        
                        <div class="form-group mr-3">
                            <label for="estado" class="sr-only">Estado</label>
                            <select name="estado" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="1" {{ request()->get('estado') == '1' ? 'selected' : '' }}>Activas</option>
                                <option value="0" {{ request()->get('estado') == '0' ? 'selected' : '' }}>Inactivas</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        
                        <button type="button" class="btn btn-info mr-2" data-toggle="collapse" data-target="#filtrosAvanzados">
                            <i class="fas fa-filter"></i> Filtros Avanzados
                        </button>
                        
                        <a href="{{ route('fichaCaracterizacion.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Limpiar
                        </a>
                    </form>
                </div>
            </div>

            <!-- Filtros avanzados -->
            <div id="filtrosAvanzados" class="collapse mb-3">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-filter"></i> Filtros Avanzados
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('ficha.search') }}" id="advancedSearchForm">
                            <div class="row">
                                <!-- Columna 1 -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="programa_id">Programa de Formación</label>
                                        <select name="programa_id" id="programa_id" class="form-control select2">
                                            <option value="">Todos los programas</option>
                                            @if(isset($programas))
                                                @foreach($programas as $programa)
                                                    <option value="{{ $programa->id }}" 
                                                            {{ request()->get('programa_id') == $programa->id ? 'selected' : '' }}>
                                                        {{ $programa->nombre }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="instructor_id">Instructor</label>
                                        <select name="instructor_id" id="instructor_id" class="form-control select2">
                                            <option value="">Todos los instructores</option>
                                            @if(isset($instructores))
                                                @foreach($instructores as $instructor)
                                                    <option value="{{ $instructor->id }}" 
                                                            {{ request()->get('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                                        {{ $instructor->persona->primer_nombre }} {{ $instructor->persona->primer_apellido }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="ambiente_id">Ambiente</label>
                                        <select name="ambiente_id" id="ambiente_id" class="form-control select2">
                                            <option value="">Todos los ambientes</option>
                                            @if(isset($ambientes))
                                                @foreach($ambientes as $ambiente)
                                                    <option value="{{ $ambiente->id }}" 
                                                            {{ request()->get('ambiente_id') == $ambiente->id ? 'selected' : '' }}>
                                                        {{ $ambiente->title }} - {{ $ambiente->piso->bloque->bloque ?? 'N/A' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="sede_id">Sede</label>
                                        <select name="sede_id" id="sede_id" class="form-control select2">
                                            <option value="">Todas las sedes</option>
                                            @if(isset($sedes))
                                                @foreach($sedes as $sede)
                                                    <option value="{{ $sede->id }}" 
                                                            {{ request()->get('sede_id') == $sede->id ? 'selected' : '' }}>
                                                        {{ $sede->nombre }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <!-- Columna 2 -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="modalidad_id">Modalidad de Formación</label>
                                        <select name="modalidad_id" id="modalidad_id" class="form-control select2">
                                            <option value="">Todas las modalidades</option>
                                            @if(isset($modalidades))
                                                @foreach($modalidades as $modalidad)
                                                    <option value="{{ $modalidad->id }}" 
                                                            {{ request()->get('modalidad_id') == $modalidad->id ? 'selected' : '' }}>
                                                        {{ $modalidad->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="jornada_id">Jornada</label>
                                        <select name="jornada_id" id="jornada_id" class="form-control select2">
                                            <option value="">Todas las jornadas</option>
                                            @if(isset($jornadas))
                                                @foreach($jornadas as $jornada)
                                                    <option value="{{ $jornada->id }}" 
                                                            {{ request()->get('jornada_id') == $jornada->id ? 'selected' : '' }}>
                                                        {{ $jornada->jornada }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="con_aprendices">Aprendices Asignados</label>
                                        <select name="con_aprendices" id="con_aprendices" class="form-control">
                                            <option value="">Todos</option>
                                            <option value="1" {{ request()->get('con_aprendices') == '1' ? 'selected' : '' }}>Con aprendices</option>
                                            <option value="0" {{ request()->get('con_aprendices') == '0' ? 'selected' : '' }}>Sin aprendices</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="sort_by">Ordenar por</label>
                                        <select name="sort_by" id="sort_by" class="form-control">
                                            <option value="id" {{ request()->get('sort_by') == 'id' ? 'selected' : '' }}>ID</option>
                                            <option value="ficha" {{ request()->get('sort_by') == 'ficha' ? 'selected' : '' }}>Número de Ficha</option>
                                            <option value="fecha_inicio" {{ request()->get('sort_by') == 'fecha_inicio' ? 'selected' : '' }}>Fecha de Inicio</option>
                                            <option value="fecha_fin" {{ request()->get('sort_by') == 'fecha_fin' ? 'selected' : '' }}>Fecha de Fin</option>
                                            <option value="total_horas" {{ request()->get('sort_by') == 'total_horas' ? 'selected' : '' }}>Total Horas</option>
                                            <option value="created_at" {{ request()->get('sort_by') == 'created_at' ? 'selected' : '' }}>Fecha de Creación</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Filtros de fecha -->
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-calendar"></i> Filtros por Fecha de Inicio</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fecha_inicio_desde">Desde</label>
                                                <input type="date" name="fecha_inicio_desde" id="fecha_inicio_desde" 
                                                       class="form-control" value="{{ request()->get('fecha_inicio_desde') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fecha_inicio_hasta">Hasta</label>
                                                <input type="date" name="fecha_inicio_hasta" id="fecha_inicio_hasta" 
                                                       class="form-control" value="{{ request()->get('fecha_inicio_hasta') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h5><i class="fas fa-calendar"></i> Filtros por Fecha de Fin</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fecha_fin_desde">Desde</label>
                                                <input type="date" name="fecha_fin_desde" id="fecha_fin_desde" 
                                                       class="form-control" value="{{ request()->get('fecha_fin_desde') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fecha_fin_hasta">Hasta</label>
                                                <input type="date" name="fecha_fin_hasta" id="fecha_fin_hasta" 
                                                       class="form-control" value="{{ request()->get('fecha_fin_hasta') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-search"></i> Buscar Avanzado
                                    </button>
                                    <button type="button" class="btn btn-warning" onclick="limpiarFiltrosAvanzados()">
                                        <i class="fas fa-broom"></i> Limpiar Filtros
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabla responsiva -->
            <div class="table-responsive" id="fichasTable">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="12%">Número de Ficha</th>
                            <th width="20%">Programa de Formación</th>
                            <th width="15%">Instructor</th>
                            <th width="12%">Sede</th>
                            <th width="10%">Modalidad</th>
                            <th width="8%">Estado</th>
                            <th width="8%">Aprendices</th>
                            <th width="10%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="fichasTableBody">
                        @forelse ($fichas as $ficha)
                            <tr>
                                <td>{{ $ficha->id }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $ficha->ficha }}</span>
                                </td>
                                <td>
                                    <strong>{{ $ficha->programaFormacion->nombre ?? 'N/A' }}</strong>
                                    @if($ficha->programaFormacion->codigo ?? false)
                                        <br><small class="text-muted">{{ $ficha->programaFormacion->codigo }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($ficha->instructor && $ficha->instructor->persona)
                                        {{ $ficha->instructor->persona->primer_nombre }} {{ $ficha->instructor->persona->primer_apellido }}
                                    @else
                                        <span class="text-muted">Sin asignar</span>
                                    @endif
                                </td>
                                <td>{{ $ficha->sede->nombre ?? 'N/A' }}</td>
                                <td>{{ $ficha->modalidadFormacion->name ?? 'N/A' }}</td>
                                <td>
                                    @if($ficha->status)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Activa
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times"></i> Inactiva
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $aprendicesCount = $ficha->aprendices->count() ?? 0;
                                    @endphp
                                    @if($aprendicesCount > 0)
                                        <span class="badge badge-primary">{{ $aprendicesCount }}</span>
                                    @else
                                        <span class="badge badge-secondary">0</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('VER PROGRAMA DE CARACTERIZACION')
                                            <a href="{{ route('fichaCaracterizacion.show', $ficha->id) }}" 
                                               class="btn btn-info btn-sm" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endcan

                                        @can('EDITAR PROGRAMA DE CARACTERIZACION')
                                            <a href="{{ route('fichaCaracterizacion.edit', $ficha->id) }}" 
                                               class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan

                                        @can('ELIMINAR PROGRAMA DE CARACTERIZACION')
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="confirmarEliminacion('{{ $ficha->ficha }}', '{{ route('fichaCaracterizacion.destroy', $ficha->id) }}')"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> No se encontraron fichas de caracterización.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($fichas->hasPages())
            <div class="card-footer">
                <div class="row">
                    <div class="col-sm-5">
                        <div class="dataTables_info">
                            Mostrando {{ $fichas->firstItem() }} a {{ $fichas->lastItem() }} 
                            de {{ $fichas->total() }} resultados
                        </div>
                    </div>
                    <div class="col-sm-7">
                        {{ $fichas->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let searchTimeout;
    let currentPage = 1;

    function confirmarEliminacion(nombre, url) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: `¿Deseas eliminar la ficha "${nombre}"? Esta acción no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear formulario para enviar la petición DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function limpiarFiltrosAvanzados() {
        document.getElementById('advancedSearchForm').reset();
        // Limpiar también los valores de los selects
        $('.select2').val(null).trigger('change');
    }

    function realizarBusquedaAjax() {
        const formData = new FormData(document.getElementById('searchForm'));
        const searchParams = new URLSearchParams();
        
        // Agregar parámetros del formulario básico
        for (let [key, value] of formData.entries()) {
            if (value.trim() !== '') {
                searchParams.append(key, value);
            }
        }

        // Mostrar indicador de carga
        mostrarIndicadorCarga();

        fetch('{{ route("ficha.search.ajax") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: searchParams.toString()
        })
        .then(response => response.json())
        .then(data => {
            ocultarIndicadorCarga();
            
            if (data.success) {
                actualizarTabla(data.data.fichas);
                actualizarPaginacion(data.data.pagination);
            } else {
                mostrarError('Error en la búsqueda: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            ocultarIndicadorCarga();
            console.error('Error:', error);
            mostrarError('Error de conexión. Por favor, intente nuevamente.');
        });
    }

    function actualizarTabla(fichas) {
        const tbody = document.getElementById('fichasTableBody');
        
        if (fichas.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No se encontraron fichas de caracterización.
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = fichas.map(ficha => `
            <tr>
                <td>${ficha.id}</td>
                <td><span class="badge badge-info">${ficha.ficha}</span></td>
                <td>
                    <strong>${ficha.programa_formacion?.nombre || 'N/A'}</strong>
                    ${ficha.programa_formacion?.codigo ? `<br><small class="text-muted">${ficha.programa_formacion.codigo}</small>` : ''}
                </td>
                <td>
                    ${ficha.instructor_principal?.persona ? 
                        `${ficha.instructor_principal.persona.primer_nombre} ${ficha.instructor_principal.persona.primer_apellido}` : 
                        '<span class="text-muted">Sin asignar</span>'
                    }
                </td>
                <td>${ficha.sede?.sede || 'N/A'}</td>
                <td>${ficha.modalidad_formacion?.nombre || 'N/A'}</td>
                <td>
                    ${ficha.status ? 
                        '<span class="badge badge-success"><i class="fas fa-check"></i> Activa</span>' :
                        '<span class="badge badge-danger"><i class="fas fa-times"></i> Inactiva</span>'
                    }
                </td>
                <td>
                    ${ficha.aprendices_count > 0 ? 
                        `<span class="badge badge-primary">${ficha.aprendices_count}</span>` :
                        '<span class="badge badge-secondary">0</span>'
                    }
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <a href="/fichaCaracterizacion/${ficha.id}" class="btn btn-info btn-sm" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/fichaCaracterizacion/${ficha.id}/edit" class="btn btn-warning btn-sm" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" 
                                onclick="confirmarEliminacion('${ficha.ficha}', '/fichaCaracterizacion/${ficha.id}')"
                                title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function actualizarPaginacion(pagination) {
        // Implementar paginación AJAX si es necesario
        console.log('Paginación:', pagination);
    }

    function mostrarIndicadorCarga() {
        const tbody = document.getElementById('fichasTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-2">Buscando fichas...</p>
                </td>
            </tr>
        `;
    }

    function ocultarIndicadorCarga() {
        // El indicador se oculta cuando se actualiza la tabla
    }

    function mostrarError(mensaje) {
        const tbody = document.getElementById('fichasTableBody');
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> ${mensaje}
                    </div>
                </td>
            </tr>
        `;
    }

    $(document).ready(function() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        // Inicializar Select2 para los filtros avanzados
        $('.select2').select2({
            placeholder: 'Seleccionar...',
            allowClear: true,
            width: '100%'
        });

        // Búsqueda en tiempo real con debounce
        $('#searchInput').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                if ($('#searchInput').val().length >= 3 || $('#searchInput').val().length === 0) {
                    realizarBusquedaAjax();
                }
            }, 500); // Esperar 500ms después del último carácter
        });

        // Envío del formulario de búsqueda básica
        $('#searchForm').on('submit', function(e) {
            e.preventDefault();
            realizarBusquedaAjax();
        });

        // Envío del formulario de búsqueda avanzada
        $('#advancedSearchForm').on('submit', function(e) {
            e.preventDefault();
            
            // Combinar datos de ambos formularios
            const basicForm = new FormData(document.getElementById('searchForm'));
            const advancedForm = new FormData(this);
            const searchParams = new URLSearchParams();
            
            // Agregar parámetros del formulario básico
            for (let [key, value] of basicForm.entries()) {
                if (value.trim() !== '') {
                    searchParams.append(key, value);
                }
            }
            
            // Agregar parámetros del formulario avanzado
            for (let [key, value] of advancedForm.entries()) {
                if (value.trim() !== '') {
                    searchParams.append(key, value);
                }
            }

            // Mostrar indicador de carga
            mostrarIndicadorCarga();

            fetch('{{ route("ficha.search.ajax") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: searchParams.toString()
            })
            .then(response => response.json())
            .then(data => {
                ocultarIndicadorCarga();
                
                if (data.success) {
                    actualizarTabla(data.data.fichas);
                    actualizarPaginacion(data.data.pagination);
                    // Colapsar los filtros avanzados después de la búsqueda
                    $('#filtrosAvanzados').collapse('hide');
                } else {
                    mostrarError('Error en la búsqueda: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                ocultarIndicadorCarga();
                console.error('Error:', error);
                mostrarError('Error de conexión. Por favor, intente nuevamente.');
            });
        });
    });
</script>
@endsection