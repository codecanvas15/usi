@foreach ($data as $item)
    @php
        $style = '';
        if ($item->payment_status == 'paid') {
            $style = 'background-color: #18d26b';
        }
    @endphp
    <tr>
        <td @if ($style) style="{{ $style }}" @endif>{{ localDate($item->date) }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->reference }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->vendor_name }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->branch_name }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->code }} / {{ $item->tax_reference }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->top_due_date }}</td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end text-right">{{ $formatNumber ? formatNumber($item->total) : $item->total }}</td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end text-right">{{ $formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate }}</td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end text-right">{{ $formatNumber ? formatNumber($item->total_idr) : $item->total_idr }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->payment_status }}</td>
    </tr>
@endforeach
