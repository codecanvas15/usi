@extends('layouts.admin.layout.index')

@php
    $main = 'mass-leave';
    $title = 'cuti-bersama';
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
                            <x-button color="info" icon="plus" label="Create" link="{{ route('admin.mass-leave.create') }}" />
                        </div>
                    </div>
                @endcan
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                @if (get_current_branch()->is_primary)
                    <div class="row">
                        <div class="col-md-3">
                            <x-select name="branch_id" label="branch" id="branch-select" required></x-select>
                        </div>
                    </div>
                @endif

                <div class="row mb-10">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="" label="dari" id="from-date" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="" label="sampai" id="to-date" required />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-self-end">
                        <div class="form-group">
                            <x-button color="primary" icon="search" fontawesome size="sm" id="btn-init" />
                        </div>
                    </div>
                </div>

                <x-table id="leave">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('tanggal') }}</th>
                        <th>{{ Str::headline('keperluan') }}</th>
                        <th>{{ Str::headline('dari') }}</th>
                        <th>{{ Str::headline('sampai') }}</th>
                        <th></th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>

            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/admin/select/employee.js') }}"></script>
    <script src="{{ asset('js/admin/select/branch.js') }}"></script>
    <script>
        var table;

        initSelectEmployee('#employee-select')
        initBranchSelect('#branch-select')

        function setTable() {

            let data = {
                branch_id: $('#branch-select').val(),
                from_date: $('#from-date').val(),
                to_date: $('#to-date').val(),
                _token: token,
            }
            table = $('table#leave').DataTable({
                "language": {
                    "emptyTable": "Tidak ada data"
                },
                "lengthMenu": [
                    [25, 50, 75, 100, -1],
                    [25, 50, 75, 100, 'All'],
                ],
                "destroy": true,
                "processing": true,
                "serverSide": true,
                "responsive": true,
                "columnDefs": [{
                    "targets": [0],
                    "visible": false,
                    "searchable": false
                }],
                order: [
                    [1, 'desc']
                ],
                "ajax": {
                    "url": `{{ route("admin.$main.data") }}`,
                    "dataType": "json",
                    "type": "POST",
                    "data": data,
                    error: function(err) {
                        console.log(err);
                    }
                },
                "columns": [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        "data": "date"
                    },
                    {
                        "data": "necessary"
                    },
                    {
                        "data": "from_date"
                    },
                    {
                        "data": "to_date"
                    },
                    {
                        "data": "action",
                        "orderable": false,
                        "searchable": false
                    },
                ],
            });
        }

        $('#btn-init').click(function(e) {
            e.preventDefault();
            setTable();
        });

        setTable();

        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#hrd-permission-sidebar');
        sidebarActive('#mass-leave')
    </script>
@endsection
