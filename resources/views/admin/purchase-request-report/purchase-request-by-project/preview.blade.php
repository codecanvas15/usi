@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-request-report';
@endphp

@section('title', Str::headline($title) . ' - ')

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
                        <a href="{{ route("admin.$main.index") }}">Laporan</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline($title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table>
        <x-slot name="header_content">
            <div class="text-center mb-30">
                <h3 class="text-uppercase">laporan {{ Str::headline($title) }}</h3>
                <h5 class="text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</h5>
            </div>
        </x-slot>
        <x-slot name="table_content">

            @foreach ($data as $item)
                <div class="border-top border-primary py-30">
                    <p>Nama Project : {{ $item['project_name'] }} / {{ $item['project_code'] }}</p>

                    <x-table theadColor="white" class="table-bordered mt-20">
                        <x-slot name="table_head">
                            @include("admin.$main.$type.table.head")
                        </x-slot>
                        <x-slot name="table_body">
                            @foreach ($item['data'] as $itemReport)
                                @include("admin.$main.$type.table.body", ['formatNumber' => true])
                            @endforeach
                        </x-slot>
                        <x-slot name="table_footer">
                            @include("admin.$main.$type.table.footer")
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
        sidebarMenuOpen('#purchase-menu');
        sidebarMenuOpen('#purchase-order-report');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
