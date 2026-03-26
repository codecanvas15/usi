@php
    $index = 1;
@endphp

@foreach ($data as $item)
    @forelse ($item->details as $detail)
        <tr>
            <td>{{ $loop->index == 0 ? $index : '' }}</td>
            <td>{{ $loop->index == 0 ? $item->code : '' }}</td>
            <td>{{ $loop->index == 0 ? localDate($item->date) : '' }}</td>
            <td>{{ $loop->index == 0 ? $item->employee : '' }}</td>
            <td>{{ $loop->index == 0 ? $item->description ?? '-' : '' }}</td>
            <td>{{ $loop->index == 0 ? ($formatNumber ? formatNumber($item->amount) : $item->amount) : '' }}</td>
            <td>{{ $loop->index == 0 ? ($formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate) : '' }}</td>
            <td>{{ $loop->index == 0 ? ($formatNumber ? formatNumber($item->amount_local) : $item->amount_local) : '' }}</td>
            <td>{{ $loop->index == 0 ? $item->status : '' }}</td>

            <td>{{ localDate($detail->date) }}</td>
            <td>{{ $detail->code }}</td>
            <td>{{ $formatNumber ? formatNumber($detail->exchange_rate) : $detail->exchange_rate }}</td>
            <td>{{ $formatNumber ? formatNumber($detail->amount_to_return) : $detail->amount_to_return }}</td>
            <td>{{ $formatNumber ? formatNumber($detail->amount_to_return_local) : $detail->amount_to_return_local }}</td>
            <td>{{ $formatNumber ? formatNumber($detail->amount_remain) : $detail->amount_remain }}</td>
            <td>{{ $formatNumber ? formatNumber($detail->amount_remain_local) : $detail->amount_remain_local }}</td>
        </tr>
    @empty
        <tr>
            <td>{{ $index }}</td>
            <td>{{ $item->code }}</td>
            <td>{{ localDate($item->date) }}</td>
            <td>{{ $item->employee }}</td>
            <td>{{ $item->description ?? '-' }}</td>
            <td>{{ $item->amount }}</td>
            <td>{{ $formatNumber ? formatNumber($item->exchange_rate) : $item->exchange_rate }}</td>
            <td>{{ $formatNumber ? formatNumber($item->amount_local) : $item->amount_local }}</td>
            <td>{{ $item->status }}</td>

            <td colspan="6" class="text-center">Tidak ada data</td>
        </tr>
    @endforelse
    @php
        $index++;
    @endphp
@endforeach
