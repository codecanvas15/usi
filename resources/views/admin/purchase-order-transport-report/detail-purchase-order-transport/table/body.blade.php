@foreach ($data as $item)
    <tr>
        <td>{{ $item->customer_name }}</td>
        <td>{{ $item->vendor_name }}</td>
        <td>{{ $item->item_name }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->previous_year_amount_delivery) : $item->previous_year_amount_delivery }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->previous_year_quantity) : $item->previous_year_quantity }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->previous_year_sub_total_local) : $item->previous_year_sub_total_local }}</td>

        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->selected_month_amount_delivery) : $item->selected_month_amount_delivery }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->selected_month_quantity) : $item->selected_month_quantity }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->selected_month_sub_total_local) : $item->selected_month_sub_total_local }}</td>

        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->january_until_selected_month_amount_delivery) : $item->january_until_selected_month_amount_delivery }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->january_until_selected_month_quantity) : $item->january_until_selected_month_quantity }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->january_until_selected_month_sub_total_local) : $item->january_until_selected_month_sub_total_local }}</td>
    </tr>
@endforeach
