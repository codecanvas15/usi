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
            <td>{{ $item->no_po_external ?? '-' }}</td>
            <td>{{ localDate($item->tanggal) }}</td>
            <td>{{ $item->customer_name }}</td>
            <td>{{ $item->item_name }}</td>
            <td>{{ $item->item_code ?? '-' }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->amount) : $item->amount }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->sended) : $item->sended }}</td>
            <td class="text-end text-right">{{ $outstanding }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->price) : $item->price }}</td>
            <td class="text-end text-right">{{ $formatNumber ? formatNumber($item->outstanding_idr) : $item->outstanding_idr }}</td>
        </tr>
    @endif
@endforeach
