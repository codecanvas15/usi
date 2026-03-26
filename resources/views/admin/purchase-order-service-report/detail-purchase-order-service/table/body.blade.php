@foreach ($data as $item)
    <tr>
        <td>{{ $item->vendor_name }}</td>
        <td>{{ $item->item_name }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->previous_year_quantity) : $item->previous_year_quantity }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->previous_year_sub_total) : $item->previous_year_sub_total }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->selected_month_quantity) : $item->selected_month_quantity }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->selected_month_sub_total) : $item->selected_month_sub_total }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->january_until_selected_month_quantity) : $item->january_until_selected_month_quantity }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->january_until_selected_month_sub_total) : $item->january_until_selected_month_sub_total }}</td>
    </tr>
@endforeach
