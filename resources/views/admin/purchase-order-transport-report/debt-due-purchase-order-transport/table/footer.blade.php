<tr>
    <td colspan="5" class="text-center">Total</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('total')) : $data->sum('total') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('pay_amount')) : $data->sum('pay_amount') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('outstanding')) : $data->sum('outstanding') }}</td>
</tr>
