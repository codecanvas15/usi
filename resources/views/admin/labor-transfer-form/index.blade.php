@extends('layouts.admin.layout.index')

@php
    $main = 'labor-transfer-form';
    $title = 'Formulir Pemindahan Tenaga Kerja';
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
    @can("view $main")
        <x-card-data-table title="{{ $title }}">
            <x-slot name="header_content">
                @can("create $main")
                    <div class="row justify-content-between mb-4">
                        <div class="col-md-3 col-md-6 col-xl-4">
                            <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                        </div>
                    </div>
                @endcan
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <div class="row">
                    @if (get_current_branch()->is_primary)
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="branch" id="branch-selectForm" required>
                                    <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
                                </x-select>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-select label="Karyawan" id="employee-selectForm" required></x-select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-select label="Approval Status" id="status-selectForm" required></x-select>
                        </div>
                    </div>
                </div>
                <div class="row mb-20">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="" label="dari" id="fromDate-input" required />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="" label="sampai" id="toDate-input" required />
                        </div>
                    </div>
                    <div class="col-auto d-flex align-self-end">
                        <div class="form-group">
                            <x-button color="info" size="sm" icon="search" fontawesome id="btn-init-table" />
                        </div>
                    </div>
                </div>
                <x-table id="labor_transfer_form">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('Kode') }}</th>
                        <th>{{ Str::headline('Tanggal') }}</th>
                        <th>{{ Str::headline('Karyawan') }}</th>
                        <th>{{ Str::headline('Diajukan Oleh') }}</th>
                        <th>{{ Str::headline('Status Pengajuan') }}</th>
                        <th>{{ Str::headline('Aksi') }}</th>
                        <th>{{ Str::headline('export') }}</th>
                    </x-slot>
                    <x-slot name="table_body"></x-slot>
                </x-table>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5//dt-1.12.1/datatables.min.js"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/branch.js') }}"></script>
    <script src="{{ asset('js/admin/select/employee.js') }}"></script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#contract-sidebar');
        sidebarActive('#labor-transfer-form');
    </script>
    <script>
        $(document).ready(function() {
            const init = () => {
                initBranchSelect('#branch-selectForm');
                initSelectEmployee("#employee-selectForm");

                let data = {
                    branch: $('#branch-selectForm').val(),
                    employee_id: $('#employee-selectForm').val(),
                    approval_status: $('#status-selectForm').val(),
                    from_date: $('#fromDate-input').val(),
                    to_date: $('#toDate-input').val(),
                };

                const table = $('table#labor_transfer_form').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    ajax: {
                        url: '{{ route("admin.$main.index") }}',
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            "data": "reference"
                        },
                        {
                            "data": "created_at"
                        },
                        {
                            "data": "employee_id"
                        },
                        {
                            "data": "submitted_by"
                        },
                        {
                            "data": "approval_status"
                        },
                        {
                            "data": "action"
                        },
                        {
                            "data": "export"
                        },
                    ]
                });
                $('table').css('width', '100%');
            };

            $('#btn-init-table').click(function() {
                init();
            });

            init();
        });
    </script>
@endsection
