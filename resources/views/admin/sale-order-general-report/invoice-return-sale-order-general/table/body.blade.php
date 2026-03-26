@foreach ($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->date_sale_order_general }}</td>
        <td>{{ $item->date_invoice_return }}</td>
        <td>{{ $item->tax_number }}</td>
        <td>{{ $item->code }}</td>
        <td>{{ $item->customer_name }}</td>
        <td>{{ $item->item_name }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->total) : $item->total }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->total_local) : $item->total_local }}</td>
        <td>{{ $item->status }}</td>
    </tr>
@endforeach
