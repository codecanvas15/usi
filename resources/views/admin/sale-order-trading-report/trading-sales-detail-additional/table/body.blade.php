@php
    $iteration = 1;
@endphp
@foreach ($data as $item)
    @foreach ($item->products ?? [] as $product)
        <tr>
            <td>{{ $iteration++ }}</td>
            <td>{{ $item->invoice_code }}</td>
            <td>{{ localDate($item->invoice_date) }}</td>
            <td>{{ $item->so_code }}</td>
            <td>{{ $item->no_po_external }}</td>
            <td>{{ $item->tax_invoice }}</td>
            <td>{{ $item->customer_name }}</td>
            <td>{{ $item->branch_name }}</td>
            <td>{{ $product['item_name'] ?? '' }}</td>
            <td>{{ $product['item_code'] ?? '' }}</td>
            <td class="text-end">{{ $formatNumber ? formatNumber($item->qty) : $item->qty }}</td>
            <td>{{ localDate($item->due_date) }}</td>
            <td class="text-end">{{ $formatNumber ? formatNumber($product['dpp'] ?? 0) : $product['dpp'] ?? 0 }}</td>
            <td class="text-end">{{ $formatNumber ? formatNumber($product['ppn']) : $product['ppn'] }}</td>
            <td class="text-end">{{ $formatNumber ? formatNumber($product['other_taxes'] ?? 0) : $product['other_taxes'] ?? 0 }}</td>
            <td class="text-end">{{ $formatNumber ? formatNumber($product['total_foreign'] ?? 0) : $product['total_foreign'] ?? 0 }}</td>
            <td class="text-end">{{ $formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate }}</td>
            <td class="text-end">{{ $formatNumber ? formatNumber($product['total'] ?? 0) : $product['total'] ?? 0 }}</td>
        </tr>
    @endforeach
@endforeach
