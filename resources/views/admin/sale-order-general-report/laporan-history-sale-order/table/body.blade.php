
@foreach ($data as $index => $item)
    @php
        $style = '';
        if ($item->payment_status == 'paid') {
            $style = 'background-color: #18d26b';
        }

        $sisa_qty = ($item->amount ?? 0) - ($item->jumlah_dikirim ?? 0);
    @endphp
    <tr>
        <td>{{ $index + 1 }}</td>

        <td @if ($style) style="{{ $style }}" @endif>{{ localDate($item->so_date) }}</td>
        <td @if ($style) style="{{ $style }}" @endif>
            <a href="{{ route('admin.sales-order-general.show', ['sales_order_general' => $item->sog_id]) }}" target="_blank">
                {{ $item->so_code }}
            </a>
        </td>
        <td @if ($style) style="{{ $style }}" @endif>
            <a href="{{ route('admin.sales-order-general.show', ['sales_order_general' => $item->sog_id]) }}" target="_blank">
                {{ $item->external_po ?? '-' }}
            </a>
        </td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->customer_name }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->keterangan ?? '-' }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->item_code }}</td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end">{{ $formatNumber ? formatNumber($item->amount) : $item->amount }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->unit_name }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $item->tgl_do ? localDate($item->tgl_do) : '-' }}</td>
        <td @if ($style) style="{{ $style }}" @endif>
            <a href="{{ route('admin.delivery-order-general.show', ['delivery_order_general' => $item->dog_id]) }}" target="_blank">
                {{ $item->do_code ?? '-' }}
            </a>
        </td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end">{{ $formatNumber ? formatNumber($item->jumlah_dikirim) : $item->jumlah_dikirim }}</td>
        <td @if ($style) style="{{ $style }}" @endif>
            <a href="{{ route('admin.invoice-general.show', ['invoice_general' => $item->ig_id]) }}" target="_blank">
                {{ $item->invoice_code ?? '-' }}
            </a>
        </td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end">{{ $formatNumber ? formatNumber($sisa_qty) : $sisa_qty }}</td>
        <td @if ($style) style="{{ $style }}" @endif>
            <div class="badge badge-lg badge-{{ payment_status()[$item->payment_status]['color'] ?? null }}">
                {{ payment_status()[$item->payment_status]['label'] ?? null }}
            </div>
        </td>
    </tr>
@endforeach


