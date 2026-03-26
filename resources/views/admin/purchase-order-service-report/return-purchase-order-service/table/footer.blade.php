<tr>
    <td colspan="7"></td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('total')) : $data->sum('total') }}</td>
    <td class="text-end text-right"></td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('total_local')) : $data->sum('total_local') }}</td>
    <td colspan="1"></td>
</tr>
