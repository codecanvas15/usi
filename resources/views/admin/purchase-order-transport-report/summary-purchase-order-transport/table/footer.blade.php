@if ($formatNumber)
    <tr>
        <td colspan="7" class="text-center">Total</td>
        <td class="text-end text-right">{{ formatNumber($data->sum('total')) }}</td>
        <td class="text-end text-right"></td>
        <td class="text-end text-right">{{ formatNumber($data->sum('total_idr')) }}</td>
        <td class="text-end text-right"></td>
    </tr>
@else
    <tr>
        <td colspan="7" class="text-center">Total</td>
        <td class="text-end text-right">=SUM(G{{ 8 }}:G{{ 8 + count($data) - 1 }})</td>
        <td class="text-end text-right"></td>
        <td class="text-end text-right">=SUM(I{{ 8 }}:I{{ 8 + count($data) - 1 }})</td>
        <td class="text-end text-right"></td>
    </tr>
@endif
