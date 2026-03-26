<tr>
    <td colspan="5" class="text-center">Total</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('previous_year_quantity')) : $data->sum('previous_year_quantity') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('previous_year_quantity_sended')) : $data->sum('previous_year_quantity_sended') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('previous_year_quantity_received')) : $data->sum('previous_year_quantity_received') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('previous_year_value')) : $data->sum('previous_year_value') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('selected_month_quantity')) : $data->sum('selected_month_quantity') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('selected_month_quantity_sended')) : $data->sum('selected_month_quantity_sended') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('selected_month_quantity_received')) : $data->sum('selected_month_quantity_received') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('selected_month_value')) : $data->sum('selected_month_value') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('january_to_selected_month_quantity')) : $data->sum('january_to_selected_month_quantity') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('january_to_selected_month_quantity_sended')) : $data->sum('january_to_selected_month_quantity_sended') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('january_to_selected_month_quantity_received')) : $data->sum('january_to_selected_month_quantity_received') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('january_to_selected_month_value')) : $data->sum('january_to_selected_month_value') }}</td>
</tr>
