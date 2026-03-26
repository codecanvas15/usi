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
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->customer_name }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->branch_name }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->code }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ localDate($item->due_date) }}</td>
        <td @if ($style) style="{{ $style }}" @endif></td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end">{{ $formatNumber ? formatNumber($item->total_final) : $item->total_final }}</td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end">{{ $formatNumber ? formatNumber($item->taxTotal) : $item->taxTotal }}</td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end">{{ $formatNumber ? formatNumber($item->cleanTotal) : $item->cleanTotal }}</td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end">{{ $item->payment_status }}</td>
    </tr>
@endforeach
