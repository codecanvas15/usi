<tr>
    <td colspan="8" class="text-center">Total</td>
    <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('sub_total')) : $data->sum('sub_total') }}</td>
    <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('total_tax')) : $data->sum('total_tax') }}</td>
    <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('total')) : $data->sum('total') }}</td>
    <td></td>
</tr>
