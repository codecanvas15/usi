@foreach ($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->customer_name }}</td>
        <td>{{ $item->code }}</td>
        <td>{{ localDate($item->date) }}</td>
        <td>{{ localDate($item->due_date) }}</td>
        <td>{{ $item->overdue }}</td>
        <td>{{ $item->branch_name }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->total) : $item->total }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->paid) : $item->paid }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->total_local) : $item->total_local }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->paid_local) : $item->paid_local }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->outstanding) : $item->outstanding }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->outstanding_local) : $item->outstanding_local }}</td>
    </tr>
@endforeach
