<tr>
    <td colspan="3" class="text-center">Total</td>
    <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($data->sum('total')) : $data->sum('total') }}</td>
    <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($data->sum('not_overdue')) : $data->sum('not_overdue') }}</td>
    <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($data->sum('first_group')) : $data->sum('first_group') }}</td>
    <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($data->sum('second_group')) : $data->sum('second_group') }}</td>
    <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($data->sum('third_group')) : $data->sum('third_group') }}</td>
    <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($data->sum('fourth_group')) : $data->sum('fourth_group') }}</td>
</tr>
