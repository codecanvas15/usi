<tr>
    <td colspan="8" class="text-center">Total</td>
    <td>{{ $formatNumber ? formatNumber($total_all->sub_total) : $total_all->sub_total }}</td>
    @foreach ($unique_taxes as $tax)
        <td>{{ $formatNumber ? formatNumber($total_all->taxes->where('tax_id', $tax->tax_id)->sum('total') ?? 0) : $total_all->taxes->where('tax_id', $tax->tax_id)->sum('total') ?? 0 }}</td>
    @endforeach
    <td>{{ $formatNumber ? formatNumber($total_all->total) : $total_all->total }}</td>
    <td></td>
    <td>{{ $formatNumber ? formatNumber($total_all->total_idr) : $total_all->total_idr }}</td>
</tr>
