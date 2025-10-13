@props([
    'title' => 'Lista de Registros',
    'columns' => [],
    'searchable' => true,
    'searchPlaceholder' => 'Buscar...',
    'searchAction' => '',
    'searchMethod' => 'GET',
    'searchName' => 'search',
    'searchValue' => '',
    'paginated' => true,
    'pagination' => '',
    'cardClass' => 'shadow-sm no-hover',
    'headerClass' => 'bg-white py-3',
    'tableClass' => 'table table-borderless table-striped mb-0',
    'showHeader' => true
])

<div class="card {{ $cardClass }}">
    @if($showHeader)
        <div class="card-header {{ $headerClass }} d-flex align-items-center">
            <h6 class="m-0 font-weight-bold text-primary flex-grow-1">{{ $title }}</h6>
            @if($searchable)
                <div class="input-group w-25">
                    @if($searchAction)
                        <form action="{{ $searchAction }}" method="{{ $searchMethod }}" class="input-group w-100">
                            <input type="text" 
                                   name="{{ $searchName }}" 
                                   class="form-control form-control-sm" 
                                   placeholder="{{ $searchPlaceholder }}" 
                                   value="{{ $searchValue }}" 
                                   autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-primary btn-sm" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    @else
                        <input type="text" 
                               class="form-control form-control-sm" 
                               placeholder="{{ $searchPlaceholder }}" 
                               autocomplete="off">
                        <div class="input-group-append">
                            <button class="btn btn-primary btn-sm" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="{{ $tableClass }}">
                <thead class="thead-light">
                    <tr>
                        @foreach($columns as $column)
                            <th class="px-4 py-3 {{ $column['class'] ?? '' }}" 
                                style="width: {{ $column['width'] ?? 'auto' }}">
                                {{ $column['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>
    
    @if($paginated && $pagination)
        <div class="card-footer bg-white">
            <div class="float-right">
                {{ $pagination }}
            </div>
        </div>
    @endif
</div>
