{{--
    Componente: Item de Información
    Uso:
    <x-cards.info-item label="Programa" value="Desarrollo de Software" />
    <x-cards.info-item label="Fecha Inicio" value="01/01/2024" size="col-md-3" />
--}}

@props([
    'label' => '',
    'value' => '',
    'size' => 'col-md-3',
    'format' => null
])

<div class="{{ $size }}">
    <div class="info-item">
        <strong>{{ $label }}:</strong><br>
        <span class="text-muted">
            @if($format && $value)
                @switch($format)
                    @case('date')
                        {{ \Carbon\Carbon::parse($value)->format('d/m/Y') }}
                        @break
                    @case('currency')
                        ${{ number_format($value, 2) }}
                        @break
                    @case('number')
                        {{ number_format($value) }}
                        @break
                    @default
                        {{ $value }}
                @endswitch
            @else
                {{ $value ?? 'No definido' }}
            @endif
        </span>
    </div>
</div>
