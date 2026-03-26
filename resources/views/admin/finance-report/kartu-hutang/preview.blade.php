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
                    @if ($vendor)
                        <p class="font-small-2 text-uppercase my-0">VENDOR : {{ $vendor->nama }} - {{ $vendor->code }}</p>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped mt-10">
                    <tbody>
                        @php
                            $total_balance = 0;
                            $total_balance_exchanged = 0;

                            $total_debit = 0;
                            $total_credit = 0;

                            $total_debit_exchanged = 0;
                            $total_credit_exchanged = 0;
                        @endphp
                        @forelse ($data as $d)
                            <tr>
                                <th colspan="14">{{ $d->nama }} - {{ $d->code }}</th>
                            </tr>
                            <tr>
                                <th class="text-center">TANGGAL</th>
                                <th class="text-center">TRANSAKSI</th>
                                <th class="text-center">NO TRANSAKSI</th>
                                <th class="text-center">NO BANK</th>
                                <th class="text-center">KETERANGAN</th>
                                <th class="text-center">LPB</th>
                                <th class="text-center">REF.</th>
                                <th class="text-center">DEBIT</th>
                                <th class="text-center">KREDIT</th>
                                <th class="text-center">SALDO</th>
                                <th class="text-center">KURS</th>
                                <th class="text-center">DEBIT {{ get_local_currency()->kode }}</th>
                                <th class="text-center">KREDIT {{ get_local_currency()->kode }}</th>
                                <th class="text-center">SALDO {{ get_local_currency()->kode }}</th>
                            </tr>
                            @php
                                $balance = $d->beginning_balance;
                                $total_balance += $balance;
                                $total_balance += $d->current_data->sum('credit') - $d->current_data->sum('debit');

                                $balance_exchanged = $d->beginning_balance_exchanged;
                                $total_balance_exchanged += $balance_exchanged;
                                $total_balance_exchanged += $d->current_data->sum('credit_exchanged') - $d->current_data->sum('debit_exchanged');

                                $total_debit += $d->current_data->sum('debit');
                                $total_credit += $d->current_data->sum('credit');

                                $total_debit_exchanged += $d->current_data->sum('debit_exchanged');
                                $total_credit_exchanged += $d->current_data->sum('credit_exchanged');
                            @endphp
                            <tr>
                                <td class="text-center">SALDO</td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-end"></td>
                                <td class="text-end"></td>
                                <td class="text-end"></td>
                                <td class="text-end"></td>
                                <td class="text-end">{{ formatNumber($balance) }}</td>
                                <td class="text-end"></td>
                                <td class="text-end"></td>
                                <td class="text-end"></td>
                                <td class="text-end">{{ formatNumber($balance_exchanged) }}</td>
                            </tr>
                            @forelse ($d->current_data as $key => $current)
                                @php
                                    $balance -= $current->debit;
                                    $balance += $current->credit;
                                    $balance_exchanged -= $current->debit_exchanged;
                                    $balance_exchanged += $current->credit_exchanged;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ localDate($current->date) }}</td>
                                    <td class="text-center">{{ $current->transaction }}</td>
                                    <td>
                                        <a href="{{ $current->link }}" target="_blank">
                                            {{ $current->transaction_code }}
                                        </a>
                                    </td>
                                    <td class="text-center">{{ $current->bank_code ?? '' }}</td>
                                    <td>{{ $current->note }}</td>
                                    <td>{!! $current->lpb_number !!}</td>
                                    <td>{!! $current->po_number !!}</td>
                                    <td class="text-end">{{ formatNumber($current->debit) }}</td>
                                    <td class="text-end">{{ formatNumber($current->credit) }}</td>
                                    <td class="text-end">{{ formatNumber($balance) }}</td>
                                    <td class="text-end">{{ formatNumber($current->exchange_rate) }}</td>
                                    <td class="text-end">{{ formatNumber($current->debit_exchanged) }}</td>
                                    <td class="text-end">{{ formatNumber($current->credit_exchanged) }}</td>
                                    <td class="text-end">{{ formatNumber($balance_exchanged) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td align="center" colspan="14">
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
                                <th class="text-center"></th>
                                <th class="text-end"></th>
                                <th class="text-end"></th>
                                <th class="text-end"></th>
                                <th class="text-end"></th>
                                <th class="text-end">{{ formatNumber($d->current_data->sum('debit_exchanged')) }}</th>
                                <th class="text-end">{{ formatNumber($d->current_data->sum('credit_exchanged')) }}</th>
                                <th class="text-end"></th>
                            </tr>
                        @empty
                            <tr>
                                <th class="text-center" colspan="14">Tidak ada data</th>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
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
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="text-center"><b>TOTAL</b></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                            <td class="text-end"></td>
                            <td class="text-end"></td>
                            <td class="text-end"></td>
                            <td class="text-end"></td>
                            <td class="text-end"></td>
                            <td class="text-end"></td>
                            <td class="text-end"><b>{{ formatNumber($total_debit) }}</b></td>
                            <td class="text-end"><b>{{ formatNumber($total_credit) }}</b></td>
                            <td class="text-end"><b>{{ formatNumber($total_balance_exchanged) }}</b></td>
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
