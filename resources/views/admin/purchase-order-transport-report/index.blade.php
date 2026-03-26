@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-order-transport';
    $title = 'laporan pembelian transport';
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-order-transport-modal">{{ Str::headline('laporan pembelian') }}</a>
                            <div class="modal fade" id="purchase-order-transport-modal" aria-hidden="true" aria-labelledby="purchase-order-transport-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-transport.report.show', ['type' => 'purchase-order-transport']) }}" method="post" id="report-purchase-order-transport-form" target="_blank">
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
                                                                <x-select name="branch_id" useBr id="branch-purchase-order-transport-select" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-purchase-order-transport-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" useBr id="customer-purchase-order-transport-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-purchase-order-transport-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr label="status" id="status-purchase-order-transport-select">
                                                                <option value="" selected></option>
                                                                @foreach (purchase_transport_status() as $key => $item)
                                                                    <option value="{{ $key }}">{{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" id="report-purchase-order-transport-format" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#report-purchase-order-transport-format').val('preview');$('#report-purchase-order-transport-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#report-purchase-order-transport-format').val('pdf');$('#report-purchase-order-transport-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#report-purchase-order-transport-format').val('excel');$('#report-purchase-order-transport-form').submit()" />
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
                                    <form action="{{ route('admin.purchase-order-transport.report.show', ['type' => 'summary-purchase-order-transport']) }}" method="post" id="summary-report-purchae-order-transport-form" target="_blank">
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
                                                                <x-select name="branch_id" useBr id="branch-summary-purchase-order-transport-select" label="branch">

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
                                                            <x-select name="status" useBr label="status" id="status-summary-purchase-order-transport-select">
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#summary-report-purchae-order-transport-form').find(`input[name='format']`).val('preview');$('#summary-report-purchae-order-transport-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#summary-report-purchae-order-transport-form').find(`input[name='format']`).val('pdf');$('#summary-report-purchae-order-transport-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#summary-report-purchae-order-transport-form').find(`input[name='format']`).val('excel');$('#summary-report-purchae-order-transport-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-order-transport-modal-detail">{{ Str::headline('laporan purchase order transport detail') }}</a>
                            <div class="modal fade" id="purchase-order-transport-modal-detail" aria-hidden="true" aria-labelledby="purchase-order-transport-modal-detail-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-transport.report.show', ['type' => 'detail-purchase-order-transport']) }}" method="post" id="detail-report-purchae-order-transport-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('laporan purchase order transport') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-detail-purchase-order-transport-select" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-detail-purchase-order-transport-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" useBr id="customer-detail-purchase-order-transport-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-detail-purchase-order-transport-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr label="status" id="status-detail-purchase-order-transport-select">
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#detail-report-purchae-order-transport-form').find(`input[name='format']`).val('preview');$('#detail-report-purchae-order-transport-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#detail-report-purchae-order-transport-form').find(`input[name='format']`).val('pdf');$('#detail-report-purchae-order-transport-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#detail-report-purchae-order-transport-form').find(`input[name='format']`).val('excel');$('#detail-report-purchae-order-transport-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-order-transport-modal-detail-receiving">{{ Str::headline('Laporan penerimaan Pembelian transport') }}</a>
                            <div class="modal fade" id="purchase-order-transport-modal-detail-receiving" aria-hidden="true" aria-labelledby="purchase-order-transport-modal-detail-receiving-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-transport.report.show', ['type' => 'purchase-order-transport-with-receiving']) }}" method="post" id="detail-report-purchae-order-transport-form-with-receiving" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan penerimaan Pembelian transport') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-detail-purchase-order-transport-select-receiving" label="branch">

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
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-detail-purchase-order-transport-select-receiving"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" useBr id="customer-detail-purchase-order-transport-select-receiving"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-detail-purchase-order-transport-select-receiving"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr label="status" id="status-detail-purchase-order-transport-select-receiving">
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#detail-report-purchae-order-transport-form-with-receiving').find(`input[name='format']`).val('preview');$('#detail-report-purchae-order-transport-form-with-receiving').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#detail-report-purchae-order-transport-form-with-receiving').find(`input[name='format']`).val('pdf');$('#detail-report-purchae-order-transport-form-with-receiving').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#detail-report-purchae-order-transport-form-with-receiving').find(`input[name='format']`).val('excel');$('#detail-report-purchae-order-transport-form-with-receiving').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#debt-due-purchase-order-transport-modal">{{ Str::headline('Laporan Hutang Jatuh Tempo Pembelian Transport') }}</a>
                            <div class="modal fade" id="debt-due-purchase-order-transport-modal" aria-hidden="true" aria-labelledby="debt-due-purchase-order-transport-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-order-transport.report.show', ['type' => 'debt-due-purchase-order-transport']) }}" method="post" id="debt-due-purchase-order-transport-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('Laporan Hutang Jatuh Tempo Pembelian Transport') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-debt-due-purchase-order-transport" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" useBr id="customer-debt-due-purchase-order-transport"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="vendor_id" label="vendor" useBr id="vendor-debt-due-purchase-order-transport"></x-select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#debt-due-purchase-order-transport-form').find(`input[name='format']`).val('preview');$('#debt-due-purchase-order-transport-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#debt-due-purchase-order-transport-form').find(`input[name='format']`).val('pdf');$('#debt-due-purchase-order-transport-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#debt-due-purchase-order-transport-form').find(`input[name='format']`).val('excel');$('#debt-due-purchase-order-transport-form').submit()" />
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
                initSelect2Search(`branch-purchase-order-transport-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-order-transport-modal');

                initSelect2Search(`item-purchase-order-transport-select`, `{{ route('admin.select.item') }}/transport`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-order-transport-modal');

                initSelect2Search(`vendor-purchase-order-transport-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-transport-modal');

                initSelect2Search(`customer-purchase-order-transport-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-transport-modal');

                $('#status-purchase-order-transport-select').select2({
                    dropdownParent: $('#purchase-order-transport-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializePurchaseOrderReport();

            const initializePurchaseOrderSummaryReport = () => {
                initSelect2Search(`branch-summary-purchase-order-transport-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#summary-purchase-modal');

                $('#status-summary-purchase-order-transport-select').select2({
                    dropdownParent: $('#summary-purchase-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializePurchaseOrderSummaryReport();

            const initializePurchaseOrderReportDetail = () => {
                initSelect2Search(`branch-detail-purchase-order-transport-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-order-transport-modal-detail-receiving');

                initSelect2Search(`item-detail-purchase-order-transport-select`, `{{ route('admin.select.item') }}/transport`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-order-transport-modal-detail');

                initSelect2Search(`vendor-detail-purchase-order-transport-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-transport-modal-detail');

                initSelect2Search(`customer-detail-purchase-order-transport-select`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-transport-modal-detail');

                $('#status-detail-purchase-order-transport-select').select2({
                    dropdownParent: $('#purchase-order-transport-modal-detail'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializePurchaseOrderReportDetail()

            const initializePurchaseOrderReportDetailReceiving = () => {
                initSelect2Search(`branch-detail-purchase-order-transport-select-receiving`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-order-transport-modal-detail-receiving');

                initSelect2Search(`item-detail-purchase-order-transport-select-receiving`, `{{ route('admin.select.item') }}/transport`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-order-transport-modal-detail-receiving');

                initSelect2Search(`customer-detail-purchase-order-transport-select-receiving`, `{{ route('admin.select.customer') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-transport-modal-detail-receiving');

                initSelect2Search(`vendor-detail-purchase-order-transport-select-receiving`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-transport-modal-detail-receiving');

                initSelect2Search(`customer-detail-purchase-order-transport-select-receiving`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#purchase-order-transport-modal-detail-receiving');

                $('#customer-detail-purchase-order-transport-select-receiving').change(function(e) {
                    e.preventDefault();

                    if (this.value) {
                        $('#sh-number-id-detail-purchase-order-transport-select-receiving').html('');
                        $('#sh-number-id-detail-purchase-order-transport-select-receiving').attr('disabled', false);

                        initSelect2Search(`sh-number-id-detail-purchase-order-transport-select-receiving`, `{{ route('admin.select.sh-number.customer') }}/${this.value}`, {
                            id: "id",
                            text: "kode,supply_point,drop_point"
                        }, 0, {}, '#purchase-order-transport-modal-detail-receiving');
                    } else {
                        $('#sh-number-id-detail-purchase-order-transport-select-receiving').html('');
                        $('#sh-number-id-detail-purchase-order-transport-select-receiving').attr('disabled', true);
                    }
                });

                $('#status-detail-purchase-order-transport-select-receiving').select2({
                    dropdownParent: $('#purchase-order-transport-modal-detail-receiving'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializePurchaseOrderReportDetailReceiving()

            const initializeDebtDuePurchaseOrderTransport = () => {
                initSelect2Search(`branch-debt-due-purchase-order-transport`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#debt-due-purchase-order-transport-modal');

                initSelect2Search(`customer-debt-due-purchase-order-transport`, `{{ route('admin.select.customer') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#debt-due-purchase-order-transport-modal');

                initSelect2Search(`vendor-debt-due-purchase-order-transport`, `{{ route('admin.select.vendor') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#debt-due-purchase-order-transport-modal');
            };

            initializeDebtDuePurchaseOrderTransport()
        });
    </script>
    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarMenuOpen('#purchase-order-report');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
