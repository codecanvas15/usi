@extends('layouts.admin.layout.index')

@php
    $main = 'inventory-report';
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
                <h3 class="text-uppercase">laporan {{ Str::headline($title) }}</h3>
            </div>

            <div class="mt-10">
                @foreach ($data as $item)
                    <x-table theadColor="white" class="table-bordered mt-20">
                        <x-slot name="table_head">
                            @include("admin.inventory-report.$type.table.header")
                        </x-slot>
                        <x-slot name="table_body">
                            <tr>
                                <td class="font-small-2"><b>Saldo Awal {{ $item['item']->nama }} </b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                                <td></td>
                                <td></td>

                                <td></td>
                                <td></td>

                                <td class="font-small-2" align="right">{{ formatNumber($item['beginning_balance']) }}</td>
                                <td class="font-small-2" align="right">{{ formatNumber($item['last_mutation']?->total) }}</td>
                            </tr>
                            @include("admin.inventory-report.$type.table.body", [
                                'formatNumber' => true,
                            ])
                        </x-slot>
                        {{-- <x-slot name="table_foot">
                            @include("admin.inventory-report.$type.table.footer", [
                                'formatNumber' => true,
                            ])
                        </x-slot> --}}
                    </x-table>
                @endforeach
            </div>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#stock-sidebar');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
