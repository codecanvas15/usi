@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-order-trading';
    $title = 'laporan pembelian trading';
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-order-trading-modal">{{ Str::headline('laporan pembelian') }}</a>
                            <div class="modal fade" id="purchase-order-trading-modal" aria-hidden="true" aria-labelledby="purchase-order-trading-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-trading.report.show', ['type' => 'purchase-order-trading']) }}" method="post" id="report-purchae-order-trading-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('laporan pembelian') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-purchase-order-trading-select" label="branch">

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
                                                            <x-select name="customer_id" label="customer" useBr id="customer-purchase-order-trading-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-purchase-order-trading-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="sh_number_id" useBr label="sh number" id="sh-number-id-purchase-order-trading-select" disabled></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-purchase-order-trading-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr label="status" id="status-purchase-order-trading-select">
                                                                <option value="" selected></option>
                                                                @foreach (status_purchase_orders() as $key => $item)
                                                                    <option value="{{ $key }}">{{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#report-purchae-order-trading-form').find(`input[name='format']`).val('preview');$('#report-purchae-order-trading-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#report-purchae-order-trading-form').find(`input[name='format']`).val('pdf');$('#report-purchae-order-trading-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#report-purchae-order-trading-form').find(`input[name='format']`).val('excel');$('#report-purchae-order-trading-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#summary-purchase-modal">{{ Str::headline('Laporan ringkasan pembelian') }}</a>
                            <div class="modal fade" id="summary-purchase-modal" aria-hidden="true" aria-labelledby="summary-purchase-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-trading.report.show', ['type' => 'summary-purchase-order-trading']) }}" method="post" id="summary-report-purchae-order-trading-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan ringkasan pembelian') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-summary-purchase-order-trading-select" label="branch">

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
                                                            <x-select name="status" useBr label="status" id="status-summary-purchase-order-trading-select">
                                                                <option value="" selected></option>
                                                                @foreach (payment_status() as $key => $item)
                                                                    <option value="{{ $key }}">{{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#summary-report-purchae-order-trading-form').find(`input[name='format']`).val('preview');$('#summary-report-purchae-order-trading-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#summary-report-purchae-order-trading-form').find(`input[name='format']`).val('pdf');$('#summary-report-purchae-order-trading-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#summary-report-purchae-order-trading-form').find(`input[name='format']`).val('excel');$('#summary-report-purchae-order-trading-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-order-trading-modal-detail">{{ Str::headline('laporan purchase order trading detail') }}</a>
                            <div class="modal fade" id="purchase-order-trading-modal-detail" aria-hidden="true" aria-labelledby="purchase-order-trading-modal-detail-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-trading.report.show', ['type' => 'detail-purchase-order-trading']) }}" method="post" id="detail-report-purchae-order-trading-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('laporan purchase order trading') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-detail-purchase-order-trading-select" label="branch">

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
                                                            <x-select name="customer_id" label="customer" useBr id="customer-detail-purchase-order-trading-select"></x-select>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-detail-purchase-order-trading-select"></x-select>
                                                        </div>
                                                    </div> --}}
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="sh_number_id" useBr label="sh number" id="sh-number-id-detail-purchase-order-trading-select" disabled></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-detail-purchase-order-trading-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr label="status" id="status-detail-purchase-order-trading-select">
                                                                <option value="" selected></option>
                                                                @foreach (status_purchase_orders() as $key => $item)
                                                                    <option value="{{ $key }}">{{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#detail-report-purchae-order-trading-form').find(`input[name='format']`).val('preview');$('#detail-report-purchae-order-trading-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#detail-report-purchae-order-trading-form').find(`input[name='format']`).val('pdf');$('#detail-report-purchae-order-trading-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#detail-report-purchae-order-trading-form').find(`input[name='format']`).val('excel');$('#detail-report-purchae-order-trading-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-order-trading-modal-detail-receiving">{{ Str::headline('Laporan penerimaan Pembelian Trading') }}</a>
                            <div class="modal fade" id="purchase-order-trading-modal-detail-receiving" aria-hidden="true" aria-labelledby="purchase-order-trading-modal-detail-receiving-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-trading.report.show', ['type' => 'purchase-order-trading-with-receiving']) }}" method="post" id="detail-report-purchae-order-trading-form-with-receiving" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan penerimaan Pembelian Trading') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-detail-purchase-order-trading-select-receiving" label="branch">

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
                                                            <x-select name="customer_id" label="customer" useBr id="customer-detail-purchase-order-trading-select-receiving"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-detail-purchase-order-trading-select-receiving"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="sh_number_id" useBr label="sh number" id="sh-number-id-detail-purchase-order-trading-select-receiving" disabled></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-detail-purchase-order-trading-select-receiving"></x-select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#detail-report-purchae-order-trading-form-with-receiving').find(`input[name='format']`).val('preview');$('#detail-report-purchae-order-trading-form-with-receiving').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#detail-report-purchae-order-trading-form-with-receiving').find(`input[name='format']`).val('pdf');$('#detail-report-purchae-order-trading-form-with-receiving').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#detail-report-purchae-order-trading-form-with-receiving').find(`input[name='format']`).val('excel');$('#detail-report-purchae-order-trading-form-with-receiving').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-order-trading-outstanding-modal">{{ Str::headline('Laporan Pembelian Trading Outstanding') }}</a>
                            <div class="modal fade" id="purchase-order-trading-outstanding-modal" aria-hidden="true" aria-labelledby="purchase-order-trading-outstanding-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-trading.report.show', ['type' => 'purchase-order-trading-outstanding']) }}" method="post" id="purchase-order-trading-outstanding-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan Pembelian Trading Outstanding') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-purchase-order-trading-outstanding" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" useBr id="customer-purchase-order-trading-outstanding"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-purchase-order-trading-outstanding"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <x-select name="sh_number_id" useBr label="sh number" id="sh-number-id-purchase-order-trading-outstanding" disabled></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-purchase-order-trading-outstanding"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr label="status" id="status-purchase-order-trading-outstanding">
                                                                <option value="" selected></option>
                                                                @foreach (status_purchase_orders() as $key => $item)
                                                                    <option value="{{ $key }}">{{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
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
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#purchase-order-trading-outstanding-form').find(`input[name='format']`).val('preview');$('#purchase-order-trading-outstanding-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#purchase-order-trading-outstanding-form').find(`input[name='format']`).val('pdf');$('#purchase-order-trading-outstanding-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#purchase-order-trading-outstanding-form').find(`input[name='format']`).val('excel');$('#purchase-order-trading-outstanding-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#stock-comparison-purchase-order-trading-outstanding-modal">{{ Str::headline('Laporan Perbandingan Stock Dengan Pembelian Trading Outstanding') }}</a>
                            <div class="modal fade" id="stock-comparison-purchase-order-trading-outstanding-modal" aria-hidden="true" aria-labelledby="stock-comparison-purchase-order-trading-outstanding-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-trading.report.show', ['type' => 'stock-comparison-purchase-order-trading-outstanding']) }}" method="post" id="stock-comparison-purchase-order-trading-outstanding-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan Perbandingan Stock Dengan Pembelian Trading Outstanding') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-stock-comparison-purchase-order-trading-outstanding" label="branch">

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
                                                            <x-select name="customer_id" label="customer" useBr id="customer-stock-comparison-purchase-order-trading-outstanding"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-stock-comparison-purchase-order-trading-outstanding"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="sh_number_id" useBr label="sh number" id="sh-number-id-stock-comparison-purchase-order-trading-outstanding" disabled></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-stock-comparison-purchase-order-trading-outstanding"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr label="status" id="status-stock-comparison-purchase-order-trading-outstanding">
                                                                <option value="" selected></option>
                                                                @foreach (status_purchase_orders() as $key => $item)
                                                                    <option value="{{ $key }}">{{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#stock-comparison-purchase-order-trading-outstanding-form').find(`input[name='format']`).val('preview');$('#stock-comparison-purchase-order-trading-outstanding-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#stock-comparison-purchase-order-trading-outstanding-form').find(`input[name='format']`).val('pdf');$('#stock-comparison-purchase-order-trading-outstanding-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#stock-comparison-purchase-order-trading-outstanding-form').find(`input[name='format']`).val('excel');$('#stock-comparison-purchase-order-trading-outstanding-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#summary-purchase-return-purchase-order-trading-modal">{{ Str::headline('Laporan Retur Pembelian Trading') }}</a>
                            <div class="modal fade" id="summary-purchase-return-purchase-order-trading-modal" aria-hidden="true" aria-labelledby="summary-purchase-return-purchase-order-trading-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-trading.report.show', ['type' => 'summary-purchase-return-purchase-order-trading']) }}" method="post" id="summary-purchase-return-purchase-order-trading-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan Retur Pembelian Trading') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-summary-purchase-return-purchase-order-trading" label="branch">

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
                                                            <x-select name="customer_id" label="customer" useBr id="customer-summary-purchase-return-purchase-order-trading"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-summary-purchase-return-purchase-order-trading"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="sh_number_id" useBr label="sh number" id="sh-number-id-summary-purchase-return-purchase-order-trading" disabled></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-summary-purchase-return-purchase-order-trading"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr label="status" id="status-summary-purchase-return-purchase-order-trading">
                                                                <option value="" selected></option>
                                                                @foreach (status_purchase_orders() as $key => $item)
                                                                    <option value="{{ $key }}">{{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#summary-purchase-return-purchase-order-trading-form').find(`input[name='format']`).val('preview');$('#summary-purchase-return-purchase-order-trading-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#summary-purchase-return-purchase-order-trading-form').find(`input[name='format']`).val('pdf');$('#summary-purchase-return-purchase-order-trading-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#summary-purchase-return-purchase-order-trading-form').find(`input[name='format']`).val('excel');$('#summary-purchase-return-purchase-order-trading-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#debt-due-purchase-order-trading-modal">{{ Str::headline('Laporan Hutang Jatuh Tempo Pembelian Trading') }}</a>
                            <div class="modal fade" id="debt-due-purchase-order-trading-modal" aria-hidden="true" aria-labelledby="debt-due-purchase-order-trading-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-trading.report.show', ['type' => 'debt-due-purchase-order-trading']) }}" method="post" id="debt-due-purchase-order-trading-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan Hutang Jatuh Tempo Pembelian Trading') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-debt-due-purchase-order-trading" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" useBr id="customer-debt-due-purchase-order-trading"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-debt-due-purchase-order-trading"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="from_date" label="dari tanggal" id="" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input class="datepicker-input" name="to_date" label="sampai tanggal" id="" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" />
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#debt-due-purchase-order-trading-form').find(`input[name='format']`).val('preview');$('#debt-due-purchase-order-trading-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#debt-due-purchase-order-trading-form').find(`input[name='format']`).val('pdf');$('#debt-due-purchase-order-trading-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#debt-due-purchase-order-trading-form').find(`input[name='format']`).val('excel');$('#debt-due-purchase-order-trading-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                </x-slot>
            </x-table>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/branch.js') }}"></script>
    <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
    <script>
        $(document).ready(function() {

            const initializePurchaseOrderReport = () => {
                initSelect2Search(`branch-purchase-order-trading-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-order-trading-modal');

                initSelect2Search(`item-purchase-order-trading-select`, `{{ route('admin.select.item') }}/trading`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-order-trading-modal');

                initSelect2Search(`customer-purchase-order-trading-select`, `{{ route('admin.select.customer') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-trading-modal');

                initSelect2Search(`vendor-purchase-order-trading-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-trading-modal');

                $('#customer-purchase-order-trading-select').change(function(e) {
                    e.preventDefault();

                    if (this.value) {
                        $('#sh-number-id-purchase-order-trading-select').html('');
                        $('#sh-number-id-purchase-order-trading-select').attr('disabled', false);

                        initSelect2Search(`sh-number-id-purchase-order-trading-select`, `{{ route('admin.select.sh-number.customer') }}/${this.value}`, {
                            id: "id",
                            text: "kode,supply_point,drop_point"
                        }, 0, {}, '#purchase-order-trading-modal');
                    } else {
                        $('#sh-number-id-purchase-order-trading-select').html('');
                        $('#sh-number-id-purchase-order-trading-select').attr('disabled', true);
                    }
                });

                $('#status-purchase-order-trading-select').select2({
                    dropdownParent: $('#purchase-order-trading-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializePurchaseOrderReport();

            const initializePurchaseOrderSummaryReport = () => {
                initSelect2Search(`branch-summary-purchase-order-trading-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#summary-purchase-modal');

                $('#status-summary-purchase-order-trading-select').select2({
                    dropdownParent: $('#summary-purchase-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializePurchaseOrderSummaryReport();

            const initializePurchaseOrderReportDetail = () => {
                initSelect2Search(`branch-detail-purchase-order-trading-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-order-trading-modal-detail-receiving');

                initSelect2Search(`item-detail-purchase-order-trading-select`, `{{ route('admin.select.item') }}/trading`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-order-trading-modal-detail');

                initSelect2Search(`customer-detail-purchase-order-trading-select`, `{{ route('admin.select.customer') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-trading-modal-detail');

                // initSelect2Search(`vendor-detail-purchase-order-trading-select`, `{{ route('admin.select.vendor') }}`, {
                //     id: "id",
                //     text: "nama"
                // }, 0, {}, '#purchase-order-trading-modal-detail');

                $('#customer-detail-purchase-order-trading-select').change(function(e) {
                    e.preventDefault();

                    if (this.value) {
                        $('#sh-number-id-detail-purchase-order-trading-select').html('');
                        $('#sh-number-id-detail-purchase-order-trading-select').attr('disabled', false);

                        initSelect2Search(`sh-number-id-detail-purchase-order-trading-select`, `{{ route('admin.select.sh-number.customer') }}/${this.value}`, {
                            id: "id",
                            text: "kode,supply_point,drop_point"
                        }, 0, {}, '#purchase-order-trading-modal-detail');
                    } else {
                        $('#sh-number-id-detail-purchase-order-trading-select').html('');
                        $('#sh-number-id-detail-purchase-order-trading-select').attr('disabled', true);
                    }
                });

                $('#status-detail-purchase-order-trading-select').select2({
                    dropdownParent: $('#purchase-order-trading-modal-detail'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializePurchaseOrderReportDetail()

            const initializePurchaseOrderReportDetailReceiving = () => {
                initSelect2Search(`branch-detail-purchase-order-trading-select-receiving`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-order-trading-modal-detail-receiving');

                initSelect2Search(`item-detail-purchase-order-trading-select-receiving`, `{{ route('admin.select.item') }}/trading`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-order-trading-modal-detail-receiving');

                initSelect2Search(`customer-detail-purchase-order-trading-select-receiving`, `{{ route('admin.select.customer') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-trading-modal-detail-receiving');

                initSelect2Search(`vendor-detail-purchase-order-trading-select-receiving`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-trading-modal-detail-receiving');

                $('#customer-detail-purchase-order-trading-select-receiving').change(function(e) {
                    e.preventDefault();

                    if (this.value) {
                        $('#sh-number-id-detail-purchase-order-trading-select-receiving').html('');
                        $('#sh-number-id-detail-purchase-order-trading-select-receiving').attr('disabled', false);

                        initSelect2Search(`sh-number-id-detail-purchase-order-trading-select-receiving`, `{{ route('admin.select.sh-number.customer') }}/${this.value}`, {
                            id: "id",
                            text: "kode,supply_point,drop_point"
                        }, 0, {}, '#purchase-order-trading-modal-detail-receiving');
                    } else {
                        $('#sh-number-id-detail-purchase-order-trading-select-receiving').html('');
                        $('#sh-number-id-detail-purchase-order-trading-select-receiving').attr('disabled', true);
                    }
                });

                $('#status-detail-purchase-order-trading-select-receiving').select2({
                    dropdownParent: $('#purchase-order-trading-modal-detail-receiving'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializePurchaseOrderReportDetailReceiving()

            const initializePurchaseOrderTradingOutstanding = () => {
                initSelect2Search(`branch-purchase-order-trading-outstanding`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-order-trading-outstanding-modal');

                initSelect2Search(`item-purchase-order-trading-outstanding`, `{{ route('admin.select.item') }}/trading`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-order-trading-outstanding-modal');

                initSelect2Search(`customer-purchase-order-trading-outstanding`, `{{ route('admin.select.customer') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-trading-outstanding-modal');

                initSelect2Search(`vendor-purchase-order-trading-outstanding`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-trading-outstanding-modal');

                $('#customer-purchase-order-trading-outstanding').change(function(e) {
                    e.preventDefault();

                    if (this.value) {
                        $('#sh-number-id-purchase-order-trading-outstanding').html('');
                        $('#sh-number-id-purchase-order-trading-outstanding').attr('disabled', false);

                        initSelect2Search(`sh-number-id-purchase-order-trading-outstanding`, `{{ route('admin.select.sh-number.customer') }}/${this.value}`, {
                            id: "id",
                            text: "kode,supply_point,drop_point"
                        }, 0, {}, '#purchase-order-trading-outstanding-modal');
                    } else {
                        $('#sh-number-id-purchase-order-trading-outstanding').html('');
                        $('#sh-number-id-purchase-order-trading-outstanding').attr('disabled', true);
                    }
                });

                $('#status-purchase-order-trading-outstanding').select2({
                    dropdownParent: $('#purchase-order-trading-outstanding-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializePurchaseOrderTradingOutstanding()

            const initializeStockComparisonPurchaseOrderTradingOutstanding = () => {
                initSelect2Search(`branch-stock-comparison-purchase-order-trading-outstanding`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#stock-comparison-purchase-order-trading-outstanding-modal');

                initSelect2Search(`item-stock-comparison-purchase-order-trading-outstanding`, `{{ route('admin.select.item') }}/trading`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#stock-comparison-purchase-order-trading-outstanding-modal');

                initSelect2Search(`customer-stock-comparison-purchase-order-trading-outstanding`, `{{ route('admin.select.customer') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#stock-comparison-purchase-order-trading-outstanding-modal');

                initSelect2Search(`vendor-stock-comparison-purchase-order-trading-outstanding`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#stock-comparison-purchase-order-trading-outstanding-modal');

                $('#customer-stock-comparison-purchase-order-trading-outstanding').change(function(e) {
                    e.preventDefault();

                    if (this.value) {
                        $('#sh-number-id-stock-comparison-purchase-order-trading-outstanding').html('');
                        $('#sh-number-id-stock-comparison-purchase-order-trading-outstanding').attr('disabled', false);

                        initSelect2Search(`sh-number-id-stock-comparison-purchase-order-trading-outstanding`, `{{ route('admin.select.sh-number.customer') }}/${this.value}`, {
                            id: "id",
                            text: "kode,supply_point,drop_point"
                        }, 0, {}, '#stock-comparison-purchase-order-trading-outstanding-modal');
                    } else {
                        $('#sh-number-id-stock-comparison-purchase-order-trading-outstanding').html('');
                        $('#sh-number-id-stock-comparison-purchase-order-trading-outstanding').attr('disabled', true);
                    }
                });

                $('#status-stock-comparison-purchase-order-trading-outstanding').select2({
                    dropdownParent: $('#stock-comparison-purchase-order-trading-outstanding-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeStockComparisonPurchaseOrderTradingOutstanding()

            const initializePurchaseReturnPurchaseOrderTrading = () => {
                initSelect2Search(`branch-summary-purchase-return-purchase-order-trading`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#summary-purchase-return-purchase-order-trading-modal');

                initSelect2Search(`item-summary-purchase-return-purchase-order-trading`, `{{ route('admin.select.item') }}/trading`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#summary-purchase-return-purchase-order-trading-modal');

                initSelect2Search(`customer-summary-purchase-return-purchase-order-trading`, `{{ route('admin.select.customer') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#summary-purchase-return-purchase-order-trading-modal');

                initSelect2Search(`vendor-summary-purchase-return-purchase-order-trading`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#summary-purchase-return-purchase-order-trading-modal');

                $('#customer-summary-purchase-return-purchase-order-trading').change(function(e) {
                    e.preventDefault();

                    if (this.value) {
                        $('#sh-number-id-summary-purchase-return-purchase-order-trading').html('');
                        $('#sh-number-id-summary-purchase-return-purchase-order-trading').attr('disabled', false);

                        initSelect2Search(`sh-number-id-summary-purchase-return-purchase-order-trading`, `{{ route('admin.select.sh-number.customer') }}/${this.value}`, {
                            id: "id",
                            text: "kode,supply_point,drop_point"
                        }, 0, {}, '#summary-purchase-return-purchase-order-trading-modal');
                    } else {
                        $('#sh-number-id-summary-purchase-return-purchase-order-trading').html('');
                        $('#sh-number-id-summary-purchase-return-purchase-order-trading').attr('disabled', true);
                    }
                });

                $('#status-summary-purchase-return-purchase-order-trading').select2({
                    dropdownParent: $('#summary-purchase-return-purchase-order-trading-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializePurchaseReturnPurchaseOrderTrading()

            const initializeDebtDuePurchaseOrderTrading = () => {
                initSelect2Search(`branch-debt-due-purchase-order-trading`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#debt-due-purchase-order-trading-modal');

                initSelect2Search(`customer-debt-due-purchase-order-trading`, `{{ route('admin.select.customer') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#debt-due-purchase-order-trading-modal');

                initSelect2Search(`vendor-debt-due-purchase-order-trading`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#debt-due-purchase-order-trading-modal');
            };

            initializeDebtDuePurchaseOrderTrading()

        });
    </script>
    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarMenuOpen('#purchase-order-report');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
