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
                    @if ($customer)
                        <p class="font-small-2 text-uppercase my-0">CUSTOMER : {{ $customer->nama }}</p>
                    @endif
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped mt-10">
                    <thead>
                        <tr>
                            <th class="text-center">NO.</th>
                            <th class="text-center">CUSTOMER</th>
                            <th class="text-center">BKK</th>
                            <th class="text-center">NO REF</th>
                            <th class="text-center">KODE SO</th>
                            <th class="text-center">TANGGAL</th>
                            <th class="text-center">MATA UANG</th>
                            <th class="text-center">TOTAL</th>
                            <th class="text-center">DIGUNAKAN</th>
                            <th class="text-center">SALDO</th>
                            <th class="text-center">KURS</th>
                            <th class="text-center">TOTAL {{ get_local_currency()->kode }}</th>
                            <th class="text-center">DIGUNAKAN {{ get_local_currency()->kode }}</th>
                            <th class="text-center">SALDO {{ get_local_currency()->kode }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $key => $d)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $d->customer_nama }}</td>
                                <td class="text-center">{{ $d->bank_code }}</td>
                                <td class="text-center">
                                    @php
                                        $link = '#';

                                        if (isset($d->cash_advance_receive_id)) {
                                            $link = route('admin.cash-advance-receive.show', ['cash_advance_receive' => $d->cash_advance_receive_id]);
                                        } elseif ($d->invoice_down_payment_id) {
                                            $link = route('admin.invoice-down-payment.show', ['invoice_down_payment' => $d->invoice_down_payment_id]);
                                        }
                                    @endphp
                                    <a href="{{ $link }}" target="_blank">{{ $d->reference }}</a>
                                </td>
                                <td class="text-center">
                                    @php
                                        $link = '#';

                                        if (isset($d->sale_order_model_id)) {
                                            if ($d->so_code) {
                                                $link = route('admin.sales-order-general.show', ['sales_order_general' => $d->sale_order_model_id]);
                                            } elseif ($d->so_trading_code) {
                                                $link = route('admin.sales-order.show', ['sales_order' => $d->sale_order_model_id]);
                                            }
                                        }
                                    @endphp
                                    <a href="{{ $link }}" target="_blank">{{ $d->so_code ?? ($d->so_trading_code ?? '') }}</a>
                                </td>
                                <td class="text-center">{{ localDate($d->cash_advance_date) }}</td>
                                <td class="text-center">{{ $d->currency_nama }}</td>
                                <td class="text-end">{{ formatNumber($d->cash_advance_amount) }}</td>
                                <td class="text-end">{{ formatNumber($d->returned_amount) }}</td>
                                <td class="text-end">{{ formatNumber($d->cash_advance_remaining_amount) }}</td>
                                <td class="text-end">{{ formatNumber($d->exchange_rate) }}</td>
                                <td class="text-end">{{ formatNumber($d->cash_advance_amount_exchanged) }}</td>
                                <td class="text-end">{{ formatNumber($d->returned_amount_exchanged) }}</td>
                                <td class="text-end">{{ formatNumber($d->cash_advance_remaining_amount_exchanged) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td align="center" colspan="14">
                                    Tidak ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-center"></th>
                            <th class="text-center" colspan="10">TOTAL</th>
                            <th class="text-end">{{ formatNumber($data->sum('cash_advance_amount_exchanged')) }}</th>
                            <th class="text-end">{{ formatNumber($data->sum('returned_amount_exchanged')) }}</th>
                            <th class="text-end">{{ formatNumber($data->sum('cash_advance_remaining_amount_exchanged')) }}</th>
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
