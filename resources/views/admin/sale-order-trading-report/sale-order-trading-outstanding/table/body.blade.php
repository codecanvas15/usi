@php
    $no = 1;
@endphp
@foreach ($data as $item)
    @php
        $outstanding = $formatNumber ? formatNumber($item->outstanding) : $item->outstanding;
    @endphp
    @if ($item->outstanding > 0)
        <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $item->kode }}</td>
            <td>{{ localDate($item->tanggal) }}</td>
            <td>{{ $item->customer_name }}</td>
            <td>{{ $item->item_name }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->jumlah) : $item->jumlah }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->sudah_dikirim) : $item->sudah_dikirim }}</td>
            <td class="text-end text-right">{{ $outstanding }}</td>
        </tr>
    @endif
@endforeach
