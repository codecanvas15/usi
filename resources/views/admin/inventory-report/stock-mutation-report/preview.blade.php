@extends('layouts.admin.layout.index')

@php
    $main = 'inventory-report';
@endphp

@section('title', Str::headline($type) . ' - ')

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
                        {{ Str::headline($main) }}
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline($type) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="">
        <x-slot name="header_content">
            <div class="text-center">
                <h3 class="text-uppercase">laporan {{ Str::headline($type) }}</h3>
                <h5 class="text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</h5>
            </div>
        </x-slot>
        <x-slot name="table_content">
            <div class="table-responsive">
                <table class="table table-bordered mt-20">
                    <tbody>
                        @foreach ($data as $item)
                            @include("admin.inventory-report.$type.table.header")
                            @include("admin.inventory-report.$type.table.body", [
                                'formatNumber' => true,
                                'data' => $item->data,
                            ])
                            @include("admin.inventory-report.$type.table.footer", [
                                'formatNumber' => true,
                                'data' => $item->data,
                            ])
                            <tr>
                                <td colspan="8"></td>
                            </tr>
                        @endforeach
                    <tbody>
                </table>
            </div>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#stock-sidebar');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
