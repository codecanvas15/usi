@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-request-report';
    $title = 'laporan purchase request';
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
                        <a href="{{ route("admin.$main.index") }}">Laporan</a>
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#purchase-request-by-project-modal">{{ Str::headline('laporan purchase request by project') }}</a>
                            <div class="modal fade" id="purchase-request-by-project-modal" aria-hidden="true" aria-labelledby="purchase-request-by-project-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.purchase-request-report.show', ['type' => 'purchase-request-by-project']) }}" method="post" id="report-purchase-request-by-project-form" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('laporan purchase request by project') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-purchase-request-by-project-select" label="branch">

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
                                                            <x-select name="project_id" label="project" useBr id="project-purchase-request-by-project-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="item_id" useBr label="item" id="item-purchase-request-by-project-select"></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="purchase_order_id[]" useBr label="purchase_order" id="purchase_order" multiple></x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="type" useBr label="type" id="type-purchase-request-by-project-select">
                                                                <option value="" selected></option>
                                                                <option value="general">General</option>
                                                                <option value="jasa">Service</option>
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr label="status" id="status-purchase-request-by-project-select">
                                                                <option value="" selected></option>
                                                                @foreach (purchase_request_status() as $key => $item)
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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#report-purchase-request-by-project-form').find(`input[name='format']`).val('preview');$('#report-purchase-request-by-project-form').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#report-purchase-request-by-project-form').find(`input[name='format']`).val('pdf');$('#report-purchase-request-by-project-form').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#report-purchase-request-by-project-form').find(`input[name='format']`).val('excel');$('#report-purchase-request-by-project-form').submit()" />
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
            const initializePurchaseRequestByProjectReport = () => {
                initSelect2Search(`branch-purchase-request-by-project-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#purchase-request-by-project-modal');

                initSelect2Search(`item-purchase-request-by-project-select`, `{{ route('admin.select.item') }}/general`, {
                    id: "id",
                    text: "nama,kode"
                }, 0, {}, '#purchase-request-by-project-modal');

                initSelect2Search(`purchase_order`, `{{ route('admin.select.purchase-request-order') }}`, {
                    id: "code",
                    text: "code"
                }, 0, {}, '#purchase-request-by-project-modal');

                initSelect2Search(`project-purchase-request-by-project-select`, `{{ route('admin.select.project') }}`, {
                    id: "id",
                    text: "code,name"
                }, 0, {}, '#purchase-request-by-project-modal');

                $('#type-purchase-request-by-project-select').select2({
                    dropdownParent: $('#purchase-request-by-project-modal'),
                    allowClear: true,
                    width: "100%",
                });
                $('#status-purchase-request-by-project-select').select2({
                    dropdownParent: $('#purchase-request-by-project-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            const init = () => {
                initializePurchaseRequestByProjectReport();
            };

            init();
        });
    </script>

    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarMenuOpen('#purchase-order-report');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
