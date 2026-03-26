<tr>
    <td colspan="5" class="text-center">Total</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('quantity')) : $data->sum('quantity') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('quantity_received')) : $data->sum('quantity_received') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('outstanding')) : $data->sum('outstanding') }}</td>
    <td></td>
</tr>
