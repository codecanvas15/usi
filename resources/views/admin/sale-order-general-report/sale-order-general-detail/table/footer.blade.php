@php
    $column_i_formula = $count_row > 0 ? '=SUM(I7:I' . $count_row + 6 . ')' : '';
    $column_l_formula = $count_row > 0 ? '=SUM(L7:L' . $count_row + 6 . ')' : '';
    $column_m_formula = $count_row > 0 ? '=SUM(M7:M' . $count_row + 6 . ')' : '';
    $column_o_formula = $count_row > 0 ? '=SUM(O7:O' . $count_row + 6 . ')' : '';
    $column_p_formula = $count_row > 0 ? '=SUM(P7:P' . $count_row + 6 . ')' : '';
    $column_q_formula = $count_row > 0 ? '=SUM(Q7:Q' . $count_row + 6 . ')' : '';
@endphp

<tr>
    <td colspan="9" class="text-end font-small-1"><b>Total</b></td>
    <td class="text-end font-small-1"><b>{{ $formatNumber && !$is_excel ? formatNumber($total_qty) : $column_i_formula }}</b></td>
    <td></td>
    <td></td>
    <td class="text-end font-small-1"><b>{{ $formatNumber && !$is_excel ? formatNumber($total_subtotal) : $column_l_formula }}</b></td>
    <td class="text-end font-small-1"><b>{{ $formatNumber && !$is_excel ? formatNumber($total_tax) : $column_m_formula }}</b></td>
    <td></td>
    <td class="text-end font-small-1" style="text-align: end;"><b>{{ $formatNumber && !$is_excel ? formatNumber($total_subtotal_idr) : $column_o_formula }}</b></td>
    <td class="text-end font-small-1" style="text-align: end;"><b>{{ $formatNumber && !$is_excel ? formatNumber($total_tax_idr) : $column_p_formula }}</b></td>
    <td class="text-end font-small-1" style="text-align: end;"><b>{{ $formatNumber && !$is_excel ? formatNumber($total_idr) : $column_q_formula }}</b></td>
</tr>
