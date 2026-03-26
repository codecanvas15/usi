@extends('layouts.admin.layout.index')

@php
    $main = 'finance-report';
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
        </x-slot>
        <x-slot name="table_content">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3 class="text-uppercase"> {{ Str::headline($type) }}</h3>
                    <h5 class="text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</h5>
                </div>
            </div>

            <div class="mt-10">
                <x-table>
                    <x-slot name="table_head">
                        @include('admin.finance-report.cash-bond.table.header')
                    </x-slot>
                    <x-slot name="table_body">
                        @include('admin.finance-report.cash-bond.table.body', [
                            'formatNumber' => true,
                        ])
                    </x-slot>
                    <x-slot name="table_foot">
                        @include('admin.finance-report.cash-bond.table.footer', [
                            'formatNumber' => true,
                        ])
                    </x-slot>
                </x-table>
            </div>

        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#finance-main-sidebar')
        sidebarActive('#finance-report');
    </script>
@endsection
