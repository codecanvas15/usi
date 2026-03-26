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
                    <h3 class="text-uppercase">laporan {{ Str::headline($type) }}</h3>
                    <h5 class="text-uppercase my-0">periode : {{ localDate($from_date) }}/{{ localDate($to_date) }}</h5>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped mt-10">
                    <thead>
                        <tr>
                            <th class="text-center">NO.</th>
                            <th class="text-center">KODE CUSTOMER</th>
                            <th class="text-center">NAMA CUSTOMER</th>
                            <th class="text-center">SALDO AWAL</th>
                            <th class="text-center">PENJUALAN</th>
                            <th class="text-center">PELUNASAN</th>
                            <th class="text-center">SALDO AKHIR PIUTANG</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $key => $d)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}.</td>
                                <td>{{ $d->code }}</td>
                                <td>{{ $d->nama }}</td>
                                <td class="text-end">{{ formatNumber($d->beginning) }}</td>
                                <td class="text-end">{{ formatNumber($d->current_in) }}</td>
                                <td class="text-end">{{ formatNumber($d->current_out) }}</td>
                                <td class="text-end">{{ formatNumber($d->final_balance) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td align="center" colspan="7">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>TOTAL</th>
                            <th class="text-end">{{ formatNumber($data->sum('beginning')) }}</th>
                            <th class="text-end">{{ formatNumber($data->sum('current_in')) }}</th>
                            <th class="text-end">{{ formatNumber($data->sum('current_out')) }}</th>
                            <th class="text-end">{{ formatNumber($data->sum('final_balance')) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#finance-main-sidebar')
        sidebarActive('#finance-report')
    </script>
@endsection
