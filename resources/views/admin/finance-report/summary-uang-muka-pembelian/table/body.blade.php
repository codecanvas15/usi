@php
    $start_row = 6;
@endphp
@forelse ($data as $key => $d)
    <tr>
        <td></td>
        <td class="font-small-1">
            <b>{{ $d->vendor_nama }}</b>
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td align="center" class="font-small-1">
            SALDO
        </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td align="right" class="font-small-1">{{ $number_format ? formatNumber($d->previous_balance) : $d->previous_balance }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td align="right" class="font-small-1">{{ $number_format ? formatNumber($d->previous_balance_exchanged) : $d->previous_balance_exchanged }}</td>
    </tr>
    @php
        $balance_row = $start_row + 2;
        $start_row += 2;
    @endphp
    @foreach ($d->current_data as $current_data)
        @php
            $start_row++;
            $amount_exchanged = "=F$start_row*I$start_row";
            $returned_amount_exchanged = "=G$start_row*I$start_row";
            $remaining_exchanged = "=J$start_row-K$start_row";
        @endphp
        <tr>
            <td class="font-small-1" align="center">{{ $loop->iteration }}.</td>
            <td class="font-small-1" align="center">{{ $current_data->bank_code }}</td>
            <td class="font-small-1" align="center">
                <a href="{{ route('admin.fund-submission.show', ['fund_submission' => $current_data->fund_submission_id]) }}" target="_blank">
                    {{ $current_data->fund_submission_code }}
                </a>
            </td>
            {{-- Start --}}
            <td class="font-small-1" align="center">
                @php
                    $route = match ($current_data->purchase_kode_model_reference) {
                        'App\\Models\\PoTrading' => 'admin.purchase-order.show',
                        'App\\Models\\PurchaseTransport' => 'admin.purchase-order-transport.show',
                        'App\\Models\\PurchaseOrderService' => 'admin.purchase-order-service.show',
                        'App\\Models\\PurchaseOrderGeneral' => 'admin.purchase-order-general.show',
                        default => null,
                    };

                    $route_param = match ($current_data->purchase_kode_model_reference) {
                        'App\\Models\\PoTrading' => 'purchase_order',
                        'App\\Models\\PurchaseTransport' => 'purchase_order_transport',
                        'App\\Models\\PurchaseOrderService' => 'purchase_order_service',
                        'App\\Models\\PurchaseOrderGeneral' => 'purchase_order_general',
                        default => null,
                    };
                @endphp

                @if ($route && $route_param)
                    <a href="{{ route($route, [$route_param => $current_data->purchase_kode_id]) }}" target="_blank">
                        {{ $current_data->purchase_kode }}
                    </a>
                @else
                    {{ $current_data->purchase_kode }}
                @endif
            </td>
            <td class="font-small-1" align="center">
                @php
                    $url = null;

                    if (!empty($row->purchase_down_payment_id)) {
                        $url = route('admin.purchase-down-payment.show', $row->purchase_down_payment_id);
                    } elseif (!empty($row->invoice_down_payment_id)) {
                        $url = route('admin.invoice-down-payment.show', $row->invoice_down_payment_id);
                    }
                @endphp

                @if($url)
                    <a href="{{ $url }}" target="_blank">
                        {{ $row->reference }}
                    </a>
                @else
                    {{ $row->reference ?? '-' }}
                @endif


            </td>
            {{-- End --}}
            <td class="font-small-1" align="center">{{ localDate($current_data->cash_advance_date) }}</td>
            <td class="font-small-1" align="center">{{ $current_data->currency_nama }}</td>
            <td class="font-small-1" align="right">{{ $number_format ? formatNumber($current_data->cash_advance_amount) : $current_data->cash_advance_amount }}</td>
            <td class="font-small-1" align="right">{{ $number_format ? formatNumber($current_data->returned_amount) : $current_data->returned_amount }}</td>
            <td class="font-small-1" align="right">{{ $number_format ? formatNumber($current_data->cash_advance_remaining_amount) : $current_data->cash_advance_remaining_amount }}</td>
            <td class="font-small-1" align="right">{{ $number_format ? formatNumber($current_data->exchange_rate) : $current_data->exchange_rate }}</td>
            <td class="font-small-1" align="right">{{ $number_format ? formatNumber($current_data->cash_advance_amount_exchanged) : $amount_exchanged }}</td>
            <td class="font-small-1" align="right">{{ $number_format ? formatNumber($current_data->returned_amount_exchanged) : $returned_amount_exchanged }}</td>
            <td class="font-small-1" align="right">{{ $number_format ? formatNumber($current_data->cash_advance_remaining_amount_exchanged) : $remaining_exchanged }}</td>
        </tr>
    @endforeach
    @php
        if (count($d->current_data) > 0) {
            $total_k = '=SUM(L' . ($balance_row + 1) . ":L$start_row)";
            $total_l = '=SUM(M' . ($balance_row + 1) . ":M$start_row)";
            $total_m = '=SUM(N' . ($balance_row + 1) . ":N$start_row)+N$balance_row";
        } else {
            $total_k = '';
            $total_l = '';
            $total_m = '=N' . $balance_row;
        }
    @endphp
    <tr>
        <td class="font-small-1" align="center"></td>
        <td class="font-small-1" align="center"><b>TOTAL</b></td>
        <td class="font-small-1" align="center"></td>
        <td class="font-small-1" align="center"></td>
        <td class="font-small-1" align="center"></td>
        <td class="font-small-1" align="center"></td>
        <td class="font-small-1" align="center"></td>
        <td class="font-small-1" align="center"></td>
        <td class="font-small-1" align="center"></td>
        <td class="font-small-1" align="center"></td>
        <td class="font-small-1" align="center"></td>
        <td class="font-small-1" align="right"><b>{{ $number_format ? formatNumber($d->current_data->sum('cash_advance_amount_exchanged')) : $total_k }}</b></td>
        <td class="font-small-1" align="right"><b>{{ $number_format ? formatNumber($d->current_data->sum('returned_amount_exchanged')) : $total_l }}</b></td>
        <td class="font-small-1" align="right"><b>{{ $number_format ? formatNumber($d->current_data->sum('cash_advance_remaining_amount_exchanged') + $d->previous_balance_exchanged) : $total_m }}</b></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    @php
        $start_row += 2;
    @endphp
@empty
    @php
        $start_row += 1;
    @endphp
    <tr>
        <td align="center" colspan="14" class="font-small-1">
            Tidak ada data
        </td>
    </tr>
@endforelse
