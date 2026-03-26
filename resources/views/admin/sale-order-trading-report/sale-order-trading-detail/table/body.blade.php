@php
    $no = 1;
@endphp
@foreach ($item->delivery_orders as $item_detail)
    <tr>
        <td style="border: 1px solid #000">{{ $no }}</td>
        <td style="border: 1px solid #000">{{ $item_detail->target_delivery }}</td>
        <td style="border: 1px solid #000">{{ $item_detail->delivery_order_code }}</td>
        <td style="border: 1px solid #000">{{ $item->item_name }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->load_quantity) : $item_detail->load_quantity }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->load_quantity_realization) : $item_detail->load_quantity_realization }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->unload_quantity_realization) : $item_detail->unload_quantity_realization }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->price) : $item_detail->price }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->sub_total) : $item_detail->sub_total }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->total_tax) : $item_detail->total_tax }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->total_tax + $item_detail->sub_total) : $item_detail->total_tax + $item_detail->sub_total }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->sub_total_final) : $item_detail->sub_total_final }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->total_tax_final) : $item_detail->total_tax_final }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_detail->total_tax_final + $item_detail->sub_total_final) : $item_detail->total_tax_final + $item_detail->sub_total_final }}</td>
    </tr>
    @php
        $no++;
    @endphp
@endforeach
@foreach ($item->additional_items as $item_additional)
    <tr>
        <td style="border: 1px solid #000">{{ $no }}</td>
        <td style="border: 1px solid #000"></td>
        <td style="border: 1px solid #000">Additional Item</td>
        <td style="border: 1px solid #000">{{ $item_additional->item_name }}</td>

        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_additional->quantity) : $item_additional->quantity }}</td>
        <td style="border: 1px solid #000"></td>
        <td style="border: 1px solid #000"></td>

        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_additional->price) : $item_additional->price }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_additional->sub_total) : $item_additional->sub_total }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_additional->total_tax) : $item_additional->total_tax }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_additional->total_tax + $item_additional->sub_total) : $item_additional->total_tax + $item_additional->sub_total }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_additional->sub_total_final) : $item_additional->sub_total_final }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_additional->total_tax_final) : $item_additional->total_tax_final }}</td>
        <td style="border: 1px solid #000" class="text-end text-right">{{ $formatNumber ? formatNumber($item_additional->total_tax_final + $item_additional->sub_total_final) : $item_additional->total_tax_final + $item_additional->sub_total_final }}</td>
    </tr>

    @php
        $no++;
    @endphp
@endforeach
