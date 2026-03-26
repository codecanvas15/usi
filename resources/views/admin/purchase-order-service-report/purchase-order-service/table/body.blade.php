@foreach ($data as $item)
    <tr>
        <td>{{ $item->vendor_name }}</td>
        <td>{{ $item->item_name }}</td>
        <td>{{ localDate($item->date) }}</td>
        <td>{{ $item->code }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->quantity) : $item->quantity }}</td>
        <td>{{ $item->unit_name }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->price) : $item->price }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->sub_total) : $item->sub_total }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->sub_total_idr) : $item->sub_total_idr }}</td>
    </tr>
@endforeach
