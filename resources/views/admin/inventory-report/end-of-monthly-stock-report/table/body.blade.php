@foreach ($item->data as $item_data)
    <tr>
        <td>{{ $item_data->item_name }}</td>
        <td>{{ $item_data->unit_name }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_data->stock_before) : $item_data->stock_before }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_data->value_before) : $item_data->value_before }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_data->stock_in) : $item_data->stock_in }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_data->value_in) : $item_data->value_in }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_data->quantity) : $item_data->quantity }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_data->value) : $item_data->value }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_data->stock_out) : $item_data->stock_out }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_data->value_out) : $item_data->value_out }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_data->stock_final) : $item_data->stock_final }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_data->value_final) : $item_data->value_final }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item_data->price) : $item_data->price }}</td>
    </tr>
@endforeach
