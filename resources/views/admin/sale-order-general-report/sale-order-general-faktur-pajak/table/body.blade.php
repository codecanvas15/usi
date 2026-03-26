@foreach ($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ localDate($item->date) }}</td>
        <td>{{ $item->customer_name }}</td>
        <td>{{ $item->code }}</td>
        <td>{{ $item->reference }}</td>
        <td>{{ number_format($item->sub_total, 2, ',', '.') }}</td>
        <td>{{ number_format($item->total_tax, 2, ',', '.') }}</td>
        <td>{{ number_format($item->sub_total + $item->total_tax, 2, ',', '.') }}</td>
        <td>{{ Str::title($item->status) }}</td>
    </tr>
@endforeach
