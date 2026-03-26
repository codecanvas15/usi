<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="widtd=device-widtd, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan {{ Str::headline($type) }}</title>
    <link rel="stylesheet" href="{{ public_path() }}/css/pdf.css">
</head>

<body>
    <div class="row">
        <table>
            <tr>
                <td>
                    <h4 class="text-danger text-uppercase my-0">{{ Str::upper(getCompany()->name) }}</h4>
                    <p class="font-small-2 my-0">{{ getCompany()->address }}</p>
                    <p class="font-small-2 my-0">Telp. {{ getCompany()->phone }} | Fax. {{ getCompany()->fax }}</p>
                </td>
                <td style="widtd: 25%">
                    {{-- <center><img src="{{ getCompany()->logo ? public_path('/storage/' . getCompany()->logo) : public_path('/images/icon.png') }}" style="widtd: 136px"></center> --}}
                </td>
            </tr>
        </table>
    </div>

    <div class="mt-2">
        <div class="row">
            <div class="text-center">
                <h5 class="text-uppercase my-0">laporan {{ Str::headline($type) }}</h5>
                <p class="font-small-2 text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</p>
            </div>
        </div>
        <br>
        @foreach ($data as $item)
            <div style="margin-top: 30px; padding-top: 10px;">
                <x-table theadColor="white" class="table-bordered mb-1">
                    <x-slot name="table_body">
                        <tr>
                            <td><b>Tanggal : {{ localDate($item->date) }}</b></td>
                            <td><b>Pelanggan : {{ $item->customer_name }}</b></td>
                        </tr>
                        <tr>
                            <td><b>No. Invoice : {{ $item->code }}</b></td>
                            <td><b>Lokasi : {{ $item->branch_name }}</b></td>
                        </tr>
                        <tr>
                            <td><b>Lost Tolerance : {{ Str::headline($item->lost_tolerance_type == 'percent' ? $item->lost_tolerance * 100 : $item->lost_tolerance) }}</b></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><b>Lost Tolerance Type : {{ Str::headline($item->lost_tolerance_type) }}</b></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><b>Calculate From : {{ Str::headline($item->calculate_from) }}</b></td>
                            <td></td>
                        </tr>
                    </x-slot>
                </x-table>

                <x-table theadColor="white" class="table-bordered mb-1">
                    <x-slot name="table_head">
                        <tr>
                            <td><b>Qty Total</b></td>
                            <td><b>Qty Losses</b></td>
                            <td><b>Losses Percentage</b></td>
                            <td><b>Qty Lost Tolerance</b></td>
                            <td><b>Qty Invoice</b></td>
                            <td><b>Harga</b></td>
                            <td><b>Sub Total</b></td>
                            <td><b>Total Pajak</b></td>
                            <td><b>Total</b></td>
                            <td><b>Kurs</b></td>
                            <td><b>Sub Total Idr</b></td>
                            <td><b>Total Pajak Idr</b></td>
                            <td><b>Total Idr</b></td>
                        </tr>
                    </x-slot>
                    <x-slot name="table_body">
                        <tr>
                            <td>{{ formatNumber($item->total_jumlah_dikirim) }}</td>
                            <td>{{ formatNumber($item->total_lost) }}</td>
                            <td>{{ formatNumber($item->losses_percentage) }}</td>
                            <td>{{ formatNumber($item->qty_losses_tolerance) }}</td>
                            <td>{{ formatNumber($item->jumlah) }}</td>
                            <td>{{ formatNumber($item->harga) }}</td>
                            <td>{{ formatNumber($item->subtotal) }}</td>
                            <td>{{ formatNumber($item->total_tax) }}</td>
                            <td>{{ formatNumber($item->total) }}</td>
                            <td>{{ formatNumber($item->exchange_rate) }}</td>
                            <td>{{ formatNumber($item->subtotal_local) }}</td>
                            <td>{{ formatNumber($item->total_tax_local) }}</td>
                            <td>{{ formatNumber($item->total_local) }}</td>
                        </tr>
                    </x-slot>
                </x-table>

                <x-table theadColor="white" class="table-bordered mb-2">
                    <x-slot name="table_head">
                        @include('admin.sale-order-trading-report.sale-order-trading-detail.table.header')
                    </x-slot>
                    <x-slot name="table_body">
                        @include('admin.sale-order-trading-report.sale-order-trading-detail.table.body', [
                            'formatNumber' => true,
                        ])
                    </x-slot>
                    <x-slot name="table_foot">
                        @include('admin.sale-order-trading-report.sale-order-trading-detail.table.footer', [
                            'formatNumber' => true,
                        ])
                    </x-slot>
                </x-table>
            </div>
        @endforeach
    </div>
</body>

</html>
