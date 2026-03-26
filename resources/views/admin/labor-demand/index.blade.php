@extends('layouts.admin.layout.index')

@php
    $main = 'labor-demand';
    $title = 'Permintaan Tenaga Kerja';
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
                    <div class="row mb-4">
                        <div class="col-md-2 col-xl-2">
                            <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                        </div>
                        <div class="col-md-3 col-xl-4">
                            <x-button link='{{ route("admin.$main.download") }}' color="info" icon="download" label="form permintaan tenaga kerja" />
                        </div>
                    </div>
                @endcan
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="branch_id" label="branch" id="branch-select"></x-select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 ">
                        <div class="form-group">
                            <x-select name="division_id" label="divisi" id="division-select"></x-select>
                        </div>
                    </div>
                    <div class="col-md-3 ">
                        <div class="form-group">
                            <x-select name="" label='status' id="status-select">

                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="from_date" label="dari tanggal" id="from_date" value="" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="to_date" label="sampai tanggal" id="to_date" value="" required />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-self-end">
                        <div class="form-group">
                            <x-button color="primary" icon="search" fontawesome size="sm" id="init-table" />
                        </div>
                    </div>
                </div>

                <x-table id="labor_demand_table">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('kode') }}</th>
                        <th>{{ Str::headline('divisi') }}</th>
                        <th>{{ Str::headline('branch') }}</th>
                        <th>{{ Str::headline('dibuat oleh') }}</th>
                        <th>{{ Str::headline('status') }}</th>
                        <th>{{ Str::headline('dibuat pada') }}</th>
                        <th>{{ Str::headline('action') }}</th>
                        <th>{{ Str::headline('export') }}</th>
                    </x-slot>
                    <x-slot name="table_body"></x-slot>
                </x-table>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    @can("view $main")
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs5//dt-1.12.1/datatables.min.js"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/admin/select/branch.js') }}"></script>

        <script>
            $(document).ready(function() {

                const initTable = () => {

                    initSelect2Search(`branch-select`, "{{ route('admin.select.branch') }}", {
                        id: "id",
                        text: "name"
                    })

                    initSelect2Search(`division-select`, "{{ route('admin.select.division') }}", {
                        id: "id",
                        text: "name"
                    })

                    const table = $('table#labor_demand_table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route("admin.$main.index") }}',
                            data: {
                                branch_id: $('#branch-select').val(),
                                division_id: $('#division-select').val(),
                                status: $('#status-select').val(),
                                from_date: $('#from_date').val(),
                                to_date: $('#to_date').val(),
                            }
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                "data": "code"
                            },
                            {
                                "data": "division_id"
                            },
                            {
                                "data": "branch_id"
                            },
                            {
                                "data": "user_id"
                            },
                            {
                                "data": "status"
                            },
                            {
                                "data": "created_at",
                                "name": 'created_at'
                            },
                            {
                                "data": "action",
                                orderable: false,
                                searchable: false
                            },
                            {
                                "data": "export",
                                orderable: false,
                                searchable: false
                            },
                        ]
                    });

                    $('table').css('width', '100%');
                };

                initTable();

                $('#init-table').click(function(e) {
                    e.preventDefault();
                    initTable();
                });
            });
        </script>

        <script>
            sidebarMenuOpen('#hrd');
            sidebarMenuOpen('#rekrutment-sidebar');
            sidebarActive('#labor-demand');
        </script>
    @endcan
@endsection
