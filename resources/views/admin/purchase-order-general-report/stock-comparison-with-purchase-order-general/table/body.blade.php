@foreach ($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->item_name }}</td>
        <td>{{ $item->code }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->quantity) : $item->quantity }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->quantity_received) : $item->quantity_received }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->outstanding) : $item->outstanding }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->stock) : $item->stock }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->gap) : $item->gap }}</td>
    </tr>
@endforeach
