@php
    $no = 1;
@endphp
@foreach ($item as $itemChild)
    @php
        $style = '';
        if ($itemChild->payment_status == 'paid') {
            $style = 'background-color: #18d26b';
        }
    @endphp
    <tr>
        <td @if ($style) style="{{ $style }}" @endif>{{ $number }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ localDate($itemChild->date) }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $key }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $itemChild->po_external }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $itemChild->code }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $itemChild->reference }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $itemChild->customer_name }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $itemChild->branch_name }}</td>
        <td @if ($style) style="{{ $style }}" @endif height="{{ 15 * count($itemChild->delivery_orders) }}">
            @forelse ($itemChild->delivery_orders as $do)
                <p>{{ $do['code'] ?? null }}</p>
            @empty
                DO Tidak ada
            @endforelse
        </td>
        <td @if ($style) style="{{ $style }}" @endif>
            @forelse ($itemChild->delivery_orders as $do)
                <p>{{ $do['target_delivery'] ? date('d-m-Y', strtotime($do['target_delivery'])) : null }}</p>
            @empty
                DO Tidak ada
            @endforelse

        </td>
        <td @if ($style) style="{{ $style }}" @endif>{{ localDate($itemChild->due_date) }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $formatNumber ? formatNumber($itemChild->jumlah) : $itemChild->jumlah }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $formatNumber ? formatNumber($itemChild->losses ?? 0) : $itemChild->losses ?? 0 }}</td>
        <td @if ($style) style="{{ $style }}" @endif></td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $formatNumber ? formatNumber($itemChild->harga) : $itemChild->harga }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $formatNumber ? formatNumber($itemChild->subtotal) : $itemChild->subtotal }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $formatNumber ? formatNumber($itemChild->subtotal_after_tax - $itemChild->subtotal) : $itemChild->subtotal_after_tax - $itemChild->subtotal }}</td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end">{{ $formatNumber ? formatNumber($itemChild->total) : $itemChild->total }}</td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end">{{ $formatNumber ? formatNumber($itemChild->exchange_rate) : $itemChild->exchange_rate }}</td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end">{{ $formatNumber ? formatNumber($itemChild->total_final) : $itemChild->total_final }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $itemChild->payment_status }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $itemChild->customer_npwp }}</td>
    </tr>
    @php
        $no++;
    @endphp
@endforeach
