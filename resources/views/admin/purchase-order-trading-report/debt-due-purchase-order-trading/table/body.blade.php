@foreach ($data as $item)
    <tr>
        <td>{{ $item->code }}</td>
        <td>{{ localDate($item->date) }}</td>
        <td>{{ localDate($item->top_due_date) }}</td>
        <td>{{ $item->customer }}</td>
        <td>{{ $item->vendor }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->total) : $item->total }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->pay_amount) : $item->pay_amount }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->outstanding) : $item->outstanding }}</td>
    </tr>
@endforeach
