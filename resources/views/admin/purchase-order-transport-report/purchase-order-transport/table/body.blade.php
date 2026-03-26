@foreach ($data as $item)
    <tr>
        <td>{{ localDate($item->purchase_transport_target_delivery) }}</td>
        <td>{{ $item->purchase_transport_code }}</td>
        <td>@if($item->sale_orders_id)
        <a target="_blank" href="{{ route('admin.sales-order.index') . '/' . $item->sale_orders_id }}">
            {{ $item->sale_orders_code }}
        </a>
    @else
        -
    @endif</td>
        <td>{{ Str::headline($item->purchase_transport_type) }}</td>
        <td>{{ $item->customer_name }}</td>
        <td>{{ $item->vendor_name }}</td>
        <td>{{ $item->item_name }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item->purchase_transport_price) : $item->purchase_transport_price }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item->delivery_order_amount_sum) : $item->delivery_order_amount_sum }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item->delivery_order_quantity_sum) : $item->delivery_order_quantity_sum }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item->purchase_transport_sub_total) : $item->purchase_transport_sub_total }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item->purchase_transport_exchange_rate) : $item->purchase_transport_exchange_rate }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item->purchase_transport_sub_total_local) : $item->purchase_transport_sub_total_local }}</td>
        <td>{{ $item->purchase_transport_status }}</td>
    </tr>
@endforeach
