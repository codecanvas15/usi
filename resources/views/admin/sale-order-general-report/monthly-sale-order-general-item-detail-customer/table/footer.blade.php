<tr>
	<td colspan="3"></td>
	<td class="text-end">{{ $formatNumber ? formatNumber($data->sum('quantity_last_year')) : $data->sum('quantity_last_year') }}</td>
	<td class="text-end">{{ $formatNumber ? formatNumber($data->sum('sub_total_last_year')) : $data->sum('sub_total_last_year') }}</td>
	<td class="text-end">{{ $formatNumber ? formatNumber($data->sum('quantity_selected_month')) : $data->sum('quantity_selected_month') }}</td>
	<td class="text-end">{{ $formatNumber ? formatNumber($data->sum('sub_total_selected_month')) : $data->sum('sub_total_selected_month') }}</td>
	<td class="text-end">{{ $formatNumber ? formatNumber($data->sum('quantity_this_year')) : $data->sum('quantity_this_year') }}</td>
	<td class="text-end">{{ $formatNumber ? formatNumber($data->sum('sub_total_this_year')) : $data->sum('sub_total_this_year') }}</td>
</tr>
