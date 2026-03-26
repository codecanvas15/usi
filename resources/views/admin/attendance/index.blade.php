@extends('layouts.admin.layout.index')

@php
    $main = 'attendance';
    $title = 'presensi';
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

            <div class="row">
                <div class="col-12">
                    @can('create presensi')
                        <x-button color="info" icon="plus" label="Create" :link='route("admin.$main.create")' />
                    @endcan

                    @can('import presensi')
                        <x-button color="info" icon="download" label="import" dataToggle="modal" dataTarget="#import-modal" />
                        <x-button color="danger" icon="trash" label="hapus presensi" dataToggle="modal" dataTarget="#delete-modal" />
                    @endcan

                    @can('export presensi')
                        <x-button color="info" icon="upload" label="export" dataToggle="modal" dataTarget="#export-modal" />
                    @endcan

                    @can('import presensi')
                        <x-modal title="Import Data" id="import-modal" headerColor="info">
                            <x-slot name="modal_body">
                                <form action="{{ route('admin.' . $main . '.import.format') }}">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <x-select name="employee_id" label="karyawan" id="importForm-select-employee">

                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <x-input class="datepicker-input" name="from_date" label="periode presensi" id="importForm-input-fromDate" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <x-input class="datepicker-input" name="to_date" label=" " id="importForm-input-toDate" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <x-button color="info" icon="download" label="import format" />
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <form action="{{ route("admin.$main.import") }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mt-10">
                                        <div class="form-group">
                                            <x-input type="file" name="file" id="" required />
                                        </div>
                                    </div>
                                    <div>
                                        <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                                        <x-button type="submit" color="primary" label="import" />
                                    </div>
                                </form>
                            </x-slot>
                        </x-modal>

                        <x-modal title="Hapus Presensi" id="delete-modal" headerColor="info">
                            <x-slot name="modal_body">
                                <form action="{{ route('admin.' . $main . '.bulk-delete') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <x-select name="employee_id" label="karyawan" id="deleteForm-select-employee">

                                                </x-select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <x-input class="datepicker-input" name="from_date" label="periode presensi" id="deleteForm-input-fromDate" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <x-input class="datepicker-input" name="to_date" label=" " id="deleteForm-input-toDate" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <x-button type="submit" color="danger" icon="trash" label="Delete Presensi" />
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </x-slot>
                        </x-modal>
                    @endcan

                    @can('export presensi')
                        <form action="{{ route("admin.$main.export") }}" method="post" enctype="multipart/form-data" id="form-export">
                            @csrf
                            <x-modal title="Export Data" id="export-modal" headerColor="info">
                                <x-slot name="modal_body">

                                    <div class="form-group">
                                        <x-select name="employee_id" label="karyawan" id="exportForm-select-employee">

                                        </x-select>
                                    </div>
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="from_date" id="exportForm-input-fromDate" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" />
                                    </div>
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="to_date" id="exportForm-input-toDate" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" />
                                    </div>

                                </x-slot>
                                <x-slot name="modal_footer">
                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                                    <x-button type="submit" color="primary" label="Export" />
                                </x-slot>
                            </x-modal>`
                        </form>
                    @endcan
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            <div class="row my-10">

                <div class="row align-items-end">
                    @if (get_current_branch()->is_primary)
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="branch" id="branch-selectForm" required>
                                </x-select>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="" label="Periode Absensi" id="fromDate-input" required value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="" label="" id="toDate-input" required value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" />
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-self-end">
                        <div class="form-group">
                            <x-button color="info" size="sm" icon="search" fontawesome id="btn-init-table" />
                        </div>
                    </div>
                </div>
            </div>

            @can('view presensi')
                <x-table id="employee_table">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('nik') }}</th>
                        <th>{{ Str::headline('nama') }}</th>
                        <th>{{ Str::headline('posisi') }}</th>
                        <th></th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            @endcan
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    @can('view presensi')
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script src="{{ asset('js/admin/select/branch.js') }}"></script>
        <script src="{{ asset('js/admin/select/employee.js') }}"></script>

        <script>
            $(document).ready(() => {

                const initializeDataTable = () => {
                    initBranchSelect('#branch-selectForm');
                    initSelectEmployee('#employee-selectForm');

                    let data = {
                        from_date: $('#fromDate-input').val(),
                        to_date: $('#toDate-input').val(),
                        branch_id: $('#branch-selectForm').val(),
                    };

                    const table = $('table#employee_table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route('admin.attendance.employee') }}',
                            data: data,
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'NIK',
                                name: 'employees.NIK'
                            },
                            {
                                data: 'name',
                                name: 'employees.name'
                            },
                            {
                                data: 'position',
                                name: 'positions.nama'
                            },
                            {
                                data: 'total_attendance',
                                name: 'employees.id'
                            },
                        ]
                    });
                    $('table').css('width', '100%');
                };

                $('#btn-init-table').click(function(e) {
                    e.preventDefault();
                    initializeDataTable();
                });

                initializeDataTable();
            });
        </script>

        <script>
            $(document).ready(function() {
                initSelectEmployee('#exportForm-select-employee', "#export-modal")
                initSelectEmployee('#importForm-select-employee', "#import-modal")
                initSelectEmployee('#deleteForm-select-employee', "#delete-modal")

                $('#form-export').submit(function(e) {

                    $(this).find('input[type=submit]').prop('disabled', false);
                    $(this).find('button[type=submit]').prop('disabled', false);

                    $(this).unbind('submit');
                });
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#hrd-permission-sidebar');
        sidebarActive('#{{ $main }}');
    </script>
@endsection
