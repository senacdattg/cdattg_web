@extends('adminlte::page')

@section('css')
    <link href="{{ asset('css/parametros.css') }}" rel="stylesheet">
@endsection

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

@section('content')
    <section class="content mt-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-session-alerts />
                    
                    <x-create-card 
                        url="{{ route('instructor.create') }}"
                        title="Crear Instructor"
                        icon="fa-plus-circle"
                        permission="CREAR INSTRUCTOR"
                    />

                    <x-data-table 
                        title="Lista de Instructores"
                        searchable="true"
                        searchAction="{{ route('instructor.index') }}"
                        searchPlaceholder="Buscar instructor..."
                        searchValue="{{ request('search') }}"
                        :columns="[
                            ['label' => '#', 'width' => '5%'],
                            ['label' => 'Nombre', 'width' => '25%'],
                            ['label' => 'Documento', 'width' => '15%'],
                            ['label' => 'Especialidades', 'width' => '20%'],
                            ['label' => 'Estado', 'width' => '10%'],
                            ['label' => 'Acciones', 'width' => '25%', 'class' => 'text-center']
                        ]"
                        :pagination="$instructores->links()"
                    >
                                        @forelse ($instructores as $instructor)
                                            <tr>
                                                <td class="px-4">{{ $loop->iteration }}</td>
                                                <td class="px-4 font-weight-medium">
                                                    {{ $instructor->persona->primer_nombre }} 
                                                    {{ $instructor->persona->primer_apellido }}
                                                </td>
                                                <td class="px-4">{{ $instructor->persona->numero_documento }}</td>
                                                <td class="px-4">
                                                    @php
                                                        $especialidades = $instructor->especialidades ?? [];
                                                        $especialidadPrincipalId = $especialidades['principal'] ?? null;
                                                        $especialidadesSecundariasIds = $especialidades['secundarias'] ?? [];
                                                        
                                                        // Convertir IDs a nombres
                                                        $especialidadPrincipalNombre = null;
                                                        if ($especialidadPrincipalId) {
                                                            $redConocimiento = \App\Models\RedConocimiento::find($especialidadPrincipalId);
                                                            $especialidadPrincipalNombre = $redConocimiento ? $redConocimiento->nombre : null;
                                                        }
                                                        
                                                        $especialidadesSecundariasNombres = [];
                                                        if (!empty($especialidadesSecundariasIds)) {
                                                            $redesConocimiento = \App\Models\RedConocimiento::whereIn('id', $especialidadesSecundariasIds)->get();
                                                            $especialidadesSecundariasNombres = $redesConocimiento->pluck('nombre')->toArray();
                                                        }
                                                    @endphp
                                                    @if($especialidadPrincipalNombre)
                                                        <div class="d-inline-block px-2 py-1 rounded-pill bg-primary-light text-primary mr-1 mb-1 font-weight-medium">
                                                            {{ $especialidadPrincipalNombre }}
                                                        </div>
                                                    @endif
                                                    @if(count($especialidadesSecundariasNombres) > 0)
                                                        @foreach(array_slice($especialidadesSecundariasNombres, 0, 2) as $especialidadNombre)
                                                            <div class="d-inline-block px-2 py-1 rounded-pill bg-secondary-light text-secondary mr-1 mb-1 font-weight-medium">{{ $especialidadNombre }}</div>
                                                        @endforeach
                                                        @if(count($especialidadesSecundariasNombres) > 2)
                                                            <div class="d-inline-block px-2 py-1 rounded-pill bg-light text-muted mr-1 mb-1 font-weight-medium">+{{ count($especialidadesSecundariasNombres) - 2 }}</div>
                                                        @endif
                                                    @endif
                                                    @if(!$especialidadPrincipalNombre && count($especialidadesSecundariasNombres) === 0)
                                                        <span class="text-muted">Sin especialidades</span>
                                                    @endif
                                                </td>
                                                <td class="px-4">
                                                    <x-status-badge :status="$instructor->status" />
                                                </td>
                                                <td class="px-4 text-center">
                                                    <x-action-buttons 
                                                        :show="true"
                                                        :edit="true"
                                                        :delete="true"
                                                        showUrl="{{ route('instructor.show', $instructor->id) }}"
                                                        editUrl="{{ route('instructor.edit', $instructor->id) }}"
                                                        deleteUrl="{{ route('instructor.destroy', $instructor->id) }}"
                                                        showPermission="VER INSTRUCTOR"
                                                        editPermission="EDITAR INSTRUCTOR"
                                                        deletePermission="ELIMINAR INSTRUCTOR"
                                                        :custom="[
                                                            [
                                                                'url' => route('instructor.gestionarEspecialidades', $instructor->id),
                                                                'title' => 'Gestionar especialidades',
                                                                'icon' => 'fas fa-graduation-cap',
                                                                'color' => 'text-primary',
                                                                'permission' => 'GESTIONAR ESPECIALIDADES INSTRUCTOR'
                                                            ],
                                                            [
                                                                'url' => route('instructor.fichasAsignadas', $instructor->id),
                                                                'title' => 'Ver fichas asignadas',
                                                                'icon' => 'fas fa-clipboard-list',
                                                                'color' => 'text-success',
                                                                'permission' => 'VER FICHAS ASIGNADAS'
                                                            ]
                                                        ]"
                                                    />
                                                </td>
                                            </tr>
                                        @empty
                                            <x-empty-state 
                                                message="No hay instructores registrados"
                                                image="img/no-data.svg"
                                                :colspan="6"
                                            />
                                        @endforelse
                    </x-data-table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer')
    @include('layouts.footer')
@endsection

@section('js')
    @vite(['resources/js/pages/instructores-index.js'])
@endsection