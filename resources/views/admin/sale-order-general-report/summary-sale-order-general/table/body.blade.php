@foreach ($data as $item)
    @php
        $style = '';
        if ($item->payment_status == 'paid') {
            $style = 'background-color: #18d26b';
        }
    @endphp
    <tr>
        <td @if ($style) style="{{ $style }}" @endif>{{ $loop->iteration }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ localDate($item->date) }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->reference }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->code }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->customer_name }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->branch_name }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ localDate($item->due_date) }}</td>
        <td @if ($style) style="{{ $style }}" @endif></td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end">{{ $formatNumber ? formatNumber($item->sub_total) : $item->sub_total }}</td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end">{{ $formatNumber ? formatNumber($item->total_tax) : $item->total_tax }}</td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end">{{ $formatNumber ? formatNumber($item->total) : $item->total }}</td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end">{{ $item->payment_status }}</td>
    </tr>
@endforeach
