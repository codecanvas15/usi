@php
    $iteration = 1;
@endphp
@if (isset($is_pdf))
    @foreach ($data ?? [] as $key => $item)
        <tr>
            <td>{{ $iteration++ }}</td>
            <td>{{ $item->target_delivery }}</td>
            <td>{{ $item->customer_name }}</td>

            <td>{{ $item->invoice_trading_code }}</td>
            <td>{{ $item->branch_name }}</td>
            <td>{{ $item->code }}</td>
            <td>{{ $item->sale_order_code }}</td>
            <td class="text-end">{{ $formatNumber ? formatNumber($item->invoice_trading_subtotal_after_tax) : $item->invoice_trading_subtotal_after_tax }}</td>
            <td class="text-end">{{ $formatNumber ? formatNumber($item->invoice_trading_additional_after_tax) : $item->invoice_trading_additional_after_tax }}</td>
            <td class="text-end">{{ $formatNumber ? formatNumber($item->invoice_trading_total) : $item->invoice_trading_total }}</td>
            <td>{{ $item->warehouse_name }}</td>
            <td>{{ $item->description }}</td>
            <td class="text-end">{{ $formatNumber ? formatNumber($item->load_quantity_realization) : $item->load_quantity_realization }}</td>
            <td class="text-end">{{ $formatNumber ? formatNumber($item->unload_quantity_realization) : $item->unload_quantity_realization }}</td>
            <td class="text-end">{{ $formatNumber ? formatNumber($item->harga) : $item->harga }}</td>
            <td>{{ $item->lost_tolerance_type == 'percent' ? number_format($item->lost_tolerance * 100, 2) . '%' : "$item->lost_tolerance" }} </td>
            <td class="text-end">{{ $formatNumber ? formatNumber($item->losses) : $item->losses }}</td>
            <td class="text-end">{{ $formatNumber ? formatNumber($item->losses_percentage) : $item->losses_percentage }}%</td>
            <td class="text-end">{{ $formatNumber ? formatNumber($item->quantity_invoice) : $item->quantity_invoice }}</td>
            <td class="text-end">{{ $formatNumber ? formatNumber($item->invoice_trading_additional_after_tax) : $item->invoice_trading_additional_after_tax }}</td>
        </tr>
    @endforeach
@else
    @foreach ($data->groupBy('invoice_trading_code') ?? [] as $key => $items)
        @foreach ($items as $key2 => $item)
            <tr>
                <td>{{ $iteration++ }}</td>
                <td>{{ $item->target_delivery }}</td>
                <td>{{ $item->customer_name }}</td>
                @if ($key2 == 0)
                    <td rowspan="{{ $items->count() ?? 0 }}" style="vertical-align: middle">{{ $item->invoice_trading_code }}</td>
                @endif
                <td>{{ $item->branch_name }}</td>
                <td>{{ $item->code }}</td>
                <td>{{ $item->sale_order_code }}</td>
                <td class="text-end">{{ $formatNumber ? formatNumber($item->delivery_main_total) : $item->delivery_main_total }}</td>
                <td class="text-end">{{ $formatNumber ? formatNumber($item->delivery_additional_total) : $item->delivery_additional_total }}</td>
                @if ($key2 == 0)
                    <td class="text-end" rowspan="{{ $items->count() ?? 0 }}" style="vertical-align: middle">{{ $formatNumber ? formatNumber($item->invoice_trading_total) : $item->invoice_trading_total }}</td>
                @endif
                <td>{{ $item->warehouse_name }}</td>
                <td>{{ $item->description }}</td>
                <td class="text-end">{{ $formatNumber ? formatNumber($item->load_quantity_realization) : $item->load_quantity_realization }}</td>
                <td class="text-end">{{ $formatNumber ? formatNumber($item->unload_quantity_realization) : $item->unload_quantity_realization }}</td>
                <td class="text-end">{{ $formatNumber ? formatNumber($item->harga) : $item->harga }}</td>
                <td>{{ $item->lost_tolerance_type == 'percent' ? number_format($item->lost_tolerance * 100, 2) . '%' : "$item->lost_tolerance" }} </td>
                <td class="text-end">{{ $formatNumber ? formatNumber($item->tolerance) : $item->tolerance }}</td>
                <td class="text-end">{{ $formatNumber ? formatNumber($item->losses) : $item->losses }}</td>
                <td class="text-end">{{ $formatNumber ? formatNumber($item->losses_percentage) : $item->losses_percentage }}%</td>
                @if ($key2 == 0)
                    <td class="text-end" rowspan="{{ $items->count() ?? 0 }}" style="vertical-align: middle">{{ $formatNumber ? formatNumber($item->quantity_invoice) : $item->quantity_invoice }}</td>
                @endif
            </tr>
        @endforeach
    @endforeach
@endif
