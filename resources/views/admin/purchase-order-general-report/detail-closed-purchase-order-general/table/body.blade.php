@foreach ($data as $item)
    <tr>
        <td class="font-small-3">{{ localDate($item->date) }}</td>
        <td class="font-small-3">{{ $item->code }}</td>
        <td class="font-small-3">{{ $item->vendor_name }}</td>
        <td class="font-small-3">{{ $item->vendor_address }}</td>
        <td class="font-small-3">{{ $item->vendor_id }}</td>
        <td class="font-small-3">{{ $item->branch_name }}</td>
        <td class="font-small-3">{{ implode(',', $item->date_receives ?? []) }}</td>
        <td class="font-small-3">{{ $item->payment_description }}</td>
        <td class="font-small-3" align="right">{{ $formatNumber ? formatNumber($item->total_all) : $item->total_all }}</td>
        <td class="font-small-3" align="right">{{ $formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate }}</td>
        <td class="font-small-3" align="right">{{ $formatNumber ? formatNumber($item->total_all * $item->exchange_rate) : $item->total_all * $item->exchange_rate }}</td>
        <td class="font-small-3" align="center">{{ strtoupper($item->status) }}</td>
    </tr>

    @foreach ($item->details as $detail)
        <tr>
            <td class="font-small-3"></td>
            <td class="font-small-3"></td>
            <td class="font-small-3">{{ $detail->item_name }}</td>
            <td class="font-small-3" align="right">{{ $formatNumber ? formatNumber($detail->quantity) : $detail->quantity }}</td>
            <td class="font-small-3" align="center">{{ $detail->unit_name }}</td>
            <td class="font-small-3" align="right">{{ $formatNumber ? formatNumber($detail->price) : $detail->price }}</td>
            <td class="font-small-3"></td>
            <td class="font-small-3"></td>
            <td class="font-small-3" align="right">{{ $formatNumber ? formatNumber($detail->total) : $detail->total }}</td>
            <td class="font-small-3"></td>
            <td class="font-small-3"></td>
            <td class="font-small-3"></td>
        </tr>
    @endforeach
    <tr>
        <td class="font-small-3"></td>
        <td class="font-small-3"></td>
        <td class="font-small-3"></td>
        <td class="font-small-3"></td>
        <td class="font-small-3"></td>
        <td class="font-small-3"></td>
        <td class="font-small-3"></td>
        <td class="font-small-3"></td>
        <td class="font-small-3"></td>
        <td class="font-small-3"></td>
        <td class="font-small-3"></td>
        <td class="font-small-3"></td>
    </tr>
@endforeach
