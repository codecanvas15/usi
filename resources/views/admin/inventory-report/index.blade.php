@extends('layouts.admin.layout.index')

@php
    $main = 'inventory-report';
    $title = 'laporan gudang';
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#stock-card-modal">{{ Str::headline('laporan stock-card') }}</a>
                            <div class="modal fade" id="stock-card-modal" aria-hidden="true" aria-labelledby="stock-card-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.inventory-report.show', ['type' => 'stock-card-report']) }}" method="post" id="report-stock-card-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('laporan stock-card') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-stock-card-select" label="branch">

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
                                                            <x-select name="item_id" useBr label="item" id="item-stock-card-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="ware_house_id" useBr label="gudang" id="warehouse-stock-card-select"></x-select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#report-stock-card-form').find(`input[name='format']`).val('preview');$('#report-stock-card-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#report-stock-card-form').find(`input[name='format']`).val('pdf');$('#report-stock-card-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#report-stock-card-form').find(`input[name='format']`).val('excel');$('#report-stock-card-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#stock-mutation-modal">{{ Str::headline('laporan stock-mutation') }}</a>
                            <div class="modal fade" id="stock-mutation-modal" aria-hidden="true" aria-labelledby="stock-mutation-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.inventory-report.show', ['type' => 'stock-mutation-report']) }}" method="post" id="report-stock-mutation-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('laporan stock-mutation') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-stock-mutation-select" label="branch">

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
                                                            <x-select name="item_id" useBr label="item" id="item-stock-mutation-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="ware_house_id" useBr label="gudang" id="warehouse-stock-mutation-select"></x-select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#report-stock-mutation-form').find(`input[name='format']`).val('preview');$('#report-stock-mutation-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#report-stock-mutation-form').find(`input[name='format']`).val('pdf');$('#report-stock-mutation-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#report-stock-mutation-form').find(`input[name='format']`).val('excel');$('#report-stock-mutation-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#end-of-monthly-stock-modal">{{ Str::headline('laporan end-of-monthly-stock') }}</a>
                            <div class="modal fade" id="end-of-monthly-stock-modal" aria-hidden="true" aria-labelledby="end-of-monthly-stock-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.inventory-report.show', ['type' => 'end-of-monthly-stock-report']) }}" method="post" id="report-end-of-monthly-stock-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('laporan end-of-monthly-stock') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-end-of-monthly-stock-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-input type="text" name="month" label="peride" class="month-year-picker-input" id="" value="" required />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_category_id" useBr label="item-category" id="itemCategory-end-of-monthly-stock-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-end-of-monthly-stock-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="ware_house_id" useBr label="gudang" id="warehouse-end-of-monthly-stock-select"></x-select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#report-end-of-monthly-stock-form').find(`input[name='format']`).val('preview');$('#report-end-of-monthly-stock-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#report-end-of-monthly-stock-form').find(`input[name='format']`).val('pdf');$('#report-end-of-monthly-stock-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#report-end-of-monthly-stock-form').find(`input[name='format']`).val('excel');$('#report-end-of-monthly-stock-form').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#stock-value-report-modal">{{ Str::headline('laporan stock-value') }}</a>
                            <div class="modal fade" id="stock-value-report-modal" aria-hidden="true" aria-labelledby="stock-value-report-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.inventory-report.show', ['type' => 'stock-value']) }}" method="post" id="stock-value-report-modal-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('laporan stock value') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="row">
                                                    @if (get_current_branch()->is_primary)
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-stock-value-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-stock-value-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <x-select name="ware_house_id" useBr label="gudang" id="warehouse-stock-value-select"></x-select>
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#stock-value-report-modal-form').find(`input[name='format']`).val('preview');$('#stock-value-report-modal-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#stock-value-report-modal-form').find(`input[name='format']`).val('pdf');$('#stock-value-report-modal-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#stock-value-report-modal-form').find(`input[name='format']`).val('excel');$('#stock-value-report-modal-form').submit()" />
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
            const init = () => {
                initializeStockCardForm();
                initializeStockMutationnForm();
                initializeEndOfMonthReport();
                initializeStockValueForm();
            };

            const initializeStockCardForm = () => {
                initSelect2Search(`branch-stock-card-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#stock-card-modal');

                initSelect2Search(`item-stock-card-select`, `{{ route('admin.select.item') }}`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#stock-card-modal');

                initSelect2Search(`warehouse-stock-card-select`, `{{ route('admin.select.ware-house') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#stock-card-modal');
            };

            const initializeStockMutationnForm = () => {
                initSelect2Search(`branch-stock-mutation-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#stock-mutation-modal');

                initSelect2Search(`item-stock-mutation-select`, `{{ route('admin.select.item') }}`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#stock-mutation-modal');

                initSelect2Search(`warehouse-stock-mutation-select`, `{{ route('admin.select.ware-house') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#stock-mutation-modal');
            };

            const initializeEndOfMonthReport = () => {
                initSelect2Search(`branch-end-of-monthly-stock-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#end-of-monthly-stock-modal');

                initSelect2Search(`itemCategory-end-of-monthly-stock-select`, `{{ route('admin.select.item-category') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#end-of-monthly-stock-modal');

                initSelect2Search(`item-end-of-monthly-stock-select`, `{{ route('admin.select.item') }}`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#end-of-monthly-stock-modal');

                initSelect2Search(`warehouse-end-of-monthly-stock-select`, `{{ route('admin.select.ware-house') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#end-of-monthly-stock-modal');
            };

            const initializeStockValueForm = () => {
                initSelect2Search(`branch-stock-value-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#stock-value-report-modal');

                initSelect2Search(`item-stock-value-select`, `{{ route('admin.select.item') }}`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#stock-value-report-modal');

                initSelect2Search(`warehouse-stock-value-select`, `{{ route('admin.select.ware-house') }}`, {
                    id: "id",
                    text: "nama"
                }, 0, {}, '#stock-value-report-modal');
            };

            init();
        });
    </script>

    <script>
        sidebarMenuOpen('#stock-sidebar');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
