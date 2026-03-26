<tr>
    <td colspan="3" class="text-center"><b>Total</b></td>
    <td class="text-end text-right"></td>
    <td class="text-end text-right"></td>
    <td class="text-end text-right"><b>{{ $formatNumber ? formatNumber($data->sum('in')) : $data->sum('in') }}</b></td>
    <td class="text-end text-right"><b>{{ $formatNumber ? formatNumber($data->sum('out')) : $data->sum('out') }}</b></td>
    <td class="text-end text-right"></td>
    <td></td>
</tr>
