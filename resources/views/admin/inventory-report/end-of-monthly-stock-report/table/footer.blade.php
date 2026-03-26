<tr>
    <td colspan="2" class="text-center">Total</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->data->sum('stock_before')) : $item->data->sum('stock_before') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->data->sum('value_before')) : $item->data->sum('value_before') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->data->sum('stock_in')) : $item->data->sum('stock_in') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->data->sum('value_in')) : $item->data->sum('value_in') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->data->sum('quantity')) : $item->data->sum('quantity') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->data->sum('value')) : $item->data->sum('value') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->data->sum('stock_out')) : $item->data->sum('stock_out') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->data->sum('value_out')) : $item->data->sum('value_out') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->data->sum('stock_final')) : $item->data->sum('stock_final') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->data->sum('value_final')) : $item->data->sum('value_final') }}</td>
    <td></td>
</tr>
