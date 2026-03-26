@extends('layouts.admin.layout.index')

@php
    $main = 'user';
    $title = 'pengguna';
@endphp

@section('title', Str::headline($title) . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
    <style>
        #DataTables_Table_0_wrapper .row:nth-child(1) {
            justify-content: start;
        }
    </style>
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
    @can('view user')
        <x-card-data-table title="{{ $title }}">
            <x-slot name="header_content">
                <div class="row justify-content-between mb-4">
                    <div class="col-md-12">
                        @can('create user')
                            <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                        @endcan
                        @can('export user')
                            <x-button link='{{ route("admin.$main.export") }}' color="info" icon="upload" label="export" />
                        @endcan
                        @can('import user')
                            <x-button link='{{ route("admin.$main.import") }}' color="info" icon="download" label="import" dataToggle="modal" dataTarget="#import-modal" />
                            <x-modal title="import data" id="import-modal" headerColor="info">
                                <x-slot name="modal_body">
                                    <x-button link='{{ route("admin.$main.import-format") }}' color="info" icon="download" label="import format" />

                                    <div class="mt-30">
                                        <form action='{{ route("admin.$main.import") }}' method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-group">
                                                <x-input type="file" label="file" name="file" required />
                                            </div>
                                            <x-button color="info" icon="download" label="import" />
                                        </form>
                                    </div>

                                </x-slot>
                            </x-modal>
                        @endcan

                    </div>
                </div>
                <div class="row" id="createdFilter">
                    <div class="col-md-2">
                        <x-select name="user_type" id="user_type" class="form-control">
                            <option value="">Semua</option>
                            <option value="employee">Pegawai</option>
                            <option value="non-employee">Bukan Pegawai</option>
                            <option value="vendor">Vendor</option>
                        </x-select>
                    </div>
                    <div class="col-md-1 row align-self-end">
                        <div class="form-group">
                            <x-button type="submit" color="primary" size="sm" icon="search" fontawesome id="search-btn" />
                        </div>
                    </div>
                </div>
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <x-table>
                    <x-slot name="table_head">
                        <th>{{ Str::upper('#') }}</th>
                        <th>{{ Str::upper('Username') }}</th>
                        <th>{{ Str::upper('Pegawai') }}</th>
                        <th>{{ Str::upper('role') }}</th>
                        <th>{{ Str::upper('Email') }}</th>
                        <th>{{ Str::upper('Created At') }}</th>
                        <th>{{ Str::upper('Last Modified At') }}</th>
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
    @can('view user')
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script>
            const table = $('table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                columnDefs: [{
                        "width": "10%",
                        "targets": 1
                    },
                    {
                        "width": "20%",
                        "targets": 3
                    },
                    {
                        "width": "20%",
                        "targets": 4
                    },
                ],
                ajax: {
                    url: '{{ route("admin.$main.index") }}',
                    type: 'get',
                    data: {
                        _token: token,
                        user_type: function() {
                            return $('#user_type').val();
                        }
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'employee',
                        name: 'employee'
                    },
                    {
                        data: 'roles',
                        name: 'id',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
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

            $('#search-btn').on('click', () => {
                table.ajax.reload();
            });
        </script>
    @endcan

    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-user-sidebar');
        sidebarActive('#user-sidebar')
    </script>
@endsection
