@php
    $start_row = 7;
@endphp
@foreach ($data as $key => $item)
    @php
        $row = $start_row + $key;
        $subtotal = "=(I$row-J$row)*G$row";
        $total = "=((I$row-J$row)*G$row)+L$row+M$row";
        $total_idr = "=(((I$row-J$row)*G$row)+L$row+M$row)*O$row";

    @endphp
    <tr>
        <td class="font-small-1">{{ $item->customer_name }}</td>
        <td class="font-small-1">{{ $item->vendor_name }}</td>
        <td class="font-small-1">{{ $item->item_name }}</td>
        <td class="font-small-1">{{ localDate($item->date) }}</td>
        <td class="font-small-1">{{ $item->code }}</td>
        <td class="font-small-1">{{ $item->sale_confirmation }}</td>
        <td class="font-small-1 text-end text-right">{{ $formatNumber ? formatNumber($item->quantity) : $item->quantity }}</td>
        <td class="font-small-1">{{ $item->unit }}</td>
        <td class="font-small-1 text-end text-right">{{ $formatNumber ? formatNumber($item->price) : $item->price }}</td>
        <td class="font-small-1 text-end text-right">{{ $formatNumber ? formatNumber($item->discount_per_liter) : $item->discount_per_liter }}</td>
        <td class="font-small-1 text-end text-right">{{ $formatNumber ? formatNumber($item->sub_total) : $subtotal }}</td>
        <td class="font-small-1 text-end text-right">{{ $formatNumber ? formatNumber($item->other_cost) : $item->other_cost }}</td>
        <td class="font-small-1 text-end text-right">{{ $formatNumber ? formatNumber($item->total_tax) : $item->total_tax }}</td>
        <td class="font-small-1 text-end text-right">{{ $formatNumber ? formatNumber($item->total) : $total }}</td>
        <td class="font-small-1 text-end text-right">{{ $formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate }}</td>
        <td class="font-small-1 text-end text-right">{{ $formatNumber ? formatNumber($item->total_idr) : $total_idr }}</td>
    </tr>
@endforeach
