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

        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            <div class="row my-10">

                @if (get_current_branch()->is_primary)
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="branch" id="branch-selectForm" required>
                                    <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
                                </x-select>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select label="karyawan" id="employee-selectForm" required>
                                @if ($employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endif
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="" label="dari" id="fromDate-input" required value="{{ localDate($from_date) }}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="" label="sampai" id="toDate-input" required value="{{ localDate($to_date) }}" />
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
                <x-table id="attendance_table">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('date') }}</th>
                        <th>{{ Str::headline('karyawan') }}</th>
                        <th>{{ Str::headline('masuk') }}</th>
                        <th>{{ Str::headline('keluar') }}</th>
                        <th>{{ Str::headline('Created At') }}</th>
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
                        branch_id: $('#branch-selectForm').val(),
                        employee_id: $('#employee-selectForm').val(),
                        from_date: $('#fromDate-input').val(),
                        to_date: $('#toDate-input').val(),
                    };

                    const table = $('table#attendance_table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            data: data,
                            url: '{{ route('admin.attendance.show-by-employee') }}'
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'date',
                                name: 'date'
                            },
                            {
                                data: 'employee_name',
                                name: 'employee.name'
                            },
                            {
                                data: 'in_time',
                                name: 'in_time'
                            },
                            {
                                data: 'out_time',
                                name: 'out_time'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
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
