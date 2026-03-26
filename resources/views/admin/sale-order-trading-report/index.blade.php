@extends('layouts.admin.layout.index')

@php
    $main = 'sale-order-trading';
    $title = 'laporan penjualan trading';
@endphp

@section('title', Str::headline($title) . ' - ')

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
                        <a href="{{ route('admin.sales-order.index') }}">Laporan</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline($title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table :title="$title">
        <x-slot name="header_content">
            @include('components.validate-error')
        </x-slot>
        <x-slot name="table_content">
            <x-table>
                <x-slot name="table_body">
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#laporan-penjualan-modal">{{ Str::headline('laporan penjualan') }}</a>
                            <div class="modal fade" id="laporan-penjualan-modal" aria-hidden="true" aria-labelledby="laporan-penjualan-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-trading-report.report.show', ['type' => 'sale-order-trading']) }}" method="post" id="laporan-penjualan-trading" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('laporan penjualan') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-penjualan-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="from_date" label="dari tanggal" id="" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="to_date" label="sampai tanggal" id="" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" id="customer-penjualan-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-penjualan-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div> --}}
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-penjualan-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="payment status" id="status-penjualan-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (payment_status() as $key => $item)
                                                                    <option value="{{ $key }}">{{ $item['label'] }}
                                                                    </option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#laporan-penjualan-trading').find(`input[name='format']`).val('preview');$('#laporan-penjualan-trading').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#laporan-penjualan-trading').find(`input[name='format']`).val('pdf');$('#laporan-penjualan-trading').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#laporan-penjualan-trading').find(`input[name='format']`).val('excel');$('#laporan-penjualan-trading').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#laporan-penjualan-faktur-pajak-modal">{{ Str::headline('laporan penjualan faktur pajak') }}</a>
                            <div class="modal fade" id="laporan-penjualan-faktur-pajak-modal" aria-hidden="true" aria-labelledby="laporan-penjualan-faktur-pajak-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-trading-report.report.show', ['type' => 'sale-order-trading-faktur-pajak']) }}" method="post" id="laporan-penjualan-faktur-pajak" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('laporan penjualan faktur pajak') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-penjualan-faktur-pajak-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="customer_id" label="customer" id="customer-penjualan-faktur-pajak-select" useBr>

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="status" label="status" id="status-penjualan-faktur-pajak-select" useBr>
                                                                    <option value=""></option>
                                                                    @foreach (sale_order_trading_status() as $key => $item)
                                                                        <option value="{{ $key }}">{{ $item['label'] }}
                                                                        </option>
                                                                    @endforeach
                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="from_date" label="dari tanggal" id="" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="to_date" label="sampai tanggal" id="" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#laporan-penjualan-faktur-pajak').find(`input[name='format']`).val('preview');$('#laporan-penjualan-faktur-pajak').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#laporan-penjualan-faktur-pajak').find(`input[name='format']`).val('pdf');$('#laporan-penjualan-faktur-pajak').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#laporan-penjualan-faktur-pajak').find(`input[name='format']`).val('excel');$('#laporan-penjualan-faktur-pajak').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#summary-laporan-penjualan-trading-modal">{{ Str::headline('Laporan Ringkasan Penjualan Trading') }}</a>
                            <div class="modal fade" id="summary-laporan-penjualan-trading-modal" aria-hidden="true" aria-labelledby="summary-laporan-penjualan-trading-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-trading-report.report.show', ['type' => 'summary-sale-order-trading']) }}" method="post" id="summary-laporan-penjualan-trading" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('Laporan Ringkasan Penjualan Trading') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="summary-branch-penjualan-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="from_date" label="dari tanggal" id="" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="to_date" label="sampai tanggal" id="" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" id="summary-customer-penjualan-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="summary-warehouse-penjualan-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div> --}}
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="summary-item-penjualan-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status pembayaran" id="summary-status-penjualan-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (payment_status() as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#summary-laporan-penjualan-trading').find(`input[name='format']`).val('preview');$('#summary-laporan-penjualan-trading').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#summary-laporan-penjualan-trading').find(`input[name='format']`).val('pdf');$('#summary-laporan-penjualan-trading').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#summary-laporan-penjualan-trading').find(`input[name='format']`).val('excel');$('#summary-laporan-penjualan-trading').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#laporan-penjualan-trading-detail-modal">{{ Str::headline('Laporan rincian penjualan trading per customer') }}</a>
                            <div class="modal fade" id="laporan-penjualan-trading-detail-modal" aria-hidden="true" aria-labelledby="laporan-penjualan-trading-detail-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-trading-report.report.show', ['type' => 'sale-order-trading-detail']) }}" method="post" id="laporan-penjualan-trading-detail" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('laporan penjualan detail') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-penjualan-select-detail" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="from_date" label="dari tanggal" id="" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="to_date" label="sampai tanggal" id="" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" id="customer-penjualan-select-detail" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-penjualan-select-detail" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div> --}}
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-penjualan-select-detail" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-penjualan-select-detail" useBr>
                                                                <option value=""></option>
                                                                @foreach (payment_status() as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#laporan-penjualan-trading-detail').find(`input[name='format']`).val('preview');$('#laporan-penjualan-trading-detail').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#laporan-penjualan-trading-detail').find(`input[name='format']`).val('pdf');$('#laporan-penjualan-trading-detail').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#laporan-penjualan-trading-detail').find(`input[name='format']`).val('excel');$('#laporan-penjualan-trading-detail').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#delivery-order-trading-modal">{{ Str::headline('delivery-order-trading') }}</a>
                            <div class="modal fade" id="delivery-order-trading-modal" aria-hidden="true" aria-labelledby="delivery-order-trading-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-trading-report.report.show', ['type' => 'delivery-order-trading']) }}" method="post" id="delivery-order-trading" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('delivery-order-trading') }}
                                                </h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-delivery-order-geenral-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="from_date" label="dari tanggal" id="" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="to_date" label="sampai tanggal" id="" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" id="customer-delivery-order-geenral-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="sale_order_id" label="sale_order" id="sale_order-delivery-order-geenral-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-delivery-order-geenral-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-delivery-order-geenral-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-delivery-order-geenral-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (get_delivery_order_status() as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#delivery-order-trading').find(`input[name='format']`).val('preview');$('#delivery-order-trading').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#delivery-order-trading').find(`input[name='format']`).val('pdf');$('#delivery-order-trading').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#delivery-order-trading').find(`input[name='format']`).val('excel');$('#delivery-order-trading').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sale-order-trading-per-period-modal">{{ Str::headline('laporan per periode penjualan trading #1') }}</a>
                            <div class="modal fade" id="sale-order-trading-per-period-modal" aria-hidden="true" aria-labelledby="sale-order-trading-per-period-modal" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-trading-report.report.show', ['type' => 'per-periode-sale-order-trading']) }}" method="post" id="per-periode-sale-order-trading" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('per periode sale order trading #1') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-sale-order-trading-per-period" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input type="text" name="month" label="periode" id="" value="" class="month-year-picker-input" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" id="customer-sale-order-trading-per-period" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-sale-order-trading-per-period" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-sale-order-trading-per-period" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-sale-order-trading-per-period" useBr>
                                                                <option value=""></option>
                                                                @foreach (payment_status() as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#per-periode-sale-order-trading').find(`input[name='format']`).val('preview');$('#per-periode-sale-order-trading').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#per-periode-sale-order-trading').find(`input[name='format']`).val('pdf');$('#per-periode-sale-order-trading').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#per-periode-sale-order-trading').find(`input[name='format']`).val('excel');$('#per-periode-sale-order-trading').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sale-order-trading-per-period-2-modal">{{ Str::headline('laporan per periode penjualan trading #2') }}</a>
                            <div class="modal fade" id="sale-order-trading-per-period-2-modal" aria-hidden="true" aria-labelledby="sale-order-trading-per-period-2-modal" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-trading-report.report.show', ['type' => 'per-periode-sale-order-trading-2']) }}" method="post" id="per-periode-sale-order-trading-2" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('per periode sale order trading #2') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-sale-order-trading-per-period-2" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input type="text" name="month" label="periode" id="" value="" class="month-year-picker-input" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" id="customer-sale-order-trading-per-period-2" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-sale-order-trading-per-period-2" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-sale-order-trading-per-period-2" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-sale-order-trading-per-period-2" useBr>
                                                                <option value=""></option>
                                                                @foreach (payment_status() as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#per-periode-sale-order-trading-2').find(`input[name='format']`).val('preview');$('#per-periode-sale-order-trading-2').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#per-periode-sale-order-trading-2').find(`input[name='format']`).val('pdf');$('#per-periode-sale-order-trading-2').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#per-periode-sale-order-trading-2').find(`input[name='format']`).val('excel');$('#per-periode-sale-order-trading-2').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#daily-sale-order-trading-item-detail-customer-modal">{{ Str::headline('Laporan Harian Rincian Item Penjualan Trading Per Customer ') }}</a>
                            <div class="modal fade" id="daily-sale-order-trading-item-detail-customer-modal" aria-hidden="true" aria-labelledby="daily-sale-order-trading-item-detail-customer-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-trading-report.report.show', ['type' => 'daily-sale-order-trading-item-detail-customer']) }}" method="post" id="daily-sale-order-trading-item-detail-customer-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('Laporan Harian Rincian Item Penjualan Trading Per Customer') }}
                                                </h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-daily-sale-order-trading-item-detail-customer-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="from_date" label="dari tanggal" id="" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="to_date" label="sampai tanggal" id="" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" id="customer-daily-sale-order-trading-item-detail-customer-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-daily-sale-order-trading-item-detail-customer-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-daily-sale-order-trading-item-detail-customer-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-daily-sale-order-trading-item-detail-customer-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (get_delivery_order_status() as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#daily-sale-order-trading-item-detail-customer-form').find(`input[name='format']`).val('preview');$('#daily-sale-order-trading-item-detail-customer-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#daily-sale-order-trading-item-detail-customer-form').find(`input[name='format']`).val('pdf');$('#daily-sale-order-trading-item-detail-customer-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#daily-sale-order-trading-item-detail-customer-form').find(`input[name='format']`).val('excel');$('#daily-sale-order-trading-item-detail-customer-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#monthly-sale-order-trading-item-detail-customer-modal">{{ Str::headline('LAPORAN BULANAN RINCIAN ITEM PENJUALAN TRADING PER CUSTOMER') }}</a>
                            <div class="modal fade" id="monthly-sale-order-trading-item-detail-customer-modal" aria-hidden="true" aria-labelledby="monthly-sale-order-trading-item-detail-customer-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-trading-report.report.show', ['type' => 'monthly-sale-order-trading-item-detail-customer']) }}" method="post" id="monthly-sale-order-trading-item-detail-customer-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('LAPORAN BULANAN RINCIAN ITEM PENJUALAN TRADING PER CUSTOMER') }}
                                                </h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-monthly-sale-order-trading-item-detail-customer-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input type="text" name="month" label="periode" id="" value="" class="month-year-picker-input" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" id="customer-monthly-sale-order-trading-item-detail-customer-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-monthly-sale-order-trading-item-detail-customer-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-monthly-sale-order-trading-item-detail-customer-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-monthly-sale-order-trading-item-detail-customer-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (get_delivery_order_status() as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#monthly-sale-order-trading-item-detail-customer-form').find(`input[name='format']`).val('preview');$('#monthly-sale-order-trading-item-detail-customer-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#monthly-sale-order-trading-item-detail-customer-form').find(`input[name='format']`).val('pdf');$('#monthly-sale-order-trading-item-detail-customer-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#monthly-sale-order-trading-item-detail-customer-form').find(`input[name='format']`).val('excel');$('#monthly-sale-order-trading-item-detail-customer-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sale-order-trading-outstanding-modal">{{ Str::headline('Laporan Penjualan Trading Outstanding') }}</a>
                            <div class="modal fade" id="sale-order-trading-outstanding-modal" aria-hidden="true" aria-labelledby="sale-order-trading-outstanding-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-trading-report.report.show', ['type' => 'sale-order-trading-outstanding']) }}" method="post" id="sale-order-trading-outstanding-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('Laporan Penjualan Trading Outstanding') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-sale-order-trading-outstanding-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="from_date" label="dari tanggal" id="" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="to_date" label="sampai tanggal" id="" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" id="customer-sale-order-trading-outstanding-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-sale-order-trading-outstanding-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-sale-order-trading-outstanding-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-sale-order-trading-outstanding-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (get_delivery_order_status() as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#sale-order-trading-outstanding-form').find(`input[name='format']`).val('preview');$('#sale-order-trading-outstanding-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#sale-order-trading-outstanding-form').find(`input[name='format']`).val('pdf');$('#sale-order-trading-outstanding-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#sale-order-trading-outstanding-form').find(`input[name='format']`).val('excel');$('#sale-order-trading-outstanding-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#stock-comparison-with-sale-order-trading-modal">{{ Str::headline('Laporan Perbandingan Stock Dengan Penjualan Trading Outstanding') }}</a>
                            <div class="modal fade" id="stock-comparison-with-sale-order-trading-modal" aria-hidden="true" aria-labelledby="stock-comparison-with-sale-order-trading-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-trading-report.report.show', ['type' => 'stock-comparison-with-sale-order-trading']) }}" method="post" id="stock-comparison-with-sale-order-trading-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('Laporan Perbandingan Stock Dengan Penjualan Trading Outstanding') }}
                                                </h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-stock-comparison-with-sale-order-trading-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="from_date" label="dari tanggal" id="" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="to_date" label="sampai tanggal" id="" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" id="customer-stock-comparison-with-sale-order-trading-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-stock-comparison-with-sale-order-trading-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-stock-comparison-with-sale-order-trading-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-stock-comparison-with-sale-order-trading-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (get_delivery_order_status() as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#stock-comparison-with-sale-order-trading-form').find(`input[name='format']`).val('preview');$('#stock-comparison-with-sale-order-trading-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#stock-comparison-with-sale-order-trading-form').find(`input[name='format']`).val('pdf');$('#stock-comparison-with-sale-order-trading-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#stock-comparison-with-sale-order-trading-form').find(`input[name='format']`).val('excel');$('#stock-comparison-with-sale-order-trading-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#summary-uang-muka-penjualan-modal">{{ Str::headline('summary-uang-muka-penjualan') }}</a>
                            <div class="modal fade" id="summary-uang-muka-penjualan-modal" aria-hidden="true" aria-labelledby="summary-uang-muka-penjualan-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'summary-uang-muka-penjualan']) }}" method="get" id="summary-uang-muka-penjualan-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="summary-uang-muka-penjualan-modalLabel">
                                                    {{ Str::headline('summary-uang-muka-penjualan') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="summary_uang_muka_penjualan_customer_id">customer</label>
                                                        <br>
                                                        <select name="customer_id" id="summary_uang_muka_penjualan_customer_id" class="form-select" autofocus></select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="dari" name="from_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#summary-uang-muka-penjualan-form').find(`input[name='format']`).val('preview');$('#summary-uang-muka-penjualan-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#summary-uang-muka-penjualan-form').find(`input[name='format']`).val('pdf');$('#summary-uang-muka-penjualan-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#summary-uang-muka-penjualan-form').find(`input[name='format']`).val('excel');$('#summary-uang-muka-penjualan-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2Search(`summary_uang_muka_penjualan_customer_id`, `{{ route('admin.select.customer') }}`, {
                                    id: "id",
                                    text: "nama"
                                }, 0, {}, '#summary-uang-muka-penjualan-modal');
                            </script>
                        @endpush
                    </tr>
                    
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#debt-due-sale-order-trading-modal">{{ Str::headline('Laporan Piutang Jatuh Tempo Penjulan Trading') }}</a>
                            <div class="modal fade" id="debt-due-sale-order-trading-modal" aria-hidden="true" aria-labelledby="debt-due-sale-order-trading-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-trading-report.report.show', ['type' => 'debt-due-sale-order-trading']) }}" method="post" id="debt-due-sale-order-trading-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('Laporan Piutang Jatuh Tempo Penjulan Trading') }}
                                                </h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="row">
                                                    @if (get_current_branch()->is_primary)
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-debt-due-sale-order-trading-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" id="customer-debt-due-sale-order-trading-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <x-input-checkbox label="piutang lancar" name="active" id="active1" value="1" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#debt-due-sale-order-trading-form').find(`input[name='format']`).val('preview');$('#debt-due-sale-order-trading-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#debt-due-sale-order-trading-form').find(`input[name='format']`).val('pdf');$('#debt-due-sale-order-trading-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#debt-due-sale-order-trading-form').find(`input[name='format']`).val('excel');$('#debt-due-sale-order-trading-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#compare-so-po-modal">{{ Str::headline('perbandingan SO dan PO') }}</a>
                            <div class="modal fade" id="compare-so-po-modal" aria-hidden="true" aria-labelledby="compare-so-po-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.sale-order-trading-report.report.show', ['type' => 'compare-so-po']) }}" method="post" id="compare-so-po" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('compare-so-po') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    @if (get_current_branch()->is_primary)
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-compare-so-po" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="from_date" label="dari tanggal" id="" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="to_date" label="sampai tanggal" id="" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="" class="form-label">Customer</label>
                                                            <br>
                                                            <select name="customer_id" class="form-select" id="customer-compare-so-po">

                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#compare-so-po').find(`input[name='format']`).val('preview');$('#compare-so-po').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#compare-so-po').find(`input[name='format']`).val('pdf');$('#compare-so-po').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#compare-so-po').find(`input[name='format']`).val('excel');$('#compare-so-po').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#losses-sales-order-modal">{{ Str::headline('losses-sales-order') }}</a>
                            <div class="modal fade" id="losses-sales-order-modal" aria-hidden="true" aria-labelledby="losses-sales-order-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-trading-report.report.show', ['type' => 'losses-sales-order']) }}" method="post" id="losses-sales-order" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('losses-sales-order') }}
                                                </h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-losses-sales-order-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="from_date" label="dari tanggal" id="" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="to_date" label="sampai tanggal" id="" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" id="customer-losses-sales-order-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-losses-sales-order-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-losses-sales-order-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (status_sale_orders() as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#losses-sales-order').find(`input[name='format']`).val('preview');$('#losses-sales-order').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#losses-sales-order').find(`input[name='format']`).val('pdf');$('#losses-sales-order').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#losses-sales-order').find(`input[name='format']`).val('excel');$('#losses-sales-order').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#receivableCardModal">{{ Str::headline('kartu piutang') }}</a>
                            <div class="modal fade" id="receivableCardModal" aria-hidden="true" aria-labelledby="receivableCardModal" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'debt-card-trading']) }}" method="get" id="receivableCard-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('kartu piutang') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="from_date" label="dari tanggal" id="" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="to_date" label="sampai tanggal" id="" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" id="customer-receivableCard" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="customer-type">Jenis Transaksi</label>
                                                            <br>
                                                            <select name="type" id="customer-type" class="form-select" name="customer-type">
                                                                <option value="">Semua</option>
                                                                <option value="{{ App\Models\InvoiceGeneral::class }}">
                                                                    General</option>
                                                                <option value="{{ App\Models\InvoiceTrading::class }}">
                                                                    Trading</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <x-input-checkbox label="piutang lancar" name="active" id="active2" value="1" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#receivableCard-form').find(`input[name='format']`).val('preview');$('#receivableCard-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#receivableCard-form').find(`input[name='format']`).val('pdf');$('#receivableCard-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#receivableCard-form').find(`input[name='format']`).val('excel');$('#receivableCard-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>

                    {{-- SISA PIUTANG --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sisa-piutang-modal">{{ Str::headline('sisa-piutang') }}</a>
                            <div class="modal fade" id="sisa-piutang-modal" aria-hidden="true" aria-labelledby="sisa-piutang-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'sisa-piutang']) }}" method="get" id="sisa-piutang-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="sisa-piutang-modalLabel">
                                                    {{ Str::headline('sisa-piutang') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="sisa_piutang_customer_id">customer</label>
                                                        <br>
                                                        <select name="customer_id" id="sisa_piutang_customer_id" class="form-select" autofocus></select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="sisa_piutang_currency_id">Mata
                                                            Uang</label>
                                                        <br>
                                                        <select name="currency_id" id="sisa_piutang_currency_id" class="form-select" autofocus>
                                                            <option value="{{ get_local_currency()->id }}">
                                                                {{ get_local_currency()->kode }} -
                                                                {{ get_local_currency()->nama }}</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <x-input-checkbox label="piutang lancar" name="active" id="active3" value="1" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#sisa-piutang-form').find(`input[name='format']`).val('preview');$('#sisa-piutang-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#sisa-piutang-form').find(`input[name='format']`).val('pdf');$('#sisa-piutang-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#sisa-piutang-form').find(`input[name='format']`).val('excel');$('#sisa-piutang-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    {{-- END SISA PIUTANG --}}

                    {{-- SISA PIUTANG PER CUSTOMER --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sisa-piutang-per-customer-modal">{{ Str::headline('sisa-piutang-per-customer') }}</a>
                            <div class="modal fade" id="sisa-piutang-per-customer-modal" aria-hidden="true" aria-labelledby="sisa-piutang-per-customer-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'sisa-piutang-per-customer']) }}" method="get" id="sisa-piutang-per-customer-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="sisa-piutang-per-customer-modalLabel">
                                                    {{ Str::headline('sisa-piutang-per-customer') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="sisa_piutang_per_customer_id">customer</label>
                                                        <br>
                                                        <select name="customer_id" id="sisa_piutang_per_customer_id" class="form-select" autofocus></select>
                                                    </div>
                                                    {{-- <div class="col-md-4">
                                                        <label class="form-label" for="sisa_piutang_per_customer_currency_id">Mata Uang</label>
                                                        <br>
                                                        <select name="currency_id" id="sisa_piutang_per_customer_currency_id" class="form-select" autofocus>
                                                            <option value="{{ get_local_currency()->id }}">{{ get_local_currency()->kode }} - {{ get_local_currency()->nama }}</option>
                                                        </select>
                                                    </div> --}}
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-6">
                                                        <x-input-checkbox label="piutang lancar" name="active" id="active4" value="1" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#sisa-piutang-per-customer-form').find(`input[name='format']`).val('preview');$('#sisa-piutang-per-customer-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#sisa-piutang-per-customer-form').find(`input[name='format']`).val('pdf');$('#sisa-piutang-per-customer-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#sisa-piutang-per-customer-form').find(`input[name='format']`).val('excel');$('#sisa-piutang-per-customer-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    {{-- END SISA PIUTANG PER CUSTOMER --}}

                    {{-- PELUNASAN PIUTANG DETAIL --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#pelunasan-piutang-detail-modal">{{ Str::headline('laporan pelunasan piutang detail') }}</a>
                            <div class="modal fade" id="pelunasan-piutang-detail-modal" aria-hidden="true" aria-labelledby="pelunasan-piutang-detail-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'pelunasan-piutang-detail']) }}" method="get" id="pelunasan-piutang-detail-form" target="_blank">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="pelunasan-piutang-detail-modalLabel">
                                                    {{ Str::headline('laporan pelunasan piutang detail') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="pelunasan_piutang_detail_status">Status
                                                                Transaksi</label>
                                                            <br>
                                                            <select name="payment_status" id="pelunasan_piutang_detail_status" class="form-select" label="status" autofocus>
                                                                <option value="">Semua</option>
                                                                <option value="unpaid">Belum Lunas</option>
                                                                <option value="partial-paid">Sebagian</option>
                                                                <option value="paid">Lunas</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="pelunasan_piutang_detail_customer_id">Customer</label>
                                                            <br>
                                                            <select name="customer_id" id="pelunasan_piutang_detail_customer_id" class="form-select" label="customer" autofocus></select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="pelunasan_piutang_detail_invoice_parent_id">No.
                                                                Dokumen</label>
                                                            <br>
                                                            <select name="invoice_parent_id" id="pelunasan_piutang_detail_invoice_parent_id" class="form-select" label="no. dokumen" autofocus></select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label class="form-label" for="pelunasan_piutang_detail_coa_id">Kas/Bank</label>
                                                            <br>
                                                            <select name="coa_id" id="pelunasan_piutang_detail_coa_id" class="form-select" label="kas/bank" autofocus></select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="dari" name="from_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#pelunasan-piutang-detail-form').find(`input[name='format']`).val('preview');$('#pelunasan-piutang-detail-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#pelunasan-piutang-detail-form').find(`input[name='format']`).val('pdf');$('#pelunasan-piutang-detail-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#pelunasan-piutang-detail-form').find(`input[name='format']`).val('excel');$('#pelunasan-piutang-detail-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    {{-- END PELUNASA PIUTANG DETAIL --}}

                    {{-- UMUR PIUTANG --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#receivable-aging-modal">{{ Str::headline('laporan umur piutang') }}</a>
                            <div class="modal fade" id="receivable-aging-modal" aria-hidden="true" aria-labelledby="receivable-aging-modalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.sale-order-general.report.show', ['type' => 'receivable-aging']) }}" method="POST" id="receivable-aging-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="receivable-aging-modalLabel">
                                                    {{ Str::headline('laporan umur piutang') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="receivable_aging_customer_id">Customer</label>
                                                        <br>
                                                        <select name="customer_id" id="receivable_aging_customer_id" class="form-select" autofocus></select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label" for="receivable_aging_customer_id">Tipe Transaksi</label>
                                                        <br>
                                                        <select name="type" id="receivable_aging_type" class="form-select" autofocus>
                                                            <option value="">Semua</option>
                                                            <option value="general">General</option>
                                                            <option value="trading">Trading</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input type="text" label="sampai" name="to_date" class="datepicker-input" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <x-input-checkbox label="piutang lancar" name="active" id="active5" value="1" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#receivable-aging-form').find(`input[name='format']`).val('preview');$('#receivable-aging-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#receivable-aging-form').find(`input[name='format']`).val('pdf');$('#receivable-aging-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#receivable-aging-form').find(`input[name='format']`).val('excel');$('#receivable-aging-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                initSelect2Search(`receivable_aging_customer_id`, `{{ route('admin.select.customer') }}`, {
                                    id: "id",
                                    text: "nama"
                                }, 0, {}, '#receivable-aging-modal');
                            </script>
                        @endpush
                    </tr>
                    {{-- END UMUR PIUTANG --}}

                    {{-- Penjualan Trading Detail & Additional --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#trading-sales-detail-additional">{{ Str::headline('Penjualan Trading Detail & Additional') }}</a>
                            <div class="modal fade" id="trading-sales-detail-additional" aria-hidden="true" aria-labelledby="trading-sales-detail-additionalLabel" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <form action="{{ route('admin.sale-order-trading-report.report.show', ['type' => 'trading-sales-detail-additional']) }}" method="POST" id="trading-sales-detail-additional-form" target="_blank">
                                        @csrf
                                        <input type="hidden" name="format">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="trading-sales-detail-additionalLabel">
                                                    {{ Str::headline('Penjualan Trading Detail & Additional') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-trading-sales-detail-additional-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="from_date" label="dari tanggal" id="" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="to_date" label="sampai tanggal" id="" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" id="customer-trading-sales-detail-additional-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-trading-sales-detail-additional-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div> --}}
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-trading-sales-detail-additional-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="payment status" id="status-trading-sales-detail-additional-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (payment_status() as $key => $item)
                                                                    <option value="{{ $key }}">{{ $item['label'] }}
                                                                    </option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#trading-sales-detail-additional-form').find(`input[name='format']`).val('preview');$('#trading-sales-detail-additional-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#trading-sales-detail-additional-form').find(`input[name='format']`).val('pdf');$('#trading-sales-detail-additional-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#trading-sales-detail-additional-form').find(`input[name='format']`).val('excel');$('#trading-sales-detail-additional-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                        @push('script')
                            <script>
                                $(document).ready(function() {
                                    initializeLaporanTradingSalesDetailAdditional();
                                });

                                const initializeLaporanTradingSalesDetailAdditional = () => {
                                    initSelect2Search(`branch-trading-sales-detail-additional-select`, `{{ route('admin.select.branch') }}`, {
                                        id: "id",
                                        text: "name"
                                    }, 0, {}, '#trading-sales-detail-additional');

                                    initSelect2Search(`customer-trading-sales-detail-additional-select`, `{{ route('admin.select.customer') }}`, {
                                        id: "id",
                                        text: "nama"
                                    }, 0, {}, '#trading-sales-detail-additional');

                                    {{--
                                        initSelect2Search(`warehouse-trading-sales-detail-additional-select`, `{{ route('admin.select.ware-house.type') }}/trading`, {
                                        id: "id",
                                        text: "nama"
                                    }, 0, {}, '#trading-sales-detail-additional');
                                        --}}

                                    initSelect2Search(`item-trading-sales-detail-additional-select`, `{{ route('admin.select.item') }}/trading`, {
                                        id: "id",
                                        text: "nama,kode"
                                    }, 0, {}, '#trading-sales-detail-additional');

                                    $('#status-trading-sales-detail-additional-select').select2({
                                        dropdownParent: $('#trading-sales-detail-additional'),
                                        allowClear: true,
                                        width: "100%",
                                    });
                                };
                            </script>
                        @endpush
                    </tr>
                    {{-- END Penjualan Trading Detail & Additional --}}

                </x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        $(document).ready(function() {

            const initializeLaporanPenjualan = () => {
                initSelect2Search(`branch-penjualan-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#laporan-penjualan-modal');

                initSelect2Search(`customer-penjualan-select`, `{{ route('admin.select.customer') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#laporan-penjualan-modal');

                {{--
                    initSelect2Search(`warehouse-penjualan-select`, `{{ route('admin.select.ware-house.type') }}/trading`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#laporan-penjualan-modal');
                    --}}

                initSelect2Search(`item-penjualan-select`, `{{ route('admin.select.item') }}/trading`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#laporan-penjualan-modal');

                $('#status-penjualan-select').select2({
                    dropdownParent: $('#laporan-penjualan-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeLaporanPenjualan();

            const initializeLaporanPenjualanFakturPajak = () => {
                initSelect2Search(`branch-penjualan-faktur-pajak-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#laporan-penjualan-faktur-pajak-modal');

                initSelect2Search(`customer-penjualan-faktur-pajak-select`, `{{ route('admin.select.customer') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#laporan-penjualan-faktur-pajak-modal');

                $('#status-penjualan-faktur-pajak-select').select2({
                    dropdownParent: $('#laporan-penjualan-faktur-pajak-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeLaporanPenjualanFakturPajak();

            const initializeLaporanPenjualanSummary = () => {
                initSelect2Search(`summary-branch-penjualan-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#summary-laporan-penjualan-trading-modal');

                initSelect2Search(`summary-customer-penjualan-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#summary-laporan-penjualan-trading-modal');

                {{--
                    initSelect2Search(`summary-warehouse-penjualan-select`, `{{ route('admin.select.ware-house.type') }}/trading`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#summary-laporan-penjualan-trading-modal');
                    --}}

                initSelect2Search(`summary-item-penjualan-select`,
                    `{{ route('admin.select.item') }}/trading`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#summary-laporan-penjualan-trading-modal');

                $('#summary-status-penjualan-select').select2({
                    dropdownParent: $('#summary-laporan-penjualan-trading-modal'),
                    allowClear: true,
                    width: "100%",
                });

                $('#summary-status-item-penjualan-select').select2({
                    dropdownParent: $('#summary-laporan-penjualan-trading-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeLaporanPenjualanSummary();

            const initializeLaporanPenjualanDetail = () => {
                initSelect2Search(`branch-penjualan-select-detail`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#laporan-penjualan-trading-detail-modal');

                initSelect2Search(`customer-penjualan-select-detail`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#laporan-penjualan-trading-detail-modal');

                {{--
                    initSelect2Search(`warehouse-penjualan-select-detail`, `{{ route('admin.select.ware-house.type') }}/trading`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#laporan-penjualan-trading-detail-modal');
                    --}}

                initSelect2Search(`item-penjualan-select-detail`,
                    `{{ route('admin.select.item') }}/trading`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#laporan-penjualan-trading-detail-modal');

                $('#status-penjualan-select-detail').select2({
                    dropdownParent: $('#laporan-penjualan-trading-detail-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeLaporanPenjualanDetail();

            const initializeLaporanDeliveryOrderTrading = () => {
                initSelect2Search(`branch-delivery-order-geenral-select`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#delivery-order-trading-modal');

                initSelect2Search(`customer-delivery-order-geenral-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#delivery-order-trading-modal');

                initSelect2Search(`warehouse-delivery-order-geenral-select`,
                    `{{ route('admin.select.ware-house.type') }}/trading`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#delivery-order-trading-modal');

                initSelect2Search(`item-delivery-order-geenral-select`,
                    `{{ route('admin.select.item') }}/trading`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#delivery-order-trading-modal');

                initSelect2SearchPaginationData(`sale_order-delivery-order-geenral-select`,
                    `{{ route('admin.select.sale-order') }}`, {
                        id: "id",
                        text: "nomor_so"
                    }, 0, {
                        customer_id: function() {
                            return $('#customer-delivery-order-geenral-select').val();
                        }
                    }, '#delivery-order-trading-modal');

                $('#status-delivery-order-geenral-select').select2({
                    dropdownParent: $('#delivery-order-trading-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeLaporanDeliveryOrderTrading();

            const initializeLaporanSaleOrderTradingPerPeriod = () => {
                initSelect2Search(`branch-sale-order-trading-per-period`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#sale-order-trading-per-period-modal');

                initSelect2Search(`customer-sale-order-trading-per-period`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#sale-order-trading-per-period-modal');

                initSelect2Search(`warehouse-sale-order-trading-per-period`,
                    `{{ route('admin.select.ware-house.type') }}/trading`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#sale-order-trading-per-period-modal');

                initSelect2Search(`item-sale-order-trading-per-period`,
                    `{{ route('admin.select.item') }}/trading`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#sale-order-trading-per-period-modal');

                $('#status-sale-order-trading-per-period').select2({
                    dropdownParent: $('#sale-order-trading-per-period-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };
            initializeLaporanSaleOrderTradingPerPeriod();

            const initializeLaporanSaleOrderTradingPerPeriod2 = () => {
                initSelect2Search(`branch-sale-order-trading-per-period-2`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#sale-order-trading-per-period-2-modal');

                initSelect2Search(`customer-sale-order-trading-per-period-2`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#sale-order-trading-per-period-2-modal');

                initSelect2Search(`warehouse-sale-order-trading-per-period-2`,
                    `{{ route('admin.select.ware-house.type') }}/trading`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#sale-order-trading-per-period-2-modal');

                initSelect2Search(`item-sale-order-trading-per-period-2`,
                    `{{ route('admin.select.item') }}/trading`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#sale-order-trading-per-period-2-modal');

                $('#status-sale-order-trading-per-period-2').select2({
                    dropdownParent: $('#sale-order-trading-per-period-2-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };
            initializeLaporanSaleOrderTradingPerPeriod2();

            const initializeDialySaleOrderTradingDetailItemCustomerReport = () => {
                initSelect2Search(`branch-daily-sale-order-trading-item-detail-customer-select`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#daily-sale-order-trading-item-detail-customer-modal');

                initSelect2Search(`customer-daily-sale-order-trading-item-detail-customer-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#daily-sale-order-trading-item-detail-customer-modal');

                initSelect2Search(`warehouse-daily-sale-order-trading-item-detail-customer-select`,
                    `{{ route('admin.select.ware-house.type') }}/trading`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#daily-sale-order-trading-item-detail-customer-modal');

                initSelect2Search(`item-daily-sale-order-trading-item-detail-customer-select`,
                    `{{ route('admin.select.item') }}/trading`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#daily-sale-order-trading-item-detail-customer-modal');

                $('#status-daily-sale-order-trading-item-detail-customer-select').select2({
                    dropdownParent: $('#daily-sale-order-trading-item-detail-customer-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeDialySaleOrderTradingDetailItemCustomerReport();

            const initializeMonthlySaleOrderTradingDetailItemCustomerReport = () => {
                initSelect2Search(`branch-monthly-sale-order-trading-item-detail-customer-select`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#monthly-sale-order-trading-item-detail-customer-modal');

                initSelect2Search(`customer-monthly-sale-order-trading-item-detail-customer-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#monthly-sale-order-trading-item-detail-customer-modal');

                initSelect2Search(`warehouse-monthly-sale-order-trading-item-detail-customer-select`,
                    `{{ route('admin.select.ware-house.type') }}/trading`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#monthly-sale-order-trading-item-detail-customer-modal');

                initSelect2Search(`item-monthly-sale-order-trading-item-detail-customer-select`,
                    `{{ route('admin.select.item') }}/trading`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#monthly-sale-order-trading-item-detail-customer-modal');

                $('#status-monthly-sale-order-trading-item-detail-customer-select').select2({
                    dropdownParent: $('#monthly-sale-order-trading-item-detail-customer-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeMonthlySaleOrderTradingDetailItemCustomerReport();

            const initializeSaleOrderTradingOutstanding = () => {
                initSelect2Search(`branch-sale-order-trading-outstanding-select`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#sale-order-trading-outstanding-modal');

                initSelect2Search(`customer-sale-order-trading-outstanding-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#sale-order-trading-outstanding-modal');

                initSelect2Search(`warehouse-sale-order-trading-outstanding-select`,
                    `{{ route('admin.select.ware-house.type') }}/trading`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#sale-order-trading-outstanding-modal');

                initSelect2Search(`item-sale-order-trading-outstanding-select`,
                    `{{ route('admin.select.item') }}/trading`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#sale-order-trading-outstanding-modal');

                $('#status-sale-order-trading-outstanding-select').select2({
                    dropdownParent: $('#sale-order-trading-outstanding-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeSaleOrderTradingOutstanding();

            const initializeStockComparisoWithSaleOrderTradingOutstanding = () => {
                initSelect2Search(`branch-stock-comparison-with-sale-order-trading-select`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#stock-comparison-with-sale-order-trading-modal');

                initSelect2Search(`customer-stock-comparison-with-sale-order-trading-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#stock-comparison-with-sale-order-trading-modal');

                initSelect2Search(`warehouse-stock-comparison-with-sale-order-trading-select`,
                    `{{ route('admin.select.ware-house.type') }}/trading`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#stock-comparison-with-sale-order-trading-modal');

                initSelect2Search(`item-stock-comparison-with-sale-order-trading-select`,
                    `{{ route('admin.select.item') }}/trading`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#stock-comparison-with-sale-order-trading-modal');

                $('#status-stock-comparison-with-sale-order-trading-select').select2({
                    dropdownParent: $('#stock-comparison-with-sale-order-trading-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeStockComparisoWithSaleOrderTradingOutstanding();

            const initializedebtDueSaleOrderTrading = () => {
                initSelect2Search(`branch-debt-due-sale-order-trading-select`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#debt-due-sale-order-trading-modal');

                initSelect2Search(`customer-debt-due-sale-order-trading-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#debt-due-sale-order-trading-modal');
            };

            initializedebtDueSaleOrderTrading();


            const initializeSaleOrderTradingLossesReport = () => {
                initSelect2Search(`branch-losses-sales-order-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#losses-sales-order-modal');

                initSelect2Search(`customer-losses-sales-order-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#losses-sales-order-modal');

                initSelect2Search(`item-losses-sales-order-select`,
                    `{{ route('admin.select.item') }}/trading`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#losses-sales-order-modal');

                $('#status-losses-sales-order-select').select2({
                    dropdownParent: $('#losses-sales-order-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeSaleOrderTradingLossesReport();
        });

        initSelect2Search(`customer-compare-so-po`, `{{ route('admin.select.customer') }}`, {
            id: "id",
            text: "nama"
        }, 0, {}, '#compare-so-po-modal');

        initSelect2Search(`branch-compare-so-po`, `{{ route('admin.select.branch') }}`, {
            id: "id",
            text: "name"
        }, 0, {}, '#compare-so-po-modal');

        const initializeDebtCardReportTrading = () => {
            initSelect2Search(`customer-receivableCard`, `{{ route('admin.select.customer') }}`, {
                id: "id",
                text: "nama"
            }, 0, {}, '#receivableCardModal');
        };

        initializeDebtCardReportTrading();

        // sisa piutang
        initSelect2Search(`sisa_piutang_customer_id`, `{{ route('admin.select.customer') }}`, {
            id: "id",
            text: "nama"
        }, 0, {}, '#sisa-piutang-modal');

        initSelect2Search(`sisa_piutang_currency_id`, `{{ route('admin.select.currency') }}`, {
            id: "id",
            text: "kode,nama"
        }, 0, {}, '#sisa-piutang-modal');

        // sisa piutang per customer
        initSelect2Search(`sisa_piutang_per_customer_id`, `{{ route('admin.select.customer') }}`, {
            id: "id",
            text: "nama"
        }, 0, {}, '#sisa-piutang-per-customer-modal');

        initSelect2Search(`sisa_piutang_per_currency_id`, `{{ route('admin.select.currency') }}`, {
            id: "id",
            text: "kode,nama"
        }, 0, {}, '#sisa-piutang-per-customer-modal');

        // pelunasan piutang detail
        initSelect2SearchPagination(`pelunasan_piutang_detail_coa_id`, `{{ route('admin.select.coa') }}`, {
            id: "id",
            text: "account_code,name"
        }, 0, {
            account_type: "Cash & Bank"
        }, '#pelunasan-piutang-detail-modal');

        initSelect2Search(`pelunasan_piutang_detail_customer_id`, `{{ route('admin.select.customer') }}`, {
            id: "id",
            text: "nama"
        }, 0, {}, '#pelunasan-piutang-detail-modal');

        initSelect2Search(`pelunasan_piutang_detail_invoice_parent_id`, `{{ route('admin.select.invoice') }}`, {
            id: "id",
            text: "code"
        }, 0, {
            payment_status: function() {
                return $('#pelunasan_piutang_detail_status').val();
            },
            customer_id: function() {
                return $('#pelunasan_piutang_detail_customer_id').val();
            },
            status: 'approve',
        }, '#pelunasan-piutang-detail-modal');
    </script>
    <script>
        sidebarMenuOpen('#trading');
        sidebarMenuOpen('#sale-order-report');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
