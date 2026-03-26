@foreach ($data as $item)
    <tr>
        <td @if ($item->add_on_item_code) rowspan="2" @endif>{{ $loop->iteration }}</td>
        <td @if ($item->add_on_item_code) rowspan="2" @endif>{{ $item->customer_name }}</td>
        <td @if ($item->add_on_item_code) rowspan="2" @endif>{{ $item->customer_code }}</td>
        <td>
            {{ $item->item_name }}
        </td>
        <td>
            {{ $item->item_code }}
        </td>
        <td class="text-end text-right">
            {{ $formatNumber ? formatNumber($item->previous_year_quantity) : $item->previous_year_quantity }}
        </td>
        <td class="text-end text-right">
            {{ $formatNumber ? formatNumber($item->previous_year_total) : $item->previous_year_total }}
        </td>
        <td class="text-end text-right">
            {{ $formatNumber ? formatNumber($item->previous_year_total_tax) : $item->previous_year_total_tax }}
        </td>
        <td class="text-end text-right">
            {{ $formatNumber ? formatNumber($item->previous_year_sub_total) : $item->previous_year_sub_total }}
        </td>
        <td class="text-end text-right">
            {{ $formatNumber ? formatNumber($item->selected_month_quantity) : $item->selected_month_quantity }}
        </td>
        <td class="text-end text-right">
            {{ $formatNumber ? formatNumber($item->selected_month_total) : $item->selected_month_total }}
        </td>
        <td class="text-end text-right">
            {{ $formatNumber ? formatNumber($item->selected_month_total_tax) : $item->selected_month_total_tax }}
        </td>
        <td class="text-end text-right">
            {{ $formatNumber ? formatNumber($item->selected_month_sub_total) : $item->selected_month_sub_total }}
        </td>
        <td class="text-end text-right">
            {{ $formatNumber ? formatNumber($item->january_to_selected_month_quantity) : $item->january_to_selected_month_quantity }}
        </td>
        <td class="text-end text-right">
            {{ $formatNumber ? formatNumber($item->january_to_selected_month_total) : $item->january_to_selected_month_total }}
        </td>
        <td class="text-end text-right">
            {{ $formatNumber ? formatNumber($item->january_to_selected_month_total_tax) : $item->january_to_selected_month_total_tax }}
        </td>
        <td class="text-end text-right">
            {{ $formatNumber ? formatNumber($item->january_to_selected_month_sub_total) : $item->january_to_selected_month_sub_total }}
        </td>
    </tr>

    @if ($item->add_on_item_code)
        <tr>
            <td>
                {{ $item->add_on_item_name }}
            </td>
            <td>
                {{ $item->add_on_item_code }}
            </td>
            <td class="text-end text-right">
                {{ $formatNumber ? formatNumber($item->previous_year_quantity) : $item->previous_year_quantity }}
            </td>
            <td class="text-end text-right">
                {{ $formatNumber ? formatNumber($item->previous_year_add_on_total) : $item->previous_year_add_on_total }}
            </td>
            <td class="text-end text-right">
                {{ $formatNumber ? formatNumber($item->previous_year_add_on_total_tax) : $item->previous_year_add_on_total_tax }}
            </td>
            <td class="text-end text-right">
                {{ $formatNumber ? formatNumber($item->previous_year_add_on_sub_total) : $item->previous_year_add_on_sub_total }}
            </td>
            <td class="text-end text-right">
                {{ $formatNumber ? formatNumber($item->selected_month_quantity) : $item->selected_month_quantity }}
            </td>
            <td class="text-end text-right">
                {{ $formatNumber ? formatNumber($item->selected_month_add_on_total) : $item->selected_month_add_on_total }}
            </td>
            <td class="text-end text-right">
                {{ $formatNumber ? formatNumber($item->selected_month_add_on_total_tax) : $item->selected_month_add_on_total_tax }}
            </td>
            <td class="text-end text-right">
                {{ $formatNumber ? formatNumber($item->selected_month_add_on_sub_total) : $item->selected_month_add_on_sub_total }}
            </td>
            <td class="text-end text-right">
                {{ $formatNumber ? formatNumber($item->january_to_selected_month_quantity) : $item->january_to_selected_month_quantity }}
            </td>
            <td class="text-end text-right">
                {{ $formatNumber ? formatNumber($item->january_to_selected_month_add_on_total) : $item->january_to_selected_month_add_on_total }}
            </td>
            <td class="text-end text-right">
                {{ $formatNumber ? formatNumber($item->january_to_selected_month_add_on_total_tax) : $item->january_to_selected_month_add_on_total_tax }}
            </td>
            <td class="text-end text-right">
                {{ $formatNumber ? formatNumber($item->january_to_selected_month_add_on_sub_total) : $item->january_to_selected_month_add_on_sub_total }}
            </td>
        </tr>
    @endif
@endforeach
