@php
    $totalDpp = 0;
    $totalPpn = 0;
    $totalOtherTaxes = 0;
    $totalForeign = 0;
    $totalAmount = 0;
    
    foreach ($data as $item) {
        foreach ($item->products ?? [] as $product) {
            $totalDpp += $product['dpp'] ?? 0;
            $totalPpn += $product['ppn'] ?? 0;
            $totalOtherTaxes += $product['other_taxes'] ?? 0;
            $totalForeign += $product['total_foreign'] ?? 0;
            $totalAmount += $product['total'] ?? 0;
        }
    }
@endphp

<tr>
    <td colspan="10" class="text-center">Total</td>
    <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('qty')) : $data->sum('qty') }}</td>
    <td></td>
    <td class="text-end">{{ $formatNumber ? formatNumber($totalDpp) : $totalDpp }}</td>
    <td class="text-end">{{ $formatNumber ? formatNumber($totalPpn) : $totalPpn }}</td>
    <td class="text-end">{{ $formatNumber ? formatNumber($totalOtherTaxes) : $totalOtherTaxes }}</td>
    <td class="text-end">{{ $formatNumber ? formatNumber($totalForeign) : $totalForeign }}</td>
    <td></td>
    <td class="text-end">{{ $formatNumber ? formatNumber($totalAmount) : $totalAmount }}</td>
</tr>
