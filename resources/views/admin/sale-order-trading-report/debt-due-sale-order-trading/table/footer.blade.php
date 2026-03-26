<tr>
    <td colspan="7"></td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('total')) : $data->sum('total') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('paid')) : $data->sum('paid') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('total_local')) : $data->sum('total_local') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('paid_local')) : $data->sum('paid_local') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('outstanding')) : $data->sum('outstanding') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('outstanding_local')) : $data->sum('outstanding_local') }}</td>
</tr>
