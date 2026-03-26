@foreach ($data as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->item_name }}</td>
        <td>{{ $item->kode }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->jumlah) : $item->jumlah }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->sudah_dikirim) : $item->sudah_dikirim }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->outstanding) : $item->outstanding }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->stock) : $item->stock }}</td>
        <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->gap) : $item->gap }}</td>
    </tr>
@endforeach
