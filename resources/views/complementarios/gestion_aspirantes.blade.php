@extends('adminlte::page')

@section('title', 'Gestión de Aspirantes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Gestión de Aspirantes</h1>
            <p class="text-muted">Administre los aspirantes a programas de formación complementaria</p>
        </div>
    </div>
@stop

@section('content')
<link rel="stylesheet" href="{{ asset('css/gestion_aspirantes.css') }}">

    <!-- Main Content -->
    <div class="container-fluid px-4 py-4">
        <div class="row">
            @forelse($programas as $programa)
            <div class="col-md-4 mb-4">
                <div class="course-card">
                    <div class="card-icon">
                        <i class="{{ $programa->icono }}"></i>
                    </div>
                    <div class="card-title">{{ $programa->nombre }}</div>
                    <span class="badge {{ $programa->badge_class }} mb-2">{{ $programa->estado_label }}</span>
                    <p>{{ $programa->descripcion }}</p>
                    <p><strong>Aspirantes:</strong> {{ $programa->aspirantes_count }}</p>
                    <a href="{{ route('aspirantes.ver', ['curso' => str_replace(' ', '-', strtolower($programa->nombre))]) }}" class="btn btn-primary w-100">
                        <i class="fas fa-users"></i> Ver Aspirantes
                    </a>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h5>No hay programas complementarios disponibles</h5>
                    <p>No se encontraron programas complementarios en el sistema.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Status tab functionality
        document.querySelectorAll('.status-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.status-tab').forEach(t => t.classList.remove('active'));
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Here you would typically filter the table based on the selected status
                const status = this.getAttribute('data-status');
                console.log('Selected status:', status);
            });
        });

        // Search functionality
        document.querySelector('.search-input input').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            console.log('Search term:', searchTerm);
            // Here you would implement the search logic
        });

        // Action button functionality
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.classList.contains('action-btn-view') ? 'view' : 
                            this.classList.contains('action-btn-edit') ? 'edit' : 'delete';
                const row = this.closest('tr');
                const id = row.cells[0].textContent;
                console.log(`${action} action for ID: ${id}`);
            });
        });
    </script>
@stop

@section('css')
@stop

@section('js')
    <script>
        console.log('Módulo de gestión de aspirantes cargado');
    </script>
@stop