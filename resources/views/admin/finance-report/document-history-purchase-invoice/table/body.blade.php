@foreach ($data as $key => $item)
    <tr>
        <td class="font-xsmall-3">{{ $key++ + 1 }}</td>
        <td class="font-xsmall-3">{{ $item->vendor_name . ' - ' . $item->vendor_code }}</td>
        <td class="font-xsmall-3">
            @php
                $type = $item?->purchase_tipe;
                $link = '';
                if ($type == 'jasa') {
                    $link = route('admin.purchase-order-service.show', ['purchase_order_service' => $item->purchase_model_id]);
                } elseif ($type == 'general') {
                    $link = route('admin.purchase-order-general.show', ['purchase_order_general' => $item->purchase_model_id]);
                } elseif ($type == 'trading') {
                    $link = route('admin.purchase-order.show', ['purchase_order' => $item->purchase_model_id]);
                } elseif ($type == 'transportir') {
                    $link = route('admin.purchase-order-transport.show', ['purchase_order_transport' => $item->purchase_model_id]);
                }
            @endphp

            <a href="{{ $link }}" target="_blank">{{ $item->purchase_code }}</a>
        </td>
        <td class="font-xsmall-3">{{ localDate($item->purchase_date) }}</td>
        <td class="font-xsmall-3">

            @if ($item->lpb_id)
                @php
                    $type = $item?->lpb_tipe;
                    $link = '';
                    if ($type == 'jasa') {
                        $link = route('admin.item-receiving-report-service.show', ['item_receiving_report_service' => $item->lpb_id]);
                    } elseif ($type == 'general') {
                        $link = route('admin.item-receiving-report-general.show', ['item_receiving_report_general' => $item->lpb_id]);
                    } elseif ($type == 'trading') {
                        $link = route('admin.item-receiving-report-trading.show', ['item_receiving_report_trading' => $item->lpb_id]);
                    } elseif ($type == 'transport') {
                        $link = route('admin.item-receiving-report-transport.show', ['item_receiving_report_transport' => $item->lpb_id]);
                    }
                @endphp
                <a href="{{ $link }}" target="_blank">{{ $item->lpb_code }}</a>
            @endif
        </td>
        <td class="font-xsmall-3">{{ localDate($item->lpb_date) }}</td>
        <td class="font-xsmall-3">
            @if ($item->si_id)
                <a href="{{ route('admin.supplier-invoice.show', ['supplier_invoice' => $item->si_id]) }}" target="_blank">{{ $item->si_code }}</a>
            @endif
        </td>
        <td class="font-xsmall-3">{{ localDate($item->si_date) }}</td>
        <td class="font-xsmall-3">
            @if ($item->ap_id)
                <a href="{{ route('admin.account-payable.show', ['account_payable' => $item->ap_id]) }}" target="_blank">{{ $item->bank_code_mutation ?? $item->ap_code }}</a>
            @endif
        </td>
        <td class="font-xsmall-3">{{ localDate($item->ap_date) }}</td>
    </tr>
@endforeach
