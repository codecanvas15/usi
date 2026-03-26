@foreach ($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ localDate($item->date) }}</td>
        <td>{{ $item->customer_name }}</td>
        <td>{{ $item->kode }}</td>
        <td>{{ $item->reference }}</td>
        <td>{{ number_format($item->subtotal, 2, ',', '.') }}</td>
        <td>{{ number_format($item->additional_tax_total, 2, ',', '.') }}</td>
        <td>{{ number_format($item->subtotal + $item->additional_tax_total, 2, ',', '.') }}</td>
        <td>{{ Str::title($item->status) }}</td>
    </tr>
@endforeach