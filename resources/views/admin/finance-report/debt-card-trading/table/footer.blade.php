@php
    $total_beginning_balance = 0;
    $total_invoice = 0;
    $total_invoice_idr = 0;
    $total_payment = 0;
    $total_payment_idr = 0;

    $start_row = 7;
    $end_row = 7;

    $grand_total_left_to_pay_formula = '=';
    $grand_total_invoice_formula = '=';
    $grand_total_payment_formula = '=';
@endphp
@foreach ($data as $item)
    @php
        $start_row += 2;
        $grand_total_left_to_pay_formula .= 'K' . $start_row;
        $start_row += count($item['invoices']) + 1;

        $grand_total_invoice_formula .= 'I' . $start_row;
        $grand_total_payment_formula .= 'J' . $start_row;
        $end_row += count($item['invoices']) + 3;

        if ($loop->iteration < count($data)) {
            $grand_total_left_to_pay_formula .= '+';
            $grand_total_invoice_formula .= '+';
            $grand_total_payment_formula .= '+';
        }

        $total_beginning_balance += $item['beginning_balance'];
        $total_invoice += $item['invoices']->sum('amount_to_receive');
        $total_invoice_idr += $item['invoices']->sum('amount_to_receive_idr');
        $total_payment += $item['invoices']->sum('receive_amount');
        $total_payment_idr += $item['invoices']->sum('receive_amount_idr');
    @endphp
@endforeach
@php
    $end_row += 2;

    $grand_total_left_to_pay_formula .= '+I' . $end_row . '-J' . $end_row;
@endphp
<tr>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
</tr>
<tr>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"><b>Grand Total</b></td>
    <td class="font-xsmall-3" align="right">
        <b>{{ $formatNumber ? formatNumber($total_invoice_idr) : $grand_total_invoice_formula }}</b>
    </td>
    <td class="font-xsmall-3" align="right">
        <b>{{ $formatNumber ? formatNumber($total_payment_idr) : $grand_total_payment_formula }}</b>
    </td>
    <td class="font-xsmall-3" align="right">
        <b>{{ $formatNumber ? formatNumber($total_beginning_balance + $total_invoice_idr - $total_payment_idr) : $grand_total_left_to_pay_formula }}</b>
    </td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
    <td class="font-xsmall-3"></td>
</tr>
