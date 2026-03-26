<tr>
    <td colspan="6"></td>
    <td class="text-end"><b>{{ $formatNumber ? formatNumber($data->sum('quantity')) : $data->sum('quantity') }}</b></td>
    <td class="text-end"><b>{{ $formatNumber ? formatNumber($data->sum('sub_total')) : $data->sum('sub_total') }}</b></td>
    <td class="text-end"><b>{{ $formatNumber ? formatNumber($data->sum('sub_total_idr')) : $data->sum('sub_total_idr') }}</b></td>
</tr>
