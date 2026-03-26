@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-order-general';
    $title = 'laporan purchase order';
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-order-general-modal">{{ Str::headline('laporan pembelian') }}</a>
                            <div class="modal fade" id="purchase-order-general-modal" aria-hidden="true" aria-labelledby="purchase-order-general-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-general.report.show', ['type' => 'purchase-order-general']) }}" method="post" id="report-purchae-order-general-form" target="_blank">
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
                                                                <x-select name="branch_id" useBr id="branch-purchase-order-general-select" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-purchase-order-general-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-purchase-order-general-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr label="status" id="status-purchase-order-general-select">
                                                                <option value="" selected></option>
                                                                @foreach (purchase_order_general_status() as $key => $item)
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#report-purchae-order-general-form').find(`input[name='format']`).val('preview');$('#report-purchae-order-general-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#report-purchae-order-general-form').find(`input[name='format']`).val('pdf');$('#report-purchae-order-general-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#report-purchae-order-general-form').find(`input[name='format']`).val('excel');$('#report-purchae-order-general-form').submit()" />
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
                                    <form action="{{ route('admin.purchase-order-general.report.show', ['type' => 'summary-purchase-order-general']) }}" method="post" id="summary-report-purchae-order-general-form" target="_blank">
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
                                                                <x-select name="branch_id" useBr id="branch-summary-purchase-order-general-select" label="branch">

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
                                                            <x-select name="status" useBr label="status" id="status-summary-purchase-order-general-select">
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#summary-report-purchae-order-general-form').find(`input[name='format']`).val('preview');$('#summary-report-purchae-order-general-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#summary-report-purchae-order-general-form').find(`input[name='format']`).val('pdf');$('#summary-report-purchae-order-general-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#summary-report-purchae-order-general-form').find(`input[name='format']`).val('excel');$('#summary-report-purchae-order-general-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-order-general-modal-detail">{{ Str::headline('Laporan detail Pembelian General') }}</a>
                            <div class="modal fade" id="purchase-order-general-modal-detail" aria-hidden="true" aria-labelledby="purchase-order-general-modal-detail-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-general.report.show', ['type' => 'detail-purchase-order-general']) }}" method="post" id="detail-report-purchae-order-general-form-with" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan detail Pembelian General') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-detail-purchase-order-general-select" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-detail-purchase-order-general-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-detail-purchase-order-general-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr label="status" id="status-detail-purchase-order-general-select">
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#detail-report-purchae-order-general-form-with').find(`input[name='format']`).val('preview');$('#detail-report-purchae-order-general-form-with').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#detail-report-purchae-order-general-form-with').find(`input[name='format']`).val('pdf');$('#detail-report-purchae-order-general-form-with').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#detail-report-purchae-order-general-form-with').find(`input[name='format']`).val('excel');$('#detail-report-purchae-order-general-form-with').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-order-general-modal-detail-receiving">{{ Str::headline('Laporan penerimaan Pembelian General') }}</a>
                            <div class="modal fade" id="purchase-order-general-modal-detail-receiving" aria-hidden="true" aria-labelledby="purchase-order-general-modal-detail-receiving-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-general.report.show', ['type' => 'purchase-order-general-receiving']) }}" method="post" id="detail-report-purchae-order-general-form-with-receiving" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan penerimaan Pembelian General') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-detail-purchase-order-general-select-receiving" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-detail-purchase-order-general-select-receiving"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-detail-purchase-order-general-select-receiving"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr label="status" id="status-detail-purchase-order-general-select-receiving">
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#detail-report-purchae-order-general-form-with-receiving').find(`input[name='format']`).val('preview');$('#detail-report-purchae-order-general-form-with-receiving').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#detail-report-purchae-order-general-form-with-receiving').find(`input[name='format']`).val('pdf');$('#detail-report-purchae-order-general-form-with-receiving').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#detail-report-purchae-order-general-form-with-receiving').find(`input[name='format']`).val('excel');$('#detail-report-purchae-order-general-form-with-receiving').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-order-general-per-period-modal">{{ Str::headline('per-periode-purchase-order-general') }}</a>
                            <div class="modal fade" id="purchase-order-general-per-period-modal" aria-hidden="true" aria-labelledby="purchase-order-general-per-period-modal" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-general.report.show', ['type' => 'per-periode-purchase-order-general']) }}" method="post" id="per-periode-purchase-order-general" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('per-periode-purchase-order-general') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-purchase-order-general-per-period" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" id="vendor-purchase-order-general-per-period" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-purchase-order-general-per-period" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-purchase-order-general-per-period" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-purchase-order-general-per-period" useBr>
                                                                <option value=""></option>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#per-periode-purchase-order-general').find(`input[name='format']`).val('preview');$('#per-periode-purchase-order-general').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#per-periode-purchase-order-general').find(`input[name='format']`).val('pdf');$('#per-periode-purchase-order-general').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#per-periode-purchase-order-general').find(`input[name='format']`).val('excel');$('#per-periode-purchase-order-general').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-order-general-outstanding-modal">{{ Str::headline('Laporan Pembelian General Outstanding') }}</a>
                            <div class="modal fade" id="purchase-order-general-outstanding-modal" aria-hidden="true" aria-labelledby="purchase-order-general-outstanding-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-general.report.show', ['type' => 'purchase-order-general-outstanding']) }}" method="post" id="purchase-order-general-outstanding-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan pembelian General Outstanding') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-purchase-order-general-outstanding-select" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" id="vendor-purchase-order-general-outstanding-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-purchase-order-general-outstanding-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-purchase-order-general-outstanding-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (purchase_order_general_status() as $key => $item)
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#purchase-order-general-outstanding-form').find(`input[name='format']`).val('preview');$('#purchase-order-general-outstanding-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#purchase-order-general-outstanding-form').find(`input[name='format']`).val('pdf');$('#purchase-order-general-outstanding-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#purchase-order-general-outstanding-form').find(`input[name='format']`).val('excel');$('#purchase-order-general-outstanding-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#stock-comparison-with-purchase-order-general-modal">{{ Str::headline('Laporan Perbandingan Stock Dengan pembelian General Outstanding') }}</a>
                            <div class="modal fade" id="stock-comparison-with-purchase-order-general-modal" aria-hidden="true" aria-labelledby="stock-comparison-with-purchase-order-general-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-general.report.show', ['type' => 'stock-comparison-with-purchase-order-general']) }}" method="post" id="stock-comparison-with-purchase-order-general-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan Perbandingan Stock Dengan pembelian General Outstanding') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-stock-comparison-with-purchase-order-general-select" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" id="vendor-stock-comparison-with-purchase-order-general-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-stock-comparison-with-purchase-order-general-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-stock-comparison-with-purchase-order-general-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (purchase_order_general_status() as $key => $item)
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#stock-comparison-with-purchase-order-general-form').find(`input[name='format']`).val('preview');$('#stock-comparison-with-purchase-order-general-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#stock-comparison-with-purchase-order-general-form').find(`input[name='format']`).val('pdf');$('#stock-comparison-with-purchase-order-general-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#stock-comparison-with-purchase-order-general-form').find(`input[name='format']`).val('excel');$('#stock-comparison-with-purchase-order-general-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#return-purchase-order-general-modal">{{ Str::headline('Laporan Ringkasan Retur pembelian General') }}</a>
                            <div class="modal fade" id="return-purchase-order-general-modal" aria-hidden="true" aria-labelledby="return-purchase-order-general-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-general.report.show', ['type' => 'return-purchase-order-general']) }}" method="post" id="invoice-return-purchase-order-general-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan Ringkasan Retur pembelian General') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-invoice-return-purchase-order-general-select" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" id="vendor-invoice-return-purchase-order-general-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-invoice-return-purchase-order-general-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-invoice-return-purchase-order-general-select" useBr>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#invoice-return-purchase-order-general-form').find(`input[name='format']`).val('preview');$('#invoice-return-purchase-order-general-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#invoice-return-purchase-order-general-form').find(`input[name='format']`).val('pdf');$('#invoice-return-purchase-order-general-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#invoice-return-purchase-order-general-form').find(`input[name='format']`).val('excel');$('#invoice-return-purchase-order-general-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#debt-due-purchase-order-general-modal">{{ Str::headline('Laporan Hutang jatuh tempo') }}</a>
                            <div class="modal fade" id="debt-due-purchase-order-general-modal" aria-hidden="true" aria-labelledby="debt-due-purchase-order-general-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-general.report.show', ['type' => 'debt-due-purchase-order-general']) }}" method="post" id="debt-due-purchase-order-general-form" target="_blank">
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
                                                                <x-select name="branch_id" useBr id="branch-debt-due-purchase-order-general-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="vendor_id" label="vendor" id="vendor-debt-due-purchase-order-general-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#debt-due-purchase-order-general-form').find(`input[name='format']`).val('preview');$('#debt-due-purchase-order-general-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#debt-due-purchase-order-general-form').find(`input[name='format']`).val('pdf');$('#debt-due-purchase-order-general-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#debt-due-purchase-order-general-form').find(`input[name='format']`).val('excel');$('#debt-due-purchase-order-general-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#detail-closed-purchase-order-general-modal">{{ Str::headline('Laporan Detail Order Pembelian') }}</a>
                            <div class="modal fade" id="detail-closed-purchase-order-general-modal" aria-hidden="true" aria-labelledby="detail-closed-purchase-order-general-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-general.report.show', ['type' => 'detail-closed-purchase-order-general']) }}" method="post" id="detail-closed-purchase-order-general-form" target="_blank">
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
                                                                <x-select name="branch_id" useBr id="branch-detail-closed-purchase-order-general-select" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-detail-closed-purchase-order-general-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-detail-closed-purchase-order-general-select"></x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#detail-closed-purchase-order-general-form').find(`input[name='format']`).val('preview');$('#detail-closed-purchase-order-general-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#detail-closed-purchase-order-general-form').find(`input[name='format']`).val('pdf');$('#detail-closed-purchase-order-general-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#detail-closed-purchase-order-general-form').find(`input[name='format']`).val('excel');$('#detail-closed-purchase-order-general-form').submit()" />
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
                initSelect2Search(`branch-purchase-order-general-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-order-general-modal');

                initSelect2Search(`item-purchase-order-general-select`, `{{ route('admin.select.item') }}/general`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-order-general-modal');

                initSelect2Search(`vendor-purchase-order-general-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-general-modal');

                $('#status-purchase-order-general-select').select2({
                    dropdownParent: $('#purchase-order-general-modal'),
                    allowClear: true,
                    placeholder: 'Select Data',
                    width: "100%",
                });
            };

            initializePurchaseOrderReport();

            const initializePurchaseOrderSummaryReport = () => {
                initSelect2Search(`branch-summary-purchase-order-general-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#summary-purchase-modal');

                $('#status-summary-purchase-order-general-select').select2({
                    dropdownParent: $('#summary-purchase-modal'),
                    allowClear: true,
                    placeholder: 'Select Data',
                    width: "100%",
                });
            };

            initializePurchaseOrderSummaryReport();

            const initializePurchaseOrderReportDetail = () => {
                initSelect2Search(`branch-detail-purchase-order-general-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-order-general-modal-detail');

                initSelect2Search(`item-detail-purchase-order-general-select`, `{{ route('admin.select.item') }}/general`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-order-general-modal-detail');

                initSelect2Search(`vendor-detail-purchase-order-general-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-general-modal-detail');

                $('#status-detail-purchase-order-general-select').select2({
                    allowClear: true,
                    placeholder: 'Select Data',
                    width: "100%",
                    dropdownParent: $('#purchase-order-general-modal-detail'),
                });
            };

            initializePurchaseOrderReportDetail()

            const initializePurchaseOrderReportDetailReceiving = () => {
                initSelect2Search(`branch-detail-purchase-order-general-select-receiving`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-order-general-modal-detail-receiving');

                initSelect2Search(`item-detail-purchase-order-general-select-receiving`, `{{ route('admin.select.item') }}/general`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-order-general-modal-detail-receiving');

                initSelect2Search(`vendor-detail-purchase-order-general-select-receiving`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-general-modal-detail-receiving');

                $('#status-detail-purchase-order-general-select-receiving').select2({
                    dropdownParent: $('#purchase-order-general-modal-detail-receiving'),
                    allowClear: true,
                    placeholder: 'Select Data',
                    width: "100%",
                });
            };

            initializePurchaseOrderReportDetailReceiving()

            const initializePurchaseOrderGeneralPerPeriod = () => {
                initSelect2Search(`branch-purchase-order-general-per-period`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-order-general-per-period-modal');

                initSelect2Search(`vendor-purchase-order-general-per-period`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-general-per-period-modal');

                initSelect2Search(`warehouse-purchase-order-general-per-period`, `{{ route('admin.select.ware-house.type') }}/general`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-general-per-period-modal');

                initSelect2Search(`item-purchase-order-general-per-period`, `{{ route('admin.select.item') }}/general`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-order-general-per-period-modal');

                $('#status-purchase-order-general-per-period').select2({
                    dropdownParent: $('#purchase-order-general-per-period-modal'),
                    allowClear: true,
                    placeholder: 'Select Data',
                    width: "100%",
                });
            }

            initializePurchaseOrderGeneralPerPeriod()

            const initializePurchaseOrderGeneralOutstanding = () => {
                initSelect2Search(`branch-purchase-order-general-outstanding-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-order-general-outstanding-modal');

                initSelect2Search(`vendor-purchase-order-general-outstanding-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-general-outstanding-modal');

                initSelect2Search(`warehouse-purchase-order-general-outstanding-select`, `{{ route('admin.select.ware-house.type') }}/general`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-general-outstanding-modal');

                initSelect2Search(`item-purchase-order-general-outstanding-select`, `{{ route('admin.select.item') }}/general`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-order-general-outstanding-modal');

                $('#status-purchase-order-general-outstanding-select').select2({
                    dropdownParent: $('#purchase-order-general-outstanding-modal'),
                    allowClear: true,
                    placeholder: 'Select Data',
                    width: "100%",
                });
            };

            initializePurchaseOrderGeneralOutstanding();

            const initializeStockComparisonWithPurchaseOrderGeneralOutstanding = () => {
                initSelect2Search(`branch-stock-comparison-with-purchase-order-general-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#stock-comparison-with-purchase-order-general-modal');

                initSelect2Search(`vendor-stock-comparison-with-purchase-order-general-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#stock-comparison-with-purchase-order-general-modal');

                initSelect2Search(`warehouse-stock-comparison-with-purchase-order-general-select`, `{{ route('admin.select.ware-house.type') }}/general`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#stock-comparison-with-purchase-order-general-modal');

                initSelect2Search(`item-stock-comparison-with-purchase-order-general-select`, `{{ route('admin.select.item') }}/general`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#stock-comparison-with-purchase-order-general-modal');

                $('#status-stock-comparison-with-purchase-order-general-select').select2({
                    dropdownParent: $('#stock-comparison-with-purchase-order-general-modal'),
                    allowClear: true,
                    placeholder: 'Select Data',
                    width: "100%",
                });
            };

            initializeStockComparisonWithPurchaseOrderGeneralOutstanding();

            const initializeInvoiceReturnSummaryPurchaseOrderGeneral = () => {
                initSelect2Search(`branch-invoice-return-purchase-order-general-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#return-purchase-order-general-modal');

                initSelect2Search(`vendor-invoice-return-purchase-order-general-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#return-purchase-order-general-modal');

                initSelect2Search(`warehouse-invoice-return-purchase-order-general-select`, `{{ route('admin.select.ware-house.type') }}/general`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#return-purchase-order-general-modal');

                initSelect2Search(`item-invoice-return-purchase-order-general-select`, `{{ route('admin.select.item') }}/general`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#return-purchase-order-general-modal');

                $('#status-invoice-return-purchase-order-general-select').select2({
                    dropdownParent: $('#return-purchase-order-general-modal'),
                    allowClear: true,
                    placeholder: 'Select Data',
                    width: "100%",
                });
            };

            initializeInvoiceReturnSummaryPurchaseOrderGeneral();

            const initializedebtDuePurchaseOrderGeneral = () => {
                initSelect2Search(`branch-debt-due-purchase-order-general-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#debt-due-purchase-order-general-modal');

                initSelect2Search(`vendor-debt-due-purchase-order-general-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#debt-due-purchase-order-general-modal');
            };

            initializedebtDuePurchaseOrderGeneral();


            const initializeDetailPurchaseOrderGeneral = () => {
                initSelect2Search(`branch-detail-closed-purchase-order-general-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#detail-closed-purchase-order-general-modal');

                initSelect2Search(`item-detail-closed-purchase-order-general-select`, `{{ route('admin.select.item') }}/general`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#detail-closed-purchase-order-general-modal');

                initSelect2Search(`vendor-detail-closed-purchase-order-general-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#detail-closed-purchase-order-general-modal');

                $('#status-detail-closed-purchase-order-general-select').select2({
                    dropdownParent: $('#detail-closed-purchase-order-general-modal'),
                    allowClear: true,
                    placeholder: 'Select Data',
                    width: "100%",
                });
            };

            initializeDetailPurchaseOrderGeneral()
        });
    </script>

    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarMenuOpen('#purchase-order-report');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
