@foreach ($item->details as $item_detail)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item_detail->item_name }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->return_qty) : $item_detail->return_qty }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->price) : $item_detail->price }}</td>

        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->subtotal) : $item_detail->subtotal }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->tax_amount) : $item_detail->tax_amount }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->total) : $item_detail->total }}</td>

        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate }}</td>

        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->subtotal_local) : $item_detail->subtotal_local }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->tax_amount_local) : $item_detail->tax_amount_local }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->total_local) : $item_detail->total_local }}</td>
    </tr>
@endforeach
