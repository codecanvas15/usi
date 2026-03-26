<tr>
    <td colspan="7" class="text-center">Total</td>
    <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('total_final')) : $data->sum('total_final') }}</td>
    <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('taxTotal')) : $data->sum('taxTotal') }}</td>
    <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('cleanTotal')) : $data->sum('cleanTotal') }}</td>
    <td></td>
</tr>
