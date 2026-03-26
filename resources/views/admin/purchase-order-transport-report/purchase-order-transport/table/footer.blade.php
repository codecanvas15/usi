@if ($formatNumber)
    <tr>
        <td class="text-end" colspan="7">Total</td>
        <td class="text-end">{{ formatNumber($data->sum('delivery_order_amount_sum')) }}</td>
        <td class="text-end">{{ formatNumber($data->sum('delivery_order_quantity_sum')) }}</td>
        <td class="text-end">{{ formatNumber($data->sum('purchase_transport_sub_total')) }}</td>
        <td class="text-end"></td>
        <td class="text-end">{{ formatNumber($data->sum('purchase_transport_sub_total_local')) }}</td>
        <td></td>
    </tr>
@else
    <tr>
        <td class="text-end" colspan="7">Total</td>
        <td class="text-end"> =SUM(H{{ 7 }}:H{{ 7 + count($data) - 1 }})</td>
        <td class="text-end"> =SUM(I{{ 7 }}:I{{ 7 + count($data) - 1 }})</td>
        <td class="text-end"></td>
        <td class="text-end"> =SUM(K{{ 7 }}:K{{ 7 + count($data) - 1 }})</td>
        <td class="text-end"> =SUM(L{{ 7 }}:L{{ 7 + count($data) - 1 }})</td>
        <td></td>
    </tr>
@endif
