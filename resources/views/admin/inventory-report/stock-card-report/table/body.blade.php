@php
    $key = 0;
    $border = '';
    if (!$formatNumber) {
        $border = 'border: 1px solid #000;';
    }
@endphp
@foreach ($item['data'] ?? [] as $item_detail)
    @if ($key == 0 && count($item['data']) > 1)
        <tr>
            <td style="{{ $border }}">Saldo Awal</td>
            <td style="{{ $border }}"></td>
            <td style="{{ $border }}"></td>
            <td style="{{ $border }}"></td>
            <td style="{{ $border }}"></td>
            <td style="{{ $border }}" class="text-end text-right"></td>
            <td style="{{ $border }}" class="text-end text-right"></td>
            <td style="{{ $border }}" class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail['stock_before']) : $item_detail['stock_before'] }}</td>
        </tr>
    @endif
    @php
        $key++;
    @endphp
    <tr>
        <td style="{{ $border }}">{{ localDate($item_detail['date']) }}</td>
        <td style="{{ $border }}">{{ localDate($item_detail['created_at']) }}</td>
        <td style="{{ $border }}">{{ $item_detail['note'] }}</td>
        <td style="{{ $border }}">{{ $item_detail['document_code'] }}</td>
        <td style="{{ $border }}">{{ $item['unit_name'] }}</td>
        <td style="{{ $border }}" class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail['in']) : $item_detail['in'] }}</td>
        <td style="{{ $border }}" class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail['out']) : $item_detail['out'] }}</td>
        <td style="{{ $border }}" class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail['stock_final']) : $item_detail['stock_final'] }}</td>
    </tr>
@endforeach
