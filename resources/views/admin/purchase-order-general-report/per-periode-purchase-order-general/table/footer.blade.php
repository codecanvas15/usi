<tr>
    <td colspan="4" class="text-center">Total</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($total->previous_year_quantity) : $total->previous_year_quantity }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($total->previous_year_sub_total) : $total->previous_year_sub_total }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($total->selected_month_quantity) : $total->selected_month_quantity }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($total->selected_month_sub_total) : $total->selected_month_sub_total }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($total->january_to_selected_month_quantity) : $total->january_to_selected_month_quantity }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($total->january_to_selected_month_sub_total) : $total->january_to_selected_month_sub_total }}</td>
</tr>
