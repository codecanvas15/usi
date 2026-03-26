@extends('layouts.admin.layout.index')

@php
    $main = 'sale-order-general';
    $title = 'laporan penjualan umum';
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
                                    <form action="{{ route('admin.sale-order-general.report.show', ['type' => 'sale-order-general']) }}" method="post" id="laporan-penjualan-general" target="_blank">
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
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-penjualan-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-penjualan-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-penjualan-select" useBr>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#laporan-penjualan-general').find(`input[name='format']`).val('preview');$('#laporan-penjualan-general').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#laporan-penjualan-general').find(`input[name='format']`).val('pdf');$('#laporan-penjualan-general').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#laporan-penjualan-general').find(`input[name='format']`).val('excel');$('#laporan-penjualan-general').submit()" />
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
                                    <form action="{{ route('admin.sale-order-general.report.show', ['type' => 'sale-order-general-faktur-pajak']) }}" method="post" id="laporan-penjualan-faktur-pajak" target="_blank">
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
                                                                    @foreach (sale_order_general_status() as $key => $item)
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#summary-laporan-penjualan-general-modal">{{ Str::headline('Laporan ringkasan penjualan general') }}</a>
                            <div class="modal fade" id="summary-laporan-penjualan-general-modal" aria-hidden="true" aria-labelledby="summary-laporan-penjualan-general-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-general.report.show', ['type' => 'summary-sale-order-general']) }}" method="post" id="summary-laporan-penjualan-general" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('Laporan ringkasan penjualan general') }}</h1>
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
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="summary-warehouse-penjualan-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#summary-laporan-penjualan-general').find(`input[name='format']`).val('preview');$('#summary-laporan-penjualan-general').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#summary-laporan-penjualan-general').find(`input[name='format']`).val('pdf');$('#summary-laporan-penjualan-general').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#summary-laporan-penjualan-general').find(`input[name='format']`).val('excel');$('#summary-laporan-penjualan-general').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#laporan-penjualan-general-detail-modal">{{ Str::headline('laporan penjualan detail') }}</a>
                            <div class="modal fade" id="laporan-penjualan-general-detail-modal" aria-hidden="true" aria-labelledby="laporan-penjualan-general-detail-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-general.report.show', ['type' => 'sale-order-general-detail']) }}" method="post" id="laporan-penjualan-general-detail" target="_blank">
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
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-penjualan-select-detail" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#laporan-penjualan-general-detail').find(`input[name='format']`).val('preview');$('#laporan-penjualan-general-detail').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#laporan-penjualan-general-detail').find(`input[name='format']`).val('pdf');$('#laporan-penjualan-general-detail').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#laporan-penjualan-general-detail').find(`input[name='format']`).val('excel');$('#laporan-penjualan-general-detail').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#delivery-order-general-modal">{{ Str::headline('delivery-order-general') }}</a>
                            <div class="modal fade" id="delivery-order-general-modal" aria-hidden="true" aria-labelledby="delivery-order-general-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-general.report.show', ['type' => 'delivery-order-general']) }}" method="post" id="delivery-order-general" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('delivery-order-general') }}
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
                                                    {{-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-delivery-order-geenral-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (delivery_order_general_status() as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#delivery-order-general').find(`input[name='format']`).val('preview');$('#delivery-order-general').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#delivery-order-general').find(`input[name='format']`).val('pdf');$('#delivery-order-general').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#delivery-order-general').find(`input[name='format']`).val('excel');$('#delivery-order-general').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sale-order-general-per-period-modal">{{ Str::headline('laporan per periode penjualan general #1') }}</a>
                            <div class="modal fade" id="sale-order-general-per-period-modal" aria-hidden="true" aria-labelledby="sale-order-general-per-period-modal" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-general.report.show', ['type' => 'per-periode-sale-order-general']) }}" method="post" id="per-periode-sale-order-general" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('per periode sale order general #1') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-sale-order-general-per-period" label="branch">

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
                                                            <x-select name="customer_id" label="customer" id="customer-sale-order-general-per-period" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-sale-order-general-per-period" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-sale-order-general-per-period" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-sale-order-general-per-period" useBr>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#per-periode-sale-order-general').find(`input[name='format']`).val('preview');$('#per-periode-sale-order-general').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#per-periode-sale-order-general').find(`input[name='format']`).val('pdf');$('#per-periode-sale-order-general').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#per-periode-sale-order-general').find(`input[name='format']`).val('excel');$('#per-periode-sale-order-general').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sale-order-general-per-period-2-modal">{{ Str::headline('laporan per periode penjualan general #2') }}</a>
                            <div class="modal fade" id="sale-order-general-per-period-2-modal" aria-hidden="true" aria-labelledby="sale-order-general-per-period-2-modal" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-general.report.show', ['type' => 'per-periode-sale-order-general-2']) }}" method="post" id="per-periode-sale-order-general-2" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('per periode sale order general #2') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-sale-order-general-per-period-2" label="branch">

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
                                                            <x-select name="customer_id" label="customer" id="customer-sale-order-general-per-period-2" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-sale-order-general-per-period-2" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-sale-order-general-per-period-2" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-sale-order-general-per-period-2" useBr>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#per-periode-sale-order-general-2').find(`input[name='format']`).val('preview');$('#per-periode-sale-order-general-2').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#per-periode-sale-order-general-2').find(`input[name='format']`).val('pdf');$('#per-periode-sale-order-general-2').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#per-periode-sale-order-general-2').find(`input[name='format']`).val('excel');$('#per-periode-sale-order-general-2').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#daily-sale-order-general-item-detail-customer-modal">{{ Str::headline('LAPORAN HARIAN RINCIAN ITEM PENJUALAN GENERAL PER CUSTOMER') }}</a>
                            <div class="modal fade" id="daily-sale-order-general-item-detail-customer-modal" aria-hidden="true" aria-labelledby="daily-sale-order-general-item-detail-customer-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-general.report.show', ['type' => 'daily-sale-order-general-item-detail-customer']) }}" method="post" id="daily-sale-order-general-item-detail-customer-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('LAPORAN HARIAN RINCIAN ITEM PENJUALAN GENERAL PER CUSTOMER') }}
                                                </h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-daily-sale-order-general-item-detail-customer-select" label="branch">

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
                                                            <x-select name="customer_id" label="customer" id="customer-daily-sale-order-general-item-detail-customer-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-daily-sale-order-general-item-detail-customer-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-daily-sale-order-general-item-detail-customer-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#daily-sale-order-general-item-detail-customer-form').find(`input[name='format']`).val('preview');$('#daily-sale-order-general-item-detail-customer-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#daily-sale-order-general-item-detail-customer-form').find(`input[name='format']`).val('pdf');$('#daily-sale-order-general-item-detail-customer-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#daily-sale-order-general-item-detail-customer-form').find(`input[name='format']`).val('excel');$('#daily-sale-order-general-item-detail-customer-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    {{-- Start of newly added laporan history sale orders --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#laporan-history-sale-order-modal">{{ Str::headline('LAPORAN HISTORY SALE ORDER') }}</a>
                            <div class="modal fade" id="laporan-history-sale-order-modal" aria-hidden="true" aria-labelledby="laporan-history-sale-order-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-general.report.show', ['type' => 'laporan-history-sale-order']) }}" method="post" id="laporan-history-sale-order-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('LAPORAN HISTORY SALE ORDER') }}
                                                </h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-laporan-history-sale-order-select" label="branch">

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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#laporan-history-sale-order-form').find(`input[name='format']`).val('preview');$('#laporan-history-sale-order-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#laporan-history-sale-order-form').find(`input[name='format']`).val('pdf');$('#laporan-history-sale-order-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#laporan-history-sale-order-form').find(`input[name='format']`).val('excel');$('#laporan-history-sale-order-form').submit()" />
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    {{-- End of newly added laporan history sale orders --}}
                    <tr>
                        <td>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#monthly-sale-order-general-item-detail-customer-modal">{{ Str::headline('LAPORAN BULANAN RINCIAN ITEM PENJUALAN GENERAL PER CUSTOMER') }}</a>
                            <div class="modal fade" id="monthly-sale-order-general-item-detail-customer-modal" aria-hidden="true" aria-labelledby="monthly-sale-order-general-item-detail-customer-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-general.report.show', ['type' => 'monthly-sale-order-general-item-detail-customer']) }}" method="post" id="monthly-sale-order-general-item-detail-customer-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('LAPORAN BULANAN RINCIAN ITEM PENJUALAN GENERAL PER CUSTOMER') }}
                                                </h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-monthly-sale-order-general-item-detail-customer-select" label="branch">

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
                                                            <x-select name="customer_id" label="customer" id="customer-monthly-sale-order-general-item-detail-customer-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="warehouse_id" label="gudang" id="warehouse-monthly-sale-order-general-item-detail-customer-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-monthly-sale-order-general-item-detail-customer-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-monthly-sale-order-general-item-detail-customer-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (get_delivery_order_status() as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#monthly-sale-order-general-item-detail-customer-form').find(`input[name='format']`).val('preview');$('#monthly-sale-order-general-item-detail-customer-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#monthly-sale-order-general-item-detail-customer-form').find(`input[name='format']`).val('pdf');$('#monthly-sale-order-general-item-detail-customer-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#monthly-sale-order-general-item-detail-customer-form').find(`input[name='format']`).val('excel');$('#monthly-sale-order-general-item-detail-customer-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#sale-order-general-outstanding-modal">{{ Str::headline('Laporan Penjualan General Outstanding') }}</a>
                            <div class="modal fade" id="sale-order-general-outstanding-modal" aria-hidden="true" aria-labelledby="sale-order-general-outstanding-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-general.report.show', ['type' => 'sale-order-general-outstanding']) }}" method="post" id="sale-order-general-outstanding-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('Laporan Penjualan General Outstanding') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-sale-order-general-outstanding-select" label="branch">

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
                                                            <x-select name="customer_id" label="customer" id="customer-sale-order-general-outstanding-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-sale-order-general-outstanding-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-sale-order-general-outstanding-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (sale_order_general_status() as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#sale-order-general-outstanding-form').find(`input[name='format']`).val('preview');$('#sale-order-general-outstanding-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#sale-order-general-outstanding-form').find(`input[name='format']`).val('pdf');$('#sale-order-general-outstanding-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#sale-order-general-outstanding-form').find(`input[name='format']`).val('excel');$('#sale-order-general-outstanding-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#stock-comparison-with-sale-order-general-modal">{{ Str::headline('Laporan Perbandingan Stock Dengan Penjualan General Outstanding') }}</a>
                            <div class="modal fade" id="stock-comparison-with-sale-order-general-modal" aria-hidden="true" aria-labelledby="stock-comparison-with-sale-order-general-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-general.report.show', ['type' => 'stock-comparison-with-sale-order-general']) }}" method="post" id="stock-comparison-with-sale-order-general-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('Laporan Perbandingan Stock Dengan Penjualan General Outstanding') }}
                                                </h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-stock-comparison-with-sale-order-general-select" label="branch">

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
                                                            <x-select name="customer_id" label="customer" id="customer-stock-comparison-with-sale-order-general-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-stock-comparison-with-sale-order-general-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-stock-comparison-with-sale-order-general-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (sale_order_general_status() as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#stock-comparison-with-sale-order-general-form').find(`input[name='format']`).val('preview');$('#stock-comparison-with-sale-order-general-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#stock-comparison-with-sale-order-general-form').find(`input[name='format']`).val('pdf');$('#stock-comparison-with-sale-order-general-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#stock-comparison-with-sale-order-general-form').find(`input[name='format']`).val('excel');$('#sale-order-general-outstanding-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#invoice-return-sale-order-general-modal">{{ Str::headline('Laporan Ringkasan Retur Penjualan General') }}</a>
                            <div class="modal fade" id="invoice-return-sale-order-general-modal" aria-hidden="true" aria-labelledby="invoice-return-sale-order-general-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-general.report.show', ['type' => 'invoice-return-sale-order-general']) }}" method="post" id="invoice-return-sale-order-general-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('Laporan Ringkasan Retur Penjualan General') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-invoice-return-sale-order-general-select" label="branch">

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
                                                            <x-select name="customer_id" label="customer" id="customer-invoice-return-sale-order-general-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" label="item" id="item-invoice-return-sale-order-general-select" useBr>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" label="status item" id="status-invoice-return-sale-order-general-select" useBr>
                                                                <option value=""></option>
                                                                @foreach (fund_submission_status() as $key => $item)
                                                                    <option value="{{ $key }}">
                                                                        {{ $item['label'] }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div> --}}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#invoice-return-sale-order-general-form').find(`input[name='format']`).val('preview');$('#invoice-return-sale-order-general-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#invoice-return-sale-order-general-form').find(`input[name='format']`).val('pdf');$('#invoice-return-sale-order-general-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#invoice-return-sale-order-general-form').find(`input[name='format']`).val('excel');$('#invoice-return-sale-order-general-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#debt-due-sale-order-general-modal">{{ Str::headline('Laporan Piutang jatuh tempo') }}</a>
                            <div class="modal fade" id="debt-due-sale-order-general-modal" aria-hidden="true" aria-labelledby="debt-due-sale-order-general-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.sale-order-general.report.show', ['type' => 'debt-due-sale-order-general']) }}" method="post" id="debt-due-sale-order-general-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">
                                                    {{ Str::headline('Laporan Piutang jatuh tempo') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="row">
                                                    @if (get_current_branch()->is_primary)
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-debt-due-sale-order-general-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="customer_id" label="customer" id="customer-debt-due-sale-order-general-select" useBr>

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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#debt-due-sale-order-general-form').find(`input[name='format']`).val('preview');$('#debt-due-sale-order-general-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#debt-due-sale-order-general-form').find(`input[name='format']`).val('pdf');$('#debt-due-sale-order-general-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#debt-due-sale-order-general-form').find(`input[name='format']`).val('excel');$('#debt-due-sale-order-general-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#receivaleCardModal">{{ Str::headline('kartu piutang') }}</a>
                            <div class="modal fade" id="receivaleCardModal" aria-hidden="true" aria-labelledby="receivaleCardModal" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.finance-report.report', ['type' => 'debt-card-trading']) }}" method="get" id="receivaleCard-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('receivaleCard') }}</h1>
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
                                                            <x-select name="customer_id" label="customer" id="customer-receivaleCard" useBr>

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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#receivaleCard-form').find(`input[name='format']`).val('preview');$('#receivaleCard-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#receivaleCard-form').find(`input[name='format']`).val('pdf');$('#receivaleCard-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#receivaleCard-form').find(`input[name='format']`).val('excel');$('#receivaleCard-form').submit()" />
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
                                                    <div class="col-md-6">
                                                        <x-input-checkbox label="piutang lancar" name="active" id="active5" value="1" />
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
                                                    <div class="col-md-6">
                                                        <x-input-checkbox label="piutang lancar" name="active" id="active6" value="1" />
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

                initSelect2Search(`warehouse-penjualan-select`,
                    `{{ route('admin.select.ware-house.type') }}/general`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#laporan-penjualan-modal');

                initSelect2Search(`item-penjualan-select`, `{{ route('admin.select.item') }}/general`, {
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
                }, 0, {}, '#summary-laporan-penjualan-general-modal');

                initSelect2Search(`summary-customer-penjualan-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#summary-laporan-penjualan-general-modal');

                initSelect2Search(`summary-warehouse-penjualan-select`,
                    `{{ route('admin.select.ware-house.type') }}/general`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#summary-laporan-penjualan-general-modal');

                initSelect2Search(`summary-item-penjualan-select`,
                    `{{ route('admin.select.item') }}/general`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#summary-laporan-penjualan-general-modal');

                $('#summary-status-penjualan-select').select2({
                    dropdownParent: $('#summary-laporan-penjualan-general-modal'),
                    allowClear: true,
                    width: "100%",
                });
                $('#summary-status-item-penjualan-select').select2({
                    dropdownParent: $('#summary-laporan-penjualan-general-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeLaporanPenjualanSummary();

            const initializeLaporanPenjualanDetail = () => {
                initSelect2Search(`branch-penjualan-select-detail`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#laporan-penjualan-general-detail-modal');

                initSelect2Search(`customer-penjualan-select-detail`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#laporan-penjualan-general-detail-modal');

                initSelect2Search(`warehouse-penjualan-select-detail`,
                    `{{ route('admin.select.ware-house.type') }}/general`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#laporan-penjualan-general-detail-modal');

                initSelect2Search(`item-penjualan-select-detail`,
                    `{{ route('admin.select.item') }}/general`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#laporan-penjualan-general-detail-modal');

                $('#status-penjualan-select-detail').select2({
                    dropdownParent: $('#laporan-penjualan-general-detail-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeLaporanPenjualanDetail();

            const initializeLaporanDeliveryOrderGeneral = () => {
                initSelect2Search(`branch-delivery-order-geenral-select`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#delivery-order-general-modal');

                initSelect2Search(`customer-delivery-order-geenral-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#delivery-order-general-modal');

                initSelect2Search(`warehouse-delivery-order-geenral-select`,
                    `{{ route('admin.select.ware-house.type') }}/general`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#delivery-order-general-modal');

                initSelect2Search(`item-delivery-order-geenral-select`,
                    `{{ route('admin.select.item') }}/general`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#delivery-order-general-modal');

                $('#status-delivery-order-geenral-select').select2({
                    dropdownParent: $('#delivery-order-general-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeLaporanDeliveryOrderGeneral();

            const initializeLaporanSaleOrderGeneralPerPeriod = () => {
                initSelect2Search(`branch-sale-order-general-per-period`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#sale-order-general-per-period-modal');

                initSelect2Search(`customer-sale-order-general-per-period`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#sale-order-general-per-period-modal');

                initSelect2Search(`warehouse-sale-order-general-per-period`,
                    `{{ route('admin.select.ware-house.type') }}/general`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#sale-order-general-per-period-modal');

                initSelect2Search(`item-sale-order-general-per-period`,
                    `{{ route('admin.select.item') }}/general`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#sale-order-general-per-period-modal');

                $('#status-sale-order-general-per-period').select2({
                    dropdownParent: $('#sale-order-general-per-period-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };
            initializeLaporanSaleOrderGeneralPerPeriod();

            const initializeLaporanSaleOrderGeneralPerPeriod2 = () => {
                initSelect2Search(`branch-sale-order-general-per-period-2`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#sale-order-general-per-period-2-modal');

                initSelect2Search(`customer-sale-order-general-per-period-2`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#sale-order-general-per-period-2-modal');

                initSelect2Search(`warehouse-sale-order-general-per-period-2`,
                    `{{ route('admin.select.ware-house.type') }}/general`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#sale-order-general-per-period-2-modal');

                initSelect2Search(`item-sale-order-general-per-period-2`,
                    `{{ route('admin.select.item') }}/general`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#sale-order-general-per-period-2-modal');

                $('#status-sale-order-general-per-period-2').select2({
                    dropdownParent: $('#sale-order-general-per-period-2-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };
            initializeLaporanSaleOrderGeneralPerPeriod2();

            const initializeDialySaleOrderGeneralDetailItemCustomerReport = () => {
                initSelect2Search(`branch-daily-sale-order-general-item-detail-customer-select`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#daily-sale-order-general-item-detail-customer-modal');

                initSelect2Search(`customer-daily-sale-order-general-item-detail-customer-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#daily-sale-order-general-item-detail-customer-modal');

                initSelect2Search(`warehouse-daily-sale-order-general-item-detail-customer-select`,
                    `{{ route('admin.select.ware-house.type') }}/general`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#daily-sale-order-general-item-detail-customer-modal');

                initSelect2Search(`item-daily-sale-order-general-item-detail-customer-select`,
                    `{{ route('admin.select.item') }}/general`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#daily-sale-order-general-item-detail-customer-modal');

                $('#status-daily-sale-order-general-item-detail-customer-select').select2({
                    dropdownParent: $('#daily-sale-order-general-item-detail-customer-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeDialySaleOrderGeneralDetailItemCustomerReport();

            const initializeLaporanHistorySaleOrderSelect = () => {
                initSelect2Search(`branch-laporan-history-sale-order-select`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#laporan-history-sale-order-modal');
            };

            initializeLaporanHistorySaleOrderSelect();

            const initializeMonthlySaleOrderGeneralDetailItemCustomerReport = () => {
                initSelect2Search(`branch-monthly-sale-order-general-item-detail-customer-select`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#monthly-sale-order-general-item-detail-customer-modal');

                initSelect2Search(`customer-monthly-sale-order-general-item-detail-customer-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#monthly-sale-order-general-item-detail-customer-modal');

                initSelect2Search(`warehouse-monthly-sale-order-general-item-detail-customer-select`,
                    `{{ route('admin.select.ware-house.type') }}/general`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#monthly-sale-order-general-item-detail-customer-modal');

                initSelect2Search(`item-monthly-sale-order-general-item-detail-customer-select`,
                    `{{ route('admin.select.item') }}/general`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#monthly-sale-order-general-item-detail-customer-modal');

                $('#status-monthly-sale-order-general-item-detail-customer-select').select2({
                    dropdownParent: $('#monthly-sale-order-general-item-detail-customer-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeMonthlySaleOrderGeneralDetailItemCustomerReport();

            const initializeSaleOrderGeneralOutstanding = () => {
                initSelect2Search(`branch-sale-order-general-outstanding-select`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#sale-order-general-outstanding-modal');

                initSelect2Search(`customer-sale-order-general-outstanding-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#sale-order-general-outstanding-modal');

                initSelect2Search(`warehouse-sale-order-general-outstanding-select`,
                    `{{ route('admin.select.ware-house.type') }}/general`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#sale-order-general-outstanding-modal');

                initSelect2Search(`item-sale-order-general-outstanding-select`,
                    `{{ route('admin.select.item') }}/general`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#sale-order-general-outstanding-modal');

                $('#status-sale-order-general-outstanding-select').select2({
                    dropdownParent: $('#sale-order-general-outstanding-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeSaleOrderGeneralOutstanding();

            const initializeStockComparisonWithSaleOrderGeneralOutstanding = () => {
                initSelect2Search(`branch-stock-comparison-with-sale-order-general-select`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#stock-comparison-with-sale-order-general-modal');

                initSelect2Search(`customer-stock-comparison-with-sale-order-general-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#stock-comparison-with-sale-order-general-modal');

                initSelect2Search(`warehouse-stock-comparison-with-sale-order-general-select`,
                    `{{ route('admin.select.ware-house.type') }}/general`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#stock-comparison-with-sale-order-general-modal');

                initSelect2Search(`item-stock-comparison-with-sale-order-general-select`,
                    `{{ route('admin.select.item') }}/general`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#stock-comparison-with-sale-order-general-modal');

                $('#status-stock-comparison-with-sale-order-general-select').select2({
                    dropdownParent: $('#stock-comparison-with-sale-order-general-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeStockComparisonWithSaleOrderGeneralOutstanding();

            const initializeInvoiceReturnSummarySaleOrderGeneral = () => {
                initSelect2Search(`branch-invoice-return-sale-order-general-select`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#invoice-return-sale-order-general-modal');

                initSelect2Search(`customer-invoice-return-sale-order-general-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#invoice-return-sale-order-general-modal');

                initSelect2Search(`warehouse-invoice-return-sale-order-general-select`,
                    `{{ route('admin.select.ware-house.type') }}/general`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#invoice-return-sale-order-general-modal');

                initSelect2Search(`item-invoice-return-sale-order-general-select`,
                    `{{ route('admin.select.item') }}/general`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#invoice-return-sale-order-general-modal');

                $('#status-invoice-return-sale-order-general-select').select2({
                    dropdownParent: $('#invoice-return-sale-order-general-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeInvoiceReturnSummarySaleOrderGeneral();

            const initializeInvoiceReturnSummarySaleOrderDetailGeneral = () => {
                initSelect2Search(`branch-invoice-return-sale-order-general-detail-select`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#invoice-return-sale-order-general-detail-modal');

                initSelect2Search(`customer-invoice-return-sale-order-general-detail-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#invoice-return-sale-order-general-detail-modal');

                initSelect2Search(`warehouse-invoice-return-sale-order-general-detail-select`,
                    `{{ route('admin.select.ware-house.type') }}/general`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#invoice-return-sale-order-general-detail-modal');

                initSelect2Search(`item-invoice-return-sale-order-general-detail-select`,
                    `{{ route('admin.select.item') }}/general`, {
                        id: "id",
                        text: "nama,kode"
                    }, 0, {}, '#invoice-return-sale-order-general-detail-modal');

                $('#status-invoice-return-sale-order-general-detail-select').select2({
                    dropdownParent: $('#invoice-return-sale-order-general-detail-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeInvoiceReturnSummarySaleOrderDetailGeneral();

            const initializedebtDueSaleOrderGeneral = () => {
                initSelect2Search(`branch-debt-due-sale-order-general-select`,
                    `{{ route('admin.select.branch') }}`, {
                        id: "id",
                        text: "name"
                    }, 0, {}, '#debt-due-sale-order-general-modal');

                initSelect2Search(`customer-debt-due-sale-order-general-select`,
                    `{{ route('admin.select.customer') }}`, {
                        id: "id",
                        text: "nama"
                    }, 0, {}, '#debt-due-sale-order-general-modal');
            };

            initializedebtDueSaleOrderGeneral();

            const initializeDebtCardReportGeneral = () => {
                initSelect2Search(`customer-receivaleCard`, `{{ route('admin.select.customer') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#receivaleCardModal');
            };

            initializeDebtCardReportGeneral();

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

            initSelect2Search(`pelunasan_piutang_detail_invoice_parent_id`,
                `{{ route('admin.select.invoice') }}`, {
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
        });
    </script>
    <script>
        sidebarMenuOpen('#trading');
        sidebarMenuOpen('#sale-order-report');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
