@php
    $total_qty = 0;
    $total_subtotal = 0;
    $total_tax = 0;
    $total_subtotal_idr = 0;
    $total_tax_idr = 0;
    $total_idr = 0;
    $count_row = 0;
@endphp
@foreach ($data as $item)
    @foreach ($item->details as $key => $item_detail)
        <tr>
            <td align="center" class="font-small-1">{{ $key + 1 }}</td>
            <td align="center" class="font-small-1">{{ localDate($item->date) }}</td>
            <td align="left" class="font-small-1">{{ $item->customer_name }}</td>
            <td align="center" class="font-small-1">{{ $item->reference }}</td>
            <td align="center" class="font-small-1">{{ $item->code }}</td>
            <td align="center" class="font-small-1">{{ localDate($item_detail->target_delivery) }}</td>
            <td align="left" class="font-small-1">{{ $item_detail->so_code }}</td>
            <td align="left" class="font-small-1">{{ $item_detail->delivery_order_code }}</td>
            {{-- kolom po here --}}
            <td align="left" class="font-small-1">{{ $item_detail->no_po_external }}</td>
            {{-- kolom po here --}}
            <td class="font-small-1">{{ $item_detail->item_name }}</td>
            <td align="right" class="font-small-1">{{ $formatNumber ? formatNumber($item_detail->quantity) : $item_detail->quantity }}</td>
            <td class="font-small-1">{{ $item_detail->unit_name }}</td>
            <td align="right" class="font-small-1">{{ $formatNumber ? formatNumber($item_detail->price) : $item_detail->price }}</td>
            <td align="right" class="font-small-1">{{ $formatNumber ? formatNumber($item_detail->detail_item_sub_total) : $item_detail->detail_item_sub_total }}</td>
            <td align="right" class="font-small-1">{{ $formatNumber ? formatNumber($item_detail->detail_item_total_tax) : $item_detail->detail_item_total_tax }}</td>
            <td align="right" class="font-small-1">{{ $formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate }}</td>
            <td align="right" class="font-small-1">{{ $formatNumber ? formatNumber($item_detail->detail_item_sub_total_final) : $item_detail->detail_item_sub_total_final }}</td>
            <td align="right" class="font-small-1">{{ $formatNumber ? formatNumber($item_detail->detail_item_total_tax_final) : $item_detail->detail_item_total_tax_final }}</td>
            <td align="right" class="font-small-1">{{ $formatNumber ? formatNumber($item_detail->detail_item_total_final) : $item_detail->detail_item_total_final }}</td>
        </tr>
        @php
            $count_row++;
            $total_qty += $item_detail->quantity;
            $total_subtotal += $item_detail->detail_item_sub_total;
            $total_tax += $item_detail->detail_item_total_tax;
            $total_subtotal_idr += $item_detail->detail_item_sub_total_final;
            $total_tax_idr += $item_detail->detail_item_total_tax_final;
            $total_idr += $item_detail->detail_item_total_final;
        @endphp
    @endforeach
    @foreach ($item->additionals as $key => $item_additional)
        <tr>
            <td class="font-small-1"></td>
            <td class="font-small-1"></td>
            <td class="font-small-1"></td>
            <td class="font-small-1"></td>
            <td class="font-small-1"></td>
            <td class="font-small-1"></td>
            <td class="font-small-1"></td>
            <td class="font-small-1"></td>
            <td class="font-small-1">Additional Item</td>
            <td class="font-small-1">{{ $item_additional->item_name }}</td>
            <td align="right" class="font-small-1">{{ $formatNumber ? formatNumber($item_additional->quantity) : $item_additional->quantity }}</td>
            <td class="font-small-1">{{ $item_additional->unit_name }}</td>
            <td align="right" class="font-small-1">{{ $formatNumber ? formatNumber($item_additional->price) : $item_additional->price }}</td>
            <td align="right" class="font-small-1">{{ $formatNumber ? formatNumber($item_additional->detail_item_sub_total) : $item_additional->detail_item_sub_total }}</td>
            <td align="right" class="font-small-1">{{ $formatNumber ? formatNumber($item_additional->detail_item_total_tax) : $item_additional->detail_item_total_tax }}</td>
            <td align="right" class="font-small-1">{{ $formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate }}</td>
            <td align="right" class="font-small-1">{{ $formatNumber ? formatNumber($item_additional->detail_item_sub_total_final) : $item_additional->detail_item_sub_total_final }}</td>
            <td align="right" class="font-small-1">{{ $formatNumber ? formatNumber($item_additional->detail_item_total_tax_final) : $item_additional->detail_item_total_tax_final }}</td>
            <td align="right" class="font-small-1">{{ $formatNumber ? formatNumber($item_additional->detail_item_total_final) : $item_additional->detail_item_total_final }}</td>
        </tr>
        @php
            $count_row++;
            $total_qty += $item_additional->quantity;
            $total_subtotal += $item_additional->detail_item_sub_total;
            $total_tax += $item_additional->detail_item_total_tax;
            $total_subtotal_idr += $item_additional->detail_item_sub_total_final;
            $total_tax_idr += $item_additional->detail_item_total_tax_final;
            $total_idr += $item_additional->detail_item_total_final;
        @endphp
    @endforeach
@endforeach
@include('admin.sale-order-general-report.sale-order-general-detail.table.footer', [
    'formatNumber' => true,
])
