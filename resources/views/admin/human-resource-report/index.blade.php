@extends('layouts.admin.layout.index')

@php
    $main = 'human-resource-report';
    $title = 'laporan HRD';
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#human-resource-report-paid-leaves-modal">{{ Str::headline('laporan Cuti') }}</a>
                            <div class="modal fade" id="human-resource-report-paid-leaves-modal" aria-hidden="true" aria-labelledby="human-resource-report-paid-leaves-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.human-resource-report.show', ['type' => 'paid-leaves']) }}" method="post" id="human-resource-report-paid-leaves" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('laporan cuti') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-human-resource-report-paid-leaves-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="reset_leave_id" useBr id="reset-leave-id" label="periode cuti" required>

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#human-resource-report-paid-leaves').find(`input[name='format']`).val('preview');$('#human-resource-report-paid-leaves').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#human-resource-report-paid-leaves').find(`input[name='format']`).val('pdf');$('#human-resource-report-paid-leaves').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#human-resource-report-paid-leaves').find(`input[name='format']`).val('excel');$('#human-resource-report-paid-leaves').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#human-resource-report-employee-permission-modal">{{ Str::headline('laporan izin pegawai') }}</a>
                            <div class="modal fade" id="human-resource-report-employee-permission-modal" aria-hidden="true" aria-labelledby="human-resource-report-employee-permission-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.human-resource-report.show', ['type' => 'employee-permission']) }}" method="post" id="human-resource-report-employee-permission" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('laporan izin pegawai') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-human-resource-report-employee-permission-select" label="branch">

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
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#human-resource-report-employee-permission').find(`input[name='format']`).val('preview');$('#human-resource-report-employee-permission').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#human-resource-report-employee-permission').find(`input[name='format']`).val('pdf');$('#human-resource-report-employee-permission').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#human-resource-report-employee-permission').find(`input[name='format']`).val('excel');$('#human-resource-report-employee-permission').submit()" />
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
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#human-resource-report-period-of-employment-modal">{{ Str::headline('laporan masa kerja karyawan') }}</a>
                            <div class="modal fade" id="human-resource-report-period-of-employment-modal" aria-hidden="true" aria-labelledby="human-resource-report-period-of-employment-modal-label" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <form action="{{ route('admin.human-resource-report.show', ['type' => 'period-of-employment']) }}" method="post" id="human-resource-report-period-of-employment" target="_blank">
                                        @csrf
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5">{{ Str::headline('laporan masa kerja karyawan') }}</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                @if (get_current_branch()->is_primary)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <x-select name="branch_id" useBr id="branch-human-resource-report-period-of-employment-select" label="branch">

                                                                </x-select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="division_id" useBr id="division-human-resource-report-period-of-employment-select" label="divisi">

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="position_id" useBr id="position-human-resource-report-period-of-employment-select" label="posisi">

                                                            </x-select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <x-select name="status" useBr label="status" id="status-human-resource-report-period-of-employment-select">
                                                                <option value="" selected></option>
                                                                @foreach (['active' => 'Aktif', 'non_active' => 'Tidak Aktif'] as $key => $item)
                                                                    <option value="{{ $key }}">{{ $item }}</option>
                                                                @endforeach
                                                            </x-select>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <div class="col-md-12 text-center">
                                                    <input type="hidden" name="format">
                                                    <x-button color="info" label="preview" class="btn-sm" type="button" fontawesome icon="eye" onclick="$('#human-resource-report-period-of-employment').find(`input[name='format']`).val('preview');$('#human-resource-report-period-of-employment').submit()" />
                                                    <x-button color="danger" label="pdf" class="btn-sm" type="button" fontawesome icon="file-pdf" onclick="$('#human-resource-report-period-of-employment').find(`input[name='format']`).val('pdf');$('#human-resource-report-period-of-employment').submit()" />
                                                    <x-button color="success" label="excel" class="btn-sm" type="button" fontawesome icon="file-excel" onclick="$('#human-resource-report-period-of-employment').find(`input[name='format']`).val('excel');$('#human-resource-report-period-of-employment').submit()" />
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
            const initializePaidLeaveReport = () => {
                initSelect2Search(`branch-human-resource-report-paid-leaves-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#human-resource-report-paid-leaves-modal');

                $('#status-human-resource-report-paid-leaves-select').select2({
                    dropdownParent: $('#human-resource-report-paid-leaves-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializePaidLeaveReport();

            const initializeEmployeePermissionReport = () => {
                initSelect2Search(`branch-human-resource-report-employee-permission-select`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                }, 0, {}, '#human-resource-report-employee-permission-modal');

                $('#status-human-resource-report-employee-permission-select').select2({
                    dropdownParent: $('#human-resource-report-employee-permission-modal'),
                    allowClear: true,
                    width: "100%",
                });
            };

            initializeEmployeePermissionReport();


            $('#human-resource-report-period-of-employment-modal').on('shown.bs.modal', periodOfEmploymentReport);
        });


        const periodOfEmploymentReport = () => {
            initSelect2Search(`branch-human-resource-report-period-of-employment-select`, `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            }, 0, {}, '#human-resource-report-period-of-employment-modal');
            initSelect2Search(`position-human-resource-report-period-of-employment-select`, `{{ route('admin.select.position') }}`, {
                id: "id",
                text: "nama"
            }, 0, {}, '#human-resource-report-period-of-employment-modal');
            initSelect2Search(`division-human-resource-report-period-of-employment-select`, `{{ route('admin.select.division') }}`, {
                id: "id",
                text: "name"
            }, 0, {}, '#human-resource-report-period-of-employment-modal');

            $('#status-human-resource-report-period-of-employment-select').select2({
                dropdownParent: $('#human-resource-report-period-of-employment-modal'),
                placeholder: 'Pilih status',
                allowClear: true,
                width: "100%",
            });
        };

        initSelect2Search(`reset-leave-id`, `{{ route('admin.select.reset-leave') }}`, {
            id: "id",
            text: "period"
        }, 0, {}, '#human-resource-report-paid-leaves-modal');
    </script>

    <script>
        sidebarMenuOpen('#hrd');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
