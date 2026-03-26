@php
    $start_row = 7;
@endphp
@foreach ($data as $item)
    @php
        $start_row++;
    @endphp
    <tr>
        <td class="font-small-1">{{ $loop->iteration }}</td>
        <td class="font-small-1">{{ $item->kode }}</td>
        <td class="font-small-1">{{ $item->nama }}</td>
        <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->po_qty) : $item->po_qty }}</td>
        <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->realization_qty) : $item->realization_qty }}</td>
        <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->po_outstanding_qty) : $item->po_outstanding_qty }}</td>
        <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->stock) : $item->stock }}</td>
        <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->total) : "=F$start_row+G$start_row" }}</td>
    </tr>
@endforeach
