@php
    $symbol = isset($symbol) ? $symbol : '?';
@endphp
<button class="btn btn-info btn-{{ $size ?? 'md' }} mb-1" type="button" href="{{ $href }}" onclick="{{ $printOption ?? false ? 'showPrintOption(event)' : 'show_print_out_modal(event, `' . $symbol . '`)' }}" {!! $condition ?? authorizePrint($type) ? 'data-model="' . $model . '" data-id="' . $did . '" data-print-type="' . $type . '" data-link="' . ($link ?? route('admin.' . str_replace('_', '-', $type) . '.show', $did)) . '" data-code="' . $code . '"' : '' !!}>
    <i class="fa fa-file"></i> {{ $label ?? 'Export' }}</button>
