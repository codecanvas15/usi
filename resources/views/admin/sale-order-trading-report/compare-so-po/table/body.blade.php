@foreach ($data as $parent_key => $itemChild)
    @foreach ($itemChild->pairings ?? [] as $pairing_key => $pairing)
        <tr>
            @if ($pairing_key == 0)
                <td class="font-small-1">{{ $parent_key + 1 }}</td>
                <td class="font-small-1">{{ localDate($itemChild->tanggal) }}</td>
                <td class="font-small-1">{{ $itemChild->nomor_so }}</td>
                <td class="font-small-1">{{ $itemChild->item_name }}</td>
                <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($pairing->so_qty) : $pairing->so_qty }}</td>
                <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($pairing->so_price) : $pairing->so_price }}</td>
                <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($pairing->so_subtotal) : $pairing->so_subtotal }}</td>
                <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($pairing->so_tax) : $pairing->so_tax }}</td>
                <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($pairing->so_total) : $pairing->so_total }}</td>
            @else
                <td class="font-small-1"></td>
                <td class="font-small-1"></td>
                <td class="font-small-1"></td>
                <td class="font-small-1"></td>
                <td class="font-small-1"></td>
                <td class="font-small-1"></td>
                <td class="font-small-1"></td>
                <td class="font-small-1"></td>
                <td class="font-small-1"></td>
            @endif
            <td class="font-small-1">{{ $pairing->nomor_po }}</td>
            <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($pairing->po_qty) : $pairing->po_qty }}</td>
            <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($pairing->po_price) : $pairing->po_price }}</td>
            <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($pairing->po_subtotal) : $pairing->po_subtotal }}</td>
            <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($pairing->po_tax) : $pairing->po_tax }}</td>
            <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($pairing->po_total) : $pairing->po_total }}</td>
        </tr>
    @endforeach
    <tr>
        <td class="font-small-1"></td>
        <td class="font-small-1"></td>
        <td class="font-small-1"></td>
        <td class="font-small-1"><b>Total SO</b></td>
        <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($itemChild->total_so_qty) : $itemChild->total_so_qty }}</b></td>
        <td class="font-small-1" align="right"></td>
        <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($itemChild->total_so_subtotal) : $itemChild->total_so_subtotal }}</b></td>
        <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($itemChild->total_so_tax) : $itemChild->total_so_tax }}</b></td>
        <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($itemChild->total_so_total) : $itemChild->total_so_total }}</b></td>
        <td class="font-small-1"><b>Total PO</b></td>
        <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($itemChild->total_po_qty) : $itemChild->total_po_qty }}</b></td>
        <td class="font-small-1" align="right"></td>
        <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($itemChild->total_po_subtotal) : $itemChild->total_po_subtotal }}</b></td>
        <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($itemChild->total_po_tax) : $itemChild->total_po_tax }}</b></td>
        <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($itemChild->total_po_total) : $itemChild->total_po_total }}</b></td>
    </tr>
@endforeach
<tr>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
</tr>
<tr>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
</tr>
<tr>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
    <td class="font-small-1">
        <br>
    </td>
</tr>
<tr>
    <td class="font-small-1"></td>
    <td class="font-small-1"></td>
    <td class="font-small-1"></td>
    <td class="font-small-1"><b>Total SO Keseluruhan</b></td>
    <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($data->sum('total_so_qty')) : $data->sum('total_so_qty') }}</b></td>
    <td class="font-small-1" align="right"></td>
    <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($data->sum('total_so_subtotal')) : $data->sum('total_so_subtotal') }}</b></td>
    <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($data->sum('total_so_tax')) : $data->sum('total_so_tax') }}</b></td>
    <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($data->sum('total_so_total')) : $data->sum('total_so_total') }}</b></td>
    <td class="font-small-1"><b>Total PO Keseluruhan</b></td>
    <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($data->sum('total_po_qty')) : $data->sum('total_po_qty') }}</b></td>
    <td class="font-small-1" align="right"></td>
    <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($data->sum('total_po_subtotal')) : $data->sum('total_po_subtotal') }}</b></td>
    <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($data->sum('total_po_tax')) : $data->sum('total_po_tax') }}</b></td>
    <td class="font-small-1" align="right"><b>{{ $formatNumber ? formatNumber($data->sum('total_po_total')) : $data->sum('total_po_total') }}</b></td>
</tr>
