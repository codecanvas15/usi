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
            <div class="table-responsive">
                <table class="table table-bordered mt-10">
                    <thead>
                        <tr>
                            <th class="text-center">NO.</th>
                            <th class="text-center">TANGGAL</th>
                            <th class="text-center">NO VOUCHER</th>
                            <th class="text-center">CUSTOMER</th>
                            <th class="text-center">KAS/BANK</th>
                            <th class="text-center">KETERANGAN</th>
                            <th class="text-center">CURRENCY</th>
                            <th class="text-center">TOTAL</th>
                            <th class="text-center">RATE</th>
                            <th class="text-center">TOTAL ({{ get_local_currency()->kode }})</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $key => $d)
                            @if ($d->code != ($data[$key - 1]->code ?? '') || $key == 0)
                                <tr class="bg-light">
                                    <td class="text-center">{{ $key + 1 }}.</td>
                                    <td class="text-center">{{ localDate($d->date) }}</td>
                                    <td class="text-left">
                                        @if ($d->receivables_payment_id)
                                            <a href="{{ route('admin.receivables-payment.show', $d->receivables_payment_id) }}" target="_blank">
                                                {{ $d->bank_code_mutation ?? $d->code }}
                                            </a>
                                        @else
                                            {{ $invoice->bank_code }}
                                        @endif
                                    </td>
                                    <td class="text-left">{{ $d->nama }}</td>
                                    <td>{{ $d->coa_account_code }} - {{ $d->coa_name }}</td>
                                    <td class="text-left"></td>
                                    <td class="text-end"></td>
                                    <td class="text-end"></td>
                                    <td class="text-end"></td>
                                </tr>
                            @endif
                            <tr>
                                <td class="text-left" colspan="5">
                                    {{-- {{ $d->invoice_code }} --}}
                                    @php
                                        $modelClass = class_basename($d->model_reference);
                                        $modelMap = [
                                            'InvoiceGeneral' => 'admin.invoice-general.show',
                                            'InvoiceDownPayment' => 'admin.invoice-down-payment.show',
                                            'InvoiceTrading' => 'admin.invoice-trading.show',
                                        ];
                                
                                        $routeName = $modelMap[$modelClass] ?? null;
                                    @endphp
                                
                                    @if ($routeName && Route::has($routeName))
                                        <a href="{{ route($routeName, $d->reference_id) }}" target="_blank">
                                            {{ $d->invoice_code }}
                                        </a>
                                    @else
                                        {{ $d->invoice_code }}
                                    @endif
                                </td>
                                <td class="text-left">{{ $d->note }}</td>
                                <td class="text-left">{{ $d->currency_simbol }}</td>
                                <td class="text-end">{{ formatNumber($d->receive_amount) }}</td>
                                <td class="text-end">{{ formatNumber($d->exchange_rate) }}</td>
                                <td class="text-end">{{ formatNumber($d->receive_amount_local) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td align="center" colspan="9">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2">TOTAL</th>
                            <th class="text-end"></th>
                            <th class="text-end"></th>
                            <th class="text-end"></th>
                            <th class="text-end"></th>
                            <th class="text-end"></th>
                            <th class="text-end"></th>
                            <th class="text-end">{{ formatNumber($data->sum('receive_amount_local')) }}</th>
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
