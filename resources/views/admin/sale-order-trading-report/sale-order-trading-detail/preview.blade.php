@extends('layouts.admin.layout.index')

@php
    $main = 'sale-order-general';
    $title = 'laporan penjualan umum';
@endphp

@section('title', Str::headline("Preview $title") . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
@endsection

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.sales-order.index') }}">Laporan</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.sale-order-general.report') }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        Preview {{ Str::headline($title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table>
        <x-slot name="header_content">
            <div class="text-center">
                <h3 class="text-uppercase">laporan {{ Str::headline($type) }}</h3>
                <h5 class="text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</h5>
            </div>
        </x-slot>
        <x-slot name="table_content">

            @foreach ($data as $item)
                <div class="border-top border-primary mt-30 pt-10">
                    <x-table theadColor="white" class="table-bordered">
                        <x-slot name="table_body">
                            <tr>
                                <th>Tanggal : {{ localDate($item->date) }}</th>
                                <th>Pelanggan : {{ $item->customer_name }}</th>
                            </tr>
                            <tr>
                                <th>No. Invoice : {{ $item->code }}</th>
                                <th>Lokasi : {{ $item->branch_name }}</th>
                            </tr>
                            <tr>
                                <th>Lost Tolerance : {{ Str::headline($item->lost_tolerance_type == 'percent' ? $item->lost_tolerance * 100 : $item->lost_tolerance) }}</th>
                                <th></th>
                            </tr>
                            <tr>
                                <th>Lost Tolerance Type : {{ Str::headline($item->lost_tolerance_type) }}</th>
                                <th></th>
                            </tr>
                            <tr>
                                <th>Calculate From : {{ Str::headline($item->calculate_from) }}</th>
                                <th></th>
                            </tr>
                        </x-slot>
                    </x-table>

                    <x-table theadColor="white" class="table-bordered">
                        <x-slot name="table_head">
                            <tr>
                                <th><b>Qty Total</b></th>
                                <th><b>Qty Losses</b></th>
                                <th><b>Losses Percentage</b></th>
                                <th><b>Qty Lost Tolerance</b></th>
                                <th><b>Qty Invoice</b></th>
                                <th><b>Harga</b></th>
                                <th><b>Sub Total</b></th>
                                <th><b>Total Pajak</b></th>
                                <th><b>Total</b></th>
                                <th><b>Kurs</b></th>
                                <th><b>Sub Total Idr</b></th>
                                <th><b>Total Pajak Idr</b></th>
                                <th><b>Total Idr</b></th>
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

                    <x-table theadColor="white" class="table-bordered text-center">
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
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#trading');
        sidebarMenuOpen('#sale-order-report');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
