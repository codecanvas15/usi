@foreach ($data as $item)
    <tr>
        <td>{{ localDate($item->date) }}</td>
        <td>{{ $item->purchase_code }}</td>
        <td>{{ $item->code }}</td>
        <td>{{ $item->vendor_name }}</td>
        <td>{{ $item->item_name }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->quantity) : $item->quantity }}</td>
        <td>{{ $item->unit_name }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->price) : $item->price }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->sub_total) : $item->sub_total }}</td>
        @foreach ($unique_taxes as $tax)
            <th class="text-end text-right">{{ $formatNumber ? formatNumber($item->taxes->where('tax_id', $tax->tax_id)->sum('tax_amount') ?? 0) : $item->taxes->where('tax_id', $tax->tax_id)->sum('tax_amount') ?? 0 }}</th>
        @endforeach
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->total) : $item->total }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->total_idr) : $item->total_idr }}</td>
    </tr>
@endforeach
