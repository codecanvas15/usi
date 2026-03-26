@extends('layouts.admin.layout.index')

@php
    $main = 'sale-order-trading';
    $title = 'laporan penjualan trading';
@endphp

@section('title', Str::headline("Preview $title") . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
    <style>
        tr td,
        tr th {
            white-space: unset !important;
        }
    </style>
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
                        <a href="{{ route('admin.sale-order-trading-report.report') }}">{{ Str::headline($title) }}</a>
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
                <h3 class="text-uppercase">{{ Str::headline($type) }}</h3>
                <h5 class="text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</h5>
            </div>
        </x-slot>
        <x-slot name="table_content">
            <x-table theadColor="white" class="table-bordered">
                <x-slot name="table_head">
                    @include('admin.finance-report.debt-card-trading.table.header')
                </x-slot>
                <x-slot name="table_body">
                    @include('admin.finance-report.debt-card-trading.table.body', [
                        'formatNumber' => true,
                    ])
                    @include('admin.finance-report.debt-card-trading.table.footer', [
                        'formatNumber' => true,
                    ])
                </x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#finance-main-sidebar')
        sidebarActive('#finance-report')
    </script>
@endsection
