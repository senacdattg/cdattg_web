<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('verificarLogin') }}" class="brand-link">
        <img src="{{ asset('dist/img/LogoSena.png') }}" alt="Logo del SENA" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">Registro Asistencia</span>
    </a>

    <div class="sidebar">
        @php
            $user = auth()->user();
            // Ejemplo: Definir permisos de forma centralizada, si se requiere.
            $canVerParametro = $user->can('VER PARAMETRO');
            $canVerTema = $user->can('VER TEMA');
            $canVerRegional = $user->can('VER REGIONAL');
            $canCrearRegional = $user->can('CREAR REGIONAL');
            // Permisos para Guías de Aprendizaje
            $canVerGuiaAprendizaje = $user->can('VER GUIA APRENDIZAJE');
            $canCrearGuiaAprendizaje = $user->can('CREAR GUIA APRENDIZAJE');
            $canEditarGuiaAprendizaje = $user->can('EDITAR GUIA APRENDIZAJE');
            $canEliminarGuiaAprendizaje = $user->can('ELIMINAR GUIA APRENDIZAJE');
            
            // Variables de permisos para uso en el sidebar
        @endphp

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">

                {{-- Panel de Control --}}
                @if ($canVerParametro || $canVerTema)
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <p>
                                Panel de Control
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('VER PARAMETRO')
                                <li class="nav-item">
                                    <a href="{{ route('parametro.index') }}" class="nav-link">
                                        <i class="fas fa-sliders-h"></i>
                                        <p>Parámetros</p>
                                    </a>
                                </li>
                            @endcan
                            @can('VER TEMA')
                                <li class="nav-item">
                                    <a href="{{ route('tema.index') }}" class="nav-link">
                                        <i class="fas fa-book"></i>
                                        <p>Temas</p>
                                    </a>
                                </li>
                            @endcan
                            @can('ASIGNAR PERMISOS')
                                <li class="nav-item">
                                    <a href="{{ route('permiso.index') }}" class="nav-link">
                                        <i class="fas fa-user-shield"></i>
                                        <p>Asignar Permisos</p>
                                    </a>
                                </li>
                            @endcan
                            @can('VER REGIONAL')
                                <li class="nav-item">
                                    <a href="{{ route('regional.index') }}" class="nav-link">
                                        <i class="fas fa-map-marked-alt"></i>
                                        <p>Regionales</p>
                                    </a>
                                </li>
                            @endcan
                            @can('VER CENTROS DE FORMACION')
                                <li class="nav-item">
                                    <a href="{{ route('centros.index') }}" class="nav-link">
                                        <i class="fas fa-school"></i>
                                        <p>Centros de Formación</p>
                                    </a>
                                </li>
                            @endcan
                            @can('VER PERSONA')
                                <li class="nav-item">
                                    <a href="{{ route('personas.index') }}" class="nav-link">
                                        <i class="fas fa-users"></i>
                                        <p>Personas</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif


                {{-- Administrar Sedes --}}
                @can('VER SEDE')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-building"></i>
                            <p>
                                Administrar Sedes
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('sede.index') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Sedes</p>
                                </a>
                            </li>
                            @can('CREAR SEDE')
                                <li class="nav-item">
                                    <a href="{{ route('sede.create') }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Crear Sede</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                {{-- Administrar Bloques --}}
                @can('VER BLOQUE')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-th-large"></i>
                            <p>
                                Administrar Bloques
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('bloque.index') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Bloques</p>
                                </a>
                            </li>
                            @can('CREAR BLOQUE')
                                <li class="nav-item">
                                    <a href="{{ route('bloque.create') }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Crear Bloque</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                {{-- Administrar Pisos --}}
                @can('VER PISO')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-layer-group"></i>
                            <p>
                                Administrar Pisos
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('piso.index') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Pisos</p>
                                </a>
                            </li>
                            @can('CREAR PISO')
                                <li class="nav-item">
                                    <a href="{{ route('piso.create') }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Crear Piso</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                {{-- Administrar Ambientes --}}
                @can('VER AMBIENTE')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-door-open"></i>
                            <p>
                                Administrar Ambientes
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('ambiente.index') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ambientes</p>
                                </a>
                            </li>
                            @can('CREAR AMBIENTE')
                                <li class="nav-item">
                                    <a href="{{ route('ambiente.create') }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Crear Ambiente</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                {{-- Administrar Personal (Instructores, Vigilantes, etc.) --}}
                @can('VER PROGRAMA DE CARACTERIZACION')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Administrar Personal
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('instructor.index') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Instructores</p>
                                </a>
                            </li>
                            @can('VER PROGRAMA DE CARACTERIZACION')
                                <li class="nav-item">
                                    <a href="{{ route('instructor.create') }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Crear Instructor</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('instructor.createImportarCSV') }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Importar CSV</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('instructor.create') }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Crear Vigilante</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                {{-- Administrar Jornadas --}}
                @can('VER PROGRAMA DE CARACTERIZACION')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>
                                Administrar Jornadas
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('jornada.index') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ver Jornadas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('jornada.create') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Crear Jornada</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                {{-- Administrar Programas --}}
                @can('VER PROGRAMA DE CARACTERIZACION')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-book"></i>
                            <p>
                                Administrar Programas
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="/programa/index" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ver Programas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/programa/create" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Crear Programa</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                {{-- Administrar Fichas --}}
                @can('VER PROGRAMA DE CARACTERIZACION')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>
                                Administrar Fichas
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('ficha.index') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ver Fichas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('fichaCaracterizacion.create') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Crear Fichas</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                {{-- Administrar Caracterización --}}
                @can('VER PROGRAMA DE CARACTERIZACION')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-edit"></i>
                            <p>
                                Admin Caracterización
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('caracter.index') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Ver Caracterización</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('caracterizacion.create') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Crear Caracterización</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                {{-- Administrar Asistencias para Administrador --}}
                @can('VER PROGRAMA DE CARACTERIZACION')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-clipboard-check"></i>
                            <p>
                                Administrar Asistencias
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('asistencia.index') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Consultas</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Consulta Personalizada</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                {{-- Administrar Asistencias para Instructores --}}
                @can('TOMAR ASISTENCIA')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-calendar-check"></i>
                            <p>
                                Tomar Asistencia
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('asistence.web') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Asistencia</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

                {{-- Administrar Carnets QR --}}
                @can('VER PROGRAMA DE CARACTERIZACION')
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-id-card"></i>
                            <p>
                                Administrar Carnet
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('carnet.index') }}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Crear Carnet</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endcan

            </ul>
        </nav>
    </div>
</aside>
