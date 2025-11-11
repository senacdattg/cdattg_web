@extends('adminlte::page')

@section('css')
    @vite(['resources/css/parametros.css'])
@endsection

@section('content_header')
    <x-page-header
        icon="fa-user-check"
        title="Asignaciones de instructores"
        subtitle="Gestión de asignaciones para fichas de caracterización"
        :breadcrumb="[
            [
                'label' => 'Inicio',
                'url' => route('verificarLogin'),
                'icon' => 'fa-home',
            ],
            [
                'label' => 'Asignaciones',
                'icon' => 'fa-user-check',
                'active' => true,
            ],
        ]"
    />
@endsection

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-session-alerts />

                    <x-create-card
                        url="{{ route('asignaciones.instructores.create') }}"
                        title="Registrar nueva asignación"
                        icon="fa-plus-circle"
                    />

                    <x-data-table
                        title="Listado de asignaciones"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Ficha', 'width' => '10%'],
                            ['label' => 'Programa', 'width' => '20%'],
                            ['label' => 'Instructor', 'width' => '20%'],
                            ['label' => 'Competencia', 'width' => '20%'],
                            ['label' => 'Resultados', 'width' => '15%'],
                            ['label' => 'Acciones', 'width' => '10%', 'class' => 'text-center'],
                        ]"
                        :pagination="$asignaciones->links()"
                        searchable="false"
                    >
                        @forelse ($asignaciones as $index => $asignacion)
                            <tr>
                                <td class="px-4">
                                    {{ ($asignaciones->firstItem() ?? 0) + $index }}
                                </td>
                                <td class="px-4 font-weight-medium">
                                    {{ $asignacion->ficha->ficha ?? 'N/A' }}
                                </td>
                                <td class="px-4">
                                    {{ $asignacion->ficha->programaFormacion->nombre ?? 'Sin programa asociado' }}
                                </td>
                                <td class="px-4">
                                    @if ($asignacion->instructor && $asignacion->instructor->persona)
                                        <div class="d-flex flex-column">
                                            <span class="font-weight-bold">
                                                {{ $asignacion->instructor->persona->primer_nombre }}
                                                {{ $asignacion->instructor->persona->primer_apellido }}
                                            </span>
                                            <small class="text-muted">
                                                {{ $asignacion->instructor->persona->numero_documento }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted">Instructor no disponible</span>
                                    @endif
                                </td>
                                <td class="px-4">
                                    <div class="d-flex flex-column">
                                        <span class="badge badge-info align-self-start mb-1">
                                            {{ $asignacion->competencia->codigo ?? 'N/A' }}
                                        </span>
                                        <span>
                                            {{ $asignacion->competencia->nombre ?? 'Competencia no disponible' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4">
                                    @if ($asignacion->resultadosAprendizaje->isEmpty())
                                        <span class="text-muted">Sin resultados asignados</span>
                                    @else
                                        <ul class="list-unstyled mb-0">
                                            @foreach ($asignacion->resultadosAprendizaje as $resultado)
                                                <li>
                                                    <span class="badge badge-secondary">
                                                        {{ $resultado->codigo }}
                                                    </span>
                                                    {{ $resultado->nombre }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <x-action-buttons
                                        :show="true"
                                        :edit="true"
                                        :delete="false"
                                        showUrl="{{ route('asignaciones.instructores.show', $asignacion) }}"
                                        editUrl="{{ route('asignaciones.instructores.edit', $asignacion) }}"
                                        showPermission=""
                                        editPermission=""
                                        deletePermission=""
                                    />
                                </td>
                            </tr>
                        @empty
                            <x-empty-state
                                colspan="7"
                                message="No hay asignaciones registradas"
                            />
                        @endforelse
                    </x-data-table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layout.footer')
@endsection
