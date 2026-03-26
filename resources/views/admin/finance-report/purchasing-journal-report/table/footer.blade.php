<tr>
    <td colspan="10" class="text-center">Total</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('debit_exchanged')) : '=SUM(K7:K' . (count($data) + 7) . ')' }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('credit_exchanged')) : '=SUM(L7:L' . (count($data) + 7) . ')' }}</td>
</tr>
