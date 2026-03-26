@php
    $body_start_row = 7;
@endphp
@foreach ($data as $item)
    @php
        $body_start_row += 2;
        $per_customer_total_invoice_formula = "=SUM(I$body_start_row:I" . $body_start_row + count($item['invoices']) . ')';
        $per_customer_total_payment_formula = "=SUM(J$body_start_row:J" . $body_start_row + count($item['invoices']) . ')';
        $per_customer_left_to_pay_formula = "=K$body_start_row+I" . ($body_start_row + count($item['invoices']) + 1) . '-J' . ($body_start_row + count($item['invoices']) + 1);
        $balance = $item['beginning_balance'];
        $balance_idr = $item['beginning_balance_idr'];
    @endphp
    <tr>
        <th class="font-xsmall-3" align="left" colspan="16">{{ $item['customer_name'] }} - {{ $item['customer_code'] }}</th>
    </tr>
    <tr>
        <td class="font-xsmall-3" align="center">Saldo Awal</td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3" align="right">{{ $formatNumber ? formatNumber($item['beginning_balance_idr']) : $item['beginning_balance_idr'] }}</td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
    </tr>
    @foreach ($item['invoices'] as $invoice)
        @php
            $body_start_row += 1;

            $body_row_formula = '=K' . ($body_start_row - 1) . "+I$body_start_row-J$body_start_row";
            $balance = $balance + $invoice->amount_to_receive - $invoice->receive_amount;
            $balance_idr = $balance_idr + $invoice->amount_to_receive_idr - $invoice->receive_amount_idr;
        @endphp
        <tr>
            {{-- !! A --}}
            <td class="font-xsmall-3" align="center">{{ localDate($invoice->date) }}</td>
            {{-- !! B --}}
            <td class="font-xsmall-3">{{ $invoice->type }}</td>
            {{-- !! C --}}
            <td class="font-xsmall-3">
                @php
                    $modelClass = class_basename($invoice->invoice_model); // e.g. InvoiceGeneral
                    $routeMap = [
                        'InvoiceGeneral' => 'admin.invoice-general.show',
                        'InvoiceDownPayment' => 'admin.invoice-down-payment.show',
                        'InvoiceTrading' => 'admin.invoice-trading.show',
                        'InvoiceReturn' => 'admin.invoice-return.show',
                    ];
                @endphp

                @if (isset($routeMap[$modelClass]))
                    <a href="{{ route($routeMap[$modelClass], $invoice->invoice_id) }}" target="_blank">
                        {{ $invoice->invoice_code ?? '-' }}
                    </a>
                @else
                    {{ $invoice->invoice_code ?? '-' }}
                @endif

                @if ($invoice->model == 'App\Models\InvoiceDownPayment')
                    <a href="{{ route('admin.invoice-down-payment.show', ['invoice_down_payment' => $invoice->reference_id]) }}" target="_blank">
                        {{ $invoice->invoice_down_payment_code ?? '-' }}
                    </a>
                @endif
            </td>
            {{-- !! D --}}
            <td class="font-xsmall-3">
                @if ($invoice->receivables_payment_id)
                    <a href="{{ route('admin.receivables-payment.show', $invoice->receivables_payment_id) }}" target="_blank">
                        {{ $invoice->bank_code }}
                    </a>
                @else
                    {{ $invoice->bank_code }}
                @endif
            </td>
            {{-- !! E  --}}
            <td class="font-xsmall-3" align="right">{{ $formatNumber ? formatNumber($invoice->amount_to_receive) : $invoice->amount_to_receive }}</td>
            {{-- !! F  --}}
            <td class="font-xsmall-3" align="right">{{ $formatNumber ? formatNumber($invoice->receive_amount) : $invoice->receive_amount }}</td>
            {{-- !! G  --}}
            <td class="font-xsmall-3" align="right">{{ $formatNumber ? formatNumber($balance) : $balance }}</td>
            {{-- !! H  --}}
            <td class="font-xsmall-3" align="right">{{ $formatNumber ? formatNumber($invoice->exchange_rate) : $invoice->exchange_rate }}</td>
            {{-- !! I  --}}
            <td class="font-xsmall-3" align="right">{{ $formatNumber ? formatNumber($invoice->amount_to_receive_idr) : $invoice->amount_to_receive_idr }}</td>
            {{-- !! J  --}}
            <td class="font-xsmall-3" align="right">{{ $formatNumber ? formatNumber($invoice->receive_amount_idr) : $invoice->receive_amount_idr }}</td>
            {{-- !! K  --}}
            <td class="font-xsmall-3" align="right">{{ $formatNumber ? formatNumber($balance_idr) : $body_row_formula }}</td>
            {{-- !! L  --}}
            <td class="font-xsmall-3" align="right">{{ $formatNumber ? formatNumber($invoice->exchange_rate) : $invoice->exchange_rate }}</td>
            {{-- !! M  --}}
            <td class="font-xsmall-3" align="right">{{ $formatNumber ? formatNumber($invoice->exchange_rate_gap) : $invoice->exchange_rate_gap }}</td>
            <td class="font-xsmall-3">{{ $invoice->due }}</td>
            <td class="font-xsmall-3">{{ localDate($invoice->due_date) }}</td>
            <td class="font-xsmall-3" style="max-width:300px">{{ $invoice->note }}</td>
        </tr>
    @endforeach
    <tr>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"><b>TOTAL</b></td>
        <td class="font-xsmall-3" align="right">
            <b>{{ $formatNumber ? formatNumber($item['invoices']->sum('amount_to_receive_idr')) : $per_customer_total_invoice_formula }}</b>
        </td>
        <td class="font-xsmall-3" align="right">
            <b>{{ $formatNumber ? formatNumber($item['invoices']->sum('receive_amount_idr')) : $per_customer_total_payment_formula }}</b>
        </td>
        <td class="font-xsmall-3" align="right">
            <b>{{ $formatNumber ? formatNumber($item['beginning_balance'] + $item['invoices']->sum('amount_to_receive_idr') - $item['invoices']->sum('receive_amount_idr')) : $per_customer_left_to_pay_formula }}</b>
        </td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
        <td class="font-xsmall-3"></td>
    </tr>
    @php
        $body_start_row += 1;
    @endphp
@endforeach
