<tr>
    <td colspan="6"></td>
    <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('quantity')) : $data->sum('quantity') }}</td>
    <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('sub_total')) : $data->sum('sub_total') }}</td>
    <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('sub_total_idr')) : $data->sum('sub_total_idr') }}</td>
</tr>
