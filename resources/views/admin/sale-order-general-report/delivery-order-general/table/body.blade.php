@foreach ($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ localDate($item->date) }}</td>
        <td>{{ $item->customer_name }}</td>
        <td>{{ $item->branch_name }}</td>
        <td>{!! implode('<br> ', $item->invoices) !!}</td>
        <td>{{ $item->code }}</td>
        <td>{{ $item->sale_order_general_code }}</td>
        <td>{{ $item->ware_house_name }}</td>
        <td>{{ $item->description ?? '-' }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->sub_total) : $item->sub_total }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->total_tax) : $item->total_tax }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->total) : $item->total }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->sub_total_final) : $item->sub_total_final }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->total_tax_final) : $item->total_tax_final }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->total_final) : $item->total_final }}</td>
        <td>{{ $item->status }}</td>
    </tr>
@endforeach
