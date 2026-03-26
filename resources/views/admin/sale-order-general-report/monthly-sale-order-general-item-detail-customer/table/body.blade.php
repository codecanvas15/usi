@foreach ($data as $item)
	<tr>
		<td>{{$loop->iteration}}</td>
		<td>{{ $item['customer_name'] }}</td>
		<td>{{ $item['item_name'] }}</td>
		<td class="text-end">{{ $formatNumber ? formatNumber($item['quantity_last_year']) : $item['quantity_last_year'] }}</td>
		<td class="text-end">{{ $formatNumber ? formatNumber($item['sub_total_last_year']) : $item['sub_total_last_year'] }}</td>
		<td class="text-end">{{ $formatNumber ? formatNumber($item['quantity_selected_month']) : $item['quantity_selected_month'] }}</td>
		<td class="text-end">{{ $formatNumber ? formatNumber($item['sub_total_selected_month']) : $item['sub_total_selected_month'] }}</td>
		<td class="text-end">{{ $formatNumber ? formatNumber($item['quantity_this_year']) : $item['quantity_this_year'] }}</td>
		<td class="text-end">{{ $formatNumber ? formatNumber($item['sub_total_this_year']) : $item['sub_total_this_year'] }}</td>
	</tr>
@endforeach
