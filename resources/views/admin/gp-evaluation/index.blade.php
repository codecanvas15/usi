@extends('layouts.admin.layout.index')

@php
    $main = 'gp-evaluation';
    $permission = 'evaluation';
    $title = 'Assessment Karyawan';
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
                        HRD
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline($title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ $title }}">
        <x-slot name="header_content">
            @can("create $permission")
                <div class="row justify-content-between mb-4">
                    <div class="col-md-3 col-md-6 col-xl-4">
                        <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                    </div>
                </div>
            @endcan
            <div class="row align-items-end">
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="from_date" label="from date" value="" id="payroll-from-date" required />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="to" label="to date" value="" id="payroll-to-date" required />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <x-button type="submit" color="primary" id="set-gp-evaluation-table" icon="search" fontawesome />
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')
            <x-table id="gp-evaluation">
                <x-slot name="table_head">
                    <th>{{ Str::headline('#') }}</th>
                    <th>{{ Str::headline('Kode') }}</th>
                    <th>{{ Str::headline('Tanggal') }}</th>
                    <th>{{ Str::headline('Karyawan') }}</th>
                    <th>{{ Str::headline('Reviewer') }}</th>
                    <th>{{ Str::headline('Keterangan') }}</th>
                    <th>{{ Str::headline('Status') }}</th>
                    <th>Aksi</th>
                </x-slot>
                <x-slot name="table_body"></x-slot>
            </x-table>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        $(document).ready(() => {
            const gpEvaluationTable = () => {
                const table = $('table#gp-evaluation').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    ajax: {
                        url: '{{ route("admin.$main.index") }}',
                        data: {
                            from_date: $('#from-date').val(),
                            to_date: $('#to-date').val()
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'reference',
                            name: 'reference'
                        },
                        {
                            data: 'date',
                            name: 'date'
                        },
                        {
                            data: 'employee_id',
                            name: 'employee_id'
                        },
                        {
                            data: 'created_by',
                            name: 'created_by'
                        },
                        {
                            data: 'notes',
                            name: 'notes'
                        },
                        {
                            data: 'approval_status',
                            name: 'approval_status'
                        },
                        {
                            "data": "action"
                        },
                    ]
                });
                $('table').css('width', '100%');
            }

            gpEvaluationTable()

            $('#set-gp-evaluation-table').click(function(e) {
                e.preventDefault();
                gpEvaluationTable()
            })
        });
    </script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarActive('#gp-evaluation')
    </script>
@endsection
