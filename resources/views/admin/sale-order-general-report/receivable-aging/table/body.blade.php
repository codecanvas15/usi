@foreach ($data as $item)
    <tr>
        <td class="font-small-1">{{ $loop->iteration }}</td>
        <td class="font-small-1">{{ $item->code }}</td>
        <td class="font-small-1">{{ $item->nama }}</td>
        <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->total) : $item->total }}</td>
        <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->not_overdue) : $item->not_overdue }}</td>
        <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->first_group) : $item->first_group }}</td>
        <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->second_group) : $item->second_group }}</td>
        <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->third_group) : $item->third_group }}</td>
        <td class="font-small-1" align="right">{{ $formatNumber ? formatNumber($item->fourth_group) : $item->fourth_group }}</td>
    </tr>
@endforeach
