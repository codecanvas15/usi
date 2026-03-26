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
                    <h5 class="text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</h5>
                    @if ($coa)
                        <p class="font-small-2 text-uppercase my-0">KAS/BANK : {{ $coa->account_code }} - {{ $coa->name }}</p>
                    @endif
                </div>
            </div>
            <table class="table table-bordered table-striped mt-10">
                <thead>
                    <tr>
                        <th class="text-center" rowspan="2">NO.</th>
                        <th class="text-center" rowspan="2">BANK</th>
                        <th class="text-center" rowspan="2">SALDO AWAL</th>
                        <th class="text-center" colspan="2">MUTASI</th>
                        <th class="text-center" rowspan="2">SALDO AKHIR</th>
                    </tr>
                    <tr>
                        <th class="text-center">PENERIMAAN</th>
                        <th class="text-center">PENGELUARAN</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $key => $d)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}.</td>
                            <td>{{ $d->account_code }} - {{ $d->name }}</td>
                            <td class="text-end">{{ formatNumber($d->balance_amount_before, true) }}</td>
                            <td class="text-end">{{ formatNumber($d->mutation_debit, true) }}</td>
                            <td class="text-end">{{ formatNumber($d->mutation_credit, true) }}</td>
                            <td class="text-end">{{ formatNumber($d->balance_final, true) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td align="center" colspan="6">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-center"></th>
                        <th>TOTAL</th>
                        <th class="text-end"></th>
                        <th class="text-end">{{ formatNumber($data->sum('mutation_debit')) }}</th>
                        <th class="text-end">{{ formatNumber($data->sum('mutation_credit')) }}</th>
                        <th class="text-end"></th>
                    </tr>
                </tfoot>
            </table>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#finance-main-sidebar')
        sidebarActive('#finance-report')
    </script>
@endsection
