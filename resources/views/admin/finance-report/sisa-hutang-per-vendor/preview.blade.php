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
                    <h5 class="text-uppercase my-0">tanggal : {{ localDate($to_date) }}</h5>
                    @if ($currency)
                        <p class="font-small-2 text-uppercase my-0">MATA UANG : {{ $currency->kode }} - {{ $currency->nama }}</p>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped mt-10">
                    <thead>
                        <tr>
                            <th class="text-center">NO.</th>
                            <th class="text-center">TANGGAL</th>
                            <th class="text-center">NO TRANSAKSI</th>
                            <th class="text-center">JATUH TEMPO</th>
                            <th class="text-center">TOTAL</th>
                            <th class="text-center">TERBAYAR</th>
                            <th class="text-center">SISA</th>
                            <th class="text-center">KURS</th>
                            <th class="text-center">TOTAL</th>
                            <th class="text-center">TERBAYAR</th>
                            <th class="text-center">SISA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $key => $d)
                            <tr>
                                <td class="text-left" colspan="11">{{ $d->vendor_nama }}</td>
                            </tr>
                            @forelse ($d->data as $key2 => $data)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}.</td>
                                    <td>{{ localDate($data->date) }}</td>
                                    <td>{{ $data->code }}</td>
                                    <td>{{ localDate($data->due_date ?? '') }}</td>
                                    <td class="text-end">{{ formatNumber($data->total, true) }}</td>
                                    <td class="text-end">{{ formatNumber($data->paid_amount, true) }}</td>
                                    <td class="text-end">{{ formatNumber($data->outstanding_amount, true) }}</td>
                                    <td class="text-end">{{ formatNumber($data->exchange_rate, true) }}</td>
                                    <td class="text-end">{{ formatNumber($data->total_exchanged, true) }}</td>
                                    <td class="text-end">{{ formatNumber($data->paid_amount_exchanged, true) }}</td>
                                    <td class="text-end">{{ formatNumber($data->outstanding_amount_exchanged, true) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td align="center" colspan="11">
                                        Tidak ada data
                                    </td>
                                </tr>
                            @endforelse
                            <tr>
                                <th class="text-center"></th>
                                <th colspan="7" class="text-center">TOTAL</th>
                                <th class="text-end">{{ formatNumber($d->data->sum('total_exchanged'), true) }}</th>
                                <th class="text-end">{{ formatNumber($d->data->sum('paid_amount_exchanged'), true) }}</th>
                                <th class="text-end">{{ formatNumber($d->data->sum('outstanding_amount_exchanged'), true) }}</th>
                            </tr>
                        @empty
                            <tr>
                                <td align="center" colspan="11">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-center"></th>
                            <th colspan="7" class="text-center">TOTAL</th>
                            <th class="text-end">{{ formatNumber($total_exchanged, true) }}</th>
                            <th class="text-end">{{ formatNumber($paid_amount_exchanged, true) }}</th>
                            <th class="text-end">{{ formatNumber($outstanding_amount_exchanged, true) }}</th>
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
