@foreach ($data as $item)
    <tr>
        <td>{{ localDate($item->date) }}</td>
        <td>{{ $item->purchase_code }}</td>
        <td>{{ $item->code }}</td>
        <td>{{ $item->vendor_name }}</td>
        <td>{{ $item->customer_name }}</td>
        <td>{{ $item->item_name }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item->price) : $item->price }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item->sended) : $item->sended }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item->received) : $item->received }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item->sub_total) : $item->sub_total }}</td>
        @foreach ($taxNames as $taxName)
            <td class="text-end">{{ $formatNumber ? formatNumber($item->taxes->where('tax_name', $taxName)->first()?->total) : $item->taxes->where('tax_name', $taxName)->first()?->total }}</td>
        @endforeach
        <td class="text-end">{{ $formatNumber ? formatNumber($item->total) : $item->total }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item->sub_total_local) : $item->sub_total_local }}</td>
        @foreach ($taxNames as $taxName)
            <td class="text-end">{{ $formatNumber ? formatNumber($item->taxes->where('tax_name', $taxName)->first()?->total_local) : $item->taxes->where('tax_name', $taxName)->first()?->total_local }}</td>
        @endforeach
        <td class="text-end">{{ $formatNumber ? formatNumber($item->total_local) : $item->total_local }}</td>
        <td>{{ $item->status }}</td>
    </tr>
@endforeach
