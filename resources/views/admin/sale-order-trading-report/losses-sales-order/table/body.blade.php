@foreach ($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
        <td>{{ $item->code }}</td>
        <td>{{ $item->customer_name }}</td>
        <td>{{ $item->branch_name }}</td>
        <td>{{ $item->item_name }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item->load_quantity) : $item->load_quantity }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item->unload_quantity_realization) : $item->unload_quantity_realization }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item->losses_quantity) : $item->losses_quantity }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item->losses_value) : $item->losses_value }}</td>
        <td>{{ Str::headline($item->status) }}</td>
    </tr>
@endforeach
