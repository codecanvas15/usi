@foreach ($data as $item)
    <tr>
        <td>{{ $item->document_code }}</td>
        <td>{{ localDate($item->date) }}</td>
        <td>{{ localDate($item->created_at) }}</td>
        <td>{{ $item->item_name }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->stock_before) : $item->stock_before }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->in) : $item->in }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->out) : $item->out }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->stock_final) : $item->stock_final }}</td>
        <td>{{ $item->unit_name }}</td>
    </tr>
@endforeach
