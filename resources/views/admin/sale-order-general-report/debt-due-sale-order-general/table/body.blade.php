@foreach ($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->code }}</td>
        <td>{{ localDate($item->date) }}</td>
        <td>{{ localDate($item->due_date) }}</td>
        <td>{{ $item->customer_name }}</td>
        <td>{{ $item->branch_name }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->total) : $item->total }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->paid) : $item->paid }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->total_local) : $item->total_local }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->paid_local) : $item->paid_local }}</td>
    </tr>
@endforeach
