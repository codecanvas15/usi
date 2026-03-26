@extends('layouts.admin.layout.index')

@php
    $main = 'sale-order-general';
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
            <x-table theadColor="white" class="table-bordered mt-20">
                <x-slot name="table_head">
                    @include('admin.sale-order-general-report.sale-order-general-outstanding.table.header')
                </x-slot>
                <x-slot name="table_body">
                    @include('admin.sale-order-general-report.sale-order-general-outstanding.table.body', [
                        'formatNumber' => true,
                    ])
                </x-slot>
                <x-slot name="table_foot">
                    @include('admin.sale-order-general-report.sale-order-general-outstanding.table.footer', [
                        'formatNumber' => true,
                    ])
                </x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#trading');
        sidebarMenuOpen('#sale-order-report');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
