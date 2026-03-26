@foreach ($data as $item)
    @foreach ($item['detail'] as $item_2)
        <tr>
            <td>{{ $no }}</td>
            <td>{{ $item_2->date }}</td>
            <td>{{ $item['customer_name'] }}</td>
            <td>{{ $item_2->item_name }}</td>
            <td>{{ $item_2->price }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_2->previous_year_quantity) : $item_2->previous_year_quantity }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_2->previous_year_sub_total) : $item_2->previous_year_sub_total }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_2->selected_month_quantity) : $item_2->selected_month_quantity }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_2->selected_month_sub_total) : $item_2->selected_month_sub_total }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_2->january_to_selected_month_quantity) : $item_2->january_to_selected_month_quantity }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_2->january_to_selected_month_sub_total) : $item_2->january_to_selected_month_sub_total }}</td>
        </tr>

        @php
            $no++;
        @endphp
    @endforeach
@endforeach
