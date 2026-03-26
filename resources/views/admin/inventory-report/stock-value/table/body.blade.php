@foreach ($item['stock_mutations'] as $item_detail)
    <tr>
        <td class="font-small-2" align="center">{{ Carbon\Carbon::parse($item_detail->date)->format('d/m/y') }}</td>
        <td class="font-small-2" align="center">{{ Carbon\Carbon::parse($item_detail->created_at)->format('d/m/y') }}</td>
        <td class="font-small-2">{{ $item_detail->item_name }}</td>
        <td class="font-small-2">{{ $item_detail->ware_house_name }}</td>
        <td class="font-small-2">{{ $item_detail->branch_name }}</td>
        <td class="font-small-2">{{ $item_detail->note }}</td>
        <td class="font-small-2">{{ $item_detail->document_code }}</td>

        <td class="font-small-2" align="right">{{ $formatNumber ? formatNumber($item_detail->in) : $item_detail->in }}</td>
        <td class="font-small-2" align="right">{{ $formatNumber ? formatNumber($item_detail->in_value) : $item_detail->in_value }}</td>

        <td class="font-small-2" align="right">{{ $formatNumber ? formatNumber($item_detail->out) : $item_detail->out }}</td>
        <td class="font-small-2" align="right">{{ $formatNumber ? formatNumber($item_detail->out_value) : $item_detail->out_value }}</td>

        <td class="font-small-2" align="right">{{ $formatNumber ? formatNumber($item_detail->final_stock) : $item_detail->final_stock }}</td>
        <td class="font-small-2" align="right">{{ $formatNumber ? formatNumber($item_detail->final_stock_value) : $item_detail->final_stock_value }}</td>

    </tr>
@endforeach
