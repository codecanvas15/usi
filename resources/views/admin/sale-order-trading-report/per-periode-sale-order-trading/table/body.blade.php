@foreach ($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->date }}</td>
        <td>{{ $item->customer_name }}</td>
        <td>{{ $item->item_name }}</td>
        <td>{{ $formatNumber ? formatNumber($item->price) : $item->price }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->previous_year_quantity) : $item->previous_year_quantity }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->previous_year_quantity_sended) : $item->previous_year_quantity_sended }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->previous_year_quantity_received) : $item->previous_year_quantity_received }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->previous_year_value) : $item->previous_year_value }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->selected_month_quantity) : $item->selected_month_quantity }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->selected_month_quantity_sended) : $item->selected_month_quantity_sended }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->selected_month_quantity_received) : $item->selected_month_quantity_received }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->selected_month_value) : $item->selected_month_value }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->january_to_selected_month_quantity) : $item->january_to_selected_month_quantity }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->january_to_selected_month_quantity_sended) : $item->january_to_selected_month_quantity_sended }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->january_to_selected_month_quantity_received) : $item->january_to_selected_month_quantity_received }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->january_to_selected_month_value) : $item->january_to_selected_month_value }}</td>
    </tr>
@endforeach
