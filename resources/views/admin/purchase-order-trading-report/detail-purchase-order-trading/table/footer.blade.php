<tr>
    <td colspan="2" class="text-center">Total</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($total_all->previous_year_quantity) : $total_all->previous_year_quantity }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($total_all->previous_year_sub_total_idr) : $total_all->previous_year_sub_total_idr }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($total_all->selected_month_quantity) : $total_all->selected_month_quantity }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($total_all->selected_month_sub_total_idr) : $total_all->selected_month_sub_total_idr }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($total_all->january_until_selected_month_quantity) : $total_all->january_until_selected_month_quantity }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($total_all->january_until_selected_month_sub_total_idr) : $total_all->january_until_selected_month_sub_total_idr }}</td>
</tr>
