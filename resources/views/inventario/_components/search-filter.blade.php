{{-- 
    Componente: Barra de búsqueda con filtro
    Props:
    - $placeholder (string): Placeholder del input
    - $inputId (string): ID del input
    - $showCounter (bool): Mostrar contador de resultados
--}}
@props([
    'placeholder' => 'Buscar...',
    'inputId' => 'search-filter',
    'showCounter' => true
])

<div class="search-filter-container mb-3">
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                </div>
                <input type="text" 
                       id="{{ $inputId }}" 
                       class="form-control" 
                       placeholder="{{ $placeholder }}">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" 
                            type="button" 
                            id="{{ $inputId }}-clear"
                            style="display: none;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
        
        @if($showCounter)
            <div class="col-md-4 text-right">
                <span id="{{ $inputId }}-counter" class="filter-counter text-muted"></span>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('{{ $inputId }}');
        const clearBtn = document.getElementById('{{ $inputId }}-clear');
        const counter = document.getElementById('{{ $inputId }}-counter');
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearBtn.style.display = this.value ? 'block' : 'none';
            });
        }
        
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                this.style.display = 'none';
            });
        }
    });
</script>
@endpush

