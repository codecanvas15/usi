@foreach ($item as $itemChild)
    @php
        $style = '';
        if ($itemChild->payment_status == 'paid') {
            $style = 'background-color: #18d26b';
        }
    @endphp
    <tr>
        <td @if ($style) style="{{ $style }}" @endif>{{ $itemChild->iteration }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ localDate($itemChild->date) }}</td>
        <td @if ($style) style="{{ $style }}" @endif>
            @forelse ($itemChild->so_codes ?? [] as $so_code)
                <p><a href="{{ route('admin.sales-order-general.show', ['sales_order_general' => $so_code->sale_order_general_id]) }}" target="_blank">{{ $so_code->so_code }}</a></p>
            @empty
                <p><a href="{{ route('admin.sales-order-general.show', ['sales_order_general' => $itemChild->sale_order_general_id ?? 1]) }}" target="_blank">{{ $itemChild->so_code }} {{ $itemChild->sale_order_general_id }}</a></p>
            @endforelse
        </td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $itemChild->code }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $itemChild->reference }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $itemChild->customer_name }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $itemChild->branch_name }}</td>
        <td @if ($style) style="{{ $style }}" @endif>
            @forelse ($itemChild->delivery_orders as $item_2)
                <p>{{ $item_2['code'] }}</p>
            @empty
                <p>Tidak ada DOG</p>
            @endforelse
        </td>
        <td @if ($style) style="{{ $style }}" @endif>
            @forelse ($itemChild->delivery_orders as $item_2)
                <p>{{ $item_2['target_delivery'] }}</p>
            @empty
                <p>Tidak ada DOG</p>
            @endforelse
        </td>
        <td @if ($style) style="{{ $style }}" @endif>{{ $itemChild->reference }}</td>
        <td @if ($style) style="{{ $style }}" @endif>{{ localDate($itemChild->due_date) }}</td>
        {{-- <td @if ($style) style="{{ $style }}" @endif></td> --}}
        <td @if ($style) style="{{ $style }}" @endif class="text-end text-right">{{ $formatNumber ? formatNumber($itemChild->total_1) : $itemChild->total_1 }}</td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end text-right">{{ $formatNumber ? formatNumber($itemChild->exchange_rate) : $itemChild->exchange_rate }}</td>
        <td @if ($style) style="{{ $style }}" @endif class="text-end text-right">{{ $formatNumber ? formatNumber($itemChild->total_2) : $itemChild->total_2 }}</td>
        <td @if ($style) style="{{ $style }}" @endif>
            <div class="badge badge-lg badge-{{ sale_order_general_status()[$itemChild->status]['color'] }}">
                {{ sale_order_general_status()[$itemChild->status]['label'] . ' - ' . sale_order_general_status()[$itemChild->status]['text'] }}
            </div>
            <div class="badge badge-lg badge-{{ payment_status()[$itemChild->payment_status]['color'] }}">
                {{ payment_status()[$itemChild->payment_status]['label'] . ' - ' . payment_status()[$itemChild->payment_status]['text'] }}
            </div>
            {{--  {{ $itemChild->payment_status }} --}}
        </td>
    </tr>
@endforeach
