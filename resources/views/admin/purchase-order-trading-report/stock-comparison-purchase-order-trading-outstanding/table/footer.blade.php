@php
    $start_row = 7;
@endphp
<tr>
    <td colspan="3" class="text-center"><b>Total</b></td>
    <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($data->sum('po_qty')) : "=SUM(D$start_row:D" . ($start_row + $data->count()) . ')' }}</b></td>
    <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($data->sum('realization_qty')) : "=SUM(E$start_row:E" . ($start_row + $data->count()) . ')' }}</b></td>
    <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($data->sum('po_outstanding_qty')) : "=SUM(F$start_row:F" . ($start_row + $data->count()) . ')' }}</b></td>
    <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($data->sum('stock')) : "=SUM(G$start_row:G" . ($start_row + $data->count()) . ')' }}</b></td>
    <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($data->sum('total')) : "=SUM(H$start_row:H" . ($start_row + $data->count()) . ')' }}</b></td>
</tr>
