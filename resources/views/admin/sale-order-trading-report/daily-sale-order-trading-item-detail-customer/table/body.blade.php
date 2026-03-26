@foreach ($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item['tanggal'] }}</td>
        <td>{{ $item['customer_name'] }}</td>
        <td>{{ $item['it_code'] }}</td>
        <td>{{ $item['code'] }}</td>
        <td>{{ $item['item_name'] }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item['quantity']) : $item['quantity'] }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item['sub_total']) : $item['sub_total'] }}</td>
        <td class="text-end">{{ $formatNumber ? formatNumber($item['sub_total_idr']) : $item['sub_total_idr'] }}</td>
    </tr>
@endforeach
