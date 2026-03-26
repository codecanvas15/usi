<tr>
    <td colspan="5" class="text-center">Total</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('quantity')) : $data->sum('quantity') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('quantity_outstanding')) : $data->sum('quantity_outstanding') }}</td>
    <td></td>
</tr>
