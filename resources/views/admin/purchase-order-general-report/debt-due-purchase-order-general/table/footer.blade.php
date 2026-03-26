<tr>
    <td colspan="6"></td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('total')) : $data->sum('total') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('paid')) : $data->sum('paid') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('total_local')) : $data->sum('total_local') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('paid_local')) : $data->sum('paid_local') }}</td>
</tr>
