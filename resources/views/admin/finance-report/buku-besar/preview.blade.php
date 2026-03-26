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
                    @if ($coa && is_array($coa) && count($coa) > 0 && isset($coa[0], $coa[1]) && $coa[0] && $coa[1])
                        <p class="font-small-2 text-uppercase my-0">
                            COA: {{ $coa[0]->name }} - {{ $coa[0]->account_code }} to {{ $coa[1]->name }} - {{ $coa[1]->account_code }}
                        </p>
                    @elseif (isset($coa[0]) && $coa[0])
                        <p class="font-small-2 text-uppercase my-0">
                            COA: {{ $coa[0]->name }} - {{ $coa[0]->account_code }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped mt-10">
                    <tbody>
                        @forelse ($data as $d)
                            <tr>
                                <th colspan="11">{{ $d->name }} - {{ $d->account_code }}</th>
                            </tr>
                            <tr>
                                <th class="text-center">TANGGAL</th>
                                <th class="text-center">ITEM</th>
                                <th class="text-center">NO TRANSAKSI</th>
                                <th class="text-center">NO REFERENSI</th>
                                <th class="text-center">NILAI</th>
                                <th class="text-center">KURS</th>
                                <th class="text-center">DEBIT {{ get_local_currency()->kode }}</th>
                                <th class="text-center">KREDIT {{ get_local_currency()->kode }}</th>
                                <th class="text-center">SALDO {{ get_local_currency()->kode }}</th>
                                <th class="text-center">KETERANGAN</th>
                            </tr>
                            <tr>
                                <td class="text-center"></td>
                                <td class="text-center"><b>SALDO AWAL</b></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-end">{{ formatNumber($d->amount_before_exchanged) }}</td>
                                <td class="text-center"></td>
                            </tr>
                            @php
                                $balance = $d->amount_before_exchanged;
                            @endphp
                            @forelse ($d->details as $key => $detail)
                                @php
                                    $balance += $detail->debit_exchanged;
                                    $balance -= $detail->credit_exchanged;

                                @endphp
                                <tr>
                                    <td class="text-center">{{ localDate($detail->journal_date) }}</td>
                                    <td class="text-left">{{ $detail->journal_remark }}</td>
                                    <td class="text-center">
                                        @if ($detail->document_reference)
                                            <a href="{{ toLocalLink($detail->document_reference->link) }}" target="_blank">
                                                {{ $detail->document_reference->code }}
                                            </a>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($detail->reference)
                                            @if ($detail->reference->link ?? null)
                                                <a href="{{ toLocalLink($detail->reference->link) }}" target="_blank">
                                                    {{ $detail->reference->code }}
                                                </a>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-end">{{ formatNumber($detail->debit != 0 ? $detail->debit : $detail->credit) }}</td>
                                    <td class="text-end">{{ formatNumber($detail->exchange_rate) }}</td>
                                    <td class="text-end">{{ formatNumber($detail->debit_exchanged) }}</td>
                                    <td class="text-end">{{ formatNumber($detail->credit_exchanged) }}</td>
                                    <td class="text-end">{{ formatNumber($balance) }}</td>
                                    <td class="text-left">{{ $detail->remark }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td align="center" colspan="10">
                                        Tidak ada data
                                    </td>
                                </tr>
                            @endforelse
                            <tr>
                                <th class="text-center">TOTAL</th>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                                <th class="text-center"></th>
                                <th class="text-end">{{ formatNumber($d->details->sum('debit_exchanged')) }}</th>
                                <th class="text-end">{{ formatNumber($d->details->sum('credit_exchanged')) }}</th>
                                <th class="text-end"></th>
                                <th class="text-center"></th>
                            </tr>
                            <tr>
                                <td colspan="10"></td>
                            </tr>
                        @empty
                            <tr>
                                <th class="text-center" colspan="10">Tidak ada data</th>
                            </tr>
                        @endforelse
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
