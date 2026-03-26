<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th class="font-small-1" align="center" rowspan="2">#</th>
            <th class="font-small-1" align="center" >Kode</th>
            <th class="font-small-1" align="center" colspan="2">Customer</th>
            <th class="font-small-1" align="center" colspan="2">Produk &amp; Additional</th>
            <th class="font-small-1" align="center" colspan="5"></th>
        </tr>
        <tr>
            <th class="font-small-1" align="center" >Invoice</th>
            <th class="font-small-1" align="center" colspan="1">Nama</th>
            <th class="font-small-1" align="center" colspan="1">Kode</th>
            <th class="font-small-1" align="center" colspan="1">Nama</th>
            <th class="font-small-1" align="center" colspan="1">Kode</th>
            <th class="font-small-1" align="center" colspan="1">Qty</th>
            <th class="font-small-1" align="center" colspan="1">Total</th>
            <th class="font-small-1" align="center" colspan="1">Nilai HPP</th>
            <th class="font-small-1" align="center" colspan="1">Nilai HPP Transport</th>
            <th class="font-small-1" align="center" colspan="1">Total Pajak</th>
        </tr>
    </thead>
    <tbody>
        @php
            $qty = 0;
            $total = 0;
            $totalTransport = 0;
            $totalAll = 0;
            $nilaihpp = 0;
            $nilaihpptransport = 0;
            $totalpajak = 0;
        @endphp
        @foreach ($data as $index => $item)
            <tr>
                <td rowspan="{{ $item->inv_trading_add_on->count() + 1 }}">{{ $index + 1 }}</td>
                <td rowspan="{{ $item->inv_trading_add_on->count() + 1 }}">{{ $item->kode }}</td>
                <td rowspan="{{ $item->inv_trading_add_on->count() + 1 }}">{{ $item->customer->nama }}</td>
                <td rowspan="{{ $item->inv_trading_add_on->count() + 1 }}">{{ $item->customer->code }}</td>
                <td>{{ $item->item?->nama ?? 'No Product' }}</td>
                <td>{{ $item->item?->kode ?? '' }}</td>
                <td class="text-end">{{ formatNumber($item->jumlah) }}</td>
                <td class="text-end">{{ formatNumber($item->subtotal) }}</td>
                {{-- <td>HPP:{{  $item->invoice_trading_details->first()->delivery_order->type }}</td> --}}
                <td class="text-end">{{ formatNumber($item->invoice_trading_details->map(function ($inv_trading_detail) {
                    return $inv_trading_detail->delivery_order->hpp * $inv_trading_detail->delivery_order->unload_quantity_realization;
                })->sum()) }}</td>
                <td class="text-end">{{ formatNumber($item->nilai_hpp_transport) }}</td>
                <td class="text-end">{{ formatNumber($item->invoice_trading_taxes->sum('amount')) }}</td>
            </tr>
            @php
                $qty += $item->jumlah;
                $total += $item->subtotal;
                $nilaihpp += $item->invoice_trading_details->map(function ($inv_trading_detail) {
                    return $inv_trading_detail->delivery_order->hpp * $inv_trading_detail->delivery_order->unload_quantity_realization;
                })->sum();
                $totalpajak += $item->invoice_trading_taxes->sum('amount');
            @endphp
            @foreach ($item->inv_trading_add_on as $addOn)
                <tr>
                    <td>{{ $addOn->item->nama }}</td>
                    <td>{{ $addOn->item->kode }}</td>
                    <td class="text-end">{{ formatNumber($addOn->quantity) }}</td>
                    <td class="text-end">{{ formatNumber($addOn->sub_total) }}</td>
                    <td class="text-end">{{ formatNumber($addOn->hpp)}}</td>
                    <td class="text-end">{{ formatNumber($item->hpp_transport) }}</td>
                    <td class="text-end">{{ formatNumber($item->transport_tax) }}</td>
                </tr>
                @php
                    $totalTransport += $addOn->sub_total;
                @endphp
            @endforeach
            @php
                $nilaihpptransport += $item->hpp_transport;
            @endphp
        @endforeach
        @php
            $totalAll += $total + $totalTransport;
        @endphp
        <tr>
            <td colspan="6" class="font-bold">
                Total
            </td>
            <td class="text-end">
                {{ formatNumber($qty) }}
            </td>
            <td class="text-end">
                {{ formatNumber($totalAll) }}
            </td>
            <td class="text-end">
                {{ formatNumber($nilaihpp) }}
            </td>
            <td class="text-end">
                {{ formatNumber($nilaihpptransport) }}
            </td>
            <td class="text-end">
                {{ formatNumber($totalpajak) }}
            </td>
        </tr>
    </tbody>
</table>

