    <tr>
        <td colspan="6"></td>
        <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('load_quantity')) : $data->sum('load_quantity') }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('unload_quantity_realization')) : $data->sum('unload_quantity_realization') }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('losses_quantity')) : $data->sum('losses_quantity') }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($data->sum('losses_value')) : $data->sum('losses_value') }}</td>
        <td></td>
    </tr>
