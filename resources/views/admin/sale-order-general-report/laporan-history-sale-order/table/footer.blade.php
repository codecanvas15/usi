<tr>
    <td colspan="7" class="text-center font-bold">Total</td>
    <td class="text-end font-bold">
        {{ $formatNumber ? formatNumber($totals['amount']) : number_format($totals['amount'], 2) }}
    </td>
    <td></td>
    <td></td>
    <td></td>
    <td class="text-end font-bold">
        {{ $formatNumber ? formatNumber($totals['jumlah_dikirim']) : number_format($totals['jumlah_dikirim'], 2) }}
    </td>
    <td></td>
    <td class="text-end font-bold">
        {{ $formatNumber ? formatNumber($totals['sisa_qty']) : number_format($totals['sisa_qty'], 2) }}
    </td>
    <td></td>
</tr>
