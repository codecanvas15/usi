@php
    $no = 1;
@endphp
@foreach ($data as $item_1)
    @foreach ($item_1['detail'] as $item_2)
        <tr>
            <td>{{ $item_1['customer_name'] }}</td>
            <td>{{ $item_2['item_name'] }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_2['previous_year_quantity']) : $item_2['previous_year_quantity'] }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_2['previous_year_sub_total_idr']) : $item_2['previous_year_sub_total_idr'] }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_2['selected_month_quantity']) : $item_2['selected_month_quantity'] }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_2['selected_month_sub_total_idr']) : $item_2['selected_month_sub_total_idr'] }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_2['january_until_selected_month_quantity']) : $item_2['january_until_selected_month_quantity'] }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_2['january_until_selected_month_sub_total_idr']) : $item_2['january_until_selected_month_sub_total_idr'] }}</td>
        </tr>
    @endforeach
@endforeach
