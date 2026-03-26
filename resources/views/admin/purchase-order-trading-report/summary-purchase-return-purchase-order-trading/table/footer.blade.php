<tr>
    <td colspan="8" class="text-center">Total</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('total')) : $data->sum('total') }}</td>
    <td></td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('total_local')) : $data->sum('total_local') }}</td>
    <td></td>
</tr>
