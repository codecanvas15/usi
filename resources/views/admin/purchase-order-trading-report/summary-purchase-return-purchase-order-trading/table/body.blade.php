@foreach ($data as $item)
    <tr>
        <td>{{ localDate($item->date) }}</td>
        <td>{{ $item->tax_number }}</td>
        <td>{{ $item->code }}</td>
        <td>{{ $item->code_item_receiving_report }}</td>
        <td>{{ $item->customer_name }}</td>
        <td>{{ $item->vendor_name }}</td>
        <td>{{ $item->ware_house_hame }}</td>
        <td>{{ $item->item_name }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->total) : $item->total }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->total_local) : $item->total_local }}</td>
        <td>{{ $item->status }}</td>
    </tr>
@endforeach
