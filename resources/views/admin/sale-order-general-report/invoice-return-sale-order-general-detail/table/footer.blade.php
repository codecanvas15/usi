<tr>
    <td colspan="2" class="text-center">Total</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->details->sum('return_qty')) : $item->details->sum('return_qty') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->details->sum('price')) : $item->details->sum('price') }}</td>

    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->details->sum('subtotal')) : $item->details->sum('subtotal') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->details->sum('tax_amount')) : $item->details->sum('tax_amount') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->details->sum('total')) : $item->details->sum('total') }}</td>

    <td class="text-end text-right"></td>

    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->details->sum('subtotal_local')) : $item->details->sum('subtotal_local') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->details->sum('tax_amount_local')) : $item->details->sum('tax_amount_local') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->details->sum('total_local')) : $item->details->sum('total_local') }}</td>
</tr>
