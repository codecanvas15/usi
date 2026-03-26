<tr>
    <td colspan="7" class="text-center">Total</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('delivery_main_total')) : $data->sum('delivery_main_total') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('delivery_additional_total')) : $data->sum('delivery_additional_total') }}</td>
    <td class="text-end text-right">{{ $formatNumber
        ? formatNumber(
            $data->groupBy('invoice_trading_code')->map(function ($item, $keys) {
                    return $item->first()->invoice_trading_total;
                })->sum(),
        )
        : $data->groupBy('invoice_trading_code')->map(function ($item, $keys) {
                return $item->first()->invoice_trading_total;
            })->sum() }}</td>
    <td colspan="2"></td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('load_quantity_realization')) : $data->sum('load_quantity_realization') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('unload_quantity_realization')) : $data->sum('unload_quantity_realization') }}</td>
    <td></td>
    <td></td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('tolerance')) : $data->sum('tolerance') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('losses')) : $data->sum('losses') }}</td>
    <td class="text-end text-right">{{ $formatNumber ? formatNumber($data->sum('losses_percentage')) : $data->sum('losses_percentage') }}</td>
    <td class="text-end text-right">{{ $formatNumber
        ? formatNumber(
            $data->groupBy('invoice_trading_code')->map(function ($item, $keys) {
                    return $item->first()->quantity_invoice;
                })->sum(),
        )
        : $data->groupBy('invoice_trading_code')->map(function ($item, $keys) {
                return $item->first()->quantity_invoice;
            })->sum() }}</td>
</tr>
