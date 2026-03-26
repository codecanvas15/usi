<tr>
    <td colspan="6" class="font-small-1 text-center">Total</td>
    <td class="font-small-1 text-end text-right">{{ $formatNumber ? formatNumber($total_all->quantity) : '=SUM(G7:G' . (count($data) + 6) . ')' }}</td>
    <td></td>
    <td></td>
    <td></td>
    <td class="font-small-1 text-end text-right">{{ $formatNumber ? formatNumber($total_all->sub_total) : '=SUM(K7:K' . (count($data) + 6) . ')' }}</td>
    <td class="font-small-1 text-end text-right">{{ $formatNumber ? formatNumber($total_all->other_cost) : '=SUM(L7:L' . (count($data) + 6) . ')' }}</td>
    <td class="font-small-1 text-end text-right">{{ $formatNumber ? formatNumber($total_all->total_tax) : '=SUM(M7:M' . (count($data) + 6) . ')' }}</td>
    <td class="font-small-1 text-end text-right">{{ $formatNumber ? formatNumber($total_all->total) : '=SUM(N7:N' . (count($data) + 6) . ')' }}</td>
    <td></td>
    <td class="font-small-1 text-end text-right">{{ $formatNumber ? formatNumber($total_all->total_idr) : '=SUM(P7:P' . (count($data) + 6) . ')' }}</td>
</tr>
