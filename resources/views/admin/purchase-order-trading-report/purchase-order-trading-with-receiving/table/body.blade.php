@foreach ($data as $item)
    <tr>
        <td class="font-small-1">{{ localDate($item->date_receive) }}</td>
        <td class="font-small-1">{{ $item->purchase_order_code }}</td>
        <td class="font-small-1">{{ $item->code }}</td>
        <td class="font-small-1">{{ $item->customer_name }}</td>
        <td class="font-small-1">{{ $item->sh_number_code }}</td>
        <td class="font-small-1">{{ $item->branch_name }}</td>
        <td class="font-small-1">{{ $item->vendor_name }}</td>
        <td class="font-small-1">{{ $item->item_name }}</td>
        <td class="font-small-1" align="right">
            @if ($format == 'excel')
                {{ $item->quantity }}
            @else
                {{ formatNumber($item->quantity) }}
            @endif
        </td>
        <td class="font-small-1" align="right">
            @if ($format == 'excel')
                {{ $item->price }}
            @else
                {{ formatNumber($item->price) }}
            @endif
        </td>
        <td class="font-small-1" align="right">
            @if ($format == 'excel')
                {{ $item->sub_total }}
            @else
                {{ formatNumber($item->sub_total) }}
            @endif
        </td>
        @foreach ($taxes as $tax)
            <td class="font-small-1" align="right">
                @if ($format == 'excel')
                    {{ $item->$tax ?? 0 }}
                @else
                    {{ formatNumber($item->$tax ?? 0) }}
                @endif
            </td>
        @endforeach
        <td class="font-small-1" align="right">
            @if ($format == 'excel')
                {{ $item->total_additional }}
            @else
                {{ formatNumber($item->total_additional) }}
            @endif
        </td>
        <td class="font-small-1" align="right">
            @if ($format == 'excel')
                {{ $item->total }}
            @else
                {{ formatNumber($item->total) }}
            @endif
        </td>
        <td class="font-small-1" align="right">
            @if ($format == 'excel')
                {{ $item->exchange_rate }}
            @else
                {{ formatNumber($item->exchange_rate) }}
            @endif
        </td>
        <td class="font-small-1" align="right">
            @if ($format == 'excel')
                {{ $item->sub_total_idr }}
            @else
                {{ formatNumber($item->sub_total_idr) }}
            @endif
        </td>
        <td class="font-small-1" align="right">
            @if ($format == 'excel')
                {{ $item->total_additional_idr }}
            @else
                {{ formatNumber($item->total_additional_idr) }}
            @endif
        </td>
        <td class="font-small-1" align="right">
            @if ($format == 'excel')
                {{ $item->total_idr }}
            @else
                {{ formatNumber($item->total_idr) }}
            @endif
        </td>
        <td class="font-small-1">{{ $item->status }}</td>
    </tr>
@endforeach
