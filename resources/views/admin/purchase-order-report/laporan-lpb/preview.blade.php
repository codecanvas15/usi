@extends('layouts.admin.layout.index')

@php
    $main = 'finance-report';
@endphp

@section('title', Str::headline('Laporan Hutang Dagang') . ' - ')

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
                    <h3 class="text-uppercase">{{ Str::headline('Laporan Hutang Dagang') }}</h3>
                    <h5 class="text-uppercase my-0">tanggal : {{ localDate($from_date) }}/{{ localDate($to_date) }}</h5>
                    @if ($vendor)
                        <p class="font-small-2 text-uppercase my-0">VENDOR : {{ $vendor->nama }}</p>
                    @endif
                    @if ($currency)
                        <p class="font-small-2 text-uppercase my-0">CURRENCY : {{ $currency->nama }}</p>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped mt-10">
                    <thead>
                        <tr>
                            <th class="text-center">NO.</th>
                            <th class="text-center">TGL LPB</th>
                            <th class="text-center">NO. PO</th>
                            <th class="text-center">PROJECT</th>
                            <th class="text-center">VENDOR</th>
                            <th class="text-center">NO LPB</th>
                            <th class="text-center">CUR.</th>
                            <th class="text-center">TOTAL LPB</th>
                            <th class="text-center">RATE</th>
                            <th class="text-center">TOTAL LPB IDR</th>
                            <th class="text-center">TGL BAYAR</th>
                            <th class="text-center">BANK</th>
                            <th class="text-center">NOMINAL</th>
                            <th class="text-center">KODE PEMBAYARAN</th>
                            <th class="text-center">SISA HUTANG</th>
                            <th class="text-center">KET.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                            $total = 0;
                            $total_all = 0;
                        @endphp
                        @forelse ($data as $key => $d)
                            @if ($key == 0 || $d->kode != ($data[$key - 1]->kode ?? ''))
                                @php
                                    $total = $d->total;
                                    $total -= $d->amount_payment;
                                    $total_all += $d->total_rp;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $no++ }}.</td>
                                    <td class="text-center">{{ localDate($d->date_receive) }}</td>
                                    <td class="text-center">{{ $d->po_code }}</td>
                                    <td class="text-center">
                                        <a href="{{ $d->po_project_link}}">
                                            {{ $d->po_project }}
                                        </a>
                                    </td>
                                    <td class="text-center">{{ $d->vendor_name }}</td>
                                    <td class="text-center">{{ $d->kode }}</td>
                                    <td class="text-center">{{ $d->currency_kode }}</td>
                                    <td class="text-end">{{ formatNumber($d->total) }}</td>
                                    <td class="text-end">{{ formatNumber($d->exchange_rate) }}</td>
                                    <td class="text-end">{{ formatNumber($d->total_rp) }}</td>
                                    <td class="text-center">{{ $d->date_payment ? localDate($d->date_payment) : '' }}</td>
                                    <td class="text-center">{{ $d->bank }}</td>
                                    <td class="text-end">{{ formatNumber($d->amount_payment) }}</td>
                                    <td class="text-center">{{ $d->bank_code }}</td>
                                    <td class="text-end">{{ formatNumber($d->outstanding) }}</td>
                                    <td class="text-left">{{ $d->note }}</td>
                                </tr>
                            @else
                                @php
                                    $total -= $d->amount_payment;
                                @endphp
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-center">{{ $d->date_payment ? localDate($d->date_payment) : '' }}</td>
                                    <td class="text-center">{{ $d->bank }}</td>
                                    <td class="text-end">{{ formatNumber($d->amount_payment) }}</td>
                                    <td class="text-center">{{ $d->bank_code }}</td>
                                    <td class="text-end"></td>
                                    <td class="text-left">{{ $d->note }}</td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td align="center" colspan="13">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">TOTAL</th>
                            <td></td>
                            <td></td>
                            <td></td>
                            <th class="text-end">{{ formatNumber($total_all) }}</th>
                            <td></td>
                            <td></td>
                            <th class="text-end">{{ formatNumber($data->sum('amount_payment')) }}</th>
                            <td></td>
                            <th class="text-end">{{ formatNumber($data->sum('outstanding')) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#purchase-menu')
        sidebarMenuOpen('#purchase-order-report')
        sidebarActive('#purchase-order-report-menu')
    </script>
@endsection
