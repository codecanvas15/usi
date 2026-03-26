@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-order-service';
    $title = 'laporan pembelian service';
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-order-service-modal">{{ Str::headline('laporan pembelian') }}</a>
                            <div class="modal fade" id="purchase-order-service-modal" aria-hidden="true" aria-labelledby="purchase-order-service-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-service.report.show', ['type' => 'purchase-order-service']) }}" method="post" id="report-purchae-order-service-form" target="_blank">
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
                                                                <x-select name="branch_id" useBr id="branch-purchase-order-service-select" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-purchase-order-service-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-purchase-order-service-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr label="status" id="status-purchase-order-service-select">
                                                                <option value="" selected></option>
                                                                @foreach (purchase_service_status() as $key => $item)
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#report-purchae-order-service-form').find(`input[name='format']`).val('preview');$('#report-purchae-order-service-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#report-purchae-order-service-form').find(`input[name='format']`).val('pdf');$('#report-purchae-order-service-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#report-purchae-order-service-form').find(`input[name='format']`).val('excel');$('#report-purchae-order-service-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#summary-purchase-modal">{{ Str::headline('laporan ringkasan pembelian') }}</a>
                            <div class="modal fade" id="summary-purchase-modal" aria-hidden="true" aria-labelledby="summary-purchase-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-service.report.show', ['type' => 'summary-purchase-order-service']) }}" method="post" id="summary-report-purchae-order-service-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('laporan ringkasan pembelian') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-summary-purchase-order-service-select" label="branch">

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
                                                            <x-select name="status" useBr label="status" id="status-summary-purchase-order-service-select">
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#summary-report-purchae-order-service-form').find(`input[name='format']`).val('preview');$('#summary-report-purchae-order-service-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#summary-report-purchae-order-service-form').find(`input[name='format']`).val('pdf');$('#summary-report-purchae-order-service-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#summary-report-purchae-order-service-form').find(`input[name='format']`).val('excel');$('#summary-report-purchae-order-service-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-order-service-modal-detail">{{ Str::headline('Laporan detail Pembelian Service') }}</a>
                            <div class="modal fade" id="purchase-order-service-modal-detail" aria-hidden="true" aria-labelledby="purchase-order-service-modal-detail-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-service.report.show', ['type' => 'detail-purchase-order-service']) }}" method="post" id="detail-report-purchae-order-service-form-with" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan detail Pembelian Service') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-detail-purchase-order-service-select" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-detail-purchase-order-service-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-detail-purchase-order-service-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr label="status" id="status-detail-purchase-order-service-select">
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#detail-report-purchae-order-service-form-with').find(`input[name='format']`).val('preview');$('#detail-report-purchae-order-service-form-with').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#detail-report-purchae-order-service-form-with').find(`input[name='format']`).val('pdf');$('#detail-report-purchae-order-service-form-with').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#detail-report-purchae-order-service-form-with').find(`input[name='format']`).val('excel');$('#detail-report-purchae-order-service-form-with').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-order-service-modal-detail-receiving">{{ Str::headline('Laporan penerimaan Pembelian Service') }}</a>
                            <div class="modal fade" id="purchase-order-service-modal-detail-receiving" aria-hidden="true" aria-labelledby="purchase-order-service-modal-detail-receiving-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-service.report.show', ['type' => 'purchase-order-service-receiving']) }}" method="post" id="detail-report-purchae-order-service-form-with-receiving" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan penerimaan Pembelian Service') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-detail-purchase-order-service-select-receiving" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-detail-purchase-order-service-select-receiving"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-detail-purchase-order-service-select-receiving"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr label="status" id="status-detail-purchase-order-service-select-receiving">
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#detail-report-purchae-order-service-form-with-receiving').find(`input[name='format']`).val('preview');$('#detail-report-purchae-order-service-form-with-receiving').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#detail-report-purchae-order-service-form-with-receiving').find(`input[name='format']`).val('pdf');$('#detail-report-purchae-order-service-form-with-receiving').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#detail-report-purchae-order-service-form-with-receiving').find(`input[name='format']`).val('excel');$('#detail-report-purchae-order-service-form-with-receiving').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-order-service-outstanding-modal">{{ Str::headline('Laporan Pembelian service Outstanding') }}</a>
                            <div class="modal fade" id="purchase-order-service-outstanding-modal" aria-hidden="true" aria-labelledby="purchase-order-service-outstanding-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-service.report.show', ['type' => 'purchase-order-service-outstanding']) }}" method="post" id="purchase-order-service-outstanding-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan Penjualan service Outstanding') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-purchase-order-service-outstanding-select" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" id="vendor-purchase-order-service-outstanding-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-purchase-order-service-outstanding-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-purchase-order-service-outstanding-select" useBr>
                                                                <option value=""></option>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#purchase-order-service-outstanding-form').find(`input[name='format']`).val('preview');$('#purchase-order-service-outstanding-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#purchase-order-service-outstanding-form').find(`input[name='format']`).val('pdf');$('#purchase-order-service-outstanding-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#purchase-order-service-outstanding-form').find(`input[name='format']`).val('excel');$('#purchase-order-service-outstanding-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#stock-comparison-with-purchase-order-service-modal">{{ Str::headline('Laporan Perbandingan Stock Dengan Pembelian service Outstanding') }}</a>
                            <div class="modal fade" id="stock-comparison-with-purchase-order-service-modal" aria-hidden="true" aria-labelledby="stock-comparison-with-purchase-order-service-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-service.report.show', ['type' => 'stock-comparison-with-purchase-order-service']) }}" method="post" id="stock-comparison-with-purchase-order-service-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan Perbandingan Stock Dengan Penjualan service Outstanding') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-stock-comparison-with-purchase-order-service-select" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" id="vendor-stock-comparison-with-purchase-order-service-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-stock-comparison-with-purchase-order-service-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-stock-comparison-with-purchase-order-service-select" useBr>
                                                                <option value=""></option>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#stock-comparison-with-purchase-order-service-form').find(`input[name='format']`).val('preview');$('#stock-comparison-with-purchase-order-service-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#stock-comparison-with-purchase-order-service-form').find(`input[name='format']`).val('pdf');$('#stock-comparison-with-purchase-order-service-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#stock-comparison-with-purchase-order-service-form').find(`input[name='format']`).val('excel');$('#stock-comparison-with-purchase-order-service-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#return-purchase-order-service-modal">{{ Str::headline('Laporan Ringkasan Retur Pembelian service') }}</a>
                            <div class="modal fade" id="return-purchase-order-service-modal" aria-hidden="true" aria-labelledby="return-purchase-order-service-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-service.report.show', ['type' => 'return-purchase-order-service']) }}" method="post" id="return-purchase-order-service-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan Ringkasan Retur Penjualan service') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-invoice-return-purchase-order-service-select" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" id="vendor-invoice-return-purchase-order-service-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-invoice-return-purchase-order-service-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-invoice-return-purchase-order-service-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (fund_submission_status() as $key => $item)
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#return-purchase-order-service-form').find(`input[name='format']`).val('preview');$('#return-purchase-order-service-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#return-purchase-order-service-form').find(`input[name='format']`).val('pdf');$('#return-purchase-order-service-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#return-purchase-order-service-form').find(`input[name='format']`).val('excel');$('#return-purchase-order-service-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#debt-due-purchase-order-service-modal">{{ Str::headline('Laporan Hutang jatuh tempo') }}</a>
                            <div class="modal fade" id="debt-due-purchase-order-service-modal" aria-hidden="true" aria-labelledby="debt-due-purchase-order-service-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-service.report.show', ['type' => 'debt-due-purchase-order-service']) }}" method="post" id="debt-due-purchase-order-service-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan Hutang jatuh tempo') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-debt-due-purchase-order-service-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="vendor_id" label="vendor" id="vendor-debt-due-purchase-order-service-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#debt-due-purchase-order-service-form').find(`input[name='format']`).val('preview');$('#debt-due-purchase-order-service-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#debt-due-purchase-order-service-form').find(`input[name='format']`).val('pdf');$('#debt-due-purchase-order-service-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#debt-due-purchase-order-service-form').find(`input[name='format']`).val('excel');$('#debt-due-purchase-order-service-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#detail-closed-purchase-order-service-modal">{{ Str::headline('Laporan Detail Order Pembelian') }}</a>
                            <div class="modal fade" id="detail-closed-purchase-order-service-modal" aria-hidden="true" aria-labelledby="detail-closed-purchase-order-service-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-service.report.show', ['type' => 'detail-closed-purchase-order-service']) }}" method="post" id="detail-closed-purchase-order-service-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan Detail Order Pembelian') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-detail-closed-purchase-order-service-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input type="text" name="from_date" label="dari" id="" value="" class="datepicker-input" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input type="text" name="to_date" label="sampai" id="" value="" class="datepicker-input" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-detail-closed-purchase-order-service-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-detail-closed-purchase-order-service-select"></x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#detail-closed-purchase-order-service-form').find(`input[name='format']`).val('preview');$('#detail-closed-purchase-order-service-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#detail-closed-purchase-order-service-form').find(`input[name='format']`).val('pdf');$('#detail-closed-purchase-order-service-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#detail-closed-purchase-order-service-form').find(`input[name='format']`).val('excel');$('#detail-closed-purchase-order-service-form').submit()" />
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

    <script>
        $(document).ready(function() {
            const initializePurchaseOrderReport = () => {
                initSelect2Search(`branch-purchase-order-service-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-order-service-modal');

                initSelect2Search(`item-purchase-order-service-select`, `{{ route('admin.select.item') }}/service`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-order-service-modal');

                initSelect2Search(`vendor-purchase-order-service-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-service-modal');

                $('#status-purchase-order-service-select').select2({
                    dropdownParent: $('#purchase-order-service-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializePurchaseOrderReport();

            const initializePurchaseOrderSummaryReport = () => {
                initSelect2Search(`branch-summary-purchase-order-service-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#summary-purchase-modal');

                $('#status-summary-purchase-order-service-select').select2({
                    dropdownParent: $('#summary-purchase-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializePurchaseOrderSummaryReport();

            const initializePurchaseOrderReportDetail = () => {
                initSelect2Search(`branch-detail-purchase-order-service-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-order-service-modal-detail');

                initSelect2Search(`item-detail-purchase-order-service-select`, `{{ route('admin.select.item') }}/service`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-order-service-modal-detail');

                initSelect2Search(`vendor-detail-purchase-order-service-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-service-modal-detail');

                $('#status-detail-purchase-order-service-select').select2({
                    dropdownParent: $('#purchase-order-service-modal-detail'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializePurchaseOrderReportDetail()

            const initializePurchaseOrderReportDetailReceiving = () => {
                initSelect2Search(`branch-detail-purchase-order-service-select-receiving`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-order-service-modal-detail-receiving');

                initSelect2Search(`item-detail-purchase-order-service-select-receiving`, `{{ route('admin.select.item') }}/service`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-order-service-modal-detail-receiving');

                initSelect2Search(`vendor-detail-purchase-order-service-select-receiving`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-service-modal-detail-receiving');

                $('#vendor-detail-purchase-order-service-select-receiving').change(function(e) {
                    e.preventDefault();

                    if (this.value) {
                        $('#sh-number-id-detail-purchase-order-service-select-receiving').html('');
                        $('#sh-number-id-detail-purchase-order-service-select-receiving').attr('disabled', false);

                        initSelect2Search(`sh-number-id-detail-purchase-order-service-select-receiving`, `{{ route('admin.select.sh-number.customer') }}/${this.value}`, {
                            id: "id",
                            text: "kode,supply_point,drop_point"
                        }, 0, {}, '#purchase-order-service-modal-detail-receiving');
                    } else {
                        $('#sh-number-id-detail-purchase-order-service-select-receiving').html('');
                        $('#sh-number-id-detail-purchase-order-service-select-receiving').attr('disabled', true);
                    }
                });

                $('#status-detail-purchase-order-service-select-receiving').select2({
                    dropdownParent: $('#purchase-order-service-modal-detail-receiving'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializePurchaseOrderReportDetailReceiving()

            const initializePurchaseOrderServiceOutstanding = () => {
                initSelect2Search(`branch-purchase-order-service-outstanding-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-order-service-outstanding-modal');

                initSelect2Search(`vendor-purchase-order-service-outstanding-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-service-outstanding-modal');

                initSelect2Search(`warehouse-purchase-order-service-outstanding-select`, `{{ route('admin.select.ware-house.type') }}/service`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-service-outstanding-modal');

                initSelect2Search(`item-purchase-order-service-outstanding-select`, `{{ route('admin.select.item') }}/service`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-order-service-outstanding-modal');

                $('#status-purchase-order-service-outstanding-select').select2({
                    dropdownParent: $('#purchase-order-service-outstanding-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializePurchaseOrderServiceOutstanding();

            const initializeStockComparisonWithPurchaseOrderServiceOutstanding = () => {
                initSelect2Search(`branch-stock-comparison-with-purchase-order-service-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#stock-comparison-with-purchase-order-service-modal');

                initSelect2Search(`vendor-stock-comparison-with-purchase-order-service-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#stock-comparison-with-purchase-order-service-modal');

                initSelect2Search(`warehouse-stock-comparison-with-purchase-order-service-select`, `{{ route('admin.select.ware-house.type') }}/service`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#stock-comparison-with-purchase-order-service-modal');

                initSelect2Search(`item-stock-comparison-with-purchase-order-service-select`, `{{ route('admin.select.item') }}/service`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#stock-comparison-with-purchase-order-service-modal');

                $('#status-stock-comparison-with-purchase-order-service-select').select2({
                    dropdownParent: $('#stock-comparison-with-purchase-order-service-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeStockComparisonWithPurchaseOrderServiceOutstanding();

            const initializeInvoiceReturnSummaryPurchaseOrderService = () => {
                initSelect2Search(`branch-invoice-return-purchase-order-service-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#return-purchase-order-service-modal');

                initSelect2Search(`vendor-invoice-return-purchase-order-service-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#return-purchase-order-service-modal');

                initSelect2Search(`warehouse-invoice-return-purchase-order-service-select`, `{{ route('admin.select.ware-house.type') }}/service`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#return-purchase-order-service-modal');

                initSelect2Search(`item-invoice-return-purchase-order-service-select`, `{{ route('admin.select.item') }}/service`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#return-purchase-order-service-modal');

                $('#status-invoice-return-purchase-order-service-select').select2({
                    dropdownParent: $('#return-purchase-order-service-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeInvoiceReturnSummaryPurchaseOrderService();

            const initializedebtDuePurchaseOrderService = () => {
                initSelect2Search(`branch-debt-due-purchase-order-service-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#debt-due-purchase-order-service-modal');

                initSelect2Search(`vendor-debt-due-purchase-order-service-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#debt-due-purchase-order-service-modal');
            };

            initializedebtDuePurchaseOrderService();

            const initializeDetailPurchaseOrderService = () => {
                initSelect2Search(`branch-detail-closed-purchase-order-service-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#detail-closed-purchase-order-service-modal');

                initSelect2Search(`item-detail-closed-purchase-order-service-select`, `{{ route('admin.select.item') }}/service`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#detail-closed-purchase-order-service-modal');

                initSelect2Search(`vendor-detail-closed-purchase-order-service-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#detail-closed-purchase-order-service-modal');

                $('#status-detail-closed-purchase-order-service-select').select2({
                    dropdownParent: $('#detail-closed-purchase-order-service-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeDetailPurchaseOrderService()
        });
    </script>

    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarMenuOpen('#purchase-order-report');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
