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
            <div class="table-responsive mt-10">
                <table class="table table-bordered-dark">
                    <tbody>
                        @foreach ($data as $key_data => $d)
                            @php
                                $bg = '';
                            @endphp
                            @if ($key_data == 0 || $key_data % 2 == 0)
                                @php
                                    $bg = 'bg-light';
                                @endphp
                            @else
                                @php
                                    $bg = '';
                                @endphp
                            @endif
                            <tr class="{{ $bg }}">
                                <th colspan="14">{{ $d->name }}</th>
                            </tr>
                            <tr class="{{ $bg }}">
                                <th class="text-center" rowspan="2">TANGGAL</th>
                                <th class="text-center" rowspan="2">NO BUKTI</th>
                                <th class="text-center" rowspan="2">NO DOKUMEN</th>
                                <th class="text-center" rowspan="2">NO CHECK/GIRO</th>
                                <th class="text-center" rowspan="2">NAMA</th>
                                <th class="text-center" rowspan="2">URAIAN</th>
                                <th class="text-center" rowspan="2">NO DETAIL/KODE ACCT</th>
                                <th class="text-center" colspan="2">MUTASI</th>
                                <th class="text-center" rowspan="2">SALDO AKHIR</th>
                                <th class="text-center" rowspan="2">KURS</th>
                                <th class="text-center" colspan="2">MUTASI ({{ get_local_currency()->simbol }})</th>
                                <th class="text-center" rowspan="2">SALDO AKHIR</th>
                            </tr>
                            <tr class="{{ $bg }}">
                                <th class="text-center">PENERIMAAN</th>
                                <th class="text-center">PENGELUARAN</th>
                                <th class="text-center">PENERIMAAN</th>
                                <th class="text-center">PENGELUARAN</th>
                            </tr>
                            <tr class="{{ $bg }}">
                                <td>SALDO AWAL</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-end">{{ formatNumber($d->foreign_beginning_balance, true) }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-end">{{ formatNumber($d->beginning_balance, true) }}</td>
                            </tr>
                            @forelse ($d->transactions as $key => $transaction)
                                <tr class="{{ $bg }}">
                                    <td class="text-center">{{ localDate($transaction->date) }}</td>
                                    <td class="text-center">{{ $transaction->bank_code_mutation }}</td>
                                    <td class="text-left"><a href="{{ toLocalLink($transaction->document_reference->link ?? '') }}" target="_blank">{{ $transaction->document_reference->code ?? '' }}</a></td>
                                    <td class="text-center">{{ $transaction->giro_in ?? $transaction->giro_out }}</td>
                                    <td>{{ $transaction->vendor_customer->nama ?? '' }}</td>
                                    <td>{{ $transaction->remark }}</td>
                                    <td>{{ $transaction->opponent_account_code ?? '' }} {{ $transaction->opponent_name ?? '' }}</td>
                                    <td class="text-end">{{ $transaction->simbol }} {{ formatNumber($transaction->debit, true) }}</td>
                                    <td class="text-end">{{ $transaction->simbol }} {{ formatNumber($transaction->credit, true) }}</td>
                                    <td class="text-end">{{ formatNumber($transaction->foreign_balance_after, true) }}</td>
                                    <td class="text-end">{{ formatNumber($transaction->exchange_rate, true) }}</td>
                                    <td class="text-end">{{ formatNumber($transaction->debit_exchanged, true) }}</td>
                                    <td class="text-end">{{ formatNumber($transaction->credit_exchanged, true) }}</td>
                                    <td class="text-end">{{ formatNumber($transaction->balance_after, true) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="{{ $bg }}" align="center" colspan="14">
                                        Tidak ada data
                                    </td>
                                </tr>
                            @endforelse
                            <tr class="{{ $bg }}">
                                <th colspan="7" class="text-end">TOTAL</th>
                                <th class="text-end">{{ formatNumber($d->transactions->sum('debit'), true) }}</th>
                                <th class="text-end">{{ formatNumber($d->transactions->sum('credit'), true) }}</th>
                                <th class="text-end">{{ formatNumber($d->foreign_final_balance, true) }}</th>
                                <th class="text-end"></th>
                                <th class="text-end">{{ formatNumber($d->transactions->sum('debit_exchanged'), true) }}</th>
                                <th class="text-end">{{ formatNumber($d->transactions->sum('credit_exchanged'), true) }}</th>
                                <th class="text-end">{{ formatNumber($d->final_balance, true) }}</th>
                            </tr>
                            <tr class="bg-dark">
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
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
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
