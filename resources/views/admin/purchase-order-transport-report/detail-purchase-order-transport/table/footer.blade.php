@if ($formatNumber)
    <tr>
        <td colspan="3" class="text-center">Total</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('previous_year_amount_delivery')) : $data->sum('previous_year_amount_delivery') }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('previous_year_quantity')) : $data->sum('previous_year_quantity') }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('previous_year_sub_total_local')) : $data->sum('previous_year_sub_total_local') }}</td>

        <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('selected_month_amount_delivery')) : $data->sum('selected_month_amount_delivery') }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('selected_month_quantity')) : $data->sum('selected_month_quantity') }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('selected_month_sub_total_local')) : $data->sum('selected_month_sub_total_local') }}</td>

        <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('january_until_selected_month_amount_delivery')) : $data->sum('january_until_selected_month_amount_delivery') }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('january_until_selected_month_quantity')) : $data->sum('january_until_selected_month_quantity') }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('january_until_selected_month_sub_total_local')) : $data->sum('january_until_selected_month_sub_total_local') }}</td>
    </tr>
@else
    <tr>
        <td colspan="3" class="text-center">Total</td>
        <td class="text-end text-right"> =SUM(D{{ 8 }}:D{{ 8 + count($data) - 1 }})</td>
        <td class="text-end text-right"> =SUM(E{{ 8 }}:E{{ 8 + count($data) - 1 }})</td>
        <td class="text-end text-right"> =SUM(F{{ 8 }}:F{{ 8 + count($data) - 1 }})</td>
        <td class="text-end text-right"> =SUM(G{{ 8 }}:G{{ 8 + count($data) - 1 }})</td>
        <td class="text-end text-right"> =SUM(H{{ 8 }}:H{{ 8 + count($data) - 1 }})</td>
        <td class="text-end text-right"> =SUM(I{{ 8 }}:I{{ 8 + count($data) - 1 }})</td>
        <td class="text-end text-right"> =SUM(J{{ 8 }}:J{{ 8 + count($data) - 1 }})</td>
        <td class="text-end text-right"> =SUM(K{{ 8 }}:K{{ 8 + count($data) - 1 }})</td>
        <td class="text-end text-right"> =SUM(L{{ 8 }}:L{{ 8 + count($data) - 1 }})</td>
    </tr>
@endif
