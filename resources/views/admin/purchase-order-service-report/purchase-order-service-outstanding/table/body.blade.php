@foreach ($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->code }}</td>
        <td>{{ localDate($item->date) }}</td>
        <td>{{ $item->vendor_name }}</td>
        <td>{{ $item->item_name }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->quantity) : $item->quantity }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->quantity_received) : $item->quantity_received }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->outstanding) : $item->outstanding }}</td>
        <td>{{ Str::upper($item->status) }}</td>
    </tr>
@endforeach
