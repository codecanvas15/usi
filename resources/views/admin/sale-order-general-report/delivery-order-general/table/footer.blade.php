<tr>
    <td colspan="9" class="text-end text-right">Total</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('sub_total')) : $data->sum('sub_total') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('total_tax')) : $data->sum('total_tax') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('total')) : $data->sum('total') }}</td>
    <td></td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('sub_total_final')) : $data->sum('sub_total_final') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('total_tax_final')) : $data->sum('total_tax_final') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('total_final')) : $data->sum('total_final') }}</td>
    <td></td>
</tr>
