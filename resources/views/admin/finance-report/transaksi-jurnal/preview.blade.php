@extends('layouts.admin.layout.index')

@php
    $main = 'finance-report';
    // dd($main);
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
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered mt-10">
                    <thead>
                        <tr>
                            <th class="text-center">TANGGAL</th>
                            <th class="text-center">TRANSAKSI</th>
                            <th class="text-center">NO DOKUMEN</th>
                            <th class="text-center">NO REFERENSI</th>
                            <th class="text-center">ITEM</th>
                            <th class="text-center" colspan="2">ACCOUNT</th>
                            <th class="text-center">DEBIT</th>
                            <th class="text-center">KREDIT</th>
                            <th class="text-center">KURS</th>
                            <th class="text-center">DEBIT ({{ get_local_currency()->kode }})</th>
                            <th class="text-center">KREDIT ({{ get_local_currency()->kode }})</th>
                            <th class="text-center">KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 0;
                        @endphp
                        @forelse ($data as $key => $d)
                            @if ($d->journal_id != ($data[$key - 1]->journal_id ?? '') || $key == 0)
                                @php
                                    $i++;
                                @endphp
                            @endif
                            <tr @if ($i % 2 != 0) class="bg-light" @endif>
                                @if ($d->journal_id != ($data[$key - 1]->journal_id ?? '') || $key == 0)
                                    <td class="text-center"><b>{{ localDate($d->journal_date) }}</b></td>
                                    <td class="text-left"><b>{{ $d->journal_type }}</b></td>
                                    <td class="text-center">
                                        @if ($d->document_reference)
                                            @if ($d->document_reference->link ?? null)
                                                <a href="{{ toLocalLink($d->document_reference->link) }}" target="_blank">{{ $d->document_reference->code }}</a>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($d->reference)
                                            @if ($d->reference->link ?? null)
                                                <a href="{{ toLocalLink($d->reference->link) }}" target="_blank">{{ $d->reference->code }}</a>
                                            @endif
                                        @endif
                                    </td>
                                @else
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                @endif
                                <td class="text-left">{{ $d->remark }}</td>
                                <td class="text-left">{{ $d->coa_code }}</td>
                                <td class="text-left">{{ $d->coa_name }}</td>
                                <td class="text-end">{{ $d->currency_symbol }} {{ formatNumber($d->debit) }}</td>
                                <td class="text-end">{{ $d->currency_symbol }} {{ formatNumber($d->credit) }}</td>
                                <td class="text-end">{{ formatNumber($d->journal_exchange_rate) }}</td>
                                <td class="text-end">{{ formatNumber($d->debit_exchanged) }}</td>
                                <td class="text-end">{{ formatNumber($d->credit_exchanged) }}</td>
                                <td class="text-left">{{ $d->journal_remark }}</td>
                            </tr>
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
                            <th class="text-center" colspan="10">TOTAL</th>
                            <th class="text-end">{{ formatNumber($data->sum('debit_exchanged')) }}</th>
                            <th class="text-end">{{ formatNumber($data->sum('credit_exchanged')) }}</th>
                            <th class="text-end"></th>
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
