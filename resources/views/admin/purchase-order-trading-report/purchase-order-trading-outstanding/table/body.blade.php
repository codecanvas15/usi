@forelse ($data as $item)
    <tr>
        <td>{{ localDate($item->date) }}</td>
        <td>{{ $item->code }}</td>
        <td>{{ $item->customer_name }}</td>
        <td>{{ $item->vendor_name }}</td>
        <td>{{ $item->item_name }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->quantity) : $item->quantity }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->quantity_outstanding) : $item->quantity_outstanding }}</td>
        <td>{{ $item->status }}</td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center">Tidak ada data</td>
    </tr>
@endforelse
